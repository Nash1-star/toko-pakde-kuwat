<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        [$from, $to] = $this->period($request->input('period', 'all'));
        $shifts = Shift::with('user')->withCount('transactions')->when($from, fn ($query) => $query->whereBetween('start_time', [$from, $to]))->latest('start_time')->get();
        $shifts->each(fn ($shift) => $shift->payment_summary = $this->summary($shift));
        $transactions = Transaction::with(['user', 'shift', 'details.product'])->when($from, fn ($query) => $query->whereBetween('created_at', [$from, $to]))->latest()->get();
        $period = $request->input('period', 'all');
        return view('reports.index', compact('shifts', 'transactions', 'period'));
    }

    public function downloadAll(Request $request): Response
    {
        [$from, $to] = $this->period($request->input('period', 'all'));
        return $this->csv(Shift::with('user')->with('transactions')->when($from, fn ($query) => $query->whereBetween('start_time', [$from, $to]))->latest('start_time')->get(), 'laporan-semua-shift-'.$request->input('period', 'all').'.csv');
    }

    public function downloadShift(Shift $shift): Response
    {
        return $this->csv(collect([$shift->load('user', 'transactions')]), 'laporan-shift-'.$shift->id.'.csv');
    }

    public function receipt(Transaction $transaction)
    {
        return view('reports.receipt', ['transaction' => $transaction->load(['user', 'details.product'])]);
    }

    private function period(string $period): array
    {
        $now = now();
        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'month' => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [null, null],
        };
    }

    private function summary(Shift $shift): object
    {
        $transactions = $shift->transactions;
        $cash = $transactions->where('payment_method', 'Tunai')->sum(fn ($transaction) => $transaction->cash_received !== null ? $transaction->cash_received - $transaction->change_given : $transaction->total_amount);
        return (object) ['cash' => (float) $cash, 'qris' => (float) $transactions->where('payment_method', 'QRIS')->sum('total_amount'), 'transfer' => (float) $transactions->where('payment_method', 'Transfer')->sum('total_amount'), 'total' => (float) $transactions->sum('total_amount')];
    }

    private function csv($shifts, string $filename): Response
    {
        $content = "Tanggal,Penjaga,Total Transaksi,Tunai,QRIS,Transfer,Kas Seharusnya,Kas Aktual,Selisih,Catatan\n";
        foreach ($shifts as $shift) {
            $summary = $this->summary($shift);
            $content .= implode(',', array_map(fn ($value) => '"'.str_replace('"', '""', (string) $value).'"', [
                optional($shift->end_time ?? $shift->start_time)->format('Y-m-d H:i'), $shift->user->name, $summary->total, $summary->cash, $summary->qris, $summary->transfer, $shift->expected_cash, $shift->actual_cash, $shift->difference, $shift->notes,
            ]))."\n";
        }
        return response($content, 200, ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="'.$filename.'"']);
    }
}
