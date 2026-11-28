<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan RentCar {{ $from }} s/d {{ $to }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print { .no-print { display: none; } body { -webkit-print-color-adjust: exact; } }</style>
</head>
<body class="p-8 text-slate-800 bg-white">
    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg">Cetak</button>
        <a href="{{ route('admin.reports.index', request()->query()) }}" class="px-4 py-2 bg-slate-100 text-xs font-bold rounded-lg">Kembali</a>
    </div>
    <h1 class="text-xl font-black">Laporan Transaksi & Pendapatan RentCar</h1>
    <p class="text-xs text-slate-500 mb-4">Periode order: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} - {{ \Carbon\Carbon::parse($to)->format('d M Y') }}{{ $status ? ' | Status: ' . $status : '' }} | Dicetak {{ now()->format('d M Y H:i') }}</p>

    @include('admin.reports._summary', ['summary' => $summary])
    @include('admin.reports._table', ['bookings' => $bookings])

    <script>window.addEventListener('load', () => setTimeout(() => window.print(), 400));</script>
</body>
</html>
