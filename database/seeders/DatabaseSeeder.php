<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use App\Models\Product;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Users
        User::create(['name' => 'Mama', 'pin' => '1234', 'role' => 'operator']);
        User::create(['name' => 'Zefa', 'pin' => '1234', 'role' => 'operator']);

        // Categories
        $catSembako = Category::create(['name' => 'Sembako', 'description' => 'Beras, Minyak, Gula, dll']);
        $catMinuman = Category::create(['name' => 'Minuman', 'description' => 'Minuman kemasan']);
        $catSnack = Category::create(['name' => 'Snack', 'description' => 'Makanan ringan']);

        // Products
        Product::create([
            'category_id' => $catSembako->id,
            'barcode' => '899999900001',
            'name' => 'Beras Pandan Wangi 5kg',
            'purchase_price' => 60000,
            'selling_price' => 65000,
            'min_stock' => 5,
            'current_stock' => 10,
        ]);

        Product::create([
            'category_id' => $catMinuman->id,
            'barcode' => '899999900002',
            'name' => 'Aqua 600ml',
            'purchase_price' => 2500,
            'selling_price' => 3500,
            'min_stock' => 10,
            'current_stock' => 24,
        ]);

        Product::create([
            'category_id' => $catMinuman->id,
            'barcode' => '899999900003',
            'name' => 'Teh Pucuk 350ml',
            'purchase_price' => 3000,
            'selling_price' => 4000,
            'min_stock' => 10,
            'current_stock' => 15,
        ]);

        Product::create([
            'category_id' => $catSnack->id,
            'barcode' => '899999900004',
            'name' => 'Chitato 68gr',
            'purchase_price' => 9000,
            'selling_price' => 12000,
            'min_stock' => 5,
            'current_stock' => 12,
        ]);
        
        Product::create([
            'category_id' => $catSembako->id,
            'barcode' => '899999900005',
            'name' => 'Indomie Goreng',
            'purchase_price' => 2500,
            'selling_price' => 3500,
            'min_stock' => 20,
            'current_stock' => 50,
        ]);
    }
}
