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
        $this->call(UserSeeder::class);

        // Catalog (Categories & Products)
        $this->call(CatalogSeeder::class);
    }
}
