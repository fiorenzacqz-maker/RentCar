<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PaymentSeeder extends Seeder
{
    /**
     * Seed payments table dengan bukti transfer yang valid.
     */
    public function run(): void
    {
        $bookings = Booking::with('user')->get();

        foreach ($bookings as $booking) {
            $paymentStatus = match ($booking->booking_status) {
                'completed', 'ongoing', 'confirmed' => 'verified',
                'cancelled' => 'rejected',
                default => 'pending',
            };

            $method = $this->getMethod($booking->id);
            $proofPath = 'payments/bukti_transfer_' . $booking->id . '.jpg';

            $this->ensureDummyReceipt(
                $proofPath,
                'BOOKING #' . $booking->id,
                $method,
                'Rp ' . number_format((float) $booking->total_price, 0, ',', '.'),
                strtoupper($paymentStatus)
            );

            Payment::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'payment_method' => $method,
                    'amount' => $booking->total_price,
                    'proof_image' => $proofPath,
                    'payment_status' => $paymentStatus,
                    'paid_at' => $booking->created_at ?? now(),
                ]
            );
        }
    }

    private function getMethod(int $id): string
    {
        $methods = [
            'Transfer Bank',
            'E-Wallet',
            'Tunai',
        ];

        return $methods[$id % count($methods)];
    }

    /**
     * Buat gambar struk bukti transfer JPG jika belum ada.
     */
    private function ensureDummyReceipt(string $relativePath, string $title, string $method, string $amount, string $status): void
    {
        $storagePath = Storage::disk('public')->path($relativePath);
        $publicPath = public_path('storage/' . $relativePath);

        if (! File::exists($storagePath)) {
            File::ensureDirectoryExists(dirname($storagePath));

            if (extension_loaded('gd')) {
                $img = imagecreatetruecolor(600, 420);
                $bg = imagecolorallocate($img, 248, 250, 252);
                $navy = imagecolorallocate($img, 15, 37, 87);
                $slate = imagecolorallocate($img, 71, 85, 105);
                $blue = imagecolorallocate($img, 37, 99, 235);

                imagefilledrectangle($img, 0, 0, 600, 420, $bg);
                imagefilledrectangle($img, 0, 0, 600, 70, $navy);
                imagestring($img, 5, 30, 26, 'BUKTI PEMBAYARAN RENTCAR', $bg);
                imagestring($img, 5, 30, 110, $title, $navy);
                imagestring($img, 4, 30, 155, 'Metode : ' . $method, $slate);
                imagestring($img, 5, 30, 195, 'Total  : ' . $amount, $blue);
                imagestring($img, 4, 30, 245, 'Status : ' . $status, $slate);
                imagejpeg($img, $storagePath, 85);
                imagedestroy($img);
            }
        }

        if (File::exists($storagePath) && ! is_link(public_path('storage')) && ! File::exists($publicPath)) {
            File::ensureDirectoryExists(dirname($publicPath));
            File::copy($storagePath, $publicPath);
        }
    }
}
