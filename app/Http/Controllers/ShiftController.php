<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    public function showOpen() {
        $activeShift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($activeShift) return redirect()->route('dashboard');
        $previousShift = Shift::with('transactions')->where('status', 'closed')->latest('end_time')->first();
        return view('shift.open', compact('previousShift'));
    }

    public function open(Request $request) {
        $request->validate(['starting_cash' => ['required', 'numeric', 'min:0']]);
        Shift::create([
            'user_id' => Auth::id(),
            'starting_cash' => $request->starting_cash,
        ]);
        return redirect()->route('dashboard');
    }

    public function showClose() {
        $shift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if (!$shift) return redirect()->route('dashboard');
        $summary = $this->paymentSummary($shift);
        $sales = $summary->total;
        $shift->expected_cash = $shift->starting_cash + $summary->cash;
        $shift->save();
        return view('shift.close', compact('shift', 'sales', 'summary'));
    }

    public function close(Request $request) {
        $shift = Shift::where('user_id', Auth::id())->where('status', 'open')->first();
        if ($shift) {
            $request->validate(['actual_cash' => ['required', 'numeric', 'min:0'], 'notes' => ['nullable', 'string', 'max:2000']]);
            $summary = $this->paymentSummary($shift);
            $shift->expected_cash = $shift->starting_cash + $summary->cash;
            $shift->actual_cash = $request->actual_cash;
            $shift->difference = $request->actual_cash - $shift->expected_cash;
            $shift->notes = $request->notes;
            $shift->end_time = now();
            $shift->status = 'closed';
            $shift->save();
        }
        Auth::logout();
        return redirect()->route('login')->with('success', 'Shift ditutup!');
    }

    private function paymentSummary(Shift $shift): object
    {
        $transactions = $shift->transactions;
        $cash = $transactions->where('payment_method', 'Tunai')->sum(fn ($transaction) => $transaction->cash_received !== null ? $transaction->cash_received - $transaction->change_given : $transaction->total_amount);

        return (object) [
            'cash' => (float) $cash,
            'qris' => (float) $transactions->where('payment_method', 'QRIS')->sum('total_amount'),
            'transfer' => (float) $transactions->where('payment_method', 'Transfer')->sum('total_amount'),
            'total' => (float) $transactions->sum('total_amount'),
        ];
    }
}