<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RentalFlowTest extends TestCase
{
    use RefreshDatabase;

    private function createCar(string $plate = 'AB 1234 XY'): Car
    {
        $category = CarCategory::firstOrCreate(['name' => 'MPV'], ['description' => 'Mobil keluarga']);

        return Car::create([
            'category_id' => $category->id,
            'name' => 'Avanza',
            'brand' => 'Toyota',
            'plate_number' => $plate,
            'year' => 2024,
            'transmission' => 'automatic',
            'seats' => 7,
            'price_per_day' => 350000,
            'image' => 'cars/avanza.jpg',
            'status' => 'available',
        ]);
    }

    public function test_customer_can_book_car_and_prevent_date_conflict(): void
    {
        Storage::fake('public');

        $customer1 = User::factory()->create(['role' => 'customer']);
        $customer2 = User::factory()->create(['role' => 'customer']);
        $car = $this->createCar();

        $start = now()->addDays(1)->format('Y-m-d');
        $end = now()->addDays(3)->format('Y-m-d');

        // Booking pertama sukses
        $response = $this->actingAs($customer1)->post(route('customer.rentals.store'), [
            'car_id' => $car->id,
            'borrower_name' => 'Peminjam Satu',
            'borrower_phone' => '081234567890',
            'service_type' => 'self_drive',
            'start_date' => $start,
            'end_date' => $end,
            'payment_method' => 'Transfer Bank',
            'proof_image' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $booking = Booking::first();
        $this->assertNotNull($booking);
        $this->assertEquals(2, $booking->total_days);
        $this->assertEquals(700000, (float) $booking->total_price);
        $response->assertRedirect(route('customer.rentals.show', $booking));

        // Booking kedua di tanggal bertabrakan harus ditolak
        $conflictResponse = $this->actingAs($customer2)->post(route('customer.rentals.store'), [
            'car_id' => $car->id,
            'borrower_name' => 'Peminjam Dua',
            'borrower_phone' => '081234567891',
            'service_type' => 'self_drive',
            'start_date' => now()->addDays(2)->format('Y-m-d'),
            'end_date' => now()->addDays(4)->format('Y-m-d'),
            'payment_method' => 'Tunai',
        ]);

        $conflictResponse->assertSessionHasErrors('start_date');
        $this->assertEquals(1, Booking::count());
    }

    public function test_admin_can_verify_payment_handover_and_process_return_with_late_fee(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $customer = User::factory()->create(['role' => 'customer']);
        $car = $this->createCar('B 9999 ZZ');

        $booking = Booking::create([
            'user_id' => $customer->id,
            'car_id' => $car->id,
            'start_date' => now()->subDays(3)->format('Y-m-d H:i:s'),
            'end_date' => now()->subHours(5)->format('Y-m-d H:i:s'),
            'total_days' => 2,
            'total_price' => 700000,
            'booking_status' => 'pending',
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'Transfer Bank',
            'amount' => 700000,
            'payment_status' => 'pending',
        ]);

        // 1. Verify payment -> Confirmed
        $this->actingAs($admin)->post(route('admin.payments.verify', $payment))->assertRedirect();
        $this->assertEquals('verified', $payment->fresh()->payment_status);
        $this->assertEquals('confirmed', $booking->fresh()->booking_status);

        // 2. Handover -> Ongoing
        $this->actingAs($admin)->post(route('admin.rentals.handover', $booking))->assertRedirect();
        $this->assertEquals('ongoing', $booking->fresh()->booking_status);

        // 3. Return 1 day late -> Completed + Late fee
        $this->actingAs($admin)->post(route('admin.rentals.return', $booking), [
            'actual_return_date' => now()->format('Y-m-d H:i:s'),
            'condition_description' => 'Kondisi baik.',
            'late_fee_per_day' => 100000,
        ])->assertRedirect();

        $booking->refresh();
        $this->assertEquals('completed', $booking->booking_status);
        $this->assertEquals(1, $booking->carReturn->late_days);
        $this->assertEquals(100000, (float) $booking->carReturn->late_fee);
        $this->assertEquals(100000, (float) $booking->carReturn->total_fine);
        $this->assertEquals('unpaid', $booking->carReturn->fine_status);
    }

    public function test_admin_can_crud_category_and_car(): void
    {
        Storage::fake('public');
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.categories.store'), [
            'name' => 'Sedan',
            'description' => 'Mobil sedan nyaman',
        ])->assertRedirect();

        $category = CarCategory::where('name', 'Sedan')->firstOrFail();

        $this->actingAs($admin)->post(route('admin.cars.store'), [
            'category_id' => $category->id,
            'name' => 'Civic',
            'brand' => 'Honda',
            'plate_number' => 'AB 8888 CC',
            'year' => 2024,
            'transmission' => 'automatic',
            'seats' => 5,
            'price_per_day' => 650000,
            'status' => 'available',
            'image' => UploadedFile::fake()->image('civic.jpg'),
        ])->assertRedirect(route('admin.cars.index'));

        $this->assertDatabaseHas('cars', ['plate_number' => 'AB 8888 CC']);
    }

    public function test_user_can_update_profile_and_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpass123'),
        ]);

        $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Nama Baru',
            'phone' => '081234567899',
            'address' => 'Yogyakarta',
        ])->assertRedirect();

        $this->assertEquals('Nama Baru', $user->fresh()->name);

        $this->actingAs($user)->put(route('profile.password'), [
            'current_password' => 'oldpass123',
            'password' => 'newpass123',
            'password_confirmation' => 'newpass123',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('newpass123', $user->fresh()->password));
    }
}
