<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\StockMutation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->get();
        $categories = Category::orderBy('name')->get();

        $totalStockValue = $products->sum(fn ($p) => (float) $p->current_stock * (float) $p->purchase_price);

        $restockCount = 0;
        $totalPcsToBuy = 0;
        $estimatedModal = 0;

        foreach ($products as $p) {
            $isLow = $p->current_stock <= $p->min_stock;
            if ($isLow) {
                $target = max(1, $p->min_stock * 2);
                $add = max(0, $target - $p->current_stock);
                $restockCount++;
                $totalPcsToBuy += $add;
                $estimatedModal += $add * (float) $p->purchase_price;
            }
        }

        return view('products.index', compact(
            'products',
            'categories',
            'totalStockValue',
            'restockCount',
            'totalPcsToBuy',
            'estimatedModal'
        ));
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $product = Product::create($validated);

        if ($product->current_stock > 0) {
            StockMutation::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'in_supplier',
                'qty' => $product->current_stock,
                'balance' => $product->current_stock,
                'description' => 'Stok awal produk baru',
            ]);
        }

        return back()->with('success', 'Barang '.$product->name.' berhasil ditambahkan.');
    }

    public function update(Request $request, Product $product)
    {
        $oldStock = $product->current_stock;
        $validated = $this->validated($request);
        $product->update($validated);

        if ($oldStock != $product->current_stock) {
            $diff = $product->current_stock - $oldStock;
            StockMutation::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => $diff > 0 ? 'in_supplier' : 'out_damage',
                'qty' => abs($diff),
                'balance' => $product->current_stock,
                'description' => 'Penyesuaian stok produk (dari '.$oldStock.' ke '.$product->current_stock.')',
            ]);
        }

        return back()->with('success', 'Barang '.$product->name.' berhasil diperbarui.');
    }

    public function restock(Request $request, Product $product)
    {
        $request->validate(['add_qty' => ['required', 'integer', 'min:1']]);
        $qty = (int) $request->add_qty;
        $product->current_stock += $qty;
        $product->save();

        StockMutation::create([
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => 'in_supplier',
            'qty' => $qty,
            'balance' => $product->current_stock,
            'description' => 'Isi ulang stok (+'.$qty.' '.$product->satuan.')',
        ]);

        return back()->with('success', 'Stok '.$product->name.' berhasil ditambah '.$qty.' '.$product->satuan.'.');
    }

    public function destroy(Product $product)
    {
        $name = $product->name;
        $product->delete();
        return back()->with('success', 'Barang '.$name.' berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode,'.$request->route('product')?->id],
            'satuan' => ['required', 'string', 'max:30'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'current_stock' => ['required', 'integer', 'min:0'],
            'min_stock' => ['required', 'integer', 'min:0'],
        ]);
    }
}
