@extends('layouts.app')
@section('content')
<div x-data="posData()" class="flex h-full gap-6">
    <!-- Left: Product List -->
    <div class="flex-1 flex flex-col h-full bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-4 border-b border-gray-200">
            <h1 class="text-2xl font-bold text-gray-800 mb-3">Buka Kasir</h1>
            <input type="text" x-model="searchQuery" placeholder="Cari barang atau scan barcode..." class="w-full pl-4 pr-10 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 text-lg">
        </div>
        <div class="flex-1 overflow-y-auto p-4">
            <template x-for="group in groupedProducts" :key="group.name">
                <section class="mb-6">
                    <h2 class="text-lg font-bold text-gray-700 border-b pb-2 mb-3" x-text="group.name"></h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <template x-for="product in group.products" :key="product.id">
                    <div @click="addToCart(product)" class="border border-gray-200 rounded-lg p-4 cursor-pointer hover:border-blue-500 hover:shadow-md transition bg-white flex flex-col justify-between">
                        <div class="text-sm text-gray-500 mb-1" x-text="product.barcode"></div>
                        <div class="font-semibold text-gray-800 mb-2 leading-tight h-10 overflow-hidden" x-text="product.name"></div>
                        <div class="flex justify-between items-end mt-2">
                            <span class="text-blue-600 font-bold text-lg" x-text="formatRupiah(product.selling_price)"></span>
                            <span class="text-xs text-gray-500" x-text="'Stok: ' + product.current_stock"></span>
                        </div>
                    </div>
                </template>
                    </div>
                </section>
            </template>
        </div>
    </div>

    <!-- Right: Cart -->
    <div class="w-96 bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full">
        <div class="p-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-bold text-gray-800">Keranjang Belanja</h2>
        </div>
        
        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            <template x-for="(item, index) in cart" :key="item.id">
                <div class="flex justify-between items-center border-b pb-2">
                    <div class="flex-1">
                        <h4 class="font-semibold text-gray-800 text-sm" x-text="item.name"></h4>
                        <div class="text-blue-600 text-sm" x-text="formatRupiah(item.price)"></div>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="decreaseQty(index)" class="w-7 h-7 rounded bg-gray-200 text-gray-700 flex items-center justify-center font-bold hover:bg-gray-300">-</button>
                        <span class="w-6 text-center font-medium" x-text="item.qty"></span>
                        <button @click="increaseQty(index)" class="w-7 h-7 rounded bg-gray-200 text-gray-700 flex items-center justify-center font-bold hover:bg-gray-300">+</button>
                    </div>
                </div>
            </template>
            <div x-show="cart.length === 0" class="text-center text-gray-500 mt-10">Keranjang masih kosong</div>
        </div>

        <div class="p-4 border-t border-gray-200 bg-gray-50">
            <div class="flex justify-between items-center mb-4">
                <span class="font-semibold text-gray-600">Total</span>
                <span class="text-2xl font-bold text-gray-900" x-text="formatRupiah(cartTotal)"></span>
            </div>
            
            <form action="{{ route('pos.checkout') }}" method="POST">
                @csrf
                <input type="hidden" name="cart" :value="JSON.stringify(cart)">
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Metode Bayar</label>
                    <select x-model="paymentMethod" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="Tunai">Tunai</option>
                        <option value="QRIS">QRIS</option>
                        <option value="Transfer">Transfer</option>
                    </select>
                </div>
                
                <div class="mb-4">
                    <label class="block text-xs font-medium text-gray-500 uppercase mb-1">Uang Diterima (Rp)</label>
                    <input type="number" name="cash_received" x-model.number="cashReceived" :required="paymentMethod === 'Tunai'" class="w-full border-gray-300 rounded-md shadow-sm p-2 text-lg font-bold" placeholder="0">
                    <input type="hidden" name="payment_method" :value="paymentMethod">
                    <div class="mt-2 text-sm">
                        <span class="text-gray-500">Uang kembalian yang diberikan:</span>
                        <input type="number" name="change_given" x-model.number="changeGiven" :value="change" min="0" class="w-40 border rounded p-1 text-right font-bold text-green-600">
                    </div>
                </div>

                <button type="submit" :disabled="cart.length === 0" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 shadow flex items-center justify-center space-x-2 disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-check-circle"></i>
                    <span>Selesaikan Transaksi</span>
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function posData() {
    return {
        searchQuery: '',
        products: @json($products),
        cart: [],
        cashReceived: '',
        changeGiven: 0,
        paymentMethod: 'Tunai',
        
        get filteredProducts() {
            if (this.searchQuery === '') return this.products;
            const lowerCaseQuery = this.searchQuery.toLowerCase();
            return this.products.filter(p =>
                p.name.toLowerCase().includes(lowerCaseQuery) || 
                (p.barcode && p.barcode.toLowerCase().includes(lowerCaseQuery))
            );
        },

        get groupedProducts() {
            const groups = {};
            this.filteredProducts.forEach(product => {
                const category = product.category?.name || 'Tanpa Kategori';
                if (!groups[category]) groups[category] = [];
                groups[category].push(product);
            });
            return Object.entries(groups).map(([name, products]) => ({name, products}));
        },
        
        addToCart(product) {
            const existing = this.cart.find(i => i.id === product.id);
            if (existing) {
                if (existing.qty < product.current_stock) existing.qty++;
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    price: product.selling_price,
                    qty: 1
                });
            }
            this.searchQuery = '';
        },
        
        increaseQty(index) {
            const product = this.products.find(p => p.id === this.cart[index].id);
            if (this.cart[index].qty < product.current_stock) {
                this.cart[index].qty++;
            }
        },
        
        decreaseQty(index) {
            if (this.cart[index].qty > 1) {
                this.cart[index].qty--;
            } else {
                this.cart.splice(index, 1);
            }
        },
        
        get cartTotal() {
            return this.cart.reduce((total, item) => total + (item.price * item.qty), 0);
        },
        
        get change() {
            if (!this.cashReceived || this.cashReceived < this.cartTotal) return 0;
            return this.cashReceived - this.cartTotal;
        },
        
        formatRupiah(amount) {
            return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
        }
    }
}
</script>
@endsection