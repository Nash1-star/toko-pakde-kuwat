<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Transaksi #{{ $transaction->id }} · Toko Pakde Kuwat</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', system-ui, sans-serif;
            max-width: 480px;
            margin: 32px auto;
            padding: 24px;
            color: #111827;
            background: #FFFFFF;
            border: 1px solid #E4E8F0;
            border-radius: 16px;
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #CDD4E0;
            padding-bottom: 16px;
            margin-bottom: 16px;
        }
        .header h1 {
            margin: 0 0 4px;
            font-size: 20px;
            font-weight: 800;
        }
        .meta {
            font-size: 13px;
            color: #5B6475;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            margin: 16px 0;
        }
        th {
            text-align: left;
            padding: 8px 0;
            border-bottom: 1px solid #E4E8F0;
            color: #6B7280;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 11px;
        }
        td {
            padding: 8px 0;
            border-bottom: 1px solid #F1F3F7;
        }
        .right {
            text-align: right;
        }
        .total-row th, .total-row td {
            font-size: 15px;
            font-weight: 800;
            border-top: 1px dashed #CDD4E0;
            border-bottom: 0;
            padding-top: 12px;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #5B6475;
            border-top: 1px dashed #CDD4E0;
            padding-top: 16px;
            margin-top: 16px;
        }
        .actions {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .btn-print {
            padding: 8px 16px;
            background: #2563EB;
            color: #FFFFFF;
            border: 0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
        }
        .btn-close {
            padding: 8px 16px;
            background: #EEF1F6;
            color: #374151;
            border: 0;
            border-radius: 8px;
            font-family: inherit;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
        }
        @media print {
            body {
                border: 0;
                margin: 0;
                padding: 0;
                max-width: 100%;
            }
            .actions {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="actions">
        <a href="javascript:window.close()" class="btn-close">Tutup</a>
        <button onclick="window.print()" class="btn-print">Cetak Nota</button>
    </div>

    <div class="header">
        <h1>Toko Pakde Kuwat</h1>
        <div class="meta">
            Nota #{{ $transaction->id }} · {{ $transaction->created_at->format('d/m/Y H:i') }}<br>
            Penjaga: <strong>{{ $transaction->user?->name ?? 'Kasir' }}</strong> · Metode: <strong>{{ $transaction->payment_method }}</strong>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Barang</th>
                <th>Qty</th>
                <th class="right">Harga</th>
                <th class="right">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->details as $detail)
                <tr>
                    <td><strong>{{ $detail->product?->name ?? 'Produk' }}</strong></td>
                    <td>{{ $detail->qty }}</td>
                    <td class="right">Rp {{ number_format($detail->price, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <th colspan="3" class="right">Total</th>
                <th class="right">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</th>
            </tr>
            @if($transaction->payment_method === 'Tunai')
                <tr>
                    <td colspan="3" class="right" style="color: #5B6475;">Uang diterima</td>
                    <td class="right">Rp {{ number_format($transaction->cash_received, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td colspan="3" class="right" style="color: #5B6475;">Kembalian</td>
                    <td class="right"><strong>Rp {{ number_format($transaction->change_given, 0, ',', '.') }}</strong></td>
                </tr>
            @endif
        </tfoot>
    </table>

    <div class="footer">
        Terima kasih telah berbelanja di Toko Pakde Kuwat!<br>
        Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan.
    </div>
</body>
</html>
