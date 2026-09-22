@extends('layouts.app')
@section('content')
<div class="max-w-xl mx-auto bg-white rounded-xl shadow p-8 mt-10">
    <h2 class="text-2xl font-bold text-gray-800 mb-6">Tutup Shift & Serah Terima</h2>
    
    <div class="bg-gray-50 p-4 rounded-lg mb-6 space-y-3 border border-gray-200">
        <div class="flex justify-between">
            <span class="text-gray-600">Waktu Mulai:</span>
            <span class="font-semibold">{{ $shift->start_time }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-600">Kas Awal:</span>
            <span class="font-semibold text-green-600">Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between border-b pb-3">
            <span class="text-gray-600">Penjualan Shift Ini:</span>
            <span class="font-semibold text-blue-600">+ Rp {{ number_format($sales, 0, ',', '.') }}</span>
        </div>
        <div class="grid grid-cols-3 gap-3 text-sm border-b pb-3">
            <div><span class="block text-gray-500">Tunai</span><strong>Rp {{ number_format($summary->cash, 0, ',', '.') }}</strong></div>
            <div><span class="block text-gray-500">QRIS</span><strong>Rp {{ number_format($summary->qris, 0, ',', '.') }}</strong></div>
            <div><span class="block text-gray-500">Transfer</span><strong>Rp {{ number_format($summary->transfer, 0, ',', '.') }}</strong></div>
        </div>
        <div class="flex justify-between pt-2">
            <span class="text-gray-800 font-bold">Kas Seharusnya:</span>
            <span class="font-bold text-xl">Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</span>
        </div>
    </div>

    <form x-data="{actual: '', expected: {{ $shift->expected_cash }}}" action="{{ route('shift.close.post') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Kas Aktual (Uang Fisik di Laci) Rp</label>
            <input type="number" name="actual_cash" x-model.number="actual" required class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 text-xl font-bold focus:outline-none focus:shadow-outline">
            <p class="text-xs text-gray-500 mt-1">Kas seharusnya hanya menghitung kas awal + transaksi Tunai. QRIS dan Transfer tersimpan di bank.</p>
            <p class="mt-2 font-bold" x-show="actual !== ''">Perkiraan selisih: <span :class="actual - expected < 0 ? 'text-red-600' : 'text-green-600'" x-text="'Rp ' + new Intl.NumberFormat('id-ID').format(actual - expected)"></span></p>
        </div>
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Catatan Serah Terima (Opsional)</label>
            <textarea name="notes" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
        </div>
        <button type="submit" class="w-full bg-red-600 text-white font-bold py-3 px-4 rounded hover:bg-red-700 shadow-md">
            Konfirmasi & Tutup Shift
        </button>
    </form>
</div>
@endsection