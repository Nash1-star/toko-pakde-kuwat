<?php

$base = __DIR__;

function makeFile($path, $content) {
    global $base;
    $fullPath = $base . '/' . $path;
    $dir = dirname($fullPath);
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    file_put_contents($fullPath, $content);
    echo "Created: $path\n";
}

// 1. web.php
makeFile('routes/web.php', <<<'EOT'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PosController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/shift/open', [ShiftController::class, 'showOpen'])->name('shift.open');
    Route::post('/shift/open', [ShiftController::class, 'open'])->name('shift.open.post');
    
    Route::middleware(['check.shift'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/shift/close', [ShiftController::class, 'showClose'])->name('shift.close');
        Route::post('/shift/close', [ShiftController::class, 'close'])->name('shift.close.post');

        Route::resource('products', ProductController::class);
        Route::resource('categories', CategoryController::class);

        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    });
});
EOT);

// 2. AuthController
makeFile('app/Http/Controllers/AuthController.php', <<<'EOT'
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() {
        \$users = User::all();
        return view('auth.login', compact('users'));
    }

    public function login(Request \$request) {
        \$user = User::find(\$request->user_id);
        if (\$user && \$user->pin == \$request->pin) {
            Auth::login(\$user);
            return redirect()->route('shift.open');
        }
        return back()->with('error', 'PIN Salah!');
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('login');
    }
}
EOT);

// 3. ShiftController
makeFile('app/Http/Controllers/ShiftController.php', <<<'EOT'
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function showOpen() {
        \$activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (\$activeShift) return redirect()->route('dashboard');
        return view('shift.open');
    }

    public function open(Request \$request) {
        Shift::create([
            'user_id' => Auth::id(),
            'starting_cash' => \$request->starting_cash,
        ]);
        return redirect()->route('dashboard');
    }

    public function showClose() {
        \$shift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!\$shift) return redirect()->route('dashboard');
        \$sales = \App\Models\Transaction::where('shift_id', \$shift->id)->sum('total_amount');
        \$shift->expected_cash = \$shift->starting_cash + \$sales;
        \$shift->save();
        return view('shift.close', compact('shift', 'sales'));
    }

    public function close(Request \$request) {
        \$shift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (\$shift) {
            \$shift->actual_cash = \$request->actual_cash;
            \$shift->difference = \$request->actual_cash - \$shift->expected_cash;
            \$shift->notes = \$request->notes;
            \$shift->end_time = now();
            \$shift->status = 'closed';
            \$shift->save();
        }
        Auth::logout();
        return redirect()->route('login')->with('success', 'Shift ditutup!');
    }
}
EOT);

// 4. CheckShift Middleware
makeFile('app/Http/Middleware/CheckShift.php', <<<'EOT'
<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class CheckShift
{
    public function handle(Request \$request, Closure \$next)
    {
        \$activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!\$activeShift) {
            return redirect()->route('shift.open');
        }
        return \$next(\$request);
    }
}
EOT);

// 5. Bootstrap App (Middleware Registration)
\$bootstrapApp = file_get_contents(\$base . '/bootstrap/app.php');
if (!str_contains(\$bootstrapApp, 'check.shift')) {
    \$bootstrapApp = str_replace(
        '->withMiddleware(function (Middleware $middleware) {',
        "->withMiddleware(function (Middleware \$middleware) {\n        \$middleware->alias(['check.shift' => \App\Http\Middleware\CheckShift::class]);",
        \$bootstrapApp
    );
    file_put_contents(\$base . '/bootstrap/app.php', \$bootstrapApp);
}

// 6. DashboardController
makeFile('app/Http/Controllers/DashboardController.php', <<<'EOT'
<?php
namespace App\Http\Controllers;
use App\Models\Product;
use App\Models\Transaction;

class DashboardController extends Controller
{
    public function index() {
        \$todaySales = Transaction::whereDate('created_at', today())->sum('total_amount');
        \$txCount = Transaction::whereDate('created_at', today())->count();
        \$lowStock = Product::whereColumn('current_stock', '<=', 'min_stock')->get();
        return view('dashboard', compact('todaySales', 'txCount', 'lowStock'));
    }
}
EOT);

// 7. ProductController & PosController ... (continued in next script due to size if needed, but I'll write PosController here)
makeFile('app/Http/Controllers/PosController.php', <<<'EOT'
<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use App\Models\StockMutation;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index() {
        \$products = Product::where('current_stock', '>', 0)->get();
        return view('pos.index', compact('products'));
    }

    public function checkout(Request \$request) {
        \$shift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        \$cart = json_decode(\$request->cart, true);
        
        if (empty(\$cart)) return back()->with('error', 'Keranjang kosong!');

        \$total = 0;
        foreach(\$cart as \$item) {
            \$total += \$item['price'] * \$item['qty'];
        }

        \$tx = Transaction::create([
            'user_id' => Auth::id(),
            'shift_id' => \$shift->id,
            'total_amount' => \$total,
            'payment_method' => \$request->payment_method ?? 'Tunai'
        ]);

        foreach(\$cart as \$item) {
            TransactionDetail::create([
                'transaction_id' => \$tx->id,
                'product_id' => \$item['id'],
                'qty' => \$item['qty'],
                'price' => \$item['price'],
                'subtotal' => \$item['price'] * \$item['qty']
            ]);

            \$product = Product::find(\$item['id']);
            \$product->current_stock -= \$item['qty'];
            \$product->save();

            StockMutation::create([
                'product_id' => \$product->id,
                'user_id' => Auth::id(),
                'type' => 'sale',
                'qty' => \$item['qty'],
                'balance' => \$product->current_stock,
                'description' => 'Terjual (TX: '.\$tx->id.')'
            ]);
        }

        return back()->with('success', 'Transaksi Berhasil!');
    }
}
EOT);

echo "Controllers created!\n";
