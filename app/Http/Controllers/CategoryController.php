<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount([
            'products',
            'products as low_stock_count' => function ($query) {
                $query->whereColumn('current_stock', '<=', 'min_stock');
            }
        ])->latest()->get();

        $totalProductsCount = Product::count();

        return view('categories.index', compact('categories', 'totalProductsCount'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $category = Category::create($validated);
        return back()->with('success', 'Kategori '.$category->name.' berhasil ditambahkan.');
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $category->update($validated);
        return back()->with('success', 'Kategori '.$category->name.' berhasil diperbarui.');
    }

    public function destroy(Category $category)
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'Kategori "'.$category->name.'" masih memiliki '.$category->products()->count().' produk dan tidak bisa dihapus. Silakan pindahkan atau hapus produk terlebih dahulu.']);
        }

        $name = $category->name;
        $category->delete();
        return back()->with('success', 'Kategori '.$name.' berhasil dihapus.');
    }
}
