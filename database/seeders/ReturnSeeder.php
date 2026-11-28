<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\CarReturn;
use Illuminate\Database\Seeder;

class ReturnSeeder extends Seeder
{
    /**
     * Seed returns table.
     */
    public function run(): void
    {
        // 1. Booking (Kafka - Avanza): Selesai, tepat waktu
        $b1 = Booking::where('booking_status', 'completed')
            ->whereHas('car', fn($q) => $q->where('name', 'Avanza'))
            ->first();

        if ($b1) {
            $endDateStr = is_string($b1->end_date) ? $b1->end_date : $b1->end_date->format('Y-m-d');

            CarReturn::updateOrCreate(
                ['booking_id' => $b1->id],
                [
                    'actual_return_date' => $endDateStr . ' 16:30:00',
                    'condition_description' => 'Mobil kembali dalam kondisi mulus, bersih, bensin full, tidak ada lecet.',
                    'late_days' => 0,
                    'late_fee' => 0.00,
                    'return_status' => 'completed',
                ]
            );
        }

        // 2. Booking (Ahmad - Xpander): Ongoing (belum kembali)
        $b2 = Booking::where('booking_status', 'ongoing')->first();
        if ($b2) {
            CarReturn::updateOrCreate(
                ['booking_id' => $b2->id],
                [
                    'actual_return_date' => null,
                    'condition_description' => 'Mobil masih dalam masa sewa (ongoing).',
                    'late_days' => 0,
                    'late_fee' => 0.00,
                    'return_status' => 'pending',
                ]
            );
        }

        // 3. Booking (Dewi - Ertiga): Selesai, telat 1 hari
        $b5 = Booking::where('booking_status', 'completed')
            ->whereHas('car', fn($q) => $q->where('name', 'Ertiga'))
            ->first();

        if ($b5) {
            $endDateStr = is_string($b5->end_date) ? $b5->end_date : $b5->end_date->format('Y-m-d');

            CarReturn::updateOrCreate(
                ['booking_id' => $b5->id],
                [
                    'actual_return_date' => date('Y-m-d H:i:s', strtotime($endDateStr . ' +1 day 10:00:00')),
                    'condition_description' => 'Kondisi mesin & bodi sangat baik. Terlambat 1 hari karena macet di jalur Puncak, denda disetujui penyewa.',
                    'late_days' => 1,
                    'late_fee' => 100000.00,
                    'total_fine' => 100000.00,
                    'fine_status' => 'unpaid',
                    'return_status' => 'completed',
                ]
            );
        }

        // 4. Booking (Kafka - Rush): Selesai, tepat waktu
        $b7 = Booking::where('booking_status', 'completed')
            ->whereHas('car', fn($q) => $q->where('name', 'Rush'))
            ->first();

        if ($b7) {
            $endDateStr = is_string($b7->end_date) ? $b7->end_date : $b7->end_date->format('Y-m-d');

            CarReturn::updateOrCreate(
                ['booking_id' => $b7->id],
                [
                    'actual_return_date' => $endDateStr . ' 17:00:00',
                    'condition_description' => 'Mobil kembali prima dan bersih terawat.',
                    'late_days' => 0,
                    'late_fee' => 0.00,
                    'return_status' => 'completed',
                ]
            );
        }
    }
}
