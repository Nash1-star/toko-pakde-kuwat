<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockMutation;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    public function index() {
        $products = Product::with('category')->where('current_stock', '>', 0)->orderBy('name')->get();
        return view('pos.index', compact('products'));
    }

    public function checkout(Request $request) {
        $shift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        $cart = json_decode($request->cart, true);
        
        if (!$shift || empty($cart)) return back()->with('error', 'Shift belum dibuka atau keranjang kosong!');

        $request->validate([
            'payment_method' => ['required', 'in:Tunai,QRIS,Transfer'],
            'cash_received' => ['nullable', 'numeric', 'min:0'],
            'change_given' => ['nullable', 'numeric', 'min:0'],
        ]);

        $total = 0;
        foreach($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }

        $paymentMethod = $request->payment_method;
        $cashReceived = $paymentMethod === 'Tunai' ? (float) ($request->cash_received ?? 0) : null;
        $changeGiven = $paymentMethod === 'Tunai' ? (float) ($request->change_given ?? max(0, $cashReceived - $total)) : 0;
        if ($paymentMethod === 'Tunai' && $cashReceived < $total) {
            return back()->with('error', 'Uang diterima belum mencukupi total transaksi.');
        }

        $tx = Transaction::create([
            'user_id' => Auth::id(),
            'shift_id' => $shift->id,
            'total_amount' => $total,
            'payment_method' => $paymentMethod,
            'cash_received' => $cashReceived,
            'change_given' => $changeGiven,
        ]);

        foreach($cart as $item) {
            TransactionDetail::create([
                'transaction_id' => $tx->id,
                'product_id' => $item['id'],
                'qty' => $item['qty'],
                'price' => $item['price'],
                'subtotal' => $item['price'] * $item['qty']
            ]);

            $product = Product::find($item['id']);
            $product->current_stock -= $item['qty'];
            $product->save();

            StockMutation::create([
                'product_id' => $product->id,
                'user_id' => Auth::id(),
                'type' => 'sale',
                'qty' => $item['qty'],
                'balance' => $product->current_stock,
                'description' => 'Terjual (TX: '.$tx->id.')'
            ]);
        }

        return back()->with('success', 'Transaksi Berhasil!');
    }
}