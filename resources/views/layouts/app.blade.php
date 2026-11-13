<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistem Manajemen') · Toko Pakde Kuwat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body {
            margin: 0;
            background: #EEF1F6;
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            color: #111827;
        }
        a {
            color: #2563EB;
        }
        a:hover {
            color: #1D4ED8;
        }
        [x-cloak] {
            display: none !important;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #EEF1F6;
        }
        ::-webkit-scrollbar-thumb {
            background: #CDD4E0;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #9CA3AF;
        }
    </style>
    @stack('styles')
</head>
<body class="antialiased min-h-screen">
    @auth
        @if(request()->routeIs('shift.open'))
            <!-- Standalone Header for Buka Shift (Matching BukaShift.dc.html) -->
            <div style="min-height: 100vh; font-family: 'Plus Jakarta Sans', system-ui, sans-serif; color: #111827; background: #EEF1F6; display: flex; flex-direction: column">
                <header style="background: #FFFFFF; border-bottom: 1px solid #E4E8F0; padding: 12px 32px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px">
                    <div style="display: flex; align-items: center; gap: 12px">
                        <div style="width: 40px; height: 40px; border-radius: 11px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l1.5-5h15L21 9"></path><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"></path><path d="M5 12v8h14v-8"></path><path d="M10 20v-5h4v5"></path></svg>
                        </div>
                        <span style="font-weight: 800; font-size: 16px; color: #111827">Toko Pakde Kuwat</span>
                    </div>

                    <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px">
                        <span style="display: inline-flex; align-items: center; gap: 10px; min-height: 44px; padding: 0 16px 0 6px; border-radius: 999px; background: #F5F7FB; font-size: 14px; color: #5B6475">
                            <span style="width: 32px; height: 32px; border-radius: 999px; background: #2563EB; color: #FFFFFF; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            Menjaga: <strong style="color: #111827">{{ Auth::user()->name }}</strong>
                        </span>
                        <form action="{{ route('logout') }}" method="POST" style="margin: 0">
                            @csrf
                            <button type="submit" style="display: inline-flex; align-items: center; gap: 8px; min-height: 44px; padding: 0 18px; border-radius: 999px; border: 0; background: #FDECEA; color: #B42318; font-family: inherit; font-size: 14px; font-weight: 700; cursor: pointer">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                Ganti Akun / Keluar
                            </button>
                        </form>
                    </div>
                </header>

                <main style="flex: 1; box-sizing: border-box; padding: 48px 24px">
                    <!-- Flash Messages -->
                    <div style="max-width: 1040px; margin: 0 auto 16px">
                        @if(session('error'))
                            <div class="mb-4" style="background: #FDECEA; border: 1px solid #F1C4BE; border-radius: 14px; padding: 14px 18px; color: #B42318; font-weight: 600;">
                                {{ session('error') }}
                            </div>
                        @endif
                    </div>
                    @yield('content')
                </main>
            </div>
        @else
            <!-- Standard App Layout with Sidebar and Top Bar -->
            <div x-data="{ mobileNav: false }" class="min-h-screen flex flex-col md:flex-row" style="background: #EEF1F6;">
                <!-- Mobile Header Bar -->
                <div class="md:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-[#E4E8F0]">
                    <div class="flex items-center gap-3">
                        <div style="width: 36px; height: 36px; border-radius: 10px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l1.5-5h15L21 9"></path><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"></path><path d="M5 12v8h14v-8"></path><path d="M10 20v-5h4v5"></path></svg>
                        </div>
                        <span class="font-extrabold text-base text-[#111827]">Toko Pakde Kuwat</span>
                    </div>
                    <button type="button" @click="mobileNav = !mobileNav" class="p-2 rounded-lg text-gray-600 hover:bg-gray-100 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>

                <!-- Sidebar Overlay for mobile -->
                <div x-show="mobileNav" x-cloak @click="mobileNav = false" class="fixed inset-0 bg-black/40 z-40 md:hidden"></div>

                <!-- Sidebar -->
                <aside :class="mobileNav ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed md:sticky top-0 left-0 h-screen z-50 md:z-10 transition-transform duration-200 ease-in-out md:translate-x-0" style="width: 256px; box-sizing: border-box; background: #FFFFFF; border-right: 1px solid #E4E8F0; padding: 24px 16px; display: flex; flex-direction: column; gap: 24px; flex-shrink: 0; overflow-y: auto;">
                    <div style="display: flex; align-items: center; gap: 12px; padding: 0 8px">
                        <div style="width: 44px; height: 44px; border-radius: 12px; background: #2563EB; color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex: none">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l1.5-5h15L21 9"></path><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"></path><path d="M5 12v8h14v-8"></path><path d="M10 20v-5h4v5"></path></svg>
                        </div>
                        <div style="display: flex; flex-direction: column; gap: 2px">
                            <span style="font-weight: 800; font-size: 16px; color: #111827;">Toko Pakde Kuwat</span>
                            <span style="font-size: 12px; color: #5B6475">Kasir &amp; Stok</span>
                        </div>
                    </div>

                    <nav aria-label="Menu utama" style="display: flex; flex-direction: column; gap: 4px">
                        <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: 12px; min-height: 44px; padding: 0 12px; border-radius: 10px; {{ request()->routeIs('dashboard') ? 'background: #E8EFFE; color: #1D4ED8; font-weight: 700;' : 'color: #374151; font-weight: 500;' }} text-decoration: none; font-size: 15px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1.5"></rect><rect x="14" y="3" width="7" height="5" rx="1.5"></rect><rect x="14" y="12" width="7" height="9" rx="1.5"></rect><rect x="3" y="16" width="7" height="5" rx="1.5"></rect></svg>
                            <span>Dashboard</span>
                        </a>

                        <span style="padding: 16px 12px 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #6B7280">Master data</span>
                        
                        <a href="{{ route('products.index') }}" style="display: flex; align-items: center; gap: 12px; min-height: 44px; padding: 0 12px; border-radius: 10px; {{ request()->routeIs('products.*') ? 'background: #E8EFFE; color: #1D4ED8; font-weight: 700;' : 'color: #374151; font-weight: 500;' }} text-decoration: none; font-size: 15px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 8l-9-5-9 5 9 5 9-5z"></path><path d="M3 8v8l9 5 9-5V8"></path><path d="M12 13v8"></path></svg>
                            <span style="flex: 1">Daftar Barang</span>
                            @if(($lowStockBadgeCount ?? 0) > 0)
                                <span style="min-width: 24px; height: 22px; padding: 0 7px; box-sizing: border-box; border-radius: 999px; background: #B42318; color: #FFFFFF; font-size: 12px; font-weight: 700; display: inline-flex; align-items: center; justify-content: center">{{ $lowStockBadgeCount }}</span>
                            @endif
                        </a>

                        <a href="{{ route('categories.index') }}" style="display: flex; align-items: center; gap: 12px; min-height: 44px; padding: 0 12px; border-radius: 10px; {{ request()->routeIs('categories.*') ? 'background: #E8EFFE; color: #1D4ED8; font-weight: 700;' : 'color: #374151; font-weight: 500;' }} text-decoration: none; font-size: 15px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20.6 13.4l-7.2 7.2a2 2 0 0 1-2.8 0L3 13V3h10l7.6 7.6a2 2 0 0 1 0 2.8z"></path><circle cx="7.5" cy="7.5" r="1.5"></circle></svg>
                            <span>Kategori</span>
                        </a>

                        <a href="{{ route('accounts.index') }}" style="display: flex; align-items: center; gap: 12px; min-height: 44px; padding: 0 12px; border-radius: 10px; {{ request()->routeIs('accounts.*') ? 'background: #E8EFFE; color: #1D4ED8; font-weight: 700;' : 'color: #374151; font-weight: 500;' }} text-decoration: none; font-size: 15px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.5"></circle><path d="M2.5 20a6.5 6.5 0 0 1 13 0"></path><path d="M16 4.5a3.5 3.5 0 0 1 0 7"></path><path d="M18 14.5a6.5 6.5 0 0 1 3.5 5.5"></path></svg>
                            <span>Kelola Akun</span>
                        </a>

                        <span style="padding: 16px 12px 6px; font-size: 11px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; color: #6B7280">Transaksi</span>

                        <a href="{{ route('pos.index') }}" style="display: flex; align-items: center; gap: 12px; min-height: 44px; padding: 0 12px; border-radius: 10px; {{ request()->routeIs('pos.*') ? 'background: #E8EFFE; color: #1D4ED8; font-weight: 700;' : 'color: #374151; font-weight: 500;' }} text-decoration: none; font-size: 15px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="12" rx="2"></rect><path d="M7 20h10"></path><path d="M12 16v4"></path><path d="M7 8h4"></path></svg>
                            <span>Kasir (POS)</span>
                        </a>

                        <a href="{{ route('reports.index') }}" style="display: flex; align-items: center; gap: 12px; min-height: 44px; padding: 0 12px; border-radius: 10px; {{ request()->routeIs('reports.*') ? 'background: #E8EFFE; color: #1D4ED8; font-weight: 700;' : 'color: #374151; font-weight: 500;' }} text-decoration: none; font-size: 15px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path><path d="M14 3v6h6"></path><path d="M8 17v-3M12 17v-6M16 17v-2"></path></svg>
                            <span>Laporan</span>
                        </a>
                    </nav>

                    <div class="mt-auto pt-4 border-t border-[#E4E8F0]">
                        <form action="{{ route('logout') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin keluar?')">
                            @csrf
                            <button type="submit" style="display: flex; align-items: center; gap: 10px; width: 100%; min-height: 40px; padding: 0 12px; border-radius: 10px; border: 0; background: transparent; color: #5B6475; font-family: inherit; font-size: 14px; font-weight: 600; cursor: pointer; text-align: left;" onmouseover="this.style.background='#FEE2E2'; this.style.color='#B42318';" onmouseout="this.style.background='transparent'; this.style.color='#5B6475';">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                                <span>Keluar Akun</span>
                            </button>
                        </form>
                    </div>
                </aside>

                <!-- Main Content Area -->
                <main style="flex: 999 1 560px; min-width: 0; box-sizing: border-box; padding: 24px 20px 48px;" class="md:px-8">
                    <!-- Top Status Bar -->
                    <div style="max-width: 1180px; margin: 0 auto 24px">
                        <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; padding: 8px 8px 8px 20px">
                            <span style="display: inline-flex; align-items: center; gap: 8px; font-size: 14px; color: #5B6475; font-weight: 600">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"></circle><path d="M12 7v5l3 2"></path></svg>
                                {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}
                                @if(isset($currentShift) && $currentShift)
                                    · shift sejak {{ \Carbon\Carbon::parse($currentShift->start_time)->format('H:i') }}
                                @endif
                            </span>
                            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px">
                                <span style="display: inline-flex; align-items: center; gap: 10px; min-height: 44px; padding: 0 16px 0 6px; border-radius: 999px; background: #F5F7FB; font-size: 14px; color: #5B6475">
                                    <span style="width: 32px; height: 32px; border-radius: 999px; background: #2563EB; color: #FFFFFF; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 13px">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </span>
                                    Menjaga: <strong style="color: #111827">{{ Auth::user()->name }}</strong>
                                </span>
                                @if(isset($currentShift) && $currentShift)
                                    <a href="{{ route('shift.close') }}" style="display: inline-flex; align-items: center; gap: 8px; min-height: 44px; padding: 0 18px; border-radius: 999px; background: #FDECEA; color: #B42318; font-size: 14px; font-weight: 700; text-decoration: none">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h4v16h-4"></path><path d="M10 8l-4 4 4 4"></path><path d="M6 12h10"></path></svg>
                                        Tutup Shift
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Flash Messages -->
                    <div style="max-width: 1180px; margin: 0 auto 16px">
                        @if(session('success'))
                            <div x-data="{ show: true }" x-show="show" class="mb-4" style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #065F46;">
                                <div class="flex items-center gap-3">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                                </div>
                                <button type="button" @click="show = false" class="text-emerald-700 hover:text-emerald-900 font-bold">&times;</button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div x-data="{ show: true }" x-show="show" class="mb-4" style="background: #FDECEA; border: 1px solid #F1C4BE; border-radius: 14px; padding: 14px 18px; display: flex; align-items: center; justify-content: space-between; gap: 12px; color: #B42318;">
                                <div class="flex items-center gap-3">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                    <span class="text-sm font-semibold">{{ session('error') }}</span>
                                </div>
                                <button type="button" @click="show = false" class="text-red-700 hover:text-red-900 font-bold">&times;</button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div x-data="{ show: true }" x-show="show" class="mb-4" style="background: #FDECEA; border: 1px solid #F1C4BE; border-radius: 14px; padding: 14px 18px; color: #B42318;">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex items-start gap-3">
                                        <svg class="mt-0.5" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                                        <div>
                                            <span class="text-sm font-bold block mb-1">Periksa kembali formulir Anda:</span>
                                            <ul class="text-sm list-disc pl-5 space-y-0.5">
                                                @foreach($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <button type="button" @click="show = false" class="text-red-700 hover:text-red-900 font-bold">&times;</button>
                                </div>
                            </div>
                        @endif
                    </div>

                    @yield('content')
                </main>
            </div>
        @endif
    @else
        <div style="min-height: 100vh; background: #EEF1F6;">
            @yield('content')
        </div>
    @endauth

    @stack('scripts')
</body>
</html>
