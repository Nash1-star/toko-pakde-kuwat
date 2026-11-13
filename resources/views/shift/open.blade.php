@extends('layouts.app')

@section('title', 'Buka Shift')

@section('content')
@php
  $defaultCash = $previousShift ? (float) ($previousShift->actual_cash ?? $previousShift->starting_cash) : 0;
  $diff = $previousShift ? (float) ($previousShift->difference ?? 0) : 0;
@endphp

<div style="max-width: 1040px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px">
  
  <!-- Page Header -->
  <div style="display: flex; flex-direction: column; gap: 6px">
    <p style="margin: 0; font-size: 14px; color: #5B6475">
      {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y · H:i') }}
    </p>
    <h1 style="margin: 0; font-size: 34px; font-weight: 800; letter-spacing: -0.02em; color: #111827">
      Mulai jaga, {{ Auth::user()->name }}
    </h1>
  </div>

  <div style="display: flex; flex-wrap: wrap; gap: 20px; align-items: stretch">
    
    <!-- Left Card: Rekap Shift Sebelumnya -->
    <section aria-labelledby="recap" style="flex: 1 1 420px; min-width: 0; background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 20px; padding: 24px; display: flex; flex-direction: column; gap: 18px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      @if($previousShift)
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px">
          <div style="display: flex; flex-direction: column; gap: 4px">
            <h2 id="recap" style="margin: 0; font-size: 19px; font-weight: 800; color: #111827">Rekap shift sebelumnya</h2>
            <span style="font-size: 14px; color: #5B6475">
              {{ $previousShift->user?->name ?? 'Kasir' }} · ditutup {{ $previousShift->end_time ? \Carbon\Carbon::parse($previousShift->end_time)->format('d M, H:i') : '' }}
            </span>
          </div>
          @if($diff == 0)
            <span style="padding: 5px 10px; border-radius: 999px; background: #E8EFFE; color: #1D4ED8; font-size: 13px; font-weight: 700; white-space: nowrap">
              Kas sesuai
            </span>
          @elseif($diff > 0)
            <span style="padding: 5px 10px; border-radius: 999px; background: #FDF1DC; color: #8A5300; font-size: 13px; font-weight: 700; white-space: nowrap">
              Kas lebih
            </span>
          @else
            <span style="padding: 5px 10px; border-radius: 999px; background: #FDECEA; color: #B42318; font-size: 13px; font-weight: 700; white-space: nowrap">
              Kas kurang
            </span>
          @endif
        </div>

        <!-- 3 Stats -->
        <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px">
          <div style="padding: 14px; border-radius: 12px; background: #EEF1F6; display: flex; flex-direction: column; gap: 4px">
            <span style="font-size: 13px; color: #5B6475">Transaksi</span>
            <span style="font-size: 20px; font-weight: 800; color: #111827">{{ $previousShift->transactions->count() }}</span>
          </div>
          <div style="padding: 14px; border-radius: 12px; background: #EEF1F6; display: flex; flex-direction: column; gap: 4px">
            <span style="font-size: 13px; color: #5B6475">Kas aktual</span>
            <span style="font-size: 20px; font-weight: 800; color: #111827">Rp {{ number_format($previousShift->actual_cash ?? 0, 0, ',', '.') }}</span>
          </div>
          <div style="padding: 14px; border-radius: 12px; background: #EEF1F6; display: flex; flex-direction: column; gap: 4px">
            <span style="font-size: 13px; color: #5B6475">Selisih</span>
            <span style="font-size: 20px; font-weight: 800; color: {{ $diff == 0 ? '#1D4ED8' : ($diff > 0 ? '#8A5300' : '#B42318') }}">
              {{ $diff > 0 ? '+' : '' }}Rp {{ number_format($diff, 0, ',', '.') }}
            </span>
          </div>
        </div>

        <!-- Catatan Serah Terima -->
        @if($previousShift->notes)
          <div style="display: flex; gap: 12px; padding: 14px 16px; border-radius: 12px; background: #FDF1DC">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#8A5300" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="flex: none; margin-top: 2px"><path d="M4 5h16v11H8l-4 4z"></path></svg>
            <div style="display: flex; flex-direction: column; gap: 3px">
              <span style="font-size: 13px; font-weight: 700; color: #6A4300">Catatan serah terima</span>
              <span style="font-size: 15px; font-weight: 600; color: #111827">“{{ $previousShift->notes }}”</span>
            </div>
          </div>
        @endif
      @else
        <div>
          <h2 id="recap" style="margin: 0; font-size: 19px; font-weight: 800; color: #111827">Belum ada shift sebelumnya</h2>
          <span style="font-size: 14px; color: #5B6475">Ini adalah shift pertama yang dibuka.</span>
        </div>
      @endif

      <!-- Perlu diisi ulang hari ini -->
      <div style="display: flex; flex-direction: column; gap: 10px; padding-top: 16px; border-top: 1px solid #EDF0F5">
        <span style="font-size: 14px; font-weight: 800; color: #B42318">
          @if($lowStockProducts->count() > 0)
            Perlu diisi ulang hari ini ({{ $lowStockProducts->count() }} barang)
          @else
            Semua stok barang aman
          @endif
        </span>

        @forelse($lowStockProducts->take(4) as $lp)
          @php
            $isCrit = $lp->current_stock <= ($lp->min_stock * 0.5);
            $target = max(1, $lp->min_stock * 2);
            $add = max(0, $target - $lp->current_stock);
          @endphp
          <div style="display: flex; justify-content: space-between; font-size: 14px; color: #374151">
            <span>
              {{ $lp->name }}
              @if($isCrit)
                <span style="color: #B42318; font-weight: 700">· Kritis</span>
              @else
                <span style="color: #8A5300; font-weight: 700">· Menipis</span>
              @endif
            </span>
            <strong style="color: #111827">+{{ $add }} {{ $lp->satuan }}</strong>
          </div>
        @empty
          <span style="font-size: 13px; color: #5B6475">Tidak ada stok yang berada di bawah batas minimum.</span>
        @endforelse
      </div>
    </section>

    <!-- Right Card: Form Buka Shift -->
    <form action="{{ route('shift.open.post') }}" method="POST" style="flex: 1 1 360px; min-width: 0; background: #FFFFFF; border: 2px solid #2563EB; border-radius: 20px; padding: 28px; display: flex; flex-direction: column; gap: 20px; box-shadow: 0 4px 14px rgba(37,99,235,0.08);">
      @csrf
      
      <div style="width: 52px; height: 52px; border-radius: 14px; background: #E8EFFE; color: #1D4ED8; display: flex; align-items: center; justify-content: center">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2.5"></circle><path d="M6 12h.01M18 12h.01"></path></svg>
      </div>

      <div style="display: flex; flex-direction: column; gap: 6px">
        <h2 style="margin: 0; font-size: 22px; font-weight: 800; color: #111827">Buka shift</h2>
        <p style="margin: 0; font-size: 14px; color: #5B6475; line-height: 1.5">
          Hitung ulang uang di laci. Angka di bawah diisi dari kas aktual shift sebelumnya.
        </p>
      </div>

      <div style="display: flex; flex-direction: column; gap: 8px">
        <label for="open-cash" style="font-size: 14px; font-weight: 700; color: #111827">Kas awal (uang di laci)</label>
        <div style="display: flex; align-items: center; gap: 10px; height: 60px; padding: 0 18px; border: 2px solid #111827; border-radius: 14px; background: #FFFFFF">
          <span style="font-weight: 700; color: #5B6475; font-size: 18px">Rp</span>
          <input id="open-cash" name="starting_cash" type="number" required min="0" value="{{ old('starting_cash', $defaultCash) }}" style="flex: 1; min-width: 0; border: 0; outline: none; font-family: inherit; font-size: 24px; font-weight: 800; color: #111827; background: transparent">
        </div>
      </div>

      <button type="submit" style="margin-top: auto; display: flex; align-items: center; justify-content: center; gap: 10px; min-height: 56px; border: 0; border-radius: 14px; background: #2563EB; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 800; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#1D4ED8'" onmouseout="this.style.background='#2563EB'">
        Buka shift sekarang
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
      </button>
    </form>

  </div>
</div>
@endsection
