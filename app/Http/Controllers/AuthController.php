<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthController extends Controller
{
    public function showLogin() {
        $users = User::all();
        return view('auth.login', compact('users'));
    }

    public function login(Request $request) {
        $request->validate([
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'pin' => ['required', 'digits:4'],
        ]);

        $key = 'login:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 5)) {
            return back()->with('error', 'Terlalu banyak percobaan. Coba lagi dalam '.RateLimiter::availableIn($key).' detik.');
        }

        $user = User::find($request->user_id);
        $storedPin = (string) $user->pin;
        $isHashed = password_get_info($storedPin)['algo'] !== null;
        $pinMatches = $isHashed
            ? Hash::check($request->pin, $storedPin)
            : hash_equals($storedPin, (string) $request->pin);

        if ($pinMatches) {
            if (!$isHashed) {
                $user->pin = $request->pin;
                $user->save();
            }
            RateLimiter::clear($key);
            Auth::login($user);
            $request->session()->regenerate();
            return redirect()->route('shift.open');
        }
        RateLimiter::hit($key, 60);
        return back()->with('error', 'PIN Salah!');
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}