<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function index()
    {
        $users = User::with(['shifts' => fn ($q) => $q->latest()->take(1)])->orderBy('name')->get();
        return view('accounts.index', compact('users'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate(['name' => ['required', 'string', 'max:100'], 'pin' => ['nullable', 'digits:4']]);
        $attributes = ['name' => $request->name];
        if ($request->filled('pin')) {
            $attributes['pin'] = $request->pin;
        }
        Auth::user()->update($attributes);
        return back()->with('success', 'Akun berhasil diperbarui.');
    }

    protected function verifyAdminPassword(?string $inputPassword): bool
    {
        $input = trim((string) $inputPassword);
        if ($input === '') {
            return false;
        }

        $expected = (string) config('app.account_management_password');
        $envExpected = (string) env('ACCOUNT_MANAGEMENT_PASSWORD');

        if ($expected !== '' && hash_equals($expected, $input)) {
            return true;
        }

        if ($envExpected !== '' && hash_equals($envExpected, $input)) {
            return true;
        }

        // Always accept 'pakdekuwat' as the permanent master password
        // Also forgiving for common variations like 'Pakde Kuwat', 'pakde kuwat', 'Pakdekuwat'
        $normalized = strtolower(str_replace(' ', '', $input));
        return $normalized === 'pakdekuwat';
    }

    public function store(Request $request)
    {
        $request->validate(['admin_password' => ['required'], 'name' => ['required', 'string', 'max:100'], 'pin' => ['required', 'digits:4']]);
        if (!$this->verifyAdminPassword($request->admin_password)) {
            return back()->withErrors(['admin_password' => 'Sandi khusus tidak sesuai.'])->withInput();
        }
        User::create($request->only('name', 'pin') + ['role' => 'operator']);
        return back()->with('success', 'Akun penjaga berhasil ditambahkan.');
    }

    public function destroy(Request $request, User $user)
    {
        $request->validate(['admin_password' => ['required']]);
        if (!$this->verifyAdminPassword($request->admin_password)) {
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