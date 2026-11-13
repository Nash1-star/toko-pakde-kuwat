@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div style="max-width: 1180px; margin: 0 auto; display: flex; flex-direction: column; gap: 24px">

  <!-- Header -->
  <header style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px">
    <div style="display: flex; flex-direction: column; gap: 6px">
      <p style="margin: 0; font-size: 14px; color: #5B6475">
        {{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }} · Kas awal Rp {{ number_format($startingCash, 0, ',', '.') }}
      </p>
      <h1 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #111827">
        {{ $greeting }}, {{ Auth::user()->name }}
      </h1>
    </div>
    <a href="{{ route('pos.index') }}" style="display: inline-flex; align-items: center; gap: 10px; min-height: 48px; padding: 0 22px; border-radius: 12px; background: #2563EB; color: #FFFFFF; font-size: 15px; font-weight: 700; text-decoration: none; transition: background 0.15s ease;" onmouseover="this.style.background='#1D4ED8'" onmouseout="this.style.background='#2563EB'">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="12" rx="2"></rect><path d="M7 20h10"></path><path d="M12 16v4"></path><path d="M7 8h4"></path></svg>
      Buka Kasir
    </a>
  </header>

  <!-- Restock Alert Section -->
  @if(count($restockItems) > 0)
    <section x-data="{ 
      copied: false,
      copyList() {
        const text = `Daftar Belanja Restock Toko Pakde Kuwat:\n` + [
          @foreach($restockItems as $r)
            `- {{ $r->name }}: tambah {{ $r->add }} {{ $r->satuan }} (Perkiraan: Rp {{ number_format($r->cost, 0, ',', '.') }})`,
          @endforeach
        ].join('\n') + `\nTotal perkiraan modal: Rp {{ number_format($totalRestockCost, 0, ',', '.') }}`;
        navigator.clipboard.writeText(text).then(() => {
          this.copied = true;
          setTimeout(() => this.copied = false, 3000);
        });
      }
    }" aria-labelledby="restock-title" style="background: #FFFFFF; border: 2px solid #F1C4BE; border-radius: 20px; overflow: hidden">
      <div style="background: #FDECEA; padding: 22px 28px; display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 16px">
        <div style="display: flex; align-items: center; gap: 16px">
          <div style="width: 52px; height: 52px; border-radius: 14px; background: #B42318; color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex: none">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l9.5 17h-19z"></path><path d="M12 10v4"></path><path d="M12 17.5v.01"></path></svg>
          </div>
          <div style="display: flex; flex-direction: column; gap: 4px">
            <h2 id="restock-title" style="margin: 0; font-size: 22px; font-weight: 800; color: #7A1A11">
              {{ count($restockItems) }} barang harus segera diisi ulang
            </h2>
            <p style="margin: 0; font-size: 14px; color: #7A2E25">
              {{ $criticalCount }} kritis, {{ $warningCount }} menipis · Perkiraan modal belanja <strong>Rp {{ number_format($totalRestockCost, 0, ',', '.') }}</strong>
            </p>
          </div>
        </div>
        <div style="display: flex; flex-wrap: wrap; gap: 10px">
          <button type="button" @click="copyList()" style="display: inline-flex; align-items: center; gap: 8px; min-height: 44px; padding: 0 16px; border-radius: 10px; border: 1px solid #E3B3AC; background: #FFFFFF; color: #7A1A11; font-family: inherit; font-size: 14px; font-weight: 700; cursor: pointer">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="8" y="3" width="8" height="4" rx="1"></rect><path d="M8 5H6a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"></path><path d="M8 12h8M8 16h5"></path></svg>
            <span x-text="copied ? 'Daftar tersalin!' : 'Salin daftar belanja'">Salin daftar belanja</span>
          </button>
          <a href="{{ route('products.index') }}" style="display: inline-flex; align-items: center; gap: 8px; min-height: 44px; padding: 0 16px; border-radius: 10px; background: #B42318; color: #FFFFFF; font-size: 14px; font-weight: 700; text-decoration: none">
            Kelola stok
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
          </a>
        </div>
      </div>

      <div style="overflow-x: auto">
        <div style="min-width: 820px">
          <div style="display: grid; grid-template-columns: minmax(220px, 2fr) 120px minmax(220px, 2fr) 150px 140px; gap: 16px; padding: 14px 28px; border-bottom: 1px solid #EDF0F5; font-size: 12px; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; color: #6B7280">
            <span>Barang</span>
            <span>Status</span>
            <span>Sisa stok vs batas minimum</span>
            <span>Harus ditambah</span>
            <span style="text-align: right">Perkiraan modal</span>
          </div>

          @foreach($restockItems as $r)
            <div style="display: grid; grid-template-columns: minmax(220px, 2fr) 120px minmax(220px, 2fr) 150px 140px; gap: 16px; align-items: center; padding: 18px 28px; border-bottom: 1px solid #EDF0F5">
              <div style="display: flex; flex-direction: column; gap: 3px">
                <span style="font-size: 16px; font-weight: 700; color: #111827">{{ $r->name }}</span>
                <span style="font-size: 13px; color: #5B6475">{{ $r->category_name }} · {{ $r->barcode }}</span>
              </div>
              <span style="display: inline-flex; align-items: center; gap: 6px; justify-self: start; padding: 5px 10px; border-radius: 999px; background: {{ $r->badge_bg }}; color: {{ $r->badge_fg }}; font-size: 13px; font-weight: 700">
                <span style="width: 8px; height: 8px; border-radius: 999px; background: {{ $r->dot }}"></span>
                {{ $r->status }}
              </span>
              <div style="display: flex; flex-direction: column; gap: 8px">
                <div style="position: relative; height: 10px; border-radius: 999px; background: #EDF0F5">
                  <div style="position: absolute; left: 0; top: 0; bottom: 0; width: {{ $r->pct }}%; border-radius: 999px; background: {{ $r->dot }}"></div>
                  <div style="position: absolute; left: 50%; top: -4px; bottom: -4px; width: 2px; background: #111827" title="Batas minimum: {{ $r->min_stock }}"></div>
                </div>
                <span style="font-size: 13px; color: #374151">
                  <strong style="color: #111827">{{ $r->current_stock }} {{ $r->satuan }}</strong> tersisa · minimum {{ $r->min_stock }}
                </span>
              </div>
              <div style="display: inline-flex; align-items: baseline; gap: 6px; justify-self: start; padding: 8px 14px; border-radius: 12px; background: #111827; color: #FFFFFF">
                <span style="font-size: 22px; font-weight: 800">+{{ $r->add }}</span>
                <span style="font-size: 13px; color: #D1D5DB">{{ $r->satuan }}</span>
              </div>
              <span style="text-align: right; font-size: 15px; font-weight: 700; color: #111827">
                Rp {{ number_format($r->cost, 0, ',', '.') }}
              </span>
            </div>
          @endforeach

          <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 12px; padding: 16px 28px; background: #F8FAFD; font-size: 13px; color: #5B6475">
            <span>Jumlah tambah = isi sampai stok ideal (2× batas minimum). Garis hitam di bar = batas minimum.</span>
            <span style="font-weight: 700; color: #111827">Total modal: Rp {{ number_format($totalRestockCost, 0, ',', '.') }}</span>
          </div>
        </div>
      </div>
    </section>
  @else
    <section style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 20px; padding: 22px 28px; display: flex; align-items: center; justify-content: space-between; gap: 16px">
      <div style="display: flex; align-items: center; gap: 16px">
        <div style="width: 52px; height: 52px; border-radius: 14px; background: #E8EFFE; color: #1D4ED8; display: flex; align-items: center; justify-content: center; flex: none">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
        </div>
        <div>
          <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: #111827">Semua stok barang dalam kondisi aman</h2>
          <p style="margin: 0; font-size: 14px; color: #5B6475">Tidak ada barang yang berada di bawah batas minimum saat ini.</p>
        </div>
      </div>
      <a href="{{ route('products.index') }}" style="display: inline-flex; align-items: center; gap: 8px; min-height: 44px; padding: 0 18px; border-radius: 10px; background: #EEF1F6; color: #111827; font-size: 14px; font-weight: 700; text-decoration: none">
        Lihat semua barang
      </a>
    </section>
  @endif

  <!-- Ringkasan Hari Ini -->
  <section aria-label="Ringkasan hari ini" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px">
    <!-- Card 1 -->
    <div style="background: #2563EB; border-radius: 20px; padding: 22px; display: flex; flex-direction: column; gap: 14px; color: #FFFFFF">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px">
        <span style="font-size: 14px; font-weight: 600">Penjualan hari ini</span>
        <span style="width: 44px; height: 44px; border-radius: 999px; background: #FFFFFF; color: #2563EB; display: inline-flex; align-items: center; justify-content: center; flex: none">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="2.5"></circle></svg>
        </span>
      </div>
      <span style="font-size: 32px; font-weight: 800; letter-spacing: -0.02em">Rp {{ number_format($todaySales, 0, ',', '.') }}</span>
      <span style="font-size: 13px; font-weight: 600">{{ $txCount }} transaksi hari ini</span>
    </div>

    <!-- Card 2 -->
    <div style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 20px; padding: 22px; display: flex; flex-direction: column; gap: 14px">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px">
        <span style="font-size: 14px; color: #5B6475; font-weight: 600">Transaksi hari ini</span>
        <span style="width: 44px; height: 44px; border-radius: 999px; background: #111827; color: #FFFFFF; display: inline-flex; align-items: center; justify-content: center; flex: none">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="20" r="1.5"></circle><circle cx="18" cy="20" r="1.5"></circle><path d="M2 3h3l2.5 12h12l2-8H6"></path></svg>
        </span>
      </div>
      <span style="font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #111827">{{ $txCount }}</span>
      <span style="font-size: 13px; color: #5B6475">{{ $todayItemsSold }} barang terjual</span>
    </div>

    <!-- Card 3 -->
    <div style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 20px; padding: 22px; display: flex; flex-direction: column; gap: 14px">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px">
        <span style="font-size: 14px; color: #5B6475; font-weight: 600">Uang di laci (perkiraan)</span>
        <span style="width: 44px; height: 44px; border-radius: 999px; background: #DBE6FE; color: #1D4ED8; display: inline-flex; align-items: center; justify-content: center; flex: none">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"></rect><path d="M16 13h2"></path><path d="M5 7l11-4 1 4"></path></svg>
        </span>
      </div>
      <span style="font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #111827">Rp {{ number_format($drawerCashEstimate, 0, ',', '.') }}</span>
      <span style="font-size: 13px; color: #5B6475">Kas awal Rp {{ number_format($startingCash, 0, ',', '.') }} + tunai</span>
    </div>

    <!-- Card 4 -->
    <div style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 20px; padding: 22px; display: flex; flex-direction: column; gap: 14px">
      <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 10px">
        <span style="font-size: 14px; color: #5B6475; font-weight: 600">Barang perlu diisi</span>
        <span style="width: 44px; height: 44px; border-radius: 999px; background: #FDECEA; color: #B42318; display: inline-flex; align-items: center; justify-content: center; flex: none">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 8l-9-5-9 5 9 5 9-5z"></path><path d="M3 8v8l9 5 9-5V8"></path><path d="M12 13v8"></path></svg>
        </span>
      </div>
      <span style="font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #111827">
        {{ count($restockItems) }} <span style="font-size: 16px; color: #5B6475; font-weight: 600">dari {{ $totalProductsCount }} barang</span>
      </span>
      <span style="display: inline-flex; gap: 6px; flex-wrap: wrap; font-size: 12px; font-weight: 700">
        <span style="padding: 3px 8px; border-radius: 999px; background: #FDECEA; color: #B42318">{{ $criticalCount }} kritis</span>
        <span style="padding: 3px 8px; border-radius: 999px; background: #FDF1DC; color: #8A5300">{{ $warningCount }} menipis</span>
      </span>
    </div>
  </section>

  <!-- Charts & Gauges Section -->
  <div style="display: flex; flex-wrap: wrap; gap: 16px; align-items: stretch">
    
    <!-- Penjualan Per Shift Bar Chart -->
    <section x-data="{
      selectedIdx: {{ count($recentShifts) > 0 ? (count($recentShifts) - 1) : 'null' }},
      shifts: {{ json_encode($recentShifts) }},
      get currentTip() {
        if (this.selectedIdx === null || !this.shifts[this.selectedIdx]) return null;
        return this.shifts[this.selectedIdx];
      },
      formatRp(val) {
        return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
      }
    }" aria-labelledby="chart-title" style="flex: 2 1 540px; min-width: 0; background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 20px; padding: 24px; display: flex; flex-direction: column; gap: 20px">
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-start; gap: 12px">
        <div style="display: flex; flex-direction: column; gap: 4px">
          <h2 id="chart-title" style="margin: 0; font-size: 19px; font-weight: 800; color: #111827">Penjualan per shift</h2>
          <span style="font-size: 14px; color: #5B6475">
            {{ count($recentShifts) }} shift terakhir · total Rp {{ number_format(collect($recentShifts)->sum('total'), 0, ',', '.') }}
          </span>
        </div>
      </div>

      @if(count($recentShifts) > 0)
        <div style="display: flex; gap: 12px">
          <div aria-hidden="true" style="width: 52px; flex: none; height: 260px; display: flex; flex-direction: column; justify-content: space-between; font-size: 12px; color: #6B7280; text-align: right">
            <span>Maks</span>
            <span>75%</span>
            <span>50%</span>
            <span>25%</span>
            <span>0</span>
          </div>

          <div style="flex: 1; min-width: 0; display: flex; flex-direction: column; gap: 10px">
            <div style="position: relative; height: 260px">
              <div aria-hidden="true" style="position: absolute; inset: 0; display: flex; flex-direction: column; justify-content: space-between">
                <div style="border-top: 1px dashed #DCE2EC"></div>
                <div style="border-top: 1px dashed #DCE2EC"></div>
                <div style="border-top: 1px dashed #DCE2EC"></div>
                <div style="border-top: 1px dashed #DCE2EC"></div>
                <div style="border-top: 1px solid #DCE2EC"></div>
              </div>

              <!-- Bars Container -->
              <div style="position: absolute; inset: 0; display: flex; gap: 10px; align-items: stretch">
                <template x-for="(b, idx) in shifts" :key="idx">
                  <button type="button" @click="selectedIdx = idx" :aria-label="'Shift ' + b.time + ' ' + b.user_name" style="position: relative; flex: 1; min-width: 0; height: 100%; padding: 0; border: 0; border-radius: 14px; background: #F3F6FB; display: flex; flex-direction: column; justify-content: flex-end; cursor: pointer;">
                    <span :style="'display: block; width: 100%; height: ' + b.height_pct + '%; border-radius: 12px; background: ' + (selectedIdx === idx ? 'repeating-linear-gradient(135deg, #2563EB 0 7px, #4F86F7 7px 14px)' : (b.total === 0 ? '#CDD4E0' : '#BFD0F5'))"></span>
                    <template x-if="selectedIdx === idx">
                      <span :style="'position: absolute; left: 50%; bottom: ' + b.height_pct + '%; width: 14px; height: 14px; margin: 0 0 -7px -7px; border-radius: 999px; background: #FFFFFF; border: 3px solid #1D4ED8; box-sizing: border-box'"></span>
                    </template>
                  </button>
                </template>
              </div>

              <!-- Tooltip popup -->
              <template x-if="currentTip">
                <div :style="'position: absolute; top: 8px; left: ' + (selectedIdx < (shifts.length / 2) ? 'calc(' + ((selectedIdx + 1) / shifts.length * 100) + '% + 8px)' : 'calc(' + (selectedIdx / shifts.length * 100) + '% - 218px)') + '; width: 210px; background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 14px; padding: 14px; box-shadow: 0 12px 28px rgba(17, 24, 39, 0.12); display: flex; flex-direction: column; gap: 8px; pointer-events: none; z-index: 20;'">
                  <span style="font-size: 14px; font-weight: 800; color: #111827" x-text="currentTip.date + ', ' + currentTip.time + ' · ' + currentTip.user_name"></span>
                  <span style="display: flex; justify-content: space-between; font-size: 13px; color: #5B6475">
                    <span>Penjualan</span><strong style="color: #111827" x-text="formatRp(currentTip.total)"></strong>
                  </span>
                  <span style="display: flex; justify-content: space-between; font-size: 13px; color: #5B6475">
                    <span>Tunai</span><span style="color: #111827" x-text="formatRp(currentTip.cash)"></span>
                  </span>
                  <span style="display: flex; justify-content: space-between; font-size: 13px; color: #5B6475">
                    <span>QRIS</span><span style="color: #111827" x-text="formatRp(currentTip.qris)"></span>
                  </span>
                </div>
              </template>
            </div>

            <!-- Bar X Axis Labels -->
            <div aria-hidden="true" style="display: flex; gap: 10px">
              <template x-for="(b, idx) in shifts" :key="idx">
                <span style="flex: 1; min-width: 0; display: flex; flex-direction: column; align-items: center; gap: 2px">
                  <span style="font-size: 13px; font-weight: 700;" :style="selectedIdx === idx ? 'color: #1D4ED8;' : 'color: #374151;'" x-text="b.time"></span>
                  <span style="font-size: 12px; color: #6B7280; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%" x-text="b.user_name"></span>
                </span>
              </template>
            </div>
          </div>
        </div>
      @else
        <div style="padding: 40px 0; text-align: center; color: #5B6475; font-size: 14px">
          Belum ada riwayat shift yang ditutup.
        </div>
      @endif
    </section>

    <!-- Kesehatan Stok Gauge -->
    <section aria-labelledby="gauge-title" style="flex: 1 1 320px; min-width: 0; background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 20px; padding: 24px; display: flex; flex-direction: column; gap: 18px">
      <div style="display: flex; flex-direction: column; gap: 4px">
        <h2 id="gauge-title" style="margin: 0; font-size: 19px; font-weight: 800; color: #111827">Kesehatan stok</h2>
        <span style="font-size: 14px; color: #5B6475">Barang dengan stok di atas batas minimum</span>
      </div>

      <!-- Segment Gauge -->
      <div role="img" aria-label="Kesehatan stok: {{ $healthPercent }}% aman" style="position: relative; width: 280px; height: 150px; margin: 4px auto 0">
        @php
          $totalTicks = 12;
          $safeTicks = (int) round(($healthPercent / 100) * $totalTicks);
          $filledColors = ['#1D4ED8', '#1F55E0', '#2563EB', '#3473F0', '#4A84F4', '#609AF7', '#7BB0F8', '#93C0F9', '#A8CEFA', '#BDDCFA', '#D2EAFA', '#E7F4FD'];
        @endphp
        @for($i = 0; $i < $totalTicks; $i++)
          @php
            $deg = -82.5 + ($i * 15);
            $color = ($i < $safeTicks) ? ($filledColors[$i % count($filledColors)]) : ($i < ($safeTicks + 2) ? '#E8A33D' : '#D9483B');
          @endphp
          <span style="position: absolute; left: 129px; top: 4px; width: 22px; height: 44px; border-radius: 8px; background: {{ $color }}; transform-origin: 11px 136px; transform: rotate({{ $deg }}deg)"></span>
        @endfor
        <div style="position: absolute; left: 0; right: 0; bottom: 0; display: flex; flex-direction: column; align-items: center; gap: 2px">
          <span style="font-size: 36px; font-weight: 800; letter-spacing: -0.02em; color: #111827">{{ $healthPercent }}%</span>
          <span style="font-size: 13px; color: #5B6475">stok aman</span>
        </div>
      </div>

      <!-- Breakdown badges -->
      <div style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 10px">
        <div style="padding: 12px; border-radius: 14px; background: #F5F7FB; display: flex; flex-direction: column; gap: 6px">
          <span style="font-size: 13px; color: #5B6475">Aman</span>
          <span style="font-size: 22px; font-weight: 800; color: #1D4ED8">{{ $safeCount }}</span>
        </div>
        <div style="padding: 12px; border-radius: 14px; background: #F5F7FB; display: flex; flex-direction: column; gap: 6px">
          <span style="font-size: 13px; color: #5B6475">Menipis</span>
          <span style="font-size: 22px; font-weight: 800; color: #8A5300">{{ $warningCount }}</span>
        </div>
        <div style="padding: 12px; border-radius: 14px; background: #F5F7FB; display: flex; flex-direction: column; gap: 6px">
          <span style="font-size: 13px; color: #5B6475">Kritis</span>
          <span style="font-size: 22px; font-weight: 800; color: #B42318">{{ $criticalCount }}</span>
        </div>
      </div>

      <a href="{{ route('products.index') }}" style="margin-top: auto; display: flex; align-items: center; justify-content: center; gap: 8px; min-height: 44px; border-radius: 12px; background: #E8EFFE; color: #1D4ED8; font-size: 14px; font-weight: 700; text-decoration: none">
        Lihat barang yang perlu diisi
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"></path></svg>
      </a>
    </section>
  </div>

  <!-- Bottom Section: Transaksi Terakhir & Catatan Serah Terima -->
  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(340px, 1fr)); gap: 16px">
    
    <!-- Transaksi Terakhir -->
    <section style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 16px; padding: 22px; display: flex; flex-direction: column; gap: 16px">
      <div style="display: flex; justify-content: space-between; align-items: center">
        <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #111827">Transaksi terakhir</h2>
        <a href="{{ route('reports.index') }}" style="font-size: 14px; font-weight: 700; text-decoration: none; color: #2563EB">Lihat laporan</a>
      </div>

      @forelse($recentTransactions as $tx)
        @php
          $itemsSummary = $tx->details->map(fn($d) => $d->product?->name . ($d->qty > 1 ? ' x' . $d->qty : ''))->take(3)->join(', ');
          if ($tx->details->count() > 3) $itemsSummary .= '...';
        @endphp
        <div style="display: flex; align-items: center; gap: 14px; padding: 14px; border-radius: 12px; background: #F5F7FB">
          <span style="width: 40px; height: 40px; border-radius: 10px; background: #E8EFFE; color: #1D4ED8; display: inline-flex; align-items: center; justify-content: center; flex: none">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="6" width="20" height="12" rx="2"></rect><circle cx="12" cy="12" r="2.5"></circle></svg>
          </span>
          <div style="display: flex; flex-direction: column; gap: 3px; flex: 1; min-width: 0">
            <span style="font-size: 15px; font-weight: 700; color: #111827; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
              {{ $itemsSummary ?: 'Pembelian produk' }}
            </span>
            <span style="font-size: 13px; color: #5B6475">
              {{ $tx->created_at->format('H:i') }} · {{ $tx->payment_method }} · {{ $tx->details->sum('qty') }} barang · {{ $tx->user?->name ?? 'Kasir' }}
            </span>
          </div>
          <span style="font-size: 15px; font-weight: 800; color: #111827">
            Rp {{ number_format($tx->total_amount, 0, ',', '.') }}
          </span>
        </div>
      @empty
        <div style="padding: 24px; text-align: center; color: #5B6475; font-size: 14px">
          Belum ada transaksi hari ini.
        </div>
      @endforelse
    </section>

    <!-- Catatan Serah Terima -->
    <section style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 16px; padding: 22px; display: flex; flex-direction: column; gap: 16px">
      <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #111827">Catatan serah terima</h2>
      @if($latestHandoverShift)
        <div style="display: flex; gap: 14px; padding: 14px; border-radius: 12px; background: #FDF1DC">
          <span style="width: 36px; height: 36px; border-radius: 999px; background: #E8E1F7; color: #4B3390; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 14px; flex: none">
            {{ strtoupper(substr($latestHandoverShift->user?->name ?? 'P', 0, 1)) }}
          </span>
          <div style="display: flex; flex-direction: column; gap: 4px">
            <span style="font-size: 15px; font-weight: 700; color: #111827">
              “{{ $latestHandoverShift->notes }}”
            </span>
            <span style="font-size: 13px; color: #6A4300">
              {{ $latestHandoverShift->user?->name ?? 'Penjaga' }} · shift {{ $latestHandoverShift->end_time ? \Carbon\Carbon::parse($latestHandoverShift->end_time)->format('H:i') : '' }} · selisih kas Rp {{ number_format($latestHandoverShift->difference ?? 0, 0, ',', '.') }}
            </span>
          </div>
        </div>
      @else
        <div style="padding: 24px; text-align: center; color: #5B6475; font-size: 14px; background: #F5F7FB; border-radius: 12px">
          Tidak ada catatan serah terima dari shift sebelumnya.
        </div>
      @endif
    </section>

  </div>
</div>
@endsection
