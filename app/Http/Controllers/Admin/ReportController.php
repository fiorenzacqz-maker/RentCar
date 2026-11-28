<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        [$from, $to, $status] = $this->filters($request);

        $bookings = $this->query($from, $to, $status)->latest()->paginate(15)->withQueryString();

        return view('admin.reports.index', [
            'bookings' => $bookings,
            'summary' => $this->summary($from, $to, $status),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'status' => $status,
        ]);
    }

    public function print(Request $request): View
    {
        [$from, $to, $status] = $this->filters($request);

        return view('admin.reports.print', [
            'bookings' => $this->query($from, $to, $status)->latest()->get(),
            'summary' => $this->summary($from, $to, $status),
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'status' => $status,
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        [$from, $to, $status] = $this->filters($request);
        $filename = 'laporan-rentcar-' . $from->toDateString() . '_' . $to->toDateString() . '.csv';

        return response()->streamDownload(function () use ($from, $to, $status): void {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
            fputcsv($out, ['ID', 'Tanggal Order', 'Customer', 'Peminjam', 'Mobil', 'Mulai', 'Selesai', 'Hari', 'Sopir', 'Biaya Sopir', 'Total Sewa', 'Status Booking', 'Status Bayar', 'Denda Telat', 'Denda Kerusakan', 'Total Denda', 'Status Denda']);

            $this->query($from, $to, $status)->orderBy('id')->chunkById(200, function ($rows) use ($out): void {
                foreach ($rows as $b) {
                    fputcsv($out, [
                        $b->id,
                        $b->created_at->format('Y-m-d H:i'),
                        $b->user->name ?? '-',
                        $b->borrower_name ?? '-',
                        trim(($b->car->brand ?? '') . ' ' . ($b->car->name ?? '')),
                        $b->start_date->format('Y-m-d H:i'),
                        $b->end_date->format('Y-m-d H:i'),
                        $b->total_days,
                        $b->with_driver ? 'Ya' : 'Tidak',
                        (float) $b->driver_fee,
                        (float) $b->total_price,
                        $b->booking_status,
                        $b->payment->payment_status ?? '-',
                        (float) ($b->carReturn->late_fee ?? 0),
                        (float) ($b->carReturn->damage_fee ?? 0),
                        (float) ($b->carReturn->total_fine ?? 0),
                        $b->carReturn->fine_status ?? '-',
                    ]);
                }
            });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** @return array{0: Carbon, 1: Carbon, 2: ?string} */
    private function filters(Request $request): array
    {
        $validated = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'status' => ['nullable', 'in:pending,confirmed,ongoing,completed,cancelled'],
        ]);

        $from = isset($validated['from']) ? Carbon::parse($validated['from'])->startOfDay() : now()->startOfMonth();
        $to = isset($validated['to']) ? Carbon::parse($validated['to'])->endOfDay() : now()->endOfDay();

        return [$from, $to, $validated['status'] ?? null];
    }

    private function query(Carbon $from, Carbon $to, ?string $status): Builder
    {
        return Booking::query()
            ->with(['user', 'car', 'payment', 'carReturn'])
            ->whereBetween('created_at', [$from, $to])
            ->when($status, fn ($q) => $q->where('booking_status', $status));
    }

    /**
     * Pendapatan = pembayaran sewa terverifikasi + denda yang sudah lunas
     * pada booking yang dibuat dalam rentang tanggal terpilih.
     *
     * @return array<string, float|int>
     */
    private function summary(Carbon $from, Carbon $to, ?string $status): array
    {
        $rows = $this->query($from, $to, $status)->get();

        $rental = (float) $rows->filter(fn ($b) => $b->payment?->payment_status === 'verified')->sum(fn ($b) => (float) $b->payment->amount);
        $fines = (float) $rows->filter(fn ($b) => $b->carReturn?->fine_status === 'paid')->sum(fn ($b) => (float) $b->carReturn->total_fine);
        $driver = (float) $rows->filter(fn ($b) => $b->payment?->payment_status === 'verified')->sum(fn ($b) => (float) $b->driver_fee);

        return [
            'transactions' => $rows->count(),
            'completed' => $rows->where('booking_status', 'completed')->count(),
            'cancelled' => $rows->where('booking_status', 'cancelled')->count(),
            'rental_revenue' => $rental,
            'driver_revenue' => $driver,
            'fine_revenue' => $fines,
            'total_revenue' => $rental + $fines,
        ];
    }
}
