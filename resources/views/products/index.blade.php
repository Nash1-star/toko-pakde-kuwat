@extends('layouts.app')

@section('title', 'Daftar Barang')

@push('styles')
<style>
  @media (max-width: 1024px) {
    .products-table {
      min-width: 560px !important;
      table-layout: fixed;
    }
    .products-table th:nth-child(1), .products-table td:nth-child(1) { width: 24%; }
    .products-table th:nth-child(2), .products-table td:nth-child(2) { width: 12%; }
    .products-table th:nth-child(3), .products-table td:nth-child(3) { width: 19%; }
    .products-table th:nth-child(4), .products-table td:nth-child(4) { width: 10%; }
    .products-table th:nth-child(5), .products-table td:nth-child(5) { width: 15%; }
    .products-table th:nth-child(6), .products-table td:nth-child(6) { width: 20%; }
    .products-table td:nth-child(6) { padding-left: 6px !important; padding-right: 6px !important; }
    .products-table td:nth-child(6) > div { gap: 3px !important; flex-wrap: nowrap; }
    .products-table td:nth-child(6) button:not([aria-label]) { min-height: 22px !important; padding: 0 5px !important; }
    .products-table td:nth-child(6) button[aria-label] { width: 22px !important; height: 22px !important; }
    .products-table td:nth-child(6) svg { width: 11px; height: 11px; }
  }
</style>
@endpush

@section('content')
@php
  // Precompute presentation attributes for Alpine
  $productRows = $products->map(function ($p) {
      $isCrit = $p->current_stock <= ($p->min_stock * 0.5);
      $isLow = $p->current_stock <= $p->min_stock;
      $level = $isCrit ? 'crit' : ($isLow ? 'low' : 'ok');
      $target = max(1, $p->min_stock * 2);
      $add = $level === 'ok' ? 0 : max(0, $target - $p->current_stock);
      $cost = $add * (float) $p->purchase_price;
      $pct = min(100, round(($p->current_stock / $target) * 100));

      return [
          'id' => $p->id,
          'category_id' => $p->category_id,
          'category_name' => $p->category?->name ?? 'Tanpa Kategori',
          'name' => $p->name,
          'barcode' => $p->barcode ?? '',
          'satuan' => $p->satuan ?? 'pcs',
          'purchase_price' => (float) $p->purchase_price,
          'selling_price' => (float) $p->selling_price,
          'current_stock' => (int) $p->current_stock,
          'min_stock' => (int) $p->min_stock,
          'level' => $level,
          'needs' => $level !== 'ok',
          'add' => $add,
          'cost' => $cost,
          'pct' => $pct,
          'badge_bg' => $isCrit ? '#FDECEA' : ($isLow ? '#FDF1DC' : '#E8EFFE'),
          'badge_fg' => $isCrit ? '#B42318' : ($isLow ? '#8A5300' : '#1D4ED8'),
          'dot' => $isCrit ? '#C8331F' : ($isLow ? '#D98A0B' : '#3B82F6'),
          'row_bg' => $isCrit ? '#FFF7F6' : ($isLow ? '#FFFBF3' : '#FFFFFF'),
          'status' => $isCrit ? 'Kritis' : ($isLow ? 'Menipis' : 'Aman'),
      ];
  });
@endphp

<div x-data="{
  filter: 'all',
  search: '',
  categoryFilter: 'all',
  products: {{ json_encode($productRows) }},
  
  // Modals state
  createModal: false,
  editModal: false,
  restockModal: false,
  deleteModal: false,
  
  activeProduct: {},
  restockQty: 1,

  openEdit(p) {
    this.activeProduct = { ...p };
    this.editModal = true;
  },

  openRestock(p) {
    this.activeProduct = { ...p };
    this.restockQty = p.add > 0 ? p.add : 1;
    this.restockModal = true;
  },

  openDelete(p) {
    this.activeProduct = { ...p };
    this.deleteModal = true;
  },

  get filteredProducts() {
    return this.products.filter(p => {
      // Filter tab
      if (this.filter === 'needs' && !p.needs) return false;
      if (this.filter === 'crit' && p.level !== 'crit') return false;
      if (this.filter === 'low' && p.level !== 'low') return false;
      if (this.filter === 'ok' && p.level !== 'ok') return false;

      // Category filter
      if (this.categoryFilter !== 'all' && String(p.category_id) !== String(this.categoryFilter)) return false;

      // Search
      if (this.search.trim() !== '') {
        const q = this.search.toLowerCase();
        const matchName = p.name.toLowerCase().includes(q);
        const matchBarcode = p.barcode.toLowerCase().includes(q);
        const matchCat = p.category_name.toLowerCase().includes(q);
        if (!matchName && !matchBarcode && !matchCat) return false;
      }

      return true;
    });
  },

  count(type) {
    if (type === 'all') return this.products.length;
    if (type === 'needs') return this.products.filter(p => p.needs).length;
    if (type === 'crit') return this.products.filter(p => p.level === 'crit').length;
    if (type === 'low') return this.products.filter(p => p.level === 'low').length;
    if (type === 'ok') return this.products.filter(p => p.level === 'ok').length;
    return 0;
  },

  formatRp(n) {
    return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
  }
}" style="max-width: 1180px; margin: 0 auto; display: flex; flex-direction: column; gap: 14px">

  <!-- Header -->
  <header style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px">
    <div style="display: flex; flex-direction: column; gap: 6px">
      <h1 style="margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.02em; color: #111827">Daftar Barang</h1>
      <p style="margin: 0; font-size: 11px; color: #5B6475">
        {{ $products->count() }} barang · {{ $categories->count() }} kategori · nilai stok (harga beli) Rp {{ number_format($totalStockValue, 0, ',', '.') }}
      </p>
    </div>
    <button type="button" @click="createModal = true" style="display: inline-flex; align-items: center; gap: 6px; min-height: 36px; padding: 0 14px; border-radius: 9px; border: 0; background: #2563EB; color: #FFFFFF; font-family: inherit; font-size: 11px; font-weight: 700; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#1D4ED8'" onmouseout="this.style.background='#2563EB'">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
      Tambah Barang
    </button>
  </header>

  <!-- Restock Notification Banner -->
  @if($restockCount > 0)
    <section aria-label="Ringkasan stok" style="display: flex; flex-wrap: wrap; align-items: center; gap: 10px 14px; padding: 11px 13px; border-radius: 11px; background: #FDECEA; border: 1px solid #F1C4BE">
      <div style="width: 30px; height: 30px; border-radius: 8px; background: #B42318; color: #FFFFFF; display: flex; align-items: center; justify-content: center; flex: none">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l9.5 17h-19z"></path><path d="M12 10v4"></path><path d="M12 17.5v.01"></path></svg>
      </div>
      <div style="display: flex; flex-direction: column; gap: 2px; flex: 1 1 280px">
        <span style="font-size: 12px; font-weight: 800; color: #7A1A11">{{ $restockCount }} barang harus diisi ulang</span>
        <span style="font-size: 9px; color: #7A2E25">Total yang perlu dibeli {{ $totalPcsToBuy }} pcs · perkiraan modal Rp {{ number_format($estimatedModal, 0, ',', '.') }}</span>
      </div>
      <button type="button" @click="filter = 'needs'" style="min-height: 30px; padding: 0 12px; border-radius: 8px; border: 0; background: #B42318; color: #FFFFFF; font-family: inherit; font-size: 9px; font-weight: 700; cursor: pointer">
        Tampilkan yang perlu diisi
      </button>
    </section>
  @endif

  <!-- Main Card with Filter, Search & Table -->
  <section style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 14px; overflow: hidden">
    <!-- Filter bar (Matching exact layout: Pills on top row, Search & Category on row below) -->
    <div style="display: flex; flex-direction: column; gap: 9px; padding: 11px 12px; border-bottom: 1px solid #EDF0F5">
      
      <!-- Row 1: Status Filter Pills -->
      <div role="group" aria-label="Filter status stok" style="display: flex; flex-wrap: wrap; gap: 6px; align-items: center">
        <!-- Semua -->
        <button type="button" @click="filter = 'all'"
          :class="filter === 'all' ? 'border-[#0F172A] bg-[#0F172A] text-white' : 'border-[#CBD5E1] bg-white text-[#0F172A]'"
          style="display: inline-flex; align-items: center; gap: 6px; min-height: 29px; padding: 0 10px; border: 1px solid #CBD5E1; border-radius: 9999px; font-family: inherit; font-size: 10px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
          <span style="width: 8px; height: 8px; border-radius: 9999px;" :class="filter === 'all' ? 'bg-white' : 'bg-[#0F172A]'"></span>
          Semua
          <span style="min-width: 18px; height: 18px; padding: 0 4px; box-sizing: border-box; border-radius: 9999px; font-size: 9px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;"
            :class="filter === 'all' ? 'bg-white text-[#0F172A]' : 'bg-[#EDF0F5] text-[#0F172A]'"
            x-text="count('all')"></span>
        </button>

        <!-- Perlu diisi -->
        <button type="button" @click="filter = 'needs'"
          :class="filter === 'needs' ? 'border-[#0F172A] bg-[#0F172A] text-white' : 'border-[#CBD5E1] bg-white text-[#0F172A]'"
          style="display: inline-flex; align-items: center; gap: 6px; min-height: 29px; padding: 0 10px; border: 1px solid #CBD5E1; border-radius: 9999px; font-family: inherit; font-size: 10px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
          <span style="width: 8px; height: 8px; border-radius: 9999px; background: #B91C1C;"></span>
          Perlu diisi
          <span style="min-width: 18px; height: 18px; padding: 0 4px; box-sizing: border-box; border-radius: 9999px; font-size: 9px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;"
            :class="filter === 'needs' ? 'bg-white text-[#0F172A]' : 'bg-[#EDF0F5] text-[#0F172A]'"
            x-text="count('needs')"></span>
        </button>

        <!-- Kritis -->
        <button type="button" @click="filter = 'crit'"
          :class="filter === 'crit' ? 'border-[#0F172A] bg-[#0F172A] text-white' : 'border-[#CBD5E1] bg-white text-[#0F172A]'"
          style="display: inline-flex; align-items: center; gap: 6px; min-height: 29px; padding: 0 10px; border: 1px solid #CBD5E1; border-radius: 9999px; font-family: inherit; font-size: 10px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
          <span style="width: 8px; height: 8px; border-radius: 9999px; background: #DC2626;"></span>
          Kritis
          <span style="min-width: 18px; height: 18px; padding: 0 4px; box-sizing: border-box; border-radius: 9999px; font-size: 9px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;"
            :class="filter === 'crit' ? 'bg-white text-[#0F172A]' : 'bg-[#EDF0F5] text-[#0F172A]'"
            x-text="count('crit')"></span>
        </button>

        <!-- Menipis -->
        <button type="button" @click="filter = 'low'"
          :class="filter === 'low' ? 'border-[#0F172A] bg-[#0F172A] text-white' : 'border-[#CBD5E1] bg-white text-[#0F172A]'"
          style="display: inline-flex; align-items: center; gap: 6px; min-height: 29px; padding: 0 10px; border: 1px solid #CBD5E1; border-radius: 9999px; font-family: inherit; font-size: 10px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
          <span style="width: 8px; height: 8px; border-radius: 9999px; background: #D97706;"></span>
          Menipis
          <span style="min-width: 18px; height: 18px; padding: 0 4px; box-sizing: border-box; border-radius: 9999px; font-size: 9px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;"
            :class="filter === 'low' ? 'bg-white text-[#0F172A]' : 'bg-[#EDF0F5] text-[#0F172A]'"
            x-text="count('low')"></span>
        </button>

        <!-- Aman -->
        <button type="button" @click="filter = 'ok'"
          :class="filter === 'ok' ? 'border-[#0F172A] bg-[#0F172A] text-white' : 'border-[#CBD5E1] bg-white text-[#0F172A]'"
          style="display: inline-flex; align-items: center; gap: 6px; min-height: 29px; padding: 0 10px; border: 1px solid #CBD5E1; border-radius: 9999px; font-family: inherit; font-size: 10px; font-weight: 700; cursor: pointer; transition: all 0.15s ease;">
          <span style="width: 8px; height: 8px; border-radius: 9999px; background: #2563EB;"></span>
          Aman
          <span style="min-width: 18px; height: 18px; padding: 0 4px; box-sizing: border-box; border-radius: 9999px; font-size: 9px; font-weight: 800; display: inline-flex; align-items: center; justify-content: center;"
            :class="filter === 'ok' ? 'bg-white text-[#0F172A]' : 'bg-[#EDF0F5] text-[#0F172A]'"
            x-text="count('ok')"></span>
        </button>
      </div>

      <!-- Row 2: Search Box & Category Dropdown (Smooth Rounded Corners, Not Lancip) -->
      <div style="display: flex; flex-wrap: wrap; gap: 8px; align-items: center">
        <!-- Search Input -->
        <label style="display: inline-flex; align-items: center; gap: 7px; height: 32px; padding: 0 10px; border: 1px solid #CBD5E1; border-radius: 9px; background: #FFFFFF; width: 164px; max-width: 100%; box-sizing: border-box;">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="7"></circle><path d="M20 20l-3.5-3.5"></path></svg>
          <input type="search" x-model="search" placeholder="Cari nama atau barcode" style="border: 0; outline: none; font-family: inherit; font-size: 9px; color: #0F172A; width: 100%; background: transparent;">
        </label>
        
        <!-- Category Dropdown Select -->
        <div style="position: relative; display: inline-flex; align-items: center; height: 32px; padding: 0 10px; border: 1px solid #CBD5E1; border-radius: 9px; background: #FFFFFF; cursor: pointer; box-sizing: border-box;">
          <span style="font-size: 9px; color: #64748B; margin-right: 6px;">Kategori</span>
          <span style="font-size: 9px; font-weight: 700; color: #0F172A; margin-right: 8px;" x-text="categoryFilter === 'all' ? 'Semua' : (document.querySelector('#catFilterSelect option[value=\'' + categoryFilter + '\']')?.text || 'Semua')">Semua</span>
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#0F172A" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9l6 6 6-6"></path></svg>
          <select id="catFilterSelect" x-model="categoryFilter" style="position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
            <option value="all">Semua</option>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}">{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
      </div>

    </div>

    <!-- Table -->
    <div style="overflow-x: auto">
      <table class="products-table" style="width: 100%; min-width: 700px; border-collapse: collapse; font-size: 10px">
        <thead>
          <tr style="text-align: left; font-size: 8px; letter-spacing: 0.06em; text-transform: uppercase; color: #6B7280; background: #F8FAFD;">
            <th scope="col" style="padding: 9px 12px; font-weight: 700">Barang</th>
            <th scope="col" style="padding: 9px 8px; font-weight: 700">Harga jual</th>
            <th scope="col" style="padding: 9px 8px; font-weight: 700; width: 135px">Stok</th>
            <th scope="col" style="padding: 9px 8px; font-weight: 700">Status</th>
            <th scope="col" style="padding: 9px 8px; font-weight: 700">Harus ditambah</th>
            <th scope="col" style="padding: 9px 12px; font-weight: 700; text-align: right">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="p in filteredProducts" :key="p.id">
            <tr :style="'border-top: 1px solid #EDF0F5; background: ' + p.row_bg">
              <!-- Column 1: Barang -->
              <td style="padding: 9px 12px">
                <div style="display: flex; flex-direction: column; gap: 3px">
                  <span style="font-size: 10px; font-weight: 700; color: #111827" x-text="p.name"></span>
                  <span style="font-size: 8px; color: #5B6475" x-text="p.category_name + ' · ' + p.satuan + (p.barcode ? ' · ' + p.barcode : '')"></span>
                </div>
              </td>

              <!-- Column 2: Harga -->
              <td style="padding: 9px 8px">
                <div style="display: flex; flex-direction: column; gap: 3px">
                  <span style="font-size: 10px; font-weight: 700; color: #111827" x-text="formatRp(p.selling_price)"></span>
                  <span style="font-size: 8px; color: #5B6475" x-text="'beli ' + formatRp(p.purchase_price)"></span>
                </div>
              </td>

              <!-- Column 3: Stok -->
              <td style="padding: 9px 8px">
                <div style="display: flex; flex-direction: column; gap: 5px">
                  <span style="font-size: 9px; color: #374151">
                    <strong style="font-size: 11px; color: #111827" x-text="p.current_stock"></strong> / min <span x-text="p.min_stock"></span>
                  </span>
                  <div style="position: relative; height: 5px; border-radius: 999px; background: #EDF0F5">
                    <div :style="'position: absolute; left: 0; top: 0; bottom: 0; border-radius: 999px; width: ' + p.pct + '%; background: ' + p.dot"></div>
                    <div style="position: absolute; left: 50%; top: -3px; bottom: -3px; width: 2px; background: #111827" title="Batas minimum"></div>
                  </div>
                </div>
              </td>

              <!-- Column 4: Status -->
              <td style="padding: 9px 8px">
                <span :style="'display: inline-flex; align-items: center; gap: 5px; padding: 4px 7px; border-radius: 999px; font-size: 9px; font-weight: 700; background: ' + p.badge_bg + '; color: ' + p.badge_fg">
                  <span :style="'display: inline-block; width: 6px; height: 6px; border-radius: 999px; background: ' + p.dot"></span>
                  <span x-text="p.status"></span>
                </span>
              </td>

              <!-- Column 5: Harus ditambah -->
              <td style="padding: 9px 8px">
                <template x-if="p.needs">
                  <div style="display: flex; flex-direction: column; gap: 3px">
                    <span style="font-size: 11px; font-weight: 800; color: #111827" x-text="'+' + p.add + ' ' + p.satuan"></span>
                    <span style="font-size: 8px; color: #5B6475" x-text="'modal ' + formatRp(p.cost)"></span>
                  </div>
                </template>
                <template x-if="!p.needs">
                  <span style="font-size: 9px; color: #5B6475">Belum perlu</span>
                </template>
              </td>

              <!-- Column 6: Aksi -->
              <td style="padding: 9px 12px">
                <div style="display: flex; justify-content: flex-end; align-items: center; gap: 6px">
                  <template x-if="p.needs">
                    <button type="button" @click="openRestock(p)" style="display: inline-flex; align-items: center; gap: 4px; min-height: 27px; padding: 0 8px; border-radius: 7px; border: 0; background: #111827; color: #FFFFFF; font-family: inherit; font-size: 8px; font-weight: 700; cursor: pointer;">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
                      Isi stok
                    </button>
                  </template>
                  <button type="button" @click="openEdit(p)" :aria-label="'Ubah ' + p.name" style="width: 27px; height: 27px; border-radius: 7px; border: 1px solid #E4E8F0; background: #FFFFFF; color: #374151; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;" title="Ubah barang">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16z"></path><path d="M14 6l4 4"></path></svg>
                  </button>
                  <button type="button" @click="openDelete(p)" :aria-label="'Hapus ' + p.name" style="width: 27px; height: 27px; border-radius: 7px; border: 1px solid #E4E8F0; background: #FFFFFF; color: #B42318; display: inline-flex; align-items: center; justify-content: center; cursor: pointer;" title="Hapus barang">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16"></path><path d="M10 11v6M14 11v6"></path><path d="M6 7l1 13h10l1-13"></path><path d="M9 7V4h6v3"></path></svg>
                  </button>
                </div>
              </td>
            </tr>
          </template>

          <template x-if="filteredProducts.length === 0">
            <tr>
              <td colspan="6" style="padding: 40px 20px; text-align: center; color: #5B6475; font-size: 14px">
                Tidak ada barang yang sesuai dengan pencarian atau filter.
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>

    <!-- Table Footer Summary -->
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; gap: 8px; padding: 9px 12px; border-top: 1px solid #EDF0F5; background: #F8FAFD; font-size: 8px; color: #5B6475">
      <span>Menampilkan <strong x-text="filteredProducts.length"></strong> dari {{ $products->count() }} barang · diurutkan dari yang terbaru</span>
      <span>Harus ditambah = isi sampai 2× batas minimum · garis hitam = batas minimum</span>
    </div>
  </section>

  <!-- MODAL: Tambah Barang -->
  <div x-show="createModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div x-show="createModal" @click="createModal = false" class="fixed inset-0 transition-opacity bg-black/50"></div>
      
      <div x-show="createModal" class="relative inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-[#E4E8F0]">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#EDF0F5]">
          <h3 class="text-xl font-bold text-[#111827]">Tambah Barang Baru</h3>
          <button type="button" @click="createModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
        </div>

        <form action="{{ route('products.store') }}" method="POST" class="space-y-4">
          @csrf
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-sm font-bold text-[#111827] mb-1">Nama Barang *</label>
              <input type="text" name="name" required placeholder="Contoh: Aqua 600ml" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Kategori *</label>
              <select name="category_id" required class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                <option value="">Pilih Kategori</option>
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Satuan *</label>
              <input type="text" name="satuan" required value="pcs" placeholder="pcs / botol / renceng" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div class="sm:col-span-2">
              <label class="block text-sm font-bold text-[#111827] mb-1">Barcode / Kode (Opsional)</label>
              <input type="text" name="barcode" placeholder="Scan atau ketik barcode produk" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Harga Beli (Rp) *</label>
              <input type="number" name="purchase_price" required min="0" placeholder="2500" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Harga Jual (Rp) *</label>
              <input type="number" name="selling_price" required min="0" placeholder="3500" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Stok Awal *</label>
              <input type="number" name="current_stock" required min="0" value="0" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Batas Minimum Stok *</label>
              <input type="number" name="min_stock" required min="0" value="5" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-[#EDF0F5]">
            <button type="button" @click="createModal = false" class="px-5 py-2.5 rounded-lg border border-[#CDD4E0] text-sm font-bold text-[#5B6475] hover:bg-gray-50">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#2563EB] text-sm font-bold text-white hover:bg-[#1D4ED8]">Simpan Barang</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: Ubah Barang -->
  <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div x-show="editModal" @click="editModal = false" class="fixed inset-0 transition-opacity bg-black/50"></div>
      
      <div x-show="editModal" class="relative inline-block w-full max-w-xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-[#E4E8F0]">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#EDF0F5]">
          <h3 class="text-xl font-bold text-[#111827]">Ubah Data Barang</h3>
          <button type="button" @click="editModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
        </div>

        <form :action="'{{ url('products') }}/' + activeProduct.id" method="POST" class="space-y-4">
          @csrf
          @method('PUT')
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="sm:col-span-2">
              <label class="block text-sm font-bold text-[#111827] mb-1">Nama Barang *</label>
              <input type="text" name="name" x-model="activeProduct.name" required class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Kategori *</label>
              <select name="category_id" x-model="activeProduct.category_id" required class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
                @foreach($categories as $cat)
                  <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Satuan *</label>
              <input type="text" name="satuan" x-model="activeProduct.satuan" required class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div class="sm:col-span-2">
              <label class="block text-sm font-bold text-[#111827] mb-1">Barcode / Kode</label>
              <input type="text" name="barcode" x-model="activeProduct.barcode" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Harga Beli (Rp) *</label>
              <input type="number" name="purchase_price" x-model="activeProduct.purchase_price" required min="0" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Harga Jual (Rp) *</label>
              <input type="number" name="selling_price" x-model="activeProduct.selling_price" required min="0" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Stok Saat Ini *</label>
              <input type="number" name="current_stock" x-model="activeProduct.current_stock" required min="0" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>

            <div>
              <label class="block text-sm font-bold text-[#111827] mb-1">Batas Minimum Stok *</label>
              <input type="number" name="min_stock" x-model="activeProduct.min_stock" required min="0" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-[#EDF0F5]">
            <button type="button" @click="editModal = false" class="px-5 py-2.5 rounded-lg border border-[#CDD4E0] text-sm font-bold text-[#5B6475] hover:bg-gray-50">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#2563EB] text-sm font-bold text-white hover:bg-[#1D4ED8]">Perbarui Barang</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: Isi Stok Cepat -->
  <div x-show="restockModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div x-show="restockModal" @click="restockModal = false" class="fixed inset-0 transition-opacity bg-black/50"></div>
      
      <div x-show="restockModal" class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-[#E4E8F0]">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#EDF0F5]">
          <h3 class="text-xl font-bold text-[#111827]">Isi Ulang Stok</h3>
          <button type="button" @click="restockModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
        </div>

        <form :action="'{{ url('products') }}/' + activeProduct.id + '/restock'" method="POST" class="space-y-4">
          @csrf
          <div style="background: #F8FAFD; border: 1px solid #E4E8F0; border-radius: 12px; padding: 14px;">
            <div class="text-base font-bold text-[#111827]" x-text="activeProduct.name"></div>
            <div class="text-sm text-[#5B6475] mt-1">
              Stok saat ini: <strong class="text-[#111827]" x-text="activeProduct.current_stock + ' ' + activeProduct.satuan"></strong> · Batas minimum: <span x-text="activeProduct.min_stock"></span>
            </div>
          </div>

          <div>
            <label class="block text-sm font-bold text-[#111827] mb-1">Jumlah yang ditambahkan</label>
            <div class="flex items-center gap-2">
              <input type="number" name="add_qty" x-model="restockQty" required min="1" class="w-full h-12 px-3 border-2 border-[#111827] rounded-xl text-lg font-extrabold text-[#111827] focus:outline-none">
              <span class="text-sm font-bold text-[#5B6475]" x-text="activeProduct.satuan"></span>
            </div>
            <p class="text-xs text-[#5B6475] mt-1.5">
              Perkiraan modal: <strong class="text-[#111827]" x-text="formatRp(restockQty * (activeProduct.purchase_price || 0))"></strong>
            </p>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-[#EDF0F5]">
            <button type="button" @click="restockModal = false" class="px-5 py-2.5 rounded-lg border border-[#CDD4E0] text-sm font-bold text-[#5B6475] hover:bg-gray-50">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#111827] text-sm font-bold text-white hover:bg-black">Konfirmasi Tambah Stok</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: Hapus Barang -->
  <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div x-show="deleteModal" @click="deleteModal = false" class="fixed inset-0 transition-opacity bg-black/50"></div>
      
      <div x-show="deleteModal" class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-[#F1C4BE]">
        <div class="flex items-center gap-3 mb-4 text-[#B42318]">
          <div class="w-12 h-12 rounded-xl bg-[#FDECEA] flex items-center justify-center flex-shrink-0">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-[#7A1A11]">Hapus Barang?</h3>
            <p class="text-sm text-[#7A2E25]">Tindakan ini tidak dapat dibatalkan.</p>
          </div>
        </div>

        <p class="text-sm text-[#374151] mb-6">
          Apakah Anda yakin ingin menghapus barang <strong class="text-[#111827]" x-text="activeProduct.name"></strong>?
        </p>

        <form :action="'{{ url('products') }}/' + activeProduct.id" method="POST" class="flex justify-end gap-3">
          @csrf
          @method('DELETE')
          <button type="button" @click="deleteModal = false" class="px-5 py-2.5 rounded-lg border border-[#CDD4E0] text-sm font-bold text-[#5B6475] hover:bg-gray-50">Batal</button>
          <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#B42318] text-sm font-bold text-white hover:bg-[#991B1B]">Ya, Hapus Barang</button>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection
