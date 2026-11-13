@extends('layouts.app')

@section('title', 'Login Operator')

@section('content')
<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8" style="background: #EEF1F6; font-family: 'Plus Jakarta Sans', system-ui, sans-serif;">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-sm border border-[#E4E8F0] p-8 space-y-7">
        
        <!-- Logo & Header -->
        <div class="text-center">
            <div style="width: 56px; height: 56px; border-radius: 16px; background: #2563EB; color: #FFFFFF; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l1.5-5h15L21 9"></path><path d="M3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"></path><path d="M5 12v8h14v-8"></path><path d="M10 20v-5h4v5"></path></svg>
            </div>
            <h1 class="text-2xl font-extrabold text-[#111827]">Toko Pakde Kuwat</h1>
            <p class="mt-1 text-sm text-[#5B6475]">Pilih akun Anda dan masukkan PIN untuk mulai bertugas</p>
        </div>
        
        <!-- Flash messages -->
        @if(session('error'))
            <div style="background: #FDECEA; border: 1px solid #F1C4BE; border-radius: 12px; padding: 12px 16px; color: #B42318; font-size: 14px; font-weight: 600;">
                {{ session('error') }}
            </div>
        @endif
        @if(session('success'))
            <div style="background: #ECFDF5; border: 1px solid #A7F3D0; border-radius: 12px; padding: 12px 16px; color: #065F46; font-size: 14px; font-weight: 600;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf
            
            <!-- Operator Selection Grid -->
            <div>
                <label class="block text-sm font-bold text-[#111827] mb-2.5">Siapa yang sedang bertugas?</label>
                <div class="grid grid-cols-2 gap-3">
                    @php
                      $avatarStyles = [
                        'M' => ['bg' => '#DBE6FE', 'fg' => '#1E40AF'],
                        'Z' => ['bg' => '#E0F2FE', 'fg' => '#075985'],
                        'T' => ['bg' => '#E8E1F7', 'fg' => '#4B3390'],
                      ];
                    @endphp
                    @foreach($users as $user)
                        @php
                          $init = strtoupper(substr($user->name, 0, 1));
                          $st = $avatarStyles[$init] ?? ['bg' => '#E8EFFE', 'fg' => '#1D4ED8'];
                        @endphp
                        <label class="cursor-pointer">
                            <input type="radio" name="user_id" value="{{ $user->id }}" class="peer sr-only" required {{ $loop->first ? 'checked' : '' }}>
                            <div class="rounded-xl border border-[#E4E8F0] bg-white p-4 text-center transition-all peer-checked:border-[#2563EB] peer-checked:ring-2 peer-checked:ring-[#2563EB] peer-checked:bg-[#E8EFFE] hover:bg-gray-50 flex flex-col items-center gap-2">
                                <span style="width: 40px; height: 40px; border-radius: 999px; background: {{ $st['bg'] }}; color: {{ $st['fg'] }}; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 15px">
                                    {{ $init }}
                                </span>
                                <span class="font-bold text-sm text-[#111827]">{{ $user->name }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <!-- PIN Input -->
            <div>
                <label for="pin" class="block text-sm font-bold text-[#111827] mb-1.5">PIN (4 Digit)</label>
                <div class="relative">
                    <input id="pin" name="pin" type="password" inputmode="numeric" maxlength="4" required placeholder="••••" class="w-full h-12 px-4 border border-[#CDD4E0] rounded-xl text-center text-xl font-extrabold tracking-widest text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB] focus:border-transparent">
                </div>
            </div>

            <!-- Submit Button -->
            <div>
                <button type="submit" style="display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; min-height: 50px; border-radius: 12px; border: 0; background: #2563EB; color: #FFFFFF; font-family: inherit; font-size: 15px; font-weight: 800; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#1D4ED8'" onmouseout="this.style.background='#2563EB'">
                    Masuk Sekarang
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
                </button>
            </div>
        </form>

        <div class="text-center pt-2">
            <span class="text-xs text-[#6B7280]">Sistem Manajemen & Kasir Toko Pakde Kuwat</span>
        </div>
    </div>
</div>
@endsection
