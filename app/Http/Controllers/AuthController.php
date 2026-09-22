<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin() {
        $users = User::all();
        return view('auth.login', compact('users'));
    }

    public function login(Request $request) {
        $user = User::find($request->user_id);
        if ($user && $user->pin == $request->pin) {
            Auth::login($user);
            return redirect()->route('shift.open');
        }
        return back()->with('error', 'PIN Salah!');
    }

    public function logout() {
        Auth::logout();
        return redirect()->route('login');
    }
}