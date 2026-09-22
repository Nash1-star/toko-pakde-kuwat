<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;

class CheckShift
{
    public function handle(Request $request, Closure $next)
    {
        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!$activeShift) {
            return redirect()->route('shift.open');
        }
        return $next($request);
    }
}