<?php
$base = __DIR__;
function makeFile($path, $content) {
    global $base;
    file_put_contents($base . '/' . $path, $content);
}

makeFile('resources/views/products/index.blade.php', <<<'EOT'
@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-bold mb-4">Daftar Barang</h1>
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
    <p>Fitur CRUD Daftar Barang dapat dikembangkan lebih lanjut di sini.</p>
</div>
@endsection
EOT);

makeFile('resources/views/categories/index.blade.php', <<<'EOT'
@extends('layouts.app')
@section('content')
<h1 class="text-2xl font-bold mb-4">Kategori</h1>
<div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
    <p>Fitur CRUD Kategori dapat dikembangkan lebih lanjut di sini.</p>
</div>
@endsection
EOT);

makeFile('app/Http/Controllers/ProductController.php', <<<'EOT'
<?php
namespace App\Http\Controllers;
class ProductController extends Controller {
    public function index() { return view('products.index'); }
}
EOT);

makeFile('app/Http/Controllers/CategoryController.php', <<<'EOT'
<?php
namespace App\Http\Controllers;
class CategoryController extends Controller {
    public function index() { return view('categories.index'); }
}
EOT);

echo "Dummy CRUD setup!";

