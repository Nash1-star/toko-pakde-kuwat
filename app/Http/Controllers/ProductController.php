<?php
namespace App\Http\Controllers;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller {
    public function index() { return view('products.index', ['products' => Product::with('category')->latest()->get(), 'categories' => Category::orderBy('name')->get()]); }
    public function store(Request $request) { Product::create($this->validated($request)); return back()->with('success', 'Barang berhasil ditambahkan.'); }
    public function update(Request $request, Product $product) { $product->update($this->validated($request)); return back()->with('success', 'Barang berhasil diperbarui.'); }
    public function destroy(Product $product) { $product->delete(); return back()->with('success', 'Barang berhasil dihapus.'); }
    private function validated(Request $request): array { return $request->validate(['category_id' => ['required', 'exists:categories,id'], 'name' => ['required', 'string', 'max:150'], 'barcode' => ['nullable', 'string', 'max:100', 'unique:products,barcode,'.$request->route('product')?->id], 'satuan' => ['required', 'string', 'max:30'], 'purchase_price' => ['required', 'numeric', 'min:0'], 'selling_price' => ['required', 'numeric', 'min:0'], 'current_stock' => ['required', 'integer', 'min:0'], 'min_stock' => ['required', 'integer', 'min:0']]); }
}