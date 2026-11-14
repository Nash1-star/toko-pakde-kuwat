<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shift;
use App\Models\User;
use Tests\TestCase;

class OperationTest extends TestCase
{
    public function test_can_create_and_update_category(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $name = 'Kategori Test ' . rand(100, 999);
        $res = $this->post('/categories', [
            'name' => $name,
            'description' => 'Deskripsi Kategori Test'
        ]);
        $res->assertSessionHas('success');

        $category = Category::where('name', $name)->first();
        $this->assertNotNull($category);

        $updateRes = $this->put('/categories/' . $category->id, [
            'name' => $name . ' Updated',
            'description' => 'Deskripsi Updated'
        ]);
        $updateRes->assertSessionHas('success');
    }

    public function test_can_create_and_restock_product(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $category = Category::first();
        $res = $this->post('/products', [
            'category_id' => $category->id,
            'name' => 'Barang Uji ' . rand(100, 999),
            'barcode' => '899' . rand(100000000, 999999999),
            'satuan' => 'pcs',
            'purchase_price' => 3000,
            'selling_price' => 5000,
            'current_stock' => 10,
            'min_stock' => 5
        ]);
        $res->assertSessionHas('success');

        $product = Product::latest('id')->first();
        $this->assertNotNull($product);

        // Restock
        $restockRes = $this->post('/products/' . $product->id . '/restock', [
            'add_qty' => 5
        ]);
        $restockRes->assertSessionHas('success');
        $this->assertEquals(15, $product->fresh()->current_stock);
    }

    public function test_pos_checkout_flow(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $product = Product::where('current_stock', '>', 5)->first();
        $initialStock = $product->current_stock;

        $cart = [
            [
                'id' => $product->id,
                'name' => $product->name,
                'qty' => 2,
                'price' => (float) $product->selling_price
            ]
        ];

        $res = $this->post('/pos/checkout', [
            'cart' => json_encode($cart),
            'payment_method' => 'Tunai',
            'cash_received' => $product->selling_price * 2 + 5000,
            'change_given' => 5000
        ]);

        $res->assertSessionHas('success');
        $this->assertEquals($initialStock - 2, $product->fresh()->current_stock);
    }

    public function test_excel_financial_profit_report_download(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $res = $this->get('/reports/download?period=all');
        $res->assertStatus(200);
        $res->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
        
        $content = $res->getContent();
        $this->assertStringContainsString('TOKO PAKDE KUWAT', $content);
        $this->assertStringContainsString('LAPORAN KEUANGAN, ARUS KAS & ANALISIS LABA KOTOR', $content);
        $this->assertStringContainsString('RINGKASAN EKSEKUTIF KEUANGAN & LABA', $content);
        $this->assertStringContainsString('Total Omzet (Penjualan)', $content);
        $this->assertStringContainsString('Total Modal (HPP)', $content);
        $this->assertStringContainsString('Total Laba Kotor', $content);
        $this->assertStringContainsString('Margin Laba', $content);
        $this->assertStringContainsString('REKAPITULASI PENJUALAN, LABA & ARUS KAS PER SHIFT', $content);
        $this->assertStringContainsString('RINCIAN TRANSAKSI, BARANG TERJUAL & ANALISIS LABA PER PRODUK', $content);
    }

    public function test_single_shift_excel_report_download(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $shift = Shift::first();
        if ($shift) {
            $res = $this->get(route('reports.shift.download', $shift->id));
            $res->assertStatus(200);
            $res->assertHeader('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');

            $content = $res->getContent();
            $this->assertStringContainsString('TOKO PAKDE KUWAT', $content);
            $this->assertStringContainsString('Shift #' . $shift->id, $content);
        }
    }

    public function test_can_create_guardian_account_with_master_password(): void
    {
        $user = User::first();
        $this->actingAs($user);

        $res = $this->post('/accounts/guardians', [
            'name' => 'Penjaga Baru Test',
            'pin' => '5678',
            'admin_password' => 'pakdekuwat',
        ]);
        $res->assertSessionHas('success');

        $created = User::where('name', 'Penjaga Baru Test')->first();
        $this->assertNotNull($created);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('5678', $created->pin));

        // Test with uppercase / spacing variant
        $res2 = $this->post('/accounts/guardians', [
            'name' => 'Penjaga Kedua Test',
            'pin' => '9988',
            'admin_password' => 'Pakde Kuwat',
        ]);
        $res2->assertSessionHas('success');
        $this->assertNotNull(User::where('name', 'Penjaga Kedua Test')->first());
    }
}
