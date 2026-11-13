@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div style="max-width: 1180px; margin: 0 auto; display: flex; flex-direction: column; gap: 22px">

  <!-- Header -->
  <header style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px">
    <div style="display: flex; flex-direction: column; gap: 6px">
      <h1 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #111827">Laporan</h1>
      <p style="margin: 0; font-size: 15px; color: #5B6475">Rekap penjualan dan kas setiap tutup shift</p>
    </div>
    <a href="{{ route('reports.download', ['period' => $period]) }}" style="display: inline-flex; align-items: center; gap: 8px; min-height: 48px; padding: 0 20px; border-radius: 12px; border: 1px solid #CDD4E0; background: #FFFFFF; color: #111827; font-family: inherit; font-size: 15px; font-weight: 700; text-decoration: none; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#F8FAFD'" onmouseout="this.style.background='#FFFFFF'">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11M7 10l5 5 5-5"></path><path d="M4 20h16"></path></svg>
      Unduh Laporan Laba &amp; Keuangan (Excel)
    </a>
  </header>

  <!-- Period Filter Buttons -->
  <div role="group" aria-label="Filter periode" style="display: flex; flex-wrap: wrap; gap: 8px">
    @php
      $periods = [
        'all' => 'Semua',
        'today' => 'Hari ini',
        'week' => '7 hari',
        'month' => '30 hari',
      ];
    @endphp
    @foreach($periods as $key => $label)
      <a href="{{ route('reports.index', ['period' => $key]) }}" style="min-height: 44px; padding: 0 16px; border-radius: 999px; text-decoration: none; font-family: inherit; font-size: 14px; font-weight: 700; display: inline-flex; align-items: center; {{ $period === $key ? 'border: 1px solid #111827; background: #111827; color: #FFFFFF;' : 'border: 1px solid #E4E8F0; background: #FFFFFF; color: #374151;' }}">
        {{ $label }}
      </a>
    @endforeach
  </div>

  <!-- Summary Cards -->
  <section aria-label="Ringkasan" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px">
    <!-- Card 1: Total Penjualan -->
    <div style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 8px">
      <span style="font-size: 14px; font-weight: 600; color: #5B6475">Total penjualan</span>
      <span style="font-size: 28px; font-weight: 800; letter-spacing: -0.02em; color: #111827">Rp {{ number_format($totalSales, 0, ',', '.') }}</span>
      <span style="font-size: 13px; color: #5B6475">{{ $closedCount }} shift ditutup</span>
    </div>

    <!-- Card 2: Metode Bayar -->
    <div style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 12px">
      <span style="font-size: 14px; font-weight: 600; color: #5B6475">Metode bayar</span>
      <div style="display: flex; height: 10px; border-radius: 999px; overflow: hidden; background: #EDF0F5; gap: 2px">
        <div style="width: {{ $cashPct }}%; background: #2563EB;" title="Tunai: {{ $cashPct }}%"></div>
        <div style="width: {{ $qrisPct }}%; background: #D98A0B;" title="QRIS: {{ $qrisPct }}%"></div>
        <div style="width: {{ $trfPct }}%; background: #6366F1;" title="Transfer: {{ $trfPct }}%"></div>
      </div>
      <div style="display: flex; flex-wrap: wrap; gap: 4px 14px; font-size: 13px; color: #374151">
        <span><strong>Tunai</strong> Rp {{ number_format($totalCash, 0, ',', '.') }}</span>
        <span><strong>QRIS</strong> Rp {{ number_format($totalQris, 0, ',', '.') }}</span>
        <span><strong>Transfer</strong> Rp {{ number_format($totalTransfer, 0, ',', '.') }}</span>
      </div>
    </div>

    <!-- Card 3: Selisih Kas Bersih -->
    <div style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 8px">
      <span style="font-size: 14px; font-weight: 600; color: #5B6475">Selisih kas bersih</span>
      <span style="font-size: 28px; font-weight: 800; letter-spacing: -0.02em; color: {{ $netDifference < 0 ? '#B42318' : ($netDifference > 0 ? '#8A5300' : '#1D4ED8') }}">
        {{ $netDifference < 0 ? '− ' : ($netDifference > 0 ? '+ ' : '') }}Rp {{ number_format(abs($netDifference), 0, ',', '.') }}
      </span>
      <span style="font-size: 13px; color: #5B6475">
        @if($mismatchedCount > 0)
          {{ $mismatchedCount }} dari {{ $closedCount }} shift tidak sesuai
        @else
          Semua shift ditutup dengan kas sesuai
        @endif
      </span>
    </div>
  </section>

  <!-- Table 1: Rekap per shift -->
  <section style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; overflow: hidden">
    <div style="padding: 18px 22px; border-bottom: 1px solid #EDF0F5; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap">
      <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #111827">Rekap per shift</h2>
      <span style="font-size: 13px; color: #5B6475">Kas seharusnya = kas awal + penjualan tunai</span>
    </div>

    <div style="overflow-x: auto">
      <table style="width: 100%; min-width: 960px; border-collapse: collapse; font-size: 14px">
        <thead>
          <tr style="text-align: left; font-size: 12px; letter-spacing: 0.06em; text-transform: uppercase; color: #6B7280; background: #F8FAFD;">
            <th scope="col" style="padding: 14px 22px; font-weight: 700">Waktu Shift</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700">Penjaga</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700; text-align: right">Penjualan</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700; text-align: right">Tunai</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700; text-align: right">QRIS</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700; text-align: right">Transfer</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700; text-align: right">Kas aktual</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700">Selisih</th>
            <th scope="col" style="padding: 14px 22px; font-weight: 700; text-align: right">Unduh</th>
          </tr>
        </thead>
        <tbody>
          @forelse($shifts as $s)
            @php
              $diff = (float) ($s->difference ?? 0);
              $isClosed = $s->status === 'closed';
              $initial = strtoupper(substr($s->user?->name ?? 'P', 0, 1));
              $colors = [
                'M' => ['bg' => '#DBE6FE', 'fg' => '#1E40AF'],
                'Z' => ['bg' => '#E0F2FE', 'fg' => '#075985'],
                'T' => ['bg' => '#E8E1F7', 'fg' => '#4B3390'],
              ];
              $avatarColor = $colors[$initial] ?? ['bg' => '#E8EFFE', 'fg' => '#1D4ED8'];
            @endphp
            <tr style="border-top: 1px solid #EDF0F5">
              <td style="padding: 14px 22px">
                <div style="display: flex; flex-direction: column; gap: 2px">
                  <span style="font-weight: 700; color: #111827">
                    {{ $s->end_time ? \Carbon\Carbon::parse($s->end_time)->format('H:i') : \Carbon\Carbon::parse($s->start_time)->format('H:i') }}
                    @if(!$isClosed)
                      <span style="margin-left: 4px; padding: 2px 6px; border-radius: 6px; background: #E8EFFE; color: #1D4ED8; font-size: 11px; font-weight: 700">Sedang aktif</span>
                    @endif
                  </span>
                  <span style="font-size: 12px; color: #5B6475">
                    {{ \Carbon\Carbon::parse($s->start_time)->format('d/m/Y') }}
                  </span>
                </div>
              </td>

              <td style="padding: 14px 12px">
                <span style="display: inline-flex; align-items: center; gap: 8px; font-weight: 600; color: #111827">
                  <span style="width: 30px; height: 30px; border-radius: 999px; background: {{ $avatarColor['bg'] }}; color: {{ $avatarColor['fg'] }}; display: inline-flex; align-items: center; justify-content: center; font-weight: 800; font-size: 12px">
                    {{ $initial }}
                  </span>
                  {{ $s->user?->name ?? 'Kasir' }}
                </span>
              </td>

              <td style="padding: 14px 12px; text-align: right; font-weight: 700; color: #111827">
                Rp {{ number_format($s->payment_summary->total, 0, ',', '.') }}
              </td>

              <td style="padding: 14px 12px; text-align: right; color: #374151">
                Rp {{ number_format($s->payment_summary->cash, 0, ',', '.') }}
              </td>

              <td style="padding: 14px 12px; text-align: right; color: #374151">
                Rp {{ number_format($s->payment_summary->qris, 0, ',', '.') }}
              </td>

              <td style="padding: 14px 12px; text-align: right; color: #374151">
                Rp {{ number_format($s->payment_summary->transfer, 0, ',', '.') }}
              </td>

              <td style="padding: 14px 12px; text-align: right; color: #374151">
                @if($isClosed)
                  Rp {{ number_format($s->actual_cash, 0, ',', '.') }}
                @else
                  <span style="color: #6B7280;">-</span>
                @endif
              </td>

              <td style="padding: 14px 12px">
                @if($isClosed)
                  @if($diff == 0)
                    <span style="display: inline-flex; padding: 5px 10px; border-radius: 999px; background: #E8EFFE; color: #1D4ED8; font-size: 13px; font-weight: 700; white-space: nowrap">
                      Sesuai
                    </span>
                  @elseif($diff > 0)
                    <span style="display: inline-flex; padding: 5px 10px; border-radius: 999px; background: #FDF1DC; color: #8A5300; font-size: 13px; font-weight: 700; white-space: nowrap">
                      Lebih Rp {{ number_format($diff, 0, ',', '.') }}
                    </span>
                  @else
                    <span style="display: inline-flex; padding: 5px 10px; border-radius: 999px; background: #FDECEA; color: #B42318; font-size: 13px; font-weight: 700; white-space: nowrap">
                      Kurang Rp {{ number_format(abs($diff), 0, ',', '.') }}
                    </span>
                  @endif
                @else
                  <span style="color: #6B7280; font-size: 13px;">Belum ditutup</span>
                @endif
              </td>

              <td style="padding: 14px 22px; text-align: right">
                <a href="{{ route('reports.shift.download', $s->id) }}" title="Unduh laporan shift ini" style="width: 40px; height: 40px; border-radius: 10px; border: 1px solid #E4E8F0; background: #FFFFFF; color: #2563EB; display: inline-flex; align-items: center; justify-content: center; text-decoration: none;">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4v11M7 10l5 5 5-5"></path><path d="M4 20h16"></path></svg>
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" style="padding: 32px; text-align: center; color: #5B6475; font-size: 14px">
                Tidak ada data shift pada periode ini.
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>

  <!-- Table 2: Laporan per transaksi with search -->
  @php
    $txRows = $transactions->map(function($t) {
        $itemsStr = $t->details->map(fn($d) => ($d->product?->name ?? 'Produk') . ' ×' . $d->qty)->join(', ');
        return [
            'id' => $t->id,
            'time' => $t->created_at->format('d/m/Y H:i'),
            'user_name' => $t->user?->name ?? 'Kasir',
            'items' => $itemsStr,
            'method' => $t->payment_method,
            'total' => (float) $t->total_amount,
            'receipt_url' => route('reports.transaction.receipt', $t->id),
        ];
    });
  @endphp

  <section x-data="{
    searchTx: '',
    transactions: {{ json_encode($txRows) }},
    get filteredTx() {
      if (!this.searchTx.trim()) return this.transactions;
      const q = this.searchTx.toLowerCase();
      return this.transactions.filter(t => {
        return String(t.id).includes(q) || t.items.toLowerCase().includes(q) || t.user_name.toLowerCase().includes(q);
      });
    },
    formatRp(val) {
      return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
    }
  }" style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; overflow: hidden">
    <div style="padding: 18px 22px; border-bottom: 1px solid #EDF0F5; display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap">
      <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #111827">Laporan per transaksi</h2>
      
      <label style="display: flex; align-items: center; gap: 8px; min-height: 40px; padding: 0 12px; border: 1px solid #CDD4E0; border-radius: 10px; color: #5B6475">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path></svg>
        <input type="search" x-model="searchTx" placeholder="Cari nomor nota atau barang" style="border: 0; outline: none; font-family: inherit; font-size: 14px; width: 180px; background: transparent; color: #111827">
      </label>
    </div>

    <div style="overflow-x: auto">
      <table style="width: 100%; min-width: 760px; border-collapse: collapse; font-size: 14px">
        <thead>
          <tr style="text-align: left; font-size: 12px; letter-spacing: 0.06em; text-transform: uppercase; color: #6B7280; background: #F8FAFD;">
            <th scope="col" style="padding: 14px 22px; font-weight: 700">Waktu</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700">Penjaga</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700">Barang</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700">Metode</th>
            <th scope="col" style="padding: 14px 12px; font-weight: 700; text-align: right">Total</th>
            <th scope="col" style="padding: 14px 22px; font-weight: 700; text-align: right">Nota</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="t in filteredTx" :key="t.id">
            <tr style="border-top: 1px solid #EDF0F5">
              <td style="padding: 14px 22px; font-weight: 700; color: #111827" x-text="t.time"></td>
              <td style="padding: 14px 12px; color: #374151" x-text="t.user_name"></td>
              <td style="padding: 14px 12px; color: #374151; max-width: 320px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" :title="t.items" x-text="t.items"></td>
              <td style="padding: 14px 12px">
                <span style="padding: 4px 10px; border-radius: 999px; background: #E8EFFE; color: #1D4ED8; font-size: 13px; font-weight: 700" x-text="t.method"></span>
              </td>
              <td style="padding: 14px 12px; text-align: right; font-weight: 700; color: #111827" x-text="formatRp(t.total)"></td>
              <td style="padding: 14px 22px; text-align: right">
                <a :href="t.receipt_url" target="_blank" style="font-weight: 700; text-decoration: none; color: #2563EB">Lihat nota</a>
              </td>
            </tr>
          </template>

          <template x-if="filteredTx.length === 0">
            <tr>
              <td colspan="6" style="padding: 32px; text-align: center; color: #5B6475; font-size: 14px">
                Tidak ada transaksi yang cocok.
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </section>

</div>
@endsection
