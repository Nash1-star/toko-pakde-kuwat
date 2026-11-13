@extends('layouts.app')

@section('title', 'Kelola Akun')

@section('content')
<div x-data="{
  deleteModal: false,
  deleteUser: {},
  openDelete(u) {
    this.deleteUser = u;
    this.deleteModal = true;
  }
}" style="max-width: 1180px; margin: 0 auto; display: flex; flex-direction: column; gap: 22px">

  <!-- Header -->
  <header style="display: flex; flex-direction: column; gap: 6px">
    <h1 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #111827">Kelola Akun</h1>
    <p style="margin: 0; font-size: 15px; color: #5B6475">Atur siapa saja yang boleh menjaga toko</p>
  </header>

  <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: flex-start">
    
    <!-- Left Column: Daftar Penjaga -->
    <section style="flex: 3 1 440px; min-width: 0; background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      <div style="padding: 20px 22px; border-bottom: 1px solid #EDF0F5; display: flex; justify-content: space-between; align-items: center">
        <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #111827">Daftar penjaga</h2>
        <span style="font-size: 13px; color: #5B6475; font-weight: 600">{{ $users->count() }} akun</span>
      </div>

      <ul style="list-style: none; margin: 0; padding: 0">
        @php
          $colors = [
            'M' => ['bg' => '#DBE6FE', 'fg' => '#1E40AF'],
            'Z' => ['bg' => '#E0F2FE', 'fg' => '#075985'],
            'T' => ['bg' => '#E8E1F7', 'fg' => '#4B3390'],
            'B' => ['bg' => '#FDF1DC', 'fg' => '#8A5300'],
            'A' => ['bg' => '#ECFDF5', 'fg' => '#065F46'],
          ];
        @endphp

        @foreach($users as $u)
          @php
            $initial = strtoupper(substr($u->name, 0, 1));
            $c = $colors[$initial] ?? ['bg' => '#E8EFFE', 'fg' => '#1D4ED8'];
            $isMe = $u->id === Auth::id();
            $latestShift = $u->shifts->first();
            $isActive = $latestShift && $latestShift->status === 'open';
          @endphp
          <li style="display: flex; align-items: center; gap: 14px; padding: 16px 22px; border-bottom: 1px solid #EDF0F5">
            <span style="width: 44px; height: 44px; border-radius: 999px; background: {{ $c['bg'] }}; color: {{ $c['fg'] }}; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 16px; flex: none">
              {{ $initial }}
            </span>
            <div style="display: flex; flex-direction: column; gap: 3px; flex: 1; min-width: 0">
              <span style="font-size: 16px; font-weight: 700; color: #111827">
                {{ $u->name }}
                @if($isMe)
                  <span style="margin-left: 6px; padding: 3px 8px; border-radius: 999px; background: #E8EFFE; color: #1D4ED8; font-size: 12px; font-weight: 700">Anda</span>
                @endif
              </span>
              <span style="font-size: 13px; color: #5B6475">
                Operator · 
                @if($isActive)
                  <strong style="color: #1D4ED8">sedang jaga sejak {{ \Carbon\Carbon::parse($latestShift->start_time)->format('H:i') }}</strong>
                @elseif($latestShift)
                  terakhir jaga {{ $latestShift->end_time ? \Carbon\Carbon::parse($latestShift->end_time)->format('d M Y, H:i') : \Carbon\Carbon::parse($latestShift->start_time)->format('d M Y, H:i') }}
                @else
                  belum pernah jaga
                @endif
              </span>
            </div>

            @if(!$isMe)
              <button type="button" @click="openDelete({ id: {{ $u->id }}, name: '{{ addslashes($u->name) }}' })" aria-label="Hapus akun {{ $u->name }}" style="width: 40px; height: 40px; border-radius: 10px; border: 1px solid #E4E8F0; background: #FFFFFF; color: #B42318; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='#FFFFFF'" title="Hapus akun">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16"></path><path d="M10 11v6M14 11v6"></path><path d="M6 7l1 13h10l1-13"></path><path d="M9 7V4h6v3"></path></svg>
              </button>
            @endif
          </li>
        @endforeach
      </ul>
    </section>

    <!-- Right Column: Profile & New User Forms -->
    <div style="flex: 2 1 340px; min-width: 0; display: flex; flex-direction: column; gap: 20px">
      
      <!-- Form 1: Ubah Akun Saya -->
      <form action="{{ route('accounts.profile') }}" method="POST" style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; padding: 22px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        @csrf
        <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #111827">Ubah akun saya</h2>
        
        <div style="display: flex; flex-direction: column; gap: 6px">
          <label for="me-name" style="font-size: 14px; font-weight: 700; color: #111827">Nama</label>
          <input id="me-name" name="name" value="{{ old('name', Auth::user()->name) }}" required style="height: 48px; border: 1px solid #CDD4E0; border-radius: 10px; padding: 0 14px; font-family: inherit; font-size: 15px; color: #111827; box-sizing: border-box; width: 100%">
        </div>

        <div style="display: flex; flex-direction: column; gap: 6px">
          <label for="me-pin" style="font-size: 14px; font-weight: 700; color: #111827">PIN baru (4 digit)</label>
          <input id="me-pin" name="pin" type="password" inputmode="numeric" maxlength="4" placeholder="Kosongkan jika tidak diganti" style="height: 48px; border: 1px solid #CDD4E0; border-radius: 10px; padding: 0 14px; font-family: inherit; font-size: 15px; color: #111827; box-sizing: border-box; width: 100%">
        </div>

        <button type="submit" style="align-self: flex-start; min-height: 44px; padding: 0 20px; border-radius: 10px; border: 0; background: #2563EB; color: #FFFFFF; font-family: inherit; font-size: 14px; font-weight: 700; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#1D4ED8'" onmouseout="this.style.background='#2563EB'">
          Simpan perubahan
        </button>
      </form>

      <!-- Form 2: Tambah Penjaga -->
      <form action="{{ route('accounts.guardians.store') }}" method="POST" style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; padding: 22px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        @csrf
        <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #111827">Tambah penjaga</h2>
        
        <div style="display: flex; flex-direction: column; gap: 6px">
          <label for="new-name" style="font-size: 14px; font-weight: 700; color: #111827">Nama</label>
          <input id="new-name" name="name" required placeholder="Contoh: Budi" value="{{ old('name') }}" style="height: 48px; border: 1px solid #CDD4E0; border-radius: 10px; padding: 0 14px; font-family: inherit; font-size: 15px; color: #111827; box-sizing: border-box; width: 100%">
        </div>

        <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px">
          <div style="display: flex; flex-direction: column; gap: 6px">
            <label for="new-pin" style="font-size: 14px; font-weight: 700; color: #111827">PIN 4 digit</label>
            <input id="new-pin" name="pin" type="password" required inputmode="numeric" maxlength="4" placeholder="••••" style="height: 48px; border: 1px solid #CDD4E0; border-radius: 10px; padding: 0 14px; font-family: inherit; font-size: 15px; color: #111827; box-sizing: border-box; width: 100%">
          </div>
          <div style="display: flex; flex-direction: column; gap: 6px">
            <label for="new-key" style="font-size: 14px; font-weight: 700; color: #111827">Sandi khusus</label>
            <input id="new-key" name="admin_password" type="password" required placeholder="Sandi pemilik" style="height: 48px; border: 1px solid #CDD4E0; border-radius: 10px; padding: 0 14px; font-family: inherit; font-size: 15px; color: #111827; box-sizing: border-box; width: 100%">
          </div>
        </div>

        <p style="margin: 0; font-size: 13px; color: #5B6475; line-height: 1.5">
          Sandi khusus diperlukan agar hanya pemilik yang bisa menambah penjaga baru.
        </p>

        <button type="submit" style="align-self: flex-start; display: inline-flex; align-items: center; gap: 8px; min-height: 44px; padding: 0 20px; border-radius: 10px; border: 1px solid #2563EB; background: #FFFFFF; color: #1D4ED8; font-family: inherit; font-size: 14px; font-weight: 700; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#E8EFFE'" onmouseout="this.style.background='#FFFFFF'">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
          Tambah penjaga
        </button>
      </form>
    </div>

  </div>

  <!-- MODAL: Hapus Akun Penjaga -->
  <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div x-show="deleteModal" @click="deleteModal = false" class="fixed inset-0 transition-opacity bg-black/50"></div>
      
      <div x-show="deleteModal" class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-[#F1C4BE]">
        <div class="flex items-center gap-3 mb-4 text-[#B42318]">
          <div class="w-12 h-12 rounded-xl bg-[#FDECEA] flex items-center justify-center flex-shrink-0">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-[#7A1A11]">Hapus Akun Penjaga?</h3>
            <p class="text-sm text-[#7A2E25]">Masukkan sandi khusus pemilik untuk konfirmasi.</p>
          </div>
        </div>

        <form :action="'{{ url('accounts') }}/' + deleteUser.id" method="POST" class="space-y-4">
          @csrf
          @method('DELETE')
          <p class="text-sm text-[#374151]">
            Apakah Anda yakin ingin menghapus akun <strong class="text-[#111827]" x-text="deleteUser.name"></strong>?
          </p>

          <div>
            <label class="block text-sm font-bold text-[#111827] mb-1">Sandi khusus pemilik *</label>
            <input type="password" name="admin_password" required placeholder="Masukkan sandi khusus" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-[#EDF0F5]">
            <button type="button" @click="deleteModal = false" class="px-5 py-2.5 rounded-lg border border-[#CDD4E0] text-sm font-bold text-[#5B6475] hover:bg-gray-50">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#B42318] text-sm font-bold text-white hover:bg-[#991B1B]">Hapus Akun</button>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection
