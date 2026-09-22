<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        return view('accounts.index', ['users' => User::orderBy('name')->get()]);
    }

    public function updateProfile(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:100'], 'pin' => ['nullable', 'digits:4']]);
        Auth::user()->update($request->only('name', 'pin'));
        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    public function store(Request $request)
    {
        $request->validate(['admin_password' => ['required'], 'name' => ['required', 'string', 'max:100'], 'pin' => ['required', 'digits:4']]);
        if (!hash_equals((string) config('app.account_management_password'), (string) $request->admin_password)) {
            return back()->withErrors(['admin_password' => 'Sandi khusus tidak sesuai.'])->withInput();
        }
        User::create($request->only('name', 'pin') + ['role' => 'operator']);
        return back()->with('success', 'Akun penjaga berhasil ditambahkan.');
    }

    public function destroy(Request $request, User $user)
    {
        $request->validate(['admin_password' => ['required']]);
        if (!hash_equals((string) config('app.account_management_password'), (string) $request->admin_password)) {
            return back()->withErrors(['admin_password' => 'Sandi khusus tidak sesuai.']);
        }
        if ($user->is(Auth::user())) {
            return back()->withErrors(['account' => 'Akun yang sedang dipakai tidak bisa dihapus.']);
        }
        if ($user->shifts()->exists() || $user->transactions()->exists()) {
            return back()->withErrors(['account' => 'Akun yang sudah memiliki riwayat shift atau transaksi tidak bisa dihapus.']);
        }
        $user->delete();
        return back()->with('success', 'Akun penjaga berhasil dihapus.');
    }
}