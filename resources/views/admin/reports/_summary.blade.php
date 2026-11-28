<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-5">
    @foreach ([
        ['Total Transaksi', number_format($summary['transactions'])],
        ['Selesai / Batal', $summary['completed'] . ' / ' . $summary['cancelled']],
        ['Pendapatan Sewa (verified)', 'Rp ' . number_format($summary['rental_revenue'], 0, ',', '.')],
        ['Pendapatan Denda (lunas)', 'Rp ' . number_format($summary['fine_revenue'], 0, ',', '.')],
    ] as [$label, $value])
        <div class="bg-white rounded-2xl border border-slate-100 p-4 shadow-sm">
            <span class="block text-[11px] text-slate-400 uppercase font-bold">{{ $label }}</span>
            <span class="block mt-1 text-lg font-black text-slate-900">{{ $value }}</span>
        </div>
    @endforeach
    <div class="col-span-2 lg:col-span-4 bg-[#0f2557] text-white rounded-2xl p-4 shadow-sm flex items-center justify-between">
        <span class="text-xs uppercase font-bold tracking-wider">Total Pendapatan (sewa + denda)</span>
        <span class="text-2xl font-black">Rp {{ number_format($summary['total_revenue'], 0, ',', '.') }}</span>
    </div>
</div>
<p class="mb-3 text-[11px] text-slate-400">Termasuk jasa sopir Rp {{ number_format($summary['driver_revenue'], 0, ',', '.') }} (sudah ada di dalam pendapatan sewa).</p>
