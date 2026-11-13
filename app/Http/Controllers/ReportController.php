<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->input('period', 'all');
        [$from, $to] = $this->period($period);

        $shifts = Shift::with(['user', 'transactions.details.product'])
            ->withCount('transactions')
            ->when($from, fn ($query) => $query->whereBetween('start_time', [$from, $to]))
            ->latest('start_time')
            ->get();

        $shifts->each(fn ($shift) => $shift->payment_summary = $this->summary($shift));

        $transactions = Transaction::with(['user', 'shift', 'details.product'])
            ->when($from, fn ($query) => $query->whereBetween('created_at', [$from, $to]))
            ->latest()
            ->get();

        // Calculate summary cards
        $totalSales = 0;
        $totalCash = 0;
        $totalQris = 0;
        $totalTransfer = 0;
        $netDifference = 0;
        $mismatchedCount = 0;
        $closedCount = 0;

        foreach ($shifts as $s) {
            $totalSales += $s->payment_summary->total;
            $totalCash += $s->payment_summary->cash;
            $totalQris += $s->payment_summary->qris;
            $totalTransfer += $s->payment_summary->transfer;

            if ($s->status === 'closed') {
                $closedCount++;
                $diff = (float) ($s->difference ?? 0);
                $netDifference += $diff;
                if ($diff != 0) {
                    $mismatchedCount++;
                }
            }
        }

        $cashPct = $totalSales > 0 ? round(($totalCash / $totalSales) * 100, 1) : 0;
        $qrisPct = $totalSales > 0 ? round(($totalQris / $totalSales) * 100, 1) : 0;
        $trfPct = $totalSales > 0 ? max(0, 100 - $cashPct - $qrisPct) : 0;

        return view('reports.index', compact(
            'shifts',
            'transactions',
            'period',
            'totalSales',
            'totalCash',
            'totalQris',
            'totalTransfer',
            'cashPct',
            'qrisPct',
            'trfPct',
            'netDifference',
            'mismatchedCount',
            'closedCount'
        ));
    }

    public function downloadAll(Request $request): Response
    {
        $period = $request->input('period', 'all');
        [$from, $to] = $this->period($period);

        $shifts = Shift::with(['user', 'transactions.details.product'])
            ->when($from, fn ($query) => $query->whereBetween('start_time', [$from, $to]))
            ->latest('start_time')
            ->get();

        $transactions = Transaction::with(['user', 'shift', 'details.product'])
            ->when($from, fn ($query) => $query->whereBetween('created_at', [$from, $to]))
            ->latest()
            ->get();

        $periodLabel = match ($period) {
            'today' => 'Hari Ini (' . Carbon::today()->format('d/m/Y') . ')',
            'week' => '7 Hari Terakhir',
            'month' => '30 Hari Terakhir',
            default => 'Semua Periode',
        };

        return $this->generateExcelReport($shifts, $transactions, $periodLabel, 'laporan-laba-keuangan-' . $period . '-' . date('Ymd-His') . '.xls');
    }

    public function downloadShift(Shift $shift): Response
    {
        $shift->load(['user', 'transactions.details.product']);
        $shifts = collect([$shift]);
        $transactions = $shift->transactions()->with(['user', 'shift', 'details.product'])->latest()->get();

        $shiftDate = $shift->end_time ? Carbon::parse($shift->end_time)->format('d M Y H:i') : Carbon::parse($shift->start_time)->format('d M Y H:i');
        $periodLabel = 'Shift #' . $shift->id . ' - ' . ($shift->user?->name ?? 'Kasir') . ' (' . $shiftDate . ')';

        return $this->generateExcelReport($shifts, $transactions, $periodLabel, 'laporan-laba-keuangan-shift-' . $shift->id . '-' . date('Ymd-His') . '.xls');
    }

    public function receipt(Transaction $transaction)
    {
        return view('reports.receipt', [
            'transaction' => $transaction->load(['user', 'details.product'])
        ]);
    }

    private function period(string $period): array
    {
        $now = now();
        return match ($period) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            'week' => [$now->copy()->subDays(7)->startOfDay(), $now->copy()->endOfDay()],
            'month' => [$now->copy()->subDays(30)->startOfDay(), $now->copy()->endOfDay()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            default => [null, null],
        };
    }

    private function summary(Shift $shift): object
    {
        $transactions = $shift->transactions;
        $cash = $transactions->where('payment_method', 'Tunai')->sum(
            fn ($transaction) => $transaction->cash_received !== null ? ($transaction->cash_received - $transaction->change_given) : $transaction->total_amount
        );

        return (object) [
            'cash' => (float) $cash,
            'qris' => (float) $transactions->where('payment_method', 'QRIS')->sum('total_amount'),
            'transfer' => (float) $transactions->where('payment_method', 'Transfer')->sum('total_amount'),
            'total' => (float) $transactions->sum('total_amount'),
        ];
    }

    /**
     * Generate a beautifully formatted HTML-based Excel spreadsheet (.xls)
     * containing executive financial summary, shift summary with profit, and product-level profit analysis.
     */
    private function generateExcelReport($shifts, $transactions, string $periodLabel, string $filename): Response
    {
        $currentUser = Auth::user()?->name ?? 'Administrator';
        $exportDate = Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y - HH:mm');

        // Financial Totals Calculation
        $totalOmzet = 0;
        $totalModal = 0;
        $totalCash = 0;
        $totalQris = 0;
        $totalTransfer = 0;
        $totalDifference = 0;
        $totalTxCount = $transactions->count();

        foreach ($transactions as $tx) {
            $totalOmzet += (float) $tx->total_amount;
            if ($tx->payment_method === 'Tunai') {
                $totalCash += (float) $tx->total_amount;
            } elseif ($tx->payment_method === 'QRIS') {
                $totalQris += (float) $tx->total_amount;
            } elseif ($tx->payment_method === 'Transfer') {
                $totalTransfer += (float) $tx->total_amount;
            }

            foreach ($tx->details as $d) {
                $unitBuy = (float) ($d->product?->purchase_price ?? 0);
                $totalModal += $unitBuy * (int) $d->qty;
            }
        }

        foreach ($shifts as $s) {
            if ($s->status === 'closed') {
                $totalDifference += (float) ($s->difference ?? 0);
            }
        }

        $totalLabaKotor = $totalOmzet - $totalModal;
        $marginPct = $totalOmzet > 0 ? round(($totalLabaKotor / $totalOmzet) * 100, 1) : 0;

        // Build Excel Output
        ob_start();
        ?>
        <html xmlns:o="urn:schemas-microsoft-com:office:office"
              xmlns:x="urn:schemas-microsoft-com:office:excel"
              xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Laporan Laba Keuangan</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                body { font-family: 'Segoe UI', Calibri, Arial, sans-serif; font-size: 11pt; color: #111827; }
                table { border-collapse: collapse; width: 100%; margin-bottom: 24px; }
                th, td { border: 1px solid #D1D5DB; padding: 8px 12px; }
                .title-main { font-size: 18pt; font-weight: bold; color: #1E3A8A; text-align: left; }
                .title-sub { font-size: 12pt; font-weight: bold; color: #4B5563; }
                .meta-table td { border: none; padding: 4px 8px; font-size: 10pt; }
                .kpi-table th { background-color: #1E40AF; color: #FFFFFF; font-weight: bold; text-align: center; }
                .kpi-table td { background-color: #F8FAFC; font-weight: bold; text-align: center; font-size: 13pt; }
                .sec-header { background-color: #1E3A8A; color: #FFFFFF; font-weight: bold; font-size: 11pt; text-align: center; }
                .sec-sub { background-color: #3B82F6; color: #FFFFFF; font-weight: bold; font-size: 10pt; text-align: center; }
                .row-zebra { background-color: #F9FAFB; }
                .num-right { text-align: right; mso-number-format: "\#\,\#\#0"; }
                .text-center { text-align: center; }
                .profit-cell { color: #047857; font-weight: bold; text-align: right; mso-number-format: "\#\,\#\#0"; }
                .loss-cell { color: #B91C1C; font-weight: bold; text-align: right; mso-number-format: "\#\,\#\#0"; }
                .total-row th, .total-row td { background-color: #DBEAFE; font-weight: bold; border-top: 2px solid #1E40AF; border-bottom: 3px double #1E40AF; }
                .badge-sesuai { background-color: #DCFCE7; color: #166534; font-weight: bold; text-align: center; }
                .badge-selisih { background-color: #FEE2E2; color: #991B1B; font-weight: bold; text-align: center; }
            </style>
        </head>
        <body>
            <!-- Header Dokumen -->
            <table>
                <tr>
                    <td colspan="10" class="title-main">TOKO PAKDE KUWAT</td>
                </tr>
                <tr>
                    <td colspan="10" class="title-sub">LAPORAN KEUANGAN, ARUS KAS & ANALISIS LABA KOTOR</td>
                </tr>
                <tr>
                    <td colspan="10" style="border: none; height: 6px;"></td>
                </tr>
            </table>

            <table class="meta-table">
                <tr>
                    <td style="width: 140px;"><strong>Periode Laporan</strong></td>
                    <td style="width: 20px;">:</td>
                    <td style="color: #1E40AF; font-weight: bold;"><?= htmlspecialchars($periodLabel) ?></td>
                    <td style="width: 140px;"><strong>Dicetak Oleh</strong></td>
                    <td style="width: 20px;">:</td>
                    <td><?= htmlspecialchars($currentUser) ?></td>
                </tr>
                <tr>
                    <td><strong>Waktu Export</strong></td>
                    <td>:</td>
                    <td><?= htmlspecialchars($exportDate) ?></td>
                    <td><strong>Status Toko</strong></td>
                    <td>:</td>
                    <td style="color: #047857; font-weight: bold;">Aktif & Terverifikasi</td>
                </tr>
            </table>

            <br/>

            <!-- RINGKASAN EKSEKUTIF FINANSIAL -->
            <table>
                <thead>
                    <tr class="sec-header">
                        <th colspan="7">RINGKASAN EKSEKUTIF KEUANGAN & LABA</th>
                    </tr>
                    <tr class="sec-sub">
                        <th>Total Omzet (Penjualan)</th>
                        <th>Total Modal (HPP)</th>
                        <th>Total Laba Kotor</th>
                        <th>Margin Laba</th>
                        <th>Penjualan Tunai</th>
                        <th>Penjualan QRIS & Trf</th>
                        <th>Selisih Kas Fisik</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="num-right" style="font-size: 13pt; font-weight: bold; color: #1E40AF;">Rp <?= number_format($totalOmzet, 0, ',', '.') ?></td>
                        <td class="num-right" style="font-size: 13pt; font-weight: bold; color: #4B5563;">Rp <?= number_format($totalModal, 0, ',', '.') ?></td>
                        <td class="num-right" style="font-size: 14pt; font-weight: bold; color: #047857; background-color: #ECFDF5;">Rp <?= number_format($totalLabaKotor, 0, ',', '.') ?></td>
                        <td class="text-center" style="font-size: 13pt; font-weight: bold; color: #047857;"><?= $marginPct ?>%</td>
                        <td class="num-right" style="font-weight: bold;">Rp <?= number_format($totalCash, 0, ',', '.') ?></td>
                        <td class="num-right" style="font-weight: bold;">Rp <?= number_format($totalQris + $totalTransfer, 0, ',', '.') ?></td>
                        <td class="text-center" style="font-weight: bold; <?= $totalDifference < 0 ? 'color: #DC2626; background: #FEE2E2;' : ($totalDifference > 0 ? 'color: #D97706;' : 'color: #047857;') ?>">
                            <?= $totalDifference < 0 ? 'Kurang Rp ' . number_format(abs($totalDifference), 0, ',', '.') : ($totalDifference > 0 ? 'Lebih Rp ' . number_format($totalDifference, 0, ',', '.') : 'Sesuai (Rp 0)') ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <br/>

            <!-- TABEL 1: REKAPITULASI PER SHIFT -->
            <table>
                <thead>
                    <tr class="sec-header">
                        <th colspan="15">REKAPITULASI PENJUALAN, LABA & ARUS KAS PER SHIFT</th>
                    </tr>
                    <tr class="sec-sub">
                        <th style="width: 40px;">No</th>
                        <th>Waktu Shift</th>
                        <th>Penjaga</th>
                        <th>Jml Transaksi</th>
                        <th>Total Omzet</th>
                        <th>Estimasi Modal (HPP)</th>
                        <th>Laba Kotor</th>
                        <th>Margin</th>
                        <th>Tunai</th>
                        <th>QRIS</th>
                        <th>Transfer</th>
                        <th>Kas Seharusnya</th>
                        <th>Kas Aktual</th>
                        <th>Selisih Kas</th>
                        <th>Catatan Serah Terima</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 1;
                    $sumShiftOmzet = 0;
                    $sumShiftModal = 0;
                    $sumShiftLaba = 0;
                    $sumShiftCash = 0;
                    $sumShiftQris = 0;
                    $sumShiftTrf = 0;
                    $sumShiftExpected = 0;
                    $sumShiftActual = 0;
                    $sumShiftDiff = 0;

                    foreach ($shifts as $s):
                        $summary = $this->summary($s);
                        $sModal = 0;
                        foreach ($s->transactions as $stx) {
                            foreach ($stx->details as $sd) {
                                $sModal += ((float) ($sd->product?->purchase_price ?? 0)) * (int) $sd->qty;
                            }
                        }
                        $sLaba = $summary->total - $sModal;
                        $sMargin = $summary->total > 0 ? round(($sLaba / $summary->total) * 100, 1) : 0;
                        $sDiff = (float) ($s->difference ?? 0);

                        $sumShiftOmzet += $summary->total;
                        $sumShiftModal += $sModal;
                        $sumShiftLaba += $sLaba;
                        $sumShiftCash += $summary->cash;
                        $sumShiftQris += $summary->qris;
                        $sumShiftTrf += $summary->transfer;
                        $sumShiftExpected += (float) $s->expected_cash;
                        $sumShiftActual += (float) ($s->actual_cash ?? 0);
                        $sumShiftDiff += $sDiff;

                        $shiftTime = ($s->end_time ? Carbon::parse($s->end_time)->format('d/m/Y H:i') : Carbon::parse($s->start_time)->format('d/m/Y H:i')) . ($s->status === 'open' ? ' (Aktif)' : '');
                        $bgClass = $no % 2 === 0 ? 'class="row-zebra"' : '';
                    ?>
                    <tr <?= $bgClass ?>>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($shiftTime) ?></td>
                        <td><strong><?= htmlspecialchars($s->user?->name ?? 'Kasir') ?></strong></td>
                        <td class="text-center"><?= $s->transactions->count() ?></td>
                        <td class="num-right">Rp <?= number_format($summary->total, 0, ',', '.') ?></td>
                        <td class="num-right">Rp <?= number_format($sModal, 0, ',', '.') ?></td>
                        <td class="profit-cell">Rp <?= number_format($sLaba, 0, ',', '.') ?></td>
                        <td class="text-center"><?= $sMargin ?>%</td>
                        <td class="num-right">Rp <?= number_format($summary->cash, 0, ',', '.') ?></td>
                        <td class="num-right">Rp <?= number_format($summary->qris, 0, ',', '.') ?></td>
                        <td class="num-right">Rp <?= number_format($summary->transfer, 0, ',', '.') ?></td>
                        <td class="num-right">Rp <?= number_format($s->expected_cash, 0, ',', '.') ?></td>
                        <td class="num-right">Rp <?= number_format($s->actual_cash ?? 0, 0, ',', '.') ?></td>
                        <td class="text-center" style="<?= $sDiff < 0 ? 'color: #DC2626; font-weight: bold;' : ($sDiff > 0 ? 'color: #D97706; font-weight: bold;' : 'color: #047857; font-weight: bold;') ?>">
                            <?= $s->status === 'open' ? '-' : ($sDiff == 0 ? 'Sesuai' : ($sDiff > 0 ? '+ Rp ' . number_format($sDiff, 0, ',', '.') : '- Rp ' . number_format(abs($sDiff), 0, ',', '.'))) ?>
                        </td>
                        <td><?= htmlspecialchars($s->notes ?: '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <th colspan="3" class="text-center">TOTAL KESELURUHAN SHIFT</th>
                        <th class="text-center"><?= $totalTxCount ?></th>
                        <th class="num-right">Rp <?= number_format($sumShiftOmzet, 0, ',', '.') ?></th>
                        <th class="num-right">Rp <?= number_format($sumShiftModal, 0, ',', '.') ?></th>
                        <th class="profit-cell" style="background-color: #DBEAFE;">Rp <?= number_format($sumShiftLaba, 0, ',', '.') ?></th>
                        <th class="text-center"><?= $sumShiftOmzet > 0 ? round(($sumShiftLaba / $sumShiftOmzet) * 100, 1) : 0 ?>%</th>
                        <th class="num-right">Rp <?= number_format($sumShiftCash, 0, ',', '.') ?></th>
                        <th class="num-right">Rp <?= number_format($sumShiftQris, 0, ',', '.') ?></th>
                        <th class="num-right">Rp <?= number_format($sumShiftTrf, 0, ',', '.') ?></th>
                        <th class="num-right">Rp <?= number_format($sumShiftExpected, 0, ',', '.') ?></th>
                        <th class="num-right">Rp <?= number_format($sumShiftActual, 0, ',', '.') ?></th>
                        <th class="text-center"><?= $sumShiftDiff == 0 ? 'Sesuai (Rp 0)' : ($sumShiftDiff > 0 ? '+ Rp ' . number_format($sumShiftDiff, 0, ',', '.') : '- Rp ' . number_format(abs($sumShiftDiff), 0, ',', '.')) ?></th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>

            <br/>

            <!-- TABEL 2: RINCIAN PENJUALAN PER PRODUK & LABA -->
            <table>
                <thead>
                    <tr class="sec-header">
                        <th colspan="13">RINCIAN TRANSAKSI, BARANG TERJUAL & ANALISIS LABA PER PRODUK</th>
                    </tr>
                    <tr class="sec-sub">
                        <th style="width: 40px;">No</th>
                        <th>No. Nota</th>
                        <th>Waktu Transaksi</th>
                        <th>Penjaga</th>
                        <th>Nama Barang</th>
                        <th>Qty</th>
                        <th>Harga Beli (Modal)</th>
                        <th>Harga Jual</th>
                        <th>Total Omzet</th>
                        <th>Total Modal (HPP)</th>
                        <th>Laba Transaksi</th>
                        <th>Margin</th>
                        <th>Metode Bayar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $itemNo = 1;
                    $grandDetailOmzet = 0;
                    $grandDetailModal = 0;
                    $grandDetailLaba = 0;
                    $grandDetailQty = 0;

                    foreach ($transactions as $tx):
                        foreach ($tx->details as $d):
                            $p = $d->product;
                            $unitBuy = (float) ($p?->purchase_price ?? 0);
                            $unitSell = (float) $d->price;
                            $lineOmzet = (float) $d->subtotal;
                            $lineModal = $unitBuy * (int) $d->qty;
                            $lineLaba = $lineOmzet - $lineModal;
                            $lineMargin = $lineOmzet > 0 ? round(($lineLaba / $lineOmzet) * 100, 1) : 0;

                            $grandDetailOmzet += $lineOmzet;
                            $grandDetailModal += $lineModal;
                            $grandDetailLaba += $lineLaba;
                            $grandDetailQty += (int) $d->qty;

                            $rowClass = $itemNo % 2 === 0 ? 'class="row-zebra"' : '';
                    ?>
                    <tr <?= $rowClass ?>>
                        <td class="text-center"><?= $itemNo++ ?></td>
                        <td class="text-center"><strong>#<?= $tx->id ?></strong></td>
                        <td class="text-center"><?= $tx->created_at->format('d/m/Y H:i') ?></td>
                        <td><?= htmlspecialchars($tx->user?->name ?? 'Kasir') ?></td>
                        <td><strong><?= htmlspecialchars($p?->name ?? 'Produk') ?></strong></td>
                        <td class="text-center"><?= $d->qty ?> <?= htmlspecialchars($p?->satuan ?? 'pcs') ?></td>
                        <td class="num-right">Rp <?= number_format($unitBuy, 0, ',', '.') ?></td>
                        <td class="num-right">Rp <?= number_format($unitSell, 0, ',', '.') ?></td>
                        <td class="num-right" style="font-weight: bold;">Rp <?= number_format($lineOmzet, 0, ',', '.') ?></td>
                        <td class="num-right">Rp <?= number_format($lineModal, 0, ',', '.') ?></td>
                        <td class="profit-cell">Rp <?= number_format($lineLaba, 0, ',', '.') ?></td>
                        <td class="text-center"><?= $lineMargin ?>%</td>
                        <td class="text-center"><strong><?= htmlspecialchars($tx->payment_method) ?></strong></td>
                    </tr>
                    <?php
                        endforeach;
                    endforeach;
                    ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <th colspan="5" class="text-center">TOTAL KESELURUHAN PENJUALAN PRODUK</th>
                        <th class="text-center"><?= $grandDetailQty ?> pcs</th>
                        <th colspan="2"></th>
                        <th class="num-right">Rp <?= number_format($grandDetailOmzet, 0, ',', '.') ?></th>
                        <th class="num-right">Rp <?= number_format($grandDetailModal, 0, ',', '.') ?></th>
                        <th class="profit-cell" style="background-color: #DBEAFE;">Rp <?= number_format($grandDetailLaba, 0, ',', '.') ?></th>
                        <th class="text-center"><?= $grandDetailOmzet > 0 ? round(($grandDetailLaba / $grandDetailOmzet) * 100, 1) : 0 ?>%</th>
                        <th></th>
                    </tr>
                </tfoot>
            </table>

            <br/>
            <p style="font-size: 9pt; color: #6B7280; text-align: right;">
                Dicetak otomatis oleh Sistem Manajemen Toko Pakde Kuwat · <?= date('Y') ?>
            </p>
        </body>
        </html>
        <?php
        $content = ob_get_clean();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
