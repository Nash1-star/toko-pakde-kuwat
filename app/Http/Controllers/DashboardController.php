<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Shift;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // Active Shift
        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->latest()->first();
        $startingCash = $activeShift ? (float) $activeShift->starting_cash : 0;

        // Sales today
        $todaySales = (float) Transaction::whereDate('created_at', $today)->sum('total_amount');
        $txCount = Transaction::whereDate('created_at', $today)->count();
        $todayItemsSold = (int) TransactionDetail::whereHas('transaction', fn ($q) => $q->whereDate('created_at', $today))->sum('qty');

        // Drawer Cash estimate: starting cash + today's cash payments
        $todayCashTransactions = Transaction::whereDate('created_at', $today)
            ->where('payment_method', 'Tunai')
            ->get();
        $todayCashAmount = $todayCashTransactions->sum(fn ($t) => $t->cash_received !== null ? ($t->cash_received - $t->change_given) : $t->total_amount);
        $drawerCashEstimate = $startingCash + $todayCashAmount;

        // Stock Health & Restock items
        $allProducts = Product::with('category')->get();
        $totalProductsCount = $allProducts->count();

        $restockItems = [];
        $criticalCount = 0;
        $warningCount = 0;
        $safeCount = 0;
        $totalRestockCost = 0;

        foreach ($allProducts as $product) {
            $isLow = $product->current_stock <= $product->min_stock;
            $isCritical = $product->current_stock <= ($product->min_stock * 0.5);

            if ($isCritical) {
                $criticalCount++;
            } elseif ($isLow) {
                $warningCount++;
            } else {
                $safeCount++;
            }

            if ($isLow) {
                $target = max(1, $product->min_stock * 2);
                $add = max(0, $target - $product->current_stock);
                $cost = $add * (float) $product->purchase_price;
                $totalRestockCost += $cost;

                $pct = min(100, round(($product->current_stock / $target) * 100));

                $restockItems[] = (object) [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category_name' => $product->category?->name ?? 'Umum',
                    'barcode' => $product->barcode ?? '-',
                    'satuan' => $product->satuan ?? 'pcs',
                    'current_stock' => $product->current_stock,
                    'min_stock' => $product->min_stock,
                    'add' => $add,
                    'cost' => $cost,
                    'pct' => $pct,
                    'is_critical' => $isCritical,
                    'status' => $isCritical ? 'Kritis' : 'Menipis',
                    'badge_bg' => $isCritical ? '#FDECEA' : '#FDF1DC',
                    'badge_fg' => $isCritical ? '#B42318' : '#8A5300',
                    'dot' => $isCritical ? '#C8331F' : '#D98A0B',
                ];
            }
        }

        // Sort restock items: critical first
        usort($restockItems, fn ($a, $b) => $b->is_critical <=> $a->is_critical);

        $healthPercent = $totalProductsCount > 0 ? round(($safeCount / $totalProductsCount) * 100) : 100;

        // Penjualan per shift (last 8 shifts)
        $recentShiftsRaw = Shift::with(['user', 'transactions'])
            ->where('status', 'closed')
            ->latest('end_time')
            ->take(8)
            ->get()
            ->reverse()
            ->values();

        $recentShifts = [];
        $maxShiftSales = 100000; // minimum scale
        foreach ($recentShiftsRaw as $s) {
            $sTotal = (float) $s->transactions->sum('total_amount');
            $sCash = (float) $s->transactions->where('payment_method', 'Tunai')->sum('total_amount');
            $sQris = (float) $s->transactions->where('payment_method', 'QRIS')->sum('total_amount');
            $sTransfer = (float) $s->transactions->where('payment_method', 'Transfer')->sum('total_amount');

            if ($sTotal > $maxShiftSales) {
                $maxShiftSales = $sTotal;
            }

            $recentShifts[] = (object) [
                'id' => $s->id,
                'time' => $s->end_time ? Carbon::parse($s->end_time)->format('H:i') : Carbon::parse($s->start_time)->format('H:i'),
                'date' => $s->end_time ? Carbon::parse($s->end_time)->format('d M') : Carbon::parse($s->start_time)->format('d M'),
                'user_name' => $s->user?->name ?? 'Kasir',
                'total' => $sTotal,
                'cash' => $sCash,
                'qris' => $sQris,
                'transfer' => $sTransfer,
            ];
        }

        // Add height percentages to recentShifts
        foreach ($recentShifts as $rs) {
            $rs->height_pct = $rs->total == 0 ? 3 : max(5, round(($rs->total / $maxShiftSales) * 100));
        }

        // Transaksi terakhir
        $recentTransactions = Transaction::with(['user', 'details.product'])
            ->latest()
            ->take(5)
            ->get();

        // Catatan serah terima terakhir
        $latestHandoverShift = Shift::with('user')
            ->where('status', 'closed')
            ->whereNotNull('notes')
            ->where('notes', '!=', '')
            ->latest('end_time')
            ->first();

        // Time greeting
        $hour = (int) date('H');
        $greeting = $hour < 11 ? 'Selamat pagi' : ($hour < 15 ? 'Selamat siang' : ($hour < 18 ? 'Selamat sore' : 'Selamat malam'));

        return view('dashboard', compact(
            'activeShift',
            'startingCash',
            'todaySales',
            'txCount',
            'todayItemsSold',
            'drawerCashEstimate',
            'restockItems',
            'totalRestockCost',
            'totalProductsCount',
            'criticalCount',
            'warningCount',
            'safeCount',
            'healthPercent',
            'recentShifts',
            'recentTransactions',
            'latestHandoverShift',
            'greeting'
        ));
    }
}
