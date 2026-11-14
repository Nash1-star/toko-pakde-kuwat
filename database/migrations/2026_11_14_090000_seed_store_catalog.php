<?php

use Database\Seeders\CatalogSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Automatically seed the 100 products and 10 categories
        (new CatalogSeeder())->run();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't remove on rollback to protect product data
    }
};
