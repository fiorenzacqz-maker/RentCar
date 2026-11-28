<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarReturn;
use App\Services\PublicStorageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FineController extends Controller
{
    /**
     * Daftar denda keterlambatan/kerusakan beserta status pelunasannya.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'waiting');

        $fines = CarReturn::with(['booking.user', 'booking.car'])
            ->where('total_fine', '>', 0)
            ->when(in_array($status, ['unpaid', 'waiting', 'paid'], true), fn ($q) => $q->where('fine_status', $status))
            ->latest('updated_at')
            ->paginate(12)
            ->withQueryString();

        return view('admin.fines.index', compact('fines', 'status'));
    }

    /**
     * Verifikasi pelunasan denda (juga dipakai untuk denda yang dibayar tunai di lokasi).
     */
    public function verify(CarReturn $carReturn): RedirectResponse
    {
        if ((float) $carReturn->total_fine <= 0 || $carReturn->fine_status === 'paid') {
            return back()->with('error', 'Denda ini tidak perlu diverifikasi atau sudah lunas.');
        }

        $carReturn->update([
            'fine_status' => 'paid',
            'fine_paid_at' => $carReturn->fine_paid_at ?? now(),
        ]);

        return back()->with('success', 'Pelunasan denda diverifikasi.');
    }

    /**
     * Tolak bukti pelunasan; customer diminta mengunggah ulang.
     */
    public function reject(CarReturn $carReturn): RedirectResponse
    {
        if ($carReturn->fine_status !== 'waiting') {
            return back()->with('error', 'Hanya bukti yang berstatus menunggu verifikasi yang dapat ditolak.');
        }

        PublicStorageService::delete($carReturn->fine_proof_image);

        $carReturn->update([
            'fine_status' => 'unpaid',
            'fine_proof_image' => null,
            'fine_paid_at' => null,
        ]);

        return back()->with('success', 'Bukti pelunasan ditolak. Customer dapat mengunggah ulang.');
    }
}
