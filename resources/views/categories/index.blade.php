@extends('layouts.app')

@section('title', 'Kategori')

@section('content')
<div x-data="{
  createModal: false,
  editModal: false,
  deleteModal: false,
  activeCategory: {},

  openEdit(c) {
    this.activeCategory = { ...c };
    this.editModal = true;
  },

  openDelete(c) {
    this.activeCategory = { ...c };
    this.deleteModal = true;
  }
}" style="max-width: 1180px; margin: 0 auto; display: flex; flex-direction: column; gap: 22px">

  <!-- Header -->
  <header style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px">
    <div style="display: flex; flex-direction: column; gap: 6px">
      <h1 style="margin: 0; font-size: 32px; font-weight: 800; letter-spacing: -0.02em; color: #111827">Kategori</h1>
      <p style="margin: 0; font-size: 15px; color: #5B6475">
        {{ $categories->count() }} kategori untuk mengelompokkan {{ $totalProductsCount }} barang
      </p>
    </div>
    <button type="button" @click="createModal = true" style="display: inline-flex; align-items: center; gap: 8px; min-height: 48px; padding: 0 20px; border-radius: 12px; border: 0; background: #2563EB; color: #FFFFFF; font-family: inherit; font-size: 15px; font-weight: 700; cursor: pointer; transition: background 0.15s ease;" onmouseover="this.style.background='#1D4ED8'" onmouseout="this.style.background='#2563EB'">
      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"></path></svg>
      Tambah Kategori
    </button>
  </header>

  <!-- Categories Grid -->
  @php
    $palette = [
      ['tint' => '#E8EFFE', 'ink' => '#1D4ED8'],
      ['tint' => '#E0F2FE', 'ink' => '#075985'],
      ['tint' => '#EEF2FF', 'ink' => '#3730A3'],
      ['tint' => '#E8E1F7', 'ink' => '#4B3390'],
      ['tint' => '#FDF1DC', 'ink' => '#8A5300'],
      ['tint' => '#ECFDF5', 'ink' => '#065F46'],
    ];
  @endphp

  <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 16px">
    @forelse($categories as $idx => $c)
      @php
        $color = $palette[$idx % count($palette)];
        $needsCount = (int) $c->low_stock_count;
        $categoryJson = [
          'id' => $c->id,
          'name' => $c->name,
          'description' => $c->description ?? '',
          'products_count' => $c->products_count,
        ];
      @endphp
      <article style="background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; padding: 22px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.02);">
        <!-- Card Top: Avatar & Action Buttons -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 12px">
          <span style="width: 48px; height: 48px; border-radius: 14px; background: {{ $color['tint'] }}; color: {{ $color['ink'] }}; display: inline-flex; align-items: center; justify-content: center; font-size: 20px; font-weight: 800">
            {{ strtoupper(substr($c->name, 0, 1)) }}
          </span>
          <div style="display: flex; gap: 4px">
            <button type="button" @click="openEdit({{ json_encode($categoryJson) }})" :aria-label="'Ubah kategori ' + '{{ $c->name }}'" style="width: 40px; height: 40px; border-radius: 10px; border: 1px solid #E4E8F0; background: #FFFFFF; color: #374151; display: inline-flex; align-items: center; justify-content: center; cursor: pointer" title="Ubah kategori">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20h4L19 9l-4-4L4 16z"></path><path d="M14 6l4 4"></path></svg>
            </button>
            <button type="button" @click="openDelete({{ json_encode($categoryJson) }})" :aria-label="'Hapus kategori ' + '{{ $c->name }}'" style="width: 40px; height: 40px; border-radius: 10px; border: 1px solid #E4E8F0; background: #FFFFFF; color: #B42318; display: inline-flex; align-items: center; justify-content: center; cursor: pointer" title="Hapus kategori">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16"></path><path d="M10 11v6M14 11v6"></path><path d="M6 7l1 13h10l1-13"></path><path d="M9 7V4h6v3"></path></svg>
            </button>
          </div>
        </div>

        <!-- Name & Desc -->
        <div style="display: flex; flex-direction: column; gap: 6px; flex: 1">
          <h2 style="margin: 0; font-size: 20px; font-weight: 800; color: #111827">{{ $c->name }}</h2>
          <p style="margin: 0; font-size: 14px; line-height: 1.5; color: #5B6475">
            {{ $c->description ?: 'Tidak ada keterangan untuk kategori ini.' }}
          </p>
        </div>

        <!-- Footer: Product count & Status Flag -->
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 10px; padding-top: 14px; border-top: 1px solid #EDF0F5">
          <a href="{{ route('products.index') }}" style="font-size: 14px; font-weight: 700; text-decoration: none; color: #2563EB">
            {{ $c->products_count }} barang
          </a>
          @if($needsCount > 0)
            <span style="padding: 5px 10px; border-radius: 999px; background: #FDECEA; color: #B42318; font-size: 13px; font-weight: 700">
              {{ $needsCount }} perlu diisi
            </span>
          @else
            <span style="padding: 5px 10px; border-radius: 999px; background: #E8EFFE; color: #1D4ED8; font-size: 13px; font-weight: 700">
              Stok aman
            </span>
          @endif
        </div>
      </article>
    @empty
      <div style="grid-column: 1 / -1; padding: 48px; background: #FFFFFF; border: 1px solid #E4E8F0; border-radius: 18px; text-align: center; color: #5B6475;">
        Belum ada kategori yang dibuat. Klik tombol "Tambah Kategori" di atas untuk menambahkan.
      </div>
    @endforelse
  </div>

  <!-- MODAL: Tambah Kategori -->
  <div x-show="createModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div x-show="createModal" @click="createModal = false" class="fixed inset-0 transition-opacity bg-black/50"></div>
      
      <div x-show="createModal" class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-[#E4E8F0]">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#EDF0F5]">
          <h3 class="text-xl font-bold text-[#111827]">Tambah Kategori</h3>
          <button type="button" @click="createModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
        </div>

        <form action="{{ route('categories.store') }}" method="POST" class="space-y-4">
          @csrf
          <div>
            <label class="block text-sm font-bold text-[#111827] mb-1">Nama Kategori *</label>
            <input type="text" name="name" required placeholder="Contoh: Sembako" class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
          </div>

          <div>
            <label class="block text-sm font-bold text-[#111827] mb-1">Keterangan (Opsional)</label>
            <textarea name="description" rows="3" placeholder="Contoh: Beras, minyak, mi instan, dan kebutuhan pokok..." class="w-full p-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-[#EDF0F5]">
            <button type="button" @click="createModal = false" class="px-5 py-2.5 rounded-lg border border-[#CDD4E0] text-sm font-bold text-[#5B6475] hover:bg-gray-50">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#2563EB] text-sm font-bold text-white hover:bg-[#1D4ED8]">Simpan Kategori</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: Ubah Kategori -->
  <div x-show="editModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div x-show="editModal" @click="editModal = false" class="fixed inset-0 transition-opacity bg-black/50"></div>
      
      <div x-show="editModal" class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-[#E4E8F0]">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-[#EDF0F5]">
          <h3 class="text-xl font-bold text-[#111827]">Ubah Kategori</h3>
          <button type="button" @click="editModal = false" class="text-gray-400 hover:text-gray-600 font-bold text-2xl">&times;</button>
        </div>

        <form :action="'{{ url('categories') }}/' + activeCategory.id" method="POST" class="space-y-4">
          @csrf
          @method('PUT')
          <div>
            <label class="block text-sm font-bold text-[#111827] mb-1">Nama Kategori *</label>
            <input type="text" name="name" x-model="activeCategory.name" required class="w-full h-11 px-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]">
          </div>

          <div>
            <label class="block text-sm font-bold text-[#111827] mb-1">Keterangan (Opsional)</label>
            <textarea name="description" x-model="activeCategory.description" rows="3" class="w-full p-3 border border-[#CDD4E0] rounded-lg text-sm text-[#111827] focus:outline-none focus:ring-2 focus:ring-[#2563EB]"></textarea>
          </div>

          <div class="flex justify-end gap-3 pt-4 border-t border-[#EDF0F5]">
            <button type="button" @click="editModal = false" class="px-5 py-2.5 rounded-lg border border-[#CDD4E0] text-sm font-bold text-[#5B6475] hover:bg-gray-50">Batal</button>
            <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#2563EB] text-sm font-bold text-white hover:bg-[#1D4ED8]">Perbarui Kategori</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- MODAL: Hapus Kategori -->
  <div x-show="deleteModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
      <div x-show="deleteModal" @click="deleteModal = false" class="fixed inset-0 transition-opacity bg-black/50"></div>
      
      <div x-show="deleteModal" class="relative inline-block w-full max-w-md p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-2xl border border-[#F1C4BE]">
        <div class="flex items-center gap-3 mb-4 text-[#B42318]">
          <div class="w-12 h-12 rounded-xl bg-[#FDECEA] flex items-center justify-center flex-shrink-0">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
          </div>
          <div>
            <h3 class="text-lg font-bold text-[#7A1A11]">Hapus Kategori?</h3>
            <p class="text-sm text-[#7A2E25]">Kategori yang masih memiliki barang tidak dapat dihapus.</p>
          </div>
        </div>

        <p class="text-sm text-[#374151] mb-6">
          Apakah Anda yakin ingin menghapus kategori <strong class="text-[#111827]" x-text="activeCategory.name"></strong>?
        </p>

        <form :action="'{{ url('categories') }}/' + activeCategory.id" method="POST" class="flex justify-end gap-3">
          @csrf
          @method('DELETE')
          <button type="button" @click="deleteModal = false" class="px-5 py-2.5 rounded-lg border border-[#CDD4E0] text-sm font-bold text-[#5B6475] hover:bg-gray-50">Batal</button>
          <button type="submit" class="px-5 py-2.5 rounded-lg bg-[#B42318] text-sm font-bold text-white hover:bg-[#991B1B]">Ya, Hapus</button>
        </form>
      </div>
    </div>
  </div>

</div>
@endsection
