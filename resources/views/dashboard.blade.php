@extends('layouts.app')
@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-gray-800">Selamat datang, {{ Auth::user()->name }} 👋</h1>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center text-gray-500 mb-2">
            <i class="fas fa-money-bill-wave text-green-500 mr-2"></i> Penjualan Hari Ini
        </div>
        <div class="text-3xl font-bold text-gray-800">Rp {{ number_format($todaySales, 0, ',', '.') }}</div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <div class="flex items-center text-gray-500 mb-2">
            <i class="fas fa-shopping-cart text-blue-500 mr-2"></i> Total Transaksi (Hari ini)
        </div>
        <div class="text-3xl font-bold text-gray-800">{{ $txCount }}</div>
    </div>
</div>

<h2 class="text-xl font-bold text-gray-800 mb-4">Peringatan Stok Menipis</h2>
<div class="bg-white shadow-sm rounded-lg overflow-hidden border border-gray-200">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sisa Stok</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batas Min.</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($lowStock as $item)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold">{{ $item->current_stock }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $item->min_stock }}</td>
            </tr>
            @empty
            <tr><td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">Semua stok aman.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection