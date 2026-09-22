<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AccountController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/shift/open', [ShiftController::class, 'showOpen'])->name('shift.open');
    Route::post('/shift/open', [ShiftController::class, 'open'])->name('shift.open.post');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download', [ReportController::class, 'downloadAll'])->name('reports.download');
    Route::get('/reports/{shift}/download', [ReportController::class, 'downloadShift'])->name('reports.shift.download');
    Route::get('/reports/transactions/{transaction}/receipt', [ReportController::class, 'receipt'])->name('reports.transaction.receipt');
    Route::get('/reports/transactions/{transaction}/receipt', [ReportController::class, 'receipt'])->name('reports.transaction.receipt');
    Route::get('/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::post('/accounts/profile', [AccountController::class, 'updateProfile'])->name('accounts.profile');
    Route::post('/accounts/guardians', [AccountController::class, 'store'])->name('accounts.guardians.store');
    Route::delete('/accounts/{user}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    
    Route::middleware(['check.shift'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        Route::get('/shift/close', [ShiftController::class, 'showClose'])->name('shift.close');
        Route::post('/shift/close', [ShiftController::class, 'close'])->name('shift.close.post');

        Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
    });
});