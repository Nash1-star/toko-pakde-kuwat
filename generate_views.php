<?php
$base = __DIR__;
function makeFile($path, $content) {
    global $base;
    $fullPath = $base . '/' . $path;
    $dir = dirname($fullPath);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    file_put_contents($fullPath, $content);
}

makeFile('resources/views/layouts/app.blade.php', <<<'EOT'
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen - Toko Pakde Kuwat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 text-gray-800 font-sans antialiased">
    @auth
        <div class="flex h-screen overflow-hidden">
            <!-- Sidebar -->
            <aside class="w-64 bg-white border-r border-gray-200 flex flex-col hidden md:flex">
                <div class="p-4 border-b border-gray-200 flex items-center space-x-2">
                    <div class="bg-blue-600 text-white p-2 rounded-lg">
                        <i class="fas fa-store"></i>
                    </div>
                    <h1 class="font-bold text-gray-700">Toko Pakde Kuwat</h1>
                </div>
                <nav class="flex-1 overflow-y-auto p-4 space-y-2">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                        <i class="fas fa-chart-pie w-5"></i> <span>Dashboard</span>
                    </a>
                    
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2 px-4">Master Data</div>
                    <a href="{{ route('products.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg {{ request()->routeIs('products.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                        <i class="fas fa-box w-5"></i> <span>Daftar Barang</span>
                    </a>
                    <a href="{{ route('categories.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg {{ request()->routeIs('categories.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                        <i class="fas fa-tags w-5"></i> <span>Kategori</span>
                    </a>

                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2 px-4">Transaksi</div>
                    <a href="{{ route('pos.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-md">
                        <i class="fas fa-cash-register w-5"></i> <span>Buka Kasir (POS)</span>
                    </a>
                </nav>
            </aside>

            <!-- Main Content -->
            <div class="flex-1 flex flex-col h-screen overflow-hidden">
                <!-- Header -->
                <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6">
                    <div>
                        <!-- Mobile menu button -->
                    </div>
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-500">Menjaga: <strong class="text-gray-800">{{ Auth::user()->name }}</strong></span>
                        <a href="{{ route('shift.close') }}" class="text-red-500 hover:text-red-700 text-sm font-semibold">Tutup Shift</a>
                    </div>
                </header>
                
                <!-- Main area -->
                <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50 p-6">
                    @yield('content')
                </main>
            </div>
        </div>
    @else
        @yield('content')
    @endauth
</body>
</html>
EOT);

makeFile('resources/views/auth/login.blade.php', <<<'EOT'
@extends('layouts.app')
@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white rounded-xl shadow-lg p-8 space-y-8">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-100 text-blue-600 mb-4">
                <i class="fas fa-store text-3xl"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Login Operator</h2>
            <p class="mt-2 text-sm text-gray-600">Toko Pakde Kuwat</p>
        </div>
        
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
        @endif

        <form class="mt-8 space-y-6" action="{{ route('login.post') }}" method="POST">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Nama Anda</label>
                <div class="grid grid-cols-2 gap-4">
                    @foreach($users as $user)
                        <label class="cursor-pointer">
                            <input type="radio" name="user_id" value="{{ $user->id }}" class="peer sr-only" required>
                            <div class="rounded-lg border border-gray-200 bg-white p-5 hover:bg-gray-50 peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500 peer-checked:bg-blue-50 transition-all text-center">
                                <span class="block font-semibold text-gray-900">{{ $user->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div>
                <label for="pin" class="block text-sm font-medium text-gray-700">PIN (4 Digit)</label>
                <input id="pin" name="pin" type="password" required class="appearance-none rounded relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 focus:outline-none focus:ring-blue-500 focus:border-blue-500 focus:z-10 sm:text-sm mt-1" placeholder="Masukkan PIN">
            </div>
            <div>
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Masuk
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
EOT);

makeFile('resources/views/shift/open.blade.php', <<<'EOT'
@extends('layouts.app')
@section('content')
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
EOT);

makeFile('resources/views/shift/close.blade.php', <<<'EOT'
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
        <div class="flex justify-between pt-2">
            <span class="text-gray-800 font-bold">Kas Seharusnya:</span>
            <span class="font-bold text-xl">Rp {{ number_format($shift->expected_cash, 0, ',', '.') }}</span>
        </div>
    </div>

    <form action="{{ route('shift.close.post') }}" method="POST">
        @csrf
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2">Kas Aktual (Uang Fisik di Laci) Rp</label>
            <input type="number" name="actual_cash" required class="shadow appearance-none border rounded w-full py-3 px-3 text-gray-700 text-xl font-bold focus:outline-none focus:shadow-outline">
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
EOT);

makeFile('resources/views/dashboard.blade.php', <<<'EOT'
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
EOT);

makeFile('resources/views/pos/index.blade.php', <<<'EOT'
@extends('layouts.app')
@section('content')
<div x-data="posData()" class="flex h-full gap-6">
    <!-- Left: Product List -->
    <div class="flex-1 flex flex-col h-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <input type="text" x-model="searchQuery" placeholder="Cari barang atau scan barcode..." class="w-full pl-4 pr-10 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-lg">
        </div>
        <div class="flex-1 overflow-y-auto p-4">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div @click="addToCart(product)" class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:shadow-md transition bg-white flex flex-col justify-between">
                        <div class="text-sm text-gray-500 mb-1" x-text="product.barcode"></div>
                        <div class="font-semibold text-gray-800 mb-2 leading-tight h-10 overflow-hidden" x-text="product.name"></div>
                        <div class="flex justify-between items-end mt-2">
                            <span class="text-blue-600 font-bold text-lg" x-text="formatRupiah(product.selling_price)"></span>
                            <span class="text-xs text-gray-500" x-text="'Stok: ' + product.current_stock"></span>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="w-96 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Keranjang Belanja</h2>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex justify-between items-center border-b pb-2">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 text-sm" x-text="item.name"></h4>
                        <div class="text-blue-600 text-sm" x-text="formatRupiah(item.price)"></div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="decreaseQty(index)" class="w-7 h-7 rounded bg-gray-200 text-gray-700 flex items-center justify-center font-bold hover:bg-gray-300">-</button>
                        <span class="w-6 text-center font-medium" x-text="item.qty"></span>
                        <button @click="increaseQty(index)" class="w-7 h-7 rounded bg-gray-200 text-gray-700 flex items-center justify-center font-bold hover:bg-gray-300">+</button>
                    </div>
                </div>
            </template>
            <div x-show="cart.length === 0" class="text-center text-gray-500 mt-10">Keranjang masih kosong</div>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <span class="font-semibold text-gray-600">Total</span>
                <span class="text-2xl font-bold text-gray-900" x-text="formatRupiah(cartTotal)"></span>
            </div>
            
            <form action="{{ route('pos.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="cart" :value="JSON.stringify(cart)">
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Metode Bayar</label>
                    <select name="payment_method" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Tunai">Tunai</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer">Transfer</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Uang Diterima (Rp)</label>
                    <input type="number" x-model.number="cashReceived" class="w-full border-gray-300 rounded-md shadow-sm p-2 text-lg font-bold" placeholder="0">
                    <div class="mt-2 text-sm">
                        <span class="text-gray-500">Kembalian: </span>
                        <span class="font-bold text-green-600" x-text="formatRupiah(change)"></span>
                    </div>
                </div>

                <button type="submit" :disabled="cart.length === 0" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 shadow flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-check-circle"></i>
                    <span>Selesaikan Transaksi</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function posData() {
    return {
        searchQuery: '',
        products: @json($products),
        cart: [],
        cashReceived: '',
        
        get filteredProducts() {
            if (this.searchQuery === '') return this.products;
            const lowerCaseQuery = this.searchQuery.toLowerCase();
            return this.products.filter(p => 
                p.name.toLowerCase().includes(lowerCaseQuery) || 
                (p.barcode && p.barcode.toLowerCase().includes(lowerCaseQuery))
            );
        },
        
        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.qty < product.current_stock) existing.qty++;
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.selling_price,
                    qty: 1
                });
            }
            this.searchQuery = '';
        },
        
        increaseQty(index) {
            const product = this.products.find(p => p.id === this.cart[index].id);
            if (this.cart[index].qty < product.current_stock) {
                this.cart[index].qty++;
            }
        },
        
        decreaseQty(index) {
            if (this.cart[index].qty > 1) {
                this.cart[index].qty--;
            } else {
                this.cart.splice(index, 1);
            }
        },
        
        get cartTotal() {
            return this.cart.reduce((total, item) => total + (item.price * item.qty), 0);
        },
        
        get change() {
            if (!this.cashReceived || this.cashReceived < this.cartTotal) return 0;
            return this.cashReceived - this.cartTotal;
        },
        
        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
        }
    }
}
</script>
@endsection
EOT);

echo "Views generated!";

