<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Goods Receipt Note - {{ $transaction->transaction_no }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #0f172a;
            margin: 20px;
            position: relative;
            min-height: 100vh;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #e2e8f0;
            font-size: 9px;
            text-transform: uppercase;
        }

        .header {
            border-bottom: 3px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company {
            font-size: 17px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .muted {
            color: #64748b;
        }

        .title {
            margin-top: 8px;
            font-size: 13px;
            font-weight: 900;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }

        .box {
            border: 1px solid #cbd5e1;
            padding: 8px;
        }

        .label {
            font-size: 9px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
        }

        .value {
            margin-top: 3px;
            font-weight: 700;
        }

        .money,
        .qty {
            text-align: right;
            font-family: monospace;
        }

        .footer-space {
            height: 120px;
        }

        .signatures {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
            background: white;
        }

        .signature {
            border-top: 1px solid #0f172a;
            padding-top: 6px;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
        }

        .signature small {
            display: block;
            margin-top: 3px;
            font-weight: normal;
            color: #64748b;
        }

        .no-print {
            margin-bottom: 15px;
        }

        button {
            background: #0f172a;
            color: white;
            border: 0;
            padding: 8px 14px;
            font-weight: 700;
            cursor: pointer;
            font-size: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 18px;
            }

            .signatures {
                bottom: 18px;
                left: 18px;
                right: 18px;
            }
        }
    </style>
</head>

<body>

<div class="no-print">
    <button onclick="window.print()">Print Goods Receipt Note</button>
</div>

<div class="header">
    <div class="company">
        {{ $transaction->project?->company?->name ?? 'Company Name' }}
    </div>

    <div class="muted">
        {{ $transaction->project?->company?->address ?? 'Company Address' }}<br>
        Tel: {{ $transaction->project?->company?->phone ?? '-' }}
        |
        Email: {{ $transaction->project?->company?->email ?? '-' }}
    </div>

    <div class="title">GOODS RECEIPT NOTE</div>
</div>

<div class="grid">
    <div class="box">
        <div class="label">GRN No.</div>
        <div class="value">{{ $transaction->transaction_no }}</div>
    </div>

    <div class="box">
        <div class="label">Date</div>
        <div class="value">{{ $transaction->transaction_date?->format('d M Y') }}</div>
    </div>

    <div class="box">
        <div class="label">Reference</div>
        <div class="value">{{ $transaction->reference ?? '-' }}</div>
    </div>

    <div class="box">
        <div class="label">Status</div>
        <div class="value">{{ strtoupper($transaction->status) }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Material Description</th>
            <th>Unit</th>
            <th>Qty Received</th>
            <th>Unit Cost</th>
            <th>Total Cost</th>
        </tr>
    </thead>

    <tbody>
        @foreach($transaction->lines as $line)
            <tr>
                <td>{{ $line->material?->material_code }}</td>
                <td>
                    <strong>{{ $line->material?->name }}</strong><br>
                    <span class="muted">{{ $line->material?->description }}</span>
                </td>
                <td>{{ $line->material?->unit }}</td>
                <td class="qty">{{ number_format((float) $line->quantity, 2) }}</td>
                <td class="money">{{ number_format((float) $line->unit_cost, 2) }}</td>
                <td class="money">{{ number_format((float) $line->line_total, 2) }}</td>
            </tr>
        @endforeach

        <tr>
            <th colspan="5" style="text-align:right;">Total</th>
            <th class="money">{{ number_format((float) $transaction->lines->sum('line_total'), 2) }}</th>
        </tr>
    </tbody>
</table>

<p>
    <strong>Remarks:</strong>
    {{ $transaction->remarks ?? '-' }}
</p>

<div class="footer-space"></div>

<div class="signatures">
    <div class="signature">
        Received By
        <small>Storekeeper</small>
    </div>

    <div class="signature">
        Checked By
        <small>Inventory Controller</small>
    </div>

    <div class="signature">
        Approved By
        <small>Management</small>
    </div>
</div>

</body>
</html>