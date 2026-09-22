@extends('layouts.app')
@section('content')
@if($previousShift)
<div x-data="{open: true}" x-show="open" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4">
    <div class="bg-white rounded-xl shadow-xl max-w-lg w-full p-6">
        <div class="flex justify-between items-start"><h3 class="text-xl font-bold">Rekap Penjualan Sebelumnya</h3><button type="button" @click="open = false" class="text-gray-400 text-xl">&times;</button></div>
        <p class="text-sm text-gray-500 mt-1">Shift {{ $previousShift->end_time }}</p>
        <div class="grid grid-cols-2 gap-3 mt-5">
            <div class="bg-gray-50 rounded p-3"><span class="text-sm text-gray-500">Total transaksi</span><strong class="block">{{ $previousShift->transactions->count() }}</strong></div>
            <div class="bg-gray-50 rounded p-3"><span class="text-sm text-gray-500">Kas aktual</span><strong class="block">Rp {{ number_format($previousShift->actual_cash, 0, ',', '.') }}</strong></div>
            <div class="bg-gray-50 rounded p-3"><span class="text-sm text-gray-500">Selisih</span><strong class="block {{ $previousShift->difference < 0 ? 'text-red-600' : 'text-green-600' }}">Rp {{ number_format($previousShift->difference, 0, ',', '.') }}</strong></div>
        </div>
        @if($previousShift->notes)<div class="mt-4 border-l-4 border-blue-500 bg-blue-50 p-3 text-sm">{{ $previousShift->notes }}</div>@endif
        <button type="button" @click="open = false" class="mt-5 w-full bg-blue-600 text-white rounded py-2">Mengerti</button>
    </div>
</div>
@endif
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Mulai Jaga (Buka Shift)</h2>
        <form action="{{ route('shift.open.post') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Kas Awal (Uang di Laci) Rp</label>
                <input type="number" name="starting_cash" value="0" required class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline text-lg">
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded hover:bg-blue-700">
                Buka Shift Sekarang
            </button>
        </form>
    </div>
</div>
@endsection