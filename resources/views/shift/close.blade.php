@extends('layouts.app')

@section('title', 'Tutup Shift')

@section('content')
@php
  $expectedCash = (float) $shift->expected_cash;
  $lowStockNames = $lowStockProducts->map(function($p) {
    $target = max(1, $p->min_stock * 2);
    $add = max(0, $target - $p->current_stock);
    return $p->name . ' +' . $add;
  })->join(', ');
@endphp

<div x-data="{
  expected: {{ $expectedCash }},
  actualStr: '{{ old('actual_cash', $expectedCash) }}',
  note: '{{ old('notes', '') }}',

  get actual() {
    return parseInt(String(this.actualStr).replace(/\D/g, ''), 10) || 0;
  },

  get diff() {
    return this.actual - this.expected;
  },

  get diffLabel() {
    if (this.diff === 0) return 'Kas sesuai';
    return this.diff > 0 ? 'Kas lebih' : 'Kas kurang';
  },

  get diffBg() {
    if (this.diff === 0) return '#E8EFFE';
    return this.diff > 0 ? '#FDF1DC' : '#FDECEA';
  },

  get diffFg() {
    if (this.diff === 0) return '#1D4ED8';
    return this.diff > 0 ? '#8A5300' : '#B42318';
  },

  formatRp(n) {
    return 'Rp ' + Number(Math.abs(n) || 0).toLocaleString('id-ID');
  },

  addStockNote() {
    const line = 'Perlu belanja: {{ addslashes($lowStockNames) }}';
    if (!this.note) {
      this.note = line;
    } else if (!this.note.includes('Perlu belanja:')) {
      this.note = this.note + '\n' + line;
    }
  }
}" style="max-width: 760px; margin: 0 auto; display: flex; flex-direction: column; gap: 20px">

  <!-- Header with back button -->
  <header style="display: flex; flex-direction: column; gap: 6px">
    <a href="{{ route('dashboard') }}" style="font-size: 14px; font-weight: 700; text-decoration: none; color: #2563EB">
      ← Kembali ke dashboard
    </a>
    <h1 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #111827">
      Tutup Shift &amp; Serah Terima
    </h1>
    <p style="margin: 0; font-size: 15px; color: #5B6475">
      Hitung uang di laci, lalu tulis catatan untuk penjaga berikutnya.
    </p>
  </header>

  <!-- Shift Recap Card -->
  <section style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; padding: 22px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
    <!-- 3 key stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 12px">
      <div style="display: flex; flex-direction: column; gap: 4px">
        <span style="font-size: 13px; color: #5B6475; font-weight: 600">Shift dimulai</span>
        <span style="font-size: 16px; font-weight: 800; color: #111827">
          {{ \Carbon\Carbon::parse($shift->start_time)->format('d M, H:i') }}
        </span>
      </div>
      <div style="display: flex; flex-direction: column; gap: 4px">
        <span style="font-size: 13px; color: #5B6475; font-weight: 600">Kas awal</span>
        <span style="font-size: 16px; font-weight: 800; color: #111827">
          Rp {{ number_format($shift->starting_cash, 0, ',', '.') }}
        </span>
      </div>
      <div style="display: flex; flex-direction: column; gap: 4px">
        <span style="font-size: 13px; color: #5B6475; font-weight: 600">Penjualan shift ini</span>
        <span style="font-size: 16px; font-weight: 800; color: #1D4ED8">
          + Rp {{ number_format($sales, 0, ',', '.') }}
        </span>
      </div>
    </div>

    <!-- 3 Payment methods breakdown pills -->
    <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px">
      <div style="padding: 12px 14px; border-radius: 12px; background: #EEF1F6; display: flex; flex-direction: column; gap: 3px">
        <span style="font-size: 13px; color: #5B6475">Tunai</span>
        <span style="font-weight: 800; color: #111827">Rp {{ number_format($summary->cash, 0, ',', '.') }}</span>
      </div>
      <div style="padding: 12px 14px; border-radius: 12px; background: #EEF1F6; display: flex; flex-direction: column; gap: 3px">
        <span style="font-size: 13px; color: #5B6475">QRIS</span>
        <span style="font-weight: 800; color: #111827">Rp {{ number_format($summary->qris, 0, ',', '.') }}</span>
      </div>
      <div style="padding: 12px 14px; border-radius: 12px; background: #EEF1F6; display: flex; flex-direction: column; gap: 3px">
        <span style="font-size: 13px; color: #5B6475">Transfer</span>
        <span style="font-weight: 800; color: #111827">Rp {{ number_format($summary->transfer, 0, ',', '.') }}</span>
      </div>
    </div>

    <!-- Expected Cash Total -->
    <div style="display: flex; justify-content: space-between; align-items: baseline; gap: 12px; padding-top: 14px; border-top: 1px dashed #CDD4E0">
      <div style="display: flex; flex-direction: column; gap: 2px">
        <span style="font-size: 16px; font-weight: 800; color: #111827">Kas seharusnya</span>
        <span style="font-size: 13px; color: #5B6475">Kas awal + tunai. QRIS &amp; transfer masuk ke rekening bank.</span>
      </div>
      <span style="font-size: 28px; font-weight: 800; letter-spacing: -0.02em; color: #111827">
        Rp {{ number_format($expectedCash, 0, ',', '.') }}
      </span>
    </div>
  </section>

  <!-- Submit Form for Close Shift -->
  <form action="{{ route('shift.close.post') }}" method="POST" style="display: flex; flex-direction: column; gap: 20px">
    @csrf

    <!-- Actual Cash Drawer Section -->
    <section style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; padding: 22px; display: flex; flex-direction: column; gap: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      <label for="actual" style="font-size: 16px; font-weight: 800; color: #111827">Uang fisik di laci</label>
      <div style="display: flex; align-items: center; gap: 10px; height: 60px; padding: 0 18px; border: 2px solid #111827; border-radius: 14px; background: #FFFFFF">
        <span style="font-weight: 700; color: #5B6475; font-size: 18px">Rp</span>
        <input id="actual" name="actual_cash" type="number" required min="0" x-model="actualStr" placeholder="0" style="flex: 1; min-width: 0; border: 0; outline: none; font-family: inherit; font-size: 24px; font-weight: 800; color: #111827; background: transparent">
      </div>

      <!-- Real-time Difference Banner -->
      <div style="display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 14px 16px; border-radius: 12px;" :style="'background: ' + diffBg">
        <span style="font-size: 15px; font-weight: 700;" :style="'color: ' + diffFg" x-text="diffLabel"></span>
        <span style="font-size: 20px; font-weight: 800;" :style="'color: ' + diffFg" x-text="(diff > 0 ? '+ ' : (diff < 0 ? '− ' : '')) + formatRp(diff)"></span>
      </div>
    </section>

    <!-- Restock Alert & Handover Notes Section -->
    <section style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; padding: 22px; display: flex; flex-direction: column; gap: 14px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
      @if($lowStockProducts->count() > 0)
        <div style="display: flex; gap: 12px; align-items: center; padding: 14px; border-radius: 12px; background: #FDECEA; flex-wrap: wrap">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#B42318" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l9.5 17h-19z"></path><path d="M12 10v4"></path><path d="M12 17.5v.01"></path></svg>
          <span style="flex: 1 1 260px; font-size: 14px; color: #7A1A11">
            <strong>{{ $lowStockProducts->count() }} barang perlu diisi ulang:</strong> {{ $lowStockProducts->pluck('name')->join(', ') }}
          </span>
          <button type="button" @click="addStockNote()" style="min-height: 40px; padding: 0 14px; border-radius: 10px; border: 1px solid #E3B3AC; background: #FFFFFF; color: #7A1A11; font-family: inherit; font-size: 13px; font-weight: 700; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#FEE2E2'" onmouseout="this.style.background='#FFFFFF'">
            Masukkan ke catatan
          </button>
        </div>
      @endif

      <label for="note" style="font-size: 16px; font-weight: 800; color: #111827">
        Catatan serah terima <span style="font-weight: 500; color: #5B6475; font-size: 14px">(opsional)</span>
      </label>
      <textarea id="note" name="notes" rows="3" x-model="note" placeholder="Contoh: uang receh tinggal sedikit atau titipan catatan..." style="border: 1px solid #CDD4E0; border-radius: 12px; padding: 14px; font-family: inherit; font-size: 15px; color: #111827; resize: vertical; box-sizing: border-box; width: 100%"></textarea>
    </section>

    <!-- Confirmation & Close Button -->
    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin menutup shift ini?')" style="display: flex; align-items: center; justify-content: center; gap: 10px; min-height: 56px; border: 0; border-radius: 14px; background: #B42318; color: #FFFFFF; font-family: inherit; font-size: 16px; font-weight: 800; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#991B1B'" onmouseout="this.style.background='#B42318'">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 4h4v16h-4"></path><path d="M10 8l-4 4 4 4"></path><path d="M6 12h10"></path></svg>
      Konfirmasi &amp; Tutup Shift
    </button>
  </form>

</div>
@endsection
