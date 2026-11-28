<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Car;
use App\Models\CarCategory;
use App\Models\CarReturn;
use App\Models\Payment;
use App\Models\Review;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RoleAndFeatureTest extends TestCase
{
    use RefreshDatabase;

    private function car(): Car
    {
        $category = CarCategory::firstOrCreate(['name' => 'MPV'], ['description' => 'Keluarga']);

        return Car::create([
            'category_id' => $category->id, 'name' => 'Avanza', 'brand' => 'Toyota',
            'plate_number' => 'AB 1 CD', 'year' => 2024, 'transmission' => 'automatic',
            'seats' => 7, 'price_per_day' => 350000, 'image' => 'cars/avanza.jpg', 'status' => 'available',
        ]);
    }

    private function booking(User $user, Car $car, array $override = []): Booking
    {
        return Booking::create(array_merge([
            'user_id' => $user->id, 'car_id' => $car->id,
            'start_date' => now()->addDay(), 'end_date' => now()->addDays(2),
            'total_days' => 1, 'total_price' => 350000, 'booking_status' => 'pending',
        ], $override));
    }

    public function test_login_and_dashboard_redirect_per_role(): void
    {
        foreach (['admin' => 'admin.dashboard', 'staff' => 'staff.dashboard', 'customer' => 'customer.dashboard'] as $role => $route) {
            $user = User::factory()->create(['role' => $role]);
            $this->actingAs($user)->get('/dashboard')->assertRedirect(route($route));
        }
    }

    public function test_role_middleware_separates_access(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = User::factory()->create(['role' => 'customer']);

        // Admin-only
        foreach (['/admin/users', '/admin/reports', '/admin/settings', '/admin/dashboard'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
            $this->actingAs($staff)->get($url)->assertForbidden();
            $this->actingAs($customer)->get($url)->assertForbidden();
        }

        // Staff & admin
        foreach (['/admin/cars', '/admin/rentals', '/admin/payments', '/admin/fines'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
            $this->actingAs($staff)->get($url)->assertOk();
            $this->actingAs($customer)->get($url)->assertForbidden();
        }

        $this->actingAs($staff)->get('/staff/dashboard')->assertOk();
        $this->actingAs($admin)->get('/staff/dashboard')->assertForbidden();
        $this->actingAs($staff)->get('/customer/dashboard')->assertForbidden();
    }

    public function test_admin_can_manage_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'Petugas Baru', 'email' => 'petugas@example.com', 'role' => 'staff',
            'password' => 'secret123', 'password_confirmation' => 'secret123',
        ])->assertRedirect(route('admin.users.index'));

        $staff = User::where('email', 'petugas@example.com')->firstOrFail();
        $this->assertEquals('staff', $staff->role);

        $this->actingAs($admin)->put(route('admin.users.update', $staff), [
            'name' => 'Petugas Ubah', 'email' => 'petugas@example.com', 'role' => 'customer',
        ])->assertRedirect();
        $this->assertEquals('customer', $staff->fresh()->role);

        // tidak boleh hapus diri sendiri
        $this->actingAs($admin)->delete(route('admin.users.destroy', $admin))->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);

        $this->actingAs($admin)->delete(route('admin.users.destroy', $staff))->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $staff->id]);
    }

    public function test_server_calculates_total_with_driver_and_ignores_client_total(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $car = $this->car();
        Setting::set('driver_fee_per_day', 150000);

        $this->actingAs($customer)->post(route('customer.rentals.store'), [
            'car_id' => $car->id,
            'borrower_name' => 'Budi', 'borrower_phone' => '08123456789',
            'service_type' => 'with_driver',
            'start_date' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_date' => now()->addDays(3)->format('Y-m-d\TH:i'),
            'payment_method' => 'Transfer Bank',
            'total_price' => 1, // manipulasi klien harus diabaikan
        ])->assertSessionHasNoErrors();

        $booking = Booking::firstOrFail();
        $this->assertEquals(2, $booking->total_days);
        $this->assertTrue($booking->with_driver);
        $this->assertEquals(300000, (float) $booking->driver_fee);
        $this->assertEquals(2 * 350000 + 300000, (float) $booking->total_price);
        $this->assertNotNull($booking->payment_deadline);
        $this->assertEquals((float) $booking->total_price, (float) $booking->payment->amount);
    }

    public function test_borrower_fields_and_service_type_are_required(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $car = $this->car();

        $this->actingAs($customer)->post(route('customer.rentals.store'), [
            'car_id' => $car->id,
            'start_date' => now()->addDay()->format('Y-m-d\TH:i'),
            'end_date' => now()->addDays(2)->format('Y-m-d\TH:i'),
            'payment_method' => 'Tunai',
        ])->assertSessionHasErrors(['borrower_name', 'borrower_phone', 'service_type']);
    }

    public function test_overdue_unpaid_booking_is_auto_cancelled_but_awaiting_verification_is_kept(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $car = $this->car();

        $unpaid = $this->booking($customer, $car, ['payment_deadline' => now()->subMinute()]);
        Payment::create(['booking_id' => $unpaid->id, 'amount' => 350000, 'payment_status' => 'pending']);

        $car2 = Car::create([
            'category_id' => $car->category_id, 'name' => 'Brio', 'brand' => 'Honda', 'plate_number' => 'AB 2 CD',
            'year' => 2024, 'transmission' => 'manual', 'seats' => 5, 'price_per_day' => 300000, 'status' => 'available',
        ]);
        $withProof = $this->booking($customer, $car2, ['payment_deadline' => now()->subMinute()]);
        Payment::create(['booking_id' => $withProof->id, 'amount' => 350000, 'payment_status' => 'pending', 'proof_image' => 'payments/x.jpg']);

        $this->assertEquals(1, Booking::expireOverdue());
        $this->assertEquals('cancelled', $unpaid->fresh()->booking_status);
        $this->assertEquals('rejected', $unpaid->payment->fresh()->payment_status);
        $this->assertEquals('pending', $withProof->fresh()->booking_status);
    }

    public function test_staff_can_process_return_with_damage_and_customer_pays_fine(): void
    {
        Storage::fake('public');
        $staff = User::factory()->create(['role' => 'staff']);
        $customer = User::factory()->create(['role' => 'customer']);
        $car = $this->car();
        $booking = $this->booking($customer, $car, [
            'start_date' => now()->subDays(2), 'end_date' => now()->subHours(2), 'booking_status' => 'ongoing',
        ]);

        $this->actingAs($staff)->post(route('admin.rentals.return', $booking), [
            'actual_return_date' => now()->format('Y-m-d H:i:s'),
            'condition_description' => 'Lecet di bumper.',
            'damage_fee' => 250000,
        ])->assertRedirect();

        $return = $booking->fresh()->carReturn;
        $this->assertEquals(250000, (float) $return->damage_fee);
        $this->assertEquals((float) $return->late_fee + 250000, (float) $return->total_fine);
        $this->assertEquals('unpaid', $return->fine_status);

        // Customer lain tidak boleh melunasi (IDOR)
        $other = User::factory()->create(['role' => 'customer']);
        $this->actingAs($other)->post(route('customer.fines.store', $booking), [
            'fine_payment_method' => 'Transfer Bank', 'fine_proof_image' => UploadedFile::fake()->image('b.jpg'),
        ])->assertForbidden();

        // Pemilik mengunggah bukti
        $this->actingAs($customer)->post(route('customer.fines.store', $booking), [
            'fine_payment_method' => 'Transfer Bank', 'fine_proof_image' => UploadedFile::fake()->image('b.jpg'),
        ])->assertSessionHasNoErrors();
        $this->assertEquals('waiting', $return->fresh()->fine_status);

        // Petugas memverifikasi
        $this->actingAs($staff)->post(route('admin.fines.verify', $return))->assertRedirect();
        $this->assertEquals('paid', $return->fresh()->fine_status);
        $this->assertNotNull($return->fresh()->fine_paid_at);
    }

    public function test_review_only_for_own_completed_booking_once(): void
    {
        $customer = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $car = $this->car();
        $ongoing = $this->booking($customer, $car, ['booking_status' => 'ongoing']);
        $done = $this->booking($customer, $car, ['booking_status' => 'completed', 'start_date' => now()->subDays(5), 'end_date' => now()->subDays(3)]);

        // belum selesai -> ditolak
        $this->actingAs($customer)->post(route('customer.reviews.store', $ongoing), ['rating' => 5])->assertRedirect();
        $this->assertEquals(0, Review::count());

        // bukan pemilik -> 403
        $this->actingAs($other)->post(route('customer.reviews.store', $done), ['rating' => 5])->assertForbidden();

        // rating tidak valid
        $this->actingAs($customer)->post(route('customer.reviews.store', $done), ['rating' => 9])->assertSessionHasErrors('rating');

        // sukses sekali saja
        $this->actingAs($customer)->post(route('customer.reviews.store', $done), ['rating' => 4, 'comment' => 'Bagus'])->assertSessionHas('success');
        $this->actingAs($customer)->post(route('customer.reviews.store', $done), ['rating' => 1])->assertSessionHas('error');
        $this->assertEquals(1, Review::count());

        // tampil di halaman detail mobil
        $this->get(route('cars.show', $car))->assertOk()->assertSee('Bagus');
    }

    public function test_customer_cannot_view_other_customers_booking(): void
    {
        $owner = User::factory()->create(['role' => 'customer']);
        $other = User::factory()->create(['role' => 'customer']);
        $booking = $this->booking($owner, $this->car());

        $this->actingAs($other)->get(route('customer.rentals.show', $booking))->assertForbidden();
        $this->actingAs($other)->post(route('customer.rentals.cancel', $booking))->assertForbidden();
    }

    public function test_admin_can_update_settings_and_export_report(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin)->put(route('admin.settings.update'), [
            'payment_deadline_minutes' => 90, 'driver_fee_per_day' => 200000,
            'late_fee_mode' => 'day', 'late_fee_per_hour' => 30000, 'late_fee_per_day' => 150000,
            'bank_info' => 'BCA 111',
        ])->assertSessionHasNoErrors();

        $this->assertEquals(90, (int) Setting::get('payment_deadline_minutes'));
        $this->assertEquals(200000, (int) Setting::get('driver_fee_per_day'));

        $this->actingAs($admin)->get(route('admin.reports.export', ['from' => now()->subMonth()->toDateString(), 'to' => now()->toDateString()]))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }
}
