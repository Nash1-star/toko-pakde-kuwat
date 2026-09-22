<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index() {
        $todaySales = Transaction::whereDate('created_at', today())->sum('total_amount');
        $txCount = Transaction::whereDate('created_at', today())->count();
        $lowStock = Product::whereColumn('current_stock', '<=', 'min_stock')->get();
        return view('dashboard', compact('todaySales', 'txCount', 'lowStock'));
    }
}