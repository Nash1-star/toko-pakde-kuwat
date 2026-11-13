<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Shift;
use App\Models\StockMutation;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->orderBy('name')->get();
        $categories = Category::orderBy('name')->get();
        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();

        return view('pos.index', compact('products', 'categories', 'activeShift'));
    }

    public function checkout(Request $request)
    {
        $shift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        $cart = json_decode($request->cart, true);

        if (! $shift) {
            return back()->with('error', 'Shift kasir belum dibuka! Silakan buka shift terlebih dahulu.');
        }

        if (empty($cart)) {
            return back()->with('error', 'Keranjang belanja masih kosong!');
        }

        $request->validate([
            'payment_method' => ['required', 'in:Tunai,QRIS,Transfer'],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
            'change_given' => ['nullable', 'numeric', 'min:0'],
        ]);

        $total = 0;
        foreach ($cart as $item) {
            $product = Product::find($item['id']);
            if (! $product) {
                return back()->with('error', 'Produk tidak ditemukan!');
            }
            if ($product->current_stock < $item['qty']) {
                return back()->with('error', 'Stok '.$product->name.' tidak mencukupi (sisa '.$product->current_stock.')!');
            }
            $total += (float) $item['price'] * (int) $item['qty'];
        }

        $paymentMethod = $request->payment_method;
        $cashReceived = $paymentMethod === 'Tunai' ? (float) ($request->cash_received ?? 0) : null;
        $changeGiven = $paymentMethod === 'Tunai' ? (float) ($request->change_given ?? max(0, $cashReceived - $total)) : 0;

        if ($paymentMethod === 'Tunai' && $cashReceived < $total) {
            return back()->with('error', 'Uang yang diterima belum mencukupi total belanja!');
        }

        DB::beginTransaction();
        try {
            $tx = Transaction::create([
                'user_id' => Auth::id(),
                'shift_id' => $shift->id,
                'total_amount' => $total,
                'payment_method' => $paymentMethod,
                'cash_received' => $cashReceived,
                'change_given' => $changeGiven,
            ]);

            foreach ($cart as $item) {
                TransactionDetail::create([
                    'transaction_id' => $tx->id,
                    'product_id' => $item['id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'subtotal' => (float) $item['price'] * (int) $item['qty'],
                ]);

                $product = Product::find($item['id']);
                $product->current_stock -= (int) $item['qty'];
                $product->save();

                StockMutation::create([
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'sale',
                    'qty' => (int) $item['qty'],
                    'balance' => $product->current_stock,
                    'description' => 'Terjual (Transaksi #'.$tx->id.')',
                ]);
            }

            DB::commit();

            return redirect()->route('pos.index')
                ->with('success', 'Transaksi #'.$tx->id.' berhasil diselesaikan!')
                ->with('last_transaction_id', $tx->id);
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses transaksi: '.$e->getMessage());
        }
    }
}
