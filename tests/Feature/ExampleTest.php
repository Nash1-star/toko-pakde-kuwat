<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Toko Pakde Kuwat');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::first() ?? User::factory()->create();
        
        // Ensure an open shift exists for the user
        $shift = Shift::where('user_id', $user->id)->where('status', 'open')->first();
        if (!$shift) {
            $shift = Shift::create([
                'user_id' => $user->id,
                'starting_cash' => 200000,
                'expected_cash' => 200000,
                'status' => 'open'
            ]);
        }

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Daftar Barang');
        $response->assertSee('Kasir');
    }

    public function test_authenticated_user_can_view_products(): void
    {
        $user = User::first() ?? User::factory()->create();
        $response = $this->actingAs($user)->get('/products');
        $response->assertStatus(200);
        $response->assertSee('Tambah Barang');
    }

    public function test_authenticated_user_can_view_categories(): void
    {
        $user = User::first() ?? User::factory()->create();
        $response = $this->actingAs($user)->get('/categories');
        $response->assertStatus(200);
        $response->assertSee('Tambah Kategori');
    }

    public function test_authenticated_user_can_view_pos(): void
    {
        $user = User::first() ?? User::factory()->create();
        $response = $this->actingAs($user)->get('/pos');
        $response->assertStatus(200);
        $response->assertSee('Keranjang');
    }

    public function test_authenticated_user_can_view_reports(): void
    {
        $user = User::first() ?? User::factory()->create();
        $response = $this->actingAs($user)->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Rekap per shift');
    }

    public function test_authenticated_user_can_view_accounts(): void
    {
        $user = User::first() ?? User::factory()->create();
        $response = $this->actingAs($user)->get('/accounts');
        $response->assertStatus(200);
        $response->assertSee('Daftar penjaga');
    }

    public function test_authenticated_user_can_view_shift_close(): void
    {
        $user = User::first() ?? User::factory()->create();
        $response = $this->actingAs($user)->get('/shift/close');
        $response->assertStatus(200);
        $response->assertSee('Tutup Shift & Serah Terima');
    }
}
