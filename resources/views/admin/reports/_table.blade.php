<table class="w-full text-xs">
    <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider">
        <tr>
            <th class="text-left px-3 py-3">ID</th>
            <th class="text-left px-3 py-3">Tgl Order</th>
            <th class="text-left px-3 py-3">Customer / Peminjam</th>
            <th class="text-left px-3 py-3">Mobil</th>
            <th class="text-left px-3 py-3">Periode Sewa</th>
            <th class="text-right px-3 py-3">Total Sewa</th>
            <th class="text-left px-3 py-3">Status</th>
            <th class="text-left px-3 py-3">Bayar</th>
            <th class="text-right px-3 py-3">Denda</th>
        </tr>
    </thead>
    <tbody class="divide-y divide-slate-100">
        @forelse ($bookings as $b)
            <tr>
                <td class="px-3 py-2.5 font-bold">#{{ $b->id }}</td>
                <td class="px-3 py-2.5">{{ $b->created_at->format('d M Y H:i') }}</td>
                <td class="px-3 py-2.5">{{ $b->user->name ?? '-' }}@if ($b->borrower_name && $b->borrower_name !== ($b->user->name ?? null))<div class="text-slate-400">Peminjam: {{ $b->borrower_name }}</div>@endif</td>
                <td class="px-3 py-2.5">{{ $b->car->brand ?? '' }} {{ $b->car->name ?? '' }}</td>
                <td class="px-3 py-2.5">{{ $b->start_date->format('d M Y H:i') }}<br>{{ $b->end_date->format('d M Y H:i') }} <span class="text-slate-400">({{ $b->total_days }} hr{{ $b->with_driver ? ', sopir' : '' }})</span></td>
                <td class="px-3 py-2.5 text-right font-bold">Rp {{ number_format($b->total_price, 0, ',', '.') }}</td>
                <td class="px-3 py-2.5 uppercase">{{ $b->booking_status }}</td>
                <td class="px-3 py-2.5 uppercase">{{ $b->payment->payment_status ?? '-' }}</td>
                <td class="px-3 py-2.5 text-right">
                    @if ($b->carReturn && $b->carReturn->total_fine > 0)
                        Rp {{ number_format($b->carReturn->total_fine, 0, ',', '.') }}
                        <div class="text-[10px] uppercase {{ $b->carReturn->fine_status === 'paid' ? 'text-emerald-600' : 'text-rose-600' }}">{{ $b->carReturn->fine_status }}</div>
                    @else
                        -
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="9" class="px-4 py-10 text-center text-slate-400">Tidak ada transaksi pada rentang ini.</td></tr>
        @endforelse
    </tbody>
</table>
