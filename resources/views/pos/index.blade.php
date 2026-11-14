@extends('layouts.app')

@section('title', 'Kasir (POS)')

@push('styles')
<style>
    .pos-category-pill {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-height: 42px !important;
        padding: 0 20px !important;
        border-radius: 9999px !important;
        border: 1px solid #CBD5E1 !important;
        background: #FFFFFF !important;
        color: #0F172A !important;
        font-family: inherit !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        box-sizing: border-box !important;
        outline: none !important;
    }
    .pos-category-pill:hover {
        border-color: #94A3B8 !important;
    }
    .pos-category-pill.active {
        border-color: #0F172A !important;
        background: #0F172A !important;
        color: #FFFFFF !important;
    }
    .pos-quick-cash-pill {
        min-height: 44px !important;
        padding: 0 10px !important;
        border-radius: 20px !important;
        border: 1.5px solid #CBD5E1 !important;
        background: #FFFFFF !important;
        color: #0F172A !important;
        font-family: inherit !important;
        font-size: 14px !important;
        font-weight: 700 !important;
        cursor: pointer !important;
        transition: all 0.15s ease !important;
        text-align: center !important;
        box-sizing: border-box !important;
        outline: none !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .pos-quick-cash-pill:hover {
        background: #F8FAFC !important;
        border-color: #94A3B8 !important;
    }
    .pos-product-grid {
        display: grid !important;
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 14px !important;
    }
    .pos-product-card {
        display: flex !important;
        flex-direction: column !important;
        justify-content: space-between !important;
        gap: 12px !important;
        text-align: left !important;
        padding: 18px !important;
        border-radius: 20px !important;
        background: #FFFFFF !important;
        font-family: inherit !important;
        color: #0F172A !important;
        min-height: 156px !important;
        transition: all 0.15s ease !important;
        border: 1px solid #E2E8F0 !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important;
        cursor: pointer !important;
        box-sizing: border-box !important;
    }
    .pos-product-card.in-cart {
        border: 2px solid #2563EB !important;
        box-shadow: 0 0 0 1px #2563EB !important;
    }
    .pos-product-card.out-of-stock {
        opacity: 0.5 !important;
        cursor: not-allowed !important;
    }
    .corner-20 {
        border-radius: 20px !important;
    }
    @media (max-width: 860px) {
        .pos-page {
            grid-template-columns: minmax(0, 1fr) !important;
        }
        .pos-product-grid {
            grid-template-columns: repeat(auto-fill, minmax(170px, 1fr)) !important;
        }
        .pos-page > section[aria-label="Keranjang belanja"] {
            grid-column: 1 !important;
            position: static !important;
        }
    }
</style>
@endpush

@section('content')
@php
  $productsJson = $products->map(function($p) {
      $isLow = $p->current_stock <= $p->min_stock;
      return [
          'id' => $p->id,
          'name' => $p->name,
          'category_id' => $p->category_id,
          'category_name' => $p->category?->name ?? 'Umum',
          'barcode' => $p->barcode ?? '',
          'price' => (float) $p->selling_price,
          'stock' => (int) $p->current_stock,
          'min_stock' => (int) $p->min_stock,
          'is_low' => $isLow,
          'satuan' => $p->satuan ?? 'pcs',
      ];
  });
@endphp

<div x-data="{
  search: '',
  selectedCategory: 'all',
  products: {{ json_encode($productsJson) }},
  cart: {}, // { id: qty }
  quantityDraft: {},
  method: 'Tunai',
  cashReceived: '',

  init() {
    // F2 shortcut to focus search
    window.addEventListener('keydown', (e) => {
      if (e.key === 'F2') {
        e.preventDefault();
        this.$refs.searchInput.focus();
        this.$refs.searchInput.select();
      }
    });
  },

  get filteredProducts() {
    return this.products.filter(p => {
      if (this.selectedCategory !== 'all' && String(p.category_id) !== String(this.selectedCategory)) return false;
      if (this.search.trim() !== '') {
        const q = this.search.toLowerCase();
        const matchName = p.name.toLowerCase().includes(q);
        const matchBarcode = p.barcode.toLowerCase().includes(q);
        if (!matchName && !matchBarcode) return false;
      }
      return true;
    });
  },

  addToCart(product) {
    if (product.stock <= 0) return;
    const currentQty = this.cart[product.id] || 0;
    if (currentQty >= product.stock) return;
    this.cart[product.id] = currentQty + 1;
    this.quantityDraft[product.id] = String(this.cart[product.id]);
    this.updateQuickCash();
  },

  increment(productId) {
    const p = this.products.find(x => String(x.id) === String(productId));
    if (!p) return;
    const currentQty = this.cart[productId] || 0;
    if (currentQty < p.stock) {
      this.cart[productId] = currentQty + 1;
      this.quantityDraft[productId] = String(this.cart[productId]);
      this.updateQuickCash();
    }
  },

  setQuantity(productId) {
    const product = this.products.find(x => String(x.id) === String(productId));
    const quantity = Number(this.quantityDraft[productId]);
    if (!product || !Number.isInteger(quantity) || quantity < 1) return;
    this.cart[productId] = Math.min(quantity, product.stock);
    this.quantityDraft[productId] = String(this.cart[productId]);
    this.updateQuickCash();
  },

  normalizeQuantity(productId) {
    const product = this.products.find(x => String(x.id) === String(productId));
    if (!product) return;
    const quantity = Number(this.quantityDraft[productId]);
    if (!Number.isInteger(quantity) || quantity < 1) {
      this.quantityDraft[productId] = String(this.cart[productId] || 1);
      return;
    }
    this.cart[productId] = Math.min(quantity, product.stock);
    this.quantityDraft[productId] = String(this.cart[productId]);
    this.updateQuickCash();
  },

  decrement(productId) {
    const currentQty = this.cart[productId] || 0;
    if (currentQty <= 1) {
      delete this.cart[productId];
      delete this.quantityDraft[productId];
    } else {
      this.cart[productId] = currentQty - 1;
      this.quantityDraft[productId] = String(this.cart[productId]);
    }
    this.updateQuickCash();
  },

  removeFromCart(productId) {
    delete this.cart[productId];
    delete this.quantityDraft[productId];
    this.updateQuickCash();
  },

  get cartLines() {
    return Object.keys(this.cart).map(id => {
      const p = this.products.find(x => x.id == id);
      const qty = this.cart[id];
      const subtotal = (p ? p.price : 0) * qty;
      return {
        id: id,
        name: p ? p.name : 'Barang',
        price: p ? p.price : 0,
        qty: qty,
        subtotal: subtotal,
        maxStock: p ? p.stock : 0
      };
    });
  },

  get totalAmount() {
    return this.cartLines.reduce((sum, item) => sum + item.subtotal, 0);
  },

  get totalItemsCount() {
    return this.cartLines.reduce((sum, item) => sum + item.qty, 0);
  },

  get cartPayload() {
    return JSON.stringify(this.cartLines.map(item => ({
      id: item.id,
      qty: item.qty,
      price: item.price
    })));
  },

  get parsedCash() {
    return parseInt(String(this.cashReceived).replace(/\D/g, ''), 10) || 0;
  },

  get changeAmount() {
    return this.parsedCash - this.totalAmount;
  },

  get canCheckout() {
    if (this.cartLines.length === 0) return false;
    if (this.method === 'Tunai') {
      return this.parsedCash >= this.totalAmount;
    }
    return true;
  },

  setQuickPaid(amount) {
    this.cashReceived = Number(amount || 0).toLocaleString('id-ID');
  },

  updateQuickCash() {
    if (this.totalAmount === 0) {
      this.cashReceived = '';
      return;
    }
    if (this.method === 'Tunai' && (this.parsedCash === 0 || this.parsedCash < this.totalAmount)) {
      this.cashReceived = Number(this.totalAmount).toLocaleString('id-ID');
    }
  },

  onCashInput(e) {
    const raw = e.target.value.replace(/\D/g, '');
    if (!raw) {
      this.cashReceived = '';
      return;
    }
    const val = parseInt(raw, 10);
    this.cashReceived = val.toLocaleString('id-ID');
  },

  handleBarcodeScan(e) {
    const query = this.search.trim().toLowerCase();
    if (!query) return;
    const match = this.products.find(p => p.barcode && p.barcode.toLowerCase() === query);
    if (match) {
      this.addToCart(match);
      this.search = '';
    }
  },

  formatRp(n) {
    return 'Rp ' + Number(n || 0).toLocaleString('id-ID');
  }
}" class="pos-page" style="max-width: 1180px; margin: 0 auto; display: grid; grid-template-columns: minmax(0, 1fr) 380px; gap: 24px; align-items: start">

  <!-- Receipt notification pill if previous checkout was made -->
  @if(session('last_transaction_id'))
    <div class="corner-20" style="grid-column: 1 / -1; background: #E8EFFE; border: 1px solid #BFD0F5; padding: 14px 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px">
      <div class="flex items-center gap-3">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#1D4ED8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
        <span style="font-size: 14px; font-weight: 700; color: #1D4ED8;">
          Transaksi #{{ session('last_transaction_id') }} berhasil! Ingin mencetak nota?
        </span>
      </div>
      <a href="{{ route('reports.transaction.receipt', session('last_transaction_id')) }}" target="_blank" class="corner-20" style="display: inline-flex; align-items: center; gap: 6px; padding: 8px 18px; background: #2563EB; color: #FFFFFF; font-size: 13px; font-weight: 700; text-decoration: none">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Cetak Nota
      </a>
    </div>
  @endif

  <!-- Left: Catalog and Product Selection -->
  <section style="grid-column: 1; min-width: 0; display: flex; flex-direction: column; gap: 16px">
    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px">
      <h1 style="margin: 0; font-size: 30px; font-weight: 800; letter-spacing: -0.02em; color: #111827">Kasir</h1>
      <span style="font-size: 14px; color: #5B6475">Klik barang untuk menambah ke keranjang</span>
    </div>

    <!-- Search / Barcode Box (Persegi dengan Corner 20) -->
    <label class="corner-20" style="display: flex; align-items: center; gap: 12px; min-height: 52px; padding: 0 18px; border: 1px solid #CBD5E1; background: #FFFFFF; color: #64748B; box-shadow: 0 1px 2px rgba(0,0,0,0.02);">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
      <input type="search" x-ref="searchInput" x-model="search" @keydown.enter.prevent="handleBarcodeScan" placeholder="Cari barang atau scan barcode…" style="flex: 1; border: 0; outline: none; font-family: inherit; font-size: 15px; color: #0F172A; background: transparent; min-width: 0">
      <span style="font-size: 12px; font-weight: 700; padding: 4px 10px; border-radius: 9999px; background: #F1F5F9; color: #475569">F2</span>
    </label>

    <!-- Category Filter Pills (Lonjong Bulat Capsule) -->
    <div role="group" aria-label="Kategori" style="display: flex; flex-wrap: wrap; gap: 8px">
      <button type="button" @click="selectedCategory = 'all'"
        :class="{ 'active': selectedCategory === 'all' }"
        class="pos-category-pill">
        Semua
      </button>

      @foreach($categories as $cat)
        <button type="button" @click="selectedCategory = '{{ $cat->id }}'"
          :class="{ 'active': selectedCategory === '{{ $cat->id }}' }"
          class="pos-category-pill">
          {{ $cat->name }}
        </button>
      @endforeach
    </div>

    <!-- Product Cards Grid (Persegi dengan Corner 20) -->
    <div class="pos-product-grid" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px">
      <template x-for="p in filteredProducts" :key="p.id">
        <div role="button" tabindex="0" @click="addToCart(p)" @keydown.enter.prevent="addToCart(p)" @keydown.space.prevent="addToCart(p)"
          :aria-label="'Tambah ' + p.name + ' ke keranjang'" :aria-disabled="p.stock <= 0"
          :class="{ 'in-cart': cart[p.id], 'out-of-stock': p.stock <= 0 }"
          class="pos-product-card corner-20">

          <!-- Category & Stock badge -->
          <span style="display: flex; justify-content: space-between; align-items: center; gap: 8px; width: 100%">
            <span style="font-size: 13px; color: #64748B; font-weight: 500" x-text="p.category_name"></span>
            <template x-if="p.stock <= 0">
              <span style="padding: 3px 8px; border-radius: 9999px; background: #FEE2E2; color: #B91C1C; font-size: 12px; font-weight: 700">Habis</span>
            </template>
            <template x-if="p.stock > 0">
              <span style="padding: 3px 8px; border-radius: 9999px; font-size: 12px; font-weight: 700;"
                :class="p.is_low ? 'bg-[#FEE2E2] text-[#B91C1C]' : 'bg-[#F1F5F9] text-[#334155]'"
                x-text="(p.is_low ? 'Sisa ' : 'Stok ') + p.stock"></span>
            </template>
          </span>

          <!-- Product Name -->
          <span style="font-size: 16px; font-weight: 700; line-height: 1.3; flex: 1; color: #0F172A" x-text="p.name"></span>

          <!-- Price & Add Button -->
          <span style="display: flex; justify-content: space-between; align-items: center; width: 100%">
            <span style="font-size: 18px; font-weight: 800; color: #2563EB" x-text="formatRp(p.price)"></span>
            <span style="width: 32px; height: 32px; border-radius: 9px; background: #2563EB; color: #FFFFFF; display: inline-flex; align-items: center; justify-content: center; transition: background 0.15s ease;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
            </span>
          </span>
        </div>
      </template>
    </div>
  </section>

  <!-- Right: Cart & Checkout Form (Persegi dengan Corner 20) -->
  <section aria-label="Keranjang belanja" class="corner-20" style="grid-column: 2; min-width: 0; max-width: 100%; box-sizing: border-box; background: #FFFFFF; border: 1px solid #E4E8F0; display: flex; flex-direction: column; overflow: hidden; position: sticky; top: 20px; box-shadow: 0 4px 16px rgba(0,0,0,0.04);">

    <div style="padding: 20px 22px; border-bottom: 1px solid #EDF0F5; display: flex; justify-content: space-between; align-items: center">
      <h2 style="margin: 0; font-size: 18px; font-weight: 800; color: #111827">Keranjang</h2>
      <span style="font-size: 13px; color: #5B6475; font-weight: 600" x-text="totalItemsCount + ' barang'"></span>
    </div>

    <!-- Cart items list -->
    <div style="padding: 8px 22px; display: flex; flex-direction: column; min-height: 120px; max-height: 320px; overflow-y: auto;">
      <template x-if="cartLines.length === 0">
        <p style="margin: 0; padding: 40px 0; text-align: center; color: #5B6475; font-size: 14px">
          Keranjang masih kosong.<br><span class="text-xs text-gray-400">Pilih barang di sebelah kiri untuk menambahkan.</span>
        </p>
      </template>

      <template x-for="c in cartLines" :key="c.id">
        <div style="display: flex; align-items: center; gap: 12px; padding: 14px 0; border-bottom: 1px solid #EDF0F5">
          <div style="display: flex; flex-direction: column; gap: 3px; flex: 1; min-width: 0">
            <span style="font-size: 15px; font-weight: 700; color: #111827" x-text="c.name"></span>
            <span style="font-size: 13px; color: #5B6475">
              <span x-text="formatRp(c.price)"></span> × <span x-text="c.qty"></span> = <strong style="color: #111827" x-text="formatRp(c.subtotal)"></strong>
            </span>
          </div>

          <div style="display: flex; align-items: center; gap: 4px; background: #F1F5F9; border-radius: 12px; padding: 3px 6px">
            <button type="button" @click="decrement(c.id)" :aria-label="'Kurangi ' + c.name" style="width: 28px; height: 28px; border: 0; border-radius: 8px; background: #FFFFFF; color: #0F172A; font-family: inherit; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.06); display: flex; align-items: center; justify-content: center;">−</button>
            <input type="number" min="1" step="1" :max="c.maxStock" x-model="quantityDraft[c.id]" @input="setQuantity(c.id)" @blur="normalizeQuantity(c.id)" :aria-label="'Jumlah ' + c.name" style="width: 32px; height: 28px; padding: 0; border: 0; background: transparent; text-align: center; font-family: inherit; font-size: 14px; font-weight: 800; color: #0F172A; outline: none;">
            <button type="button" @click="increment(c.id)" :aria-label="'Tambah ' + c.name" style="width: 28px; height: 28px; border: 0; border-radius: 8px; background: #FFFFFF; color: #0F172A; font-family: inherit; font-size: 15px; font-weight: 700; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.06); display: flex; align-items: center; justify-content: center;">+</button>
          </div>
        </div>
      </template>
    </div>

    <!-- Checkout Form Details -->
    <form action="{{ route('pos.checkout') }}" method="POST" style="padding: 22px; background: #FFFFFF; border-top: 1px solid #EDF0F5; display: flex; flex-direction: column; gap: 16px">
      @csrf
      <input type="hidden" name="cart" :value="cartPayload">
      <input type="hidden" name="payment_method" :value="method">
      <input type="hidden" name="cash_received" :value="method === 'Tunai' ? parsedCash : totalAmount">
      <input type="hidden" name="change_given" :value="method === 'Tunai' ? Math.max(0, changeAmount) : 0">

      <div style="display: flex; justify-content: space-between; align-items: baseline">
        <span style="font-size: 15px; font-weight: 600; color: #475569">Total</span>
        <span style="font-size: 28px; font-weight: 800; letter-spacing: -0.02em; color: #0F172A" x-text="formatRp(totalAmount)"></span>
      </div>

      <!-- Payment Method Selector (Persegi dengan Corner 20) -->
      <div style="display: flex; flex-direction: column; gap: 8px">
        <span style="font-size: 13px; font-weight: 700; color: #334155">Metode bayar</span>
        <div role="group" aria-label="Metode bayar" class="corner-20" style="display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px; padding: 4px; background: #E6EBF3">
          <button type="button" @click="method = 'Tunai'; updateQuickCash();"
            :class="method === 'Tunai' ? 'bg-white text-[#0F172A] shadow-sm font-extrabold' : 'bg-transparent text-[#475569] font-semibold'"
            style="min-height: 44px; border: 0; border-radius: 16px; font-family: inherit; font-size: 14px; cursor: pointer; transition: all 0.15s ease;">
            Tunai
          </button>
          <button type="button" @click="method = 'QRIS'"
            :class="method === 'QRIS' ? 'bg-white text-[#0F172A] shadow-sm font-extrabold' : 'bg-transparent text-[#475569] font-semibold'"
            style="min-height: 44px; border: 0; border-radius: 16px; font-family: inherit; font-size: 14px; cursor: pointer; transition: all 0.15s ease;">
            QRIS
          </button>
          <button type="button" @click="method = 'Transfer'"
            :class="method === 'Transfer' ? 'bg-white text-[#0F172A] shadow-sm font-extrabold' : 'bg-transparent text-[#475569] font-semibold'"
            style="min-height: 44px; border: 0; border-radius: 16px; font-family: inherit; font-size: 14px; cursor: pointer; transition: all 0.15s ease;">
            Transfer
          </button>
        </div>
      </div>

      <!-- Cash Received Controls (Persegi dengan Corner 20) -->
      <template x-if="method === 'Tunai'">
        <div style="display: flex; flex-direction: column; gap: 12px">
          <label for="paid" style="font-size: 14px; font-weight: 700; color: #0F172A">Uang diterima</label>
          <div class="corner-20" style="display: flex; align-items: center; gap: 10px; height: 56px; padding: 0 18px; border: 2.5px solid #0F172A; background: #FFFFFF; box-sizing: border-box;">
            <span style="font-size: 20px; font-weight: 700; color: #475569">Rp</span>
            <input id="paid" type="text" inputmode="numeric" x-model="cashReceived" @input="onCashInput" placeholder="0" style="flex: 1; min-width: 0; border: 0; outline: none; font-family: inherit; font-size: 24px; font-weight: 800; color: #0F172A; background: transparent">
          </div>

          <!-- Quick Cash Buttons (Persegi dengan Corner 20) -->
          <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 8px">
            <button type="button" @click="setQuickPaid(totalAmount)" class="pos-quick-cash-pill">
              Uang pas
            </button>
            <template x-if="totalAmount <= 50000">
              <div style="display: contents">
                <button type="button" @click="setQuickPaid(10000)" class="pos-quick-cash-pill">
                  10rb
                </button>
                <button type="button" @click="setQuickPaid(20000)" class="pos-quick-cash-pill">
                  20rb
                </button>
                <button type="button" @click="setQuickPaid(50000)" class="pos-quick-cash-pill">
                  50rb
                </button>
              </div>
            </template>
            <template x-if="totalAmount > 50000">
              <div style="display: contents">
                <button type="button" @click="setQuickPaid(50000)" class="pos-quick-cash-pill">
                  50rb
                </button>
                <button type="button" @click="setQuickPaid(100000)" class="pos-quick-cash-pill">
                  100rb
                </button>
                <button type="button" @click="setQuickPaid(200000)" class="pos-quick-cash-pill">
                  200rb
                </button>
              </div>
            </template>
          </div>

          <!-- Kembalian / Uang Kurang Indicator (Persegi dengan Corner 20) -->
          <div :class="changeAmount < 0 ? 'bg-[#FEE2E2] text-[#B91C1C]' : 'bg-[#E8EFFE] text-[#1D4ED8]'" class="corner-20" style="display: flex; justify-content: space-between; align-items: center; padding: 14px 18px;">
            <span :class="changeAmount < 0 ? 'text-[#B91C1C]' : 'text-[#2563EB]'" style="font-size: 15px; font-weight: 700;" x-text="changeAmount < 0 ? 'Uang kurang' : 'Kembalian'"></span>
            <span style="font-size: 24px; font-weight: 800;" x-text="formatRp(Math.abs(changeAmount))"></span>
          </div>
        </div>
      </template>

      <!-- Submit Checkout Button (Persegi dengan Corner 20) -->
      <button type="submit" :disabled="!canCheckout" :class="!canCheckout ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer'" class="corner-20" style="display: flex; align-items: center; justify-content: center; gap: 8px; min-height: 52px; border: 0; background: #2563EB; color: #FFFFFF; font-family: inherit; font-size: 15px; font-weight: 700; transition: background 0.15s ease; box-shadow: 0 2px 6px rgba(37,99,235,0.25);" onmouseover="this.style.background='#1D4ED8'" onmouseout="this.style.background='#2563EB'">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="20 6 9 17 4 12"></polyline></svg>
        Selesaikan Transaksi
      </button>
    </form>
  </section>

</div>
@endsection
