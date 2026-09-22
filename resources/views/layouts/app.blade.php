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
                    <a href="{{ route('accounts.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg {{ request()->routeIs('accounts.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                        <i class="fas fa-users-cog w-5"></i> <span>Kelola Akun</span>
                    </a>

                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider mt-4 mb-2 px-4">Transaksi</div>
                    <a href="{{ route('pos.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-md">
                        <i class="fas fa-cash-register w-5"></i> <span>Buka Kasir (POS)</span>
                    </a>
                    <a href="{{ route('reports.index') }}" class="flex items-center space-x-2 px-4 py-2 rounded-lg {{ request()->routeIs('reports.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:bg-gray-50' }}">
                        <i class="fas fa-file-invoice-dollar w-5"></i> <span>Laporan</span>
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