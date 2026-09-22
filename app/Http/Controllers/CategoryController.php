<?php
namespace App\Http\Controllers;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller {
    public function index() { return view('categories.index', ['categories' => Category::withCount('products')->latest()->get()]); }
    public function store(Request $request) { Category::create($request->validate(['name' => ['required', 'string', 'max:100', 'unique:categories,name'], 'description' => ['nullable', 'string', 'max:500']])); return back()->with('success', 'Kategori berhasil ditambahkan.'); }
    public function update(Request $request, Category $category) { $category->update($request->validate(['name' => ['required', 'string', 'max:100', 'unique:categories,name,'.$category->id], 'description' => ['nullable', 'string', 'max:500']])); return back()->with('success', 'Kategori berhasil diperbarui.'); }
    public function destroy(Category $category) { if ($category->products()->exists()) return back()->withErrors(['category' => 'Kategori yang masih memiliki produk tidak bisa dihapus.']); $category->delete(); return back()->with('success', 'Kategori berhasil dihapus.'); }
}