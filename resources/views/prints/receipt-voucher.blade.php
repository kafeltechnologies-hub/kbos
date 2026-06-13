<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Receipt Voucher - {{ $voucher->receipt_number }}</title>

    <style>
        @page { size: A4; margin: 15mm; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
        }

        .header {
            border-bottom: 3px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 18px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company { font-size: 22px; font-weight: 800; }
        .muted { color: #6b7280; font-size: 11px; margin-top: 4px; }

        .badge {
            border: 1px solid #111827;
            padding: 8px 14px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 12px;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: 800;
            margin: 18px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; vertical-align: top; }
        th { background: #f3f4f6; text-align: left; width: 22%; }

        .amount {
            font-size: 20px;
            font-weight: 800;
            color: #047857;
        }

        .words {
            font-weight: 800;
            color: #065f46;
            line-height: 1.6;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 22px;
            margin-top: 60px;
        }

        .signature {
            border-top: 1px solid #111827;
            padding-top: 8px;
            text-align: center;
            font-size: 12px;
            min-height: 45px;
        }

        .footer {
            margin-top: 35px;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
            text-align: center;
        }

        .print-btn {
            margin-bottom: 20px;
            padding: 8px 14px;
            background: #111827;
            color: white;
            border: 0;
            cursor: pointer;
        }

        @media print {
            .print-btn { display: none; }
        }
    </style>
</head>

<body>

<button class="print-btn" onclick="window.print()">Print Receipt</button>

<div class="header">
    <div>
        <div class="company">
            {{ $voucher->project?->company?->name ?? config('app.company_name', 'Company Name') }}
        </div>
        <div class="muted">Official Finance Receipt Voucher</div>
        <div class="muted">Electrical Engineering • ICT • Projects • Operations</div>
    </div>

    <div class="badge">Receipt Voucher</div>
</div>

<div class="title">Official Receipt Voucher</div>

<table>
    <tr>
        <th>Receipt No.</th>
        <td>{{ $voucher->receipt_number }}</td>
        <th>Date</th>
        <td>{{ $voucher->receipt_date }}</td>
    </tr>

    <tr>
        <th>Payer</th>
        <td>{{ $voucher->payer_name }}</td>
        <th>Method</th>
        <td>{{ $voucher->receipt_method }}</td>
    </tr>

    <tr>
        <th>Reference No.</th>
        <td>{{ $voucher->reference_no }}</td>
        <th>Status</th>
        <td>{{ strtoupper($voucher->status) }}</td>
    </tr>

    <tr>
        <th>Receipt Type</th>
        <td>{{ ucwords(str_replace('_', ' ', $voucher->receipt_type)) }}</td>
        <th>Income Category</th>
        <td>{{ $voucher->category?->name ?? '-' }}</td>
    </tr>

    @if($voucher->project)
        <tr>
            <th>Project</th>
            <td colspan="3">{{ $voucher->project->project_name }}</td>
        </tr>
    @endif

    <tr>
        <th>Narration</th>
        <td colspan="3">{{ $voucher->narration }}</td>
    </tr>
</table>

<table>
    @if($voucher->receipt_type === 'project_receipt')
        <tr>
            <th>Project Contract Value</th>
            <td>GHS {{ number_format((float) $voucher->project_value, 2) }}</td>
        </tr>

        <tr>
            <th>Previous Receipts</th>
            <td>GHS {{ number_format((float) $voucher->previous_receipts, 2) }}</td>
        </tr>

        <tr>
            <th>Outstanding Before Receipt</th>
            <td>GHS {{ number_format((float) $voucher->outstanding_before_receipt, 2) }}</td>
        </tr>
    @endif

    <tr>
        <th>Amount Received</th>
        <td class="amount">GHS {{ number_format((float) $voucher->amount_received, 2) }}</td>
    </tr>

    <tr>
        <th>Amount In Words</th>
        <td class="words">{{ $voucher->amount_in_words }}</td>
    </tr>

    @if($voucher->receipt_type === 'project_receipt')
        <tr>
            <th>Balance After Receipt</th>
            <td>GHS {{ number_format((float) $voucher->balance_after_receipt, 2) }}</td>
        </tr>
    @endif
</table>

<div class="signature-grid">
    <div class="signature">
        Prepared By<br>
        {{ $voucher->prepared_by ?: '________________' }}
    </div>

    <div class="signature">
        Checked By<br>
        {{ $voucher->checked_by ?: '________________' }}
    </div>

    <div class="signature">
        Approved By<br>
        {{ $voucher->approved_by ?: '________________' }}
    </div>

    <div class="signature">
        Received By<br>
        {{ $voucher->received_by ?: '________________' }}
    </div>
</div>

<div class="footer">
    Generated from Kafel ERP on {{ now()->format('d M Y H:i') }}.
</div>

<script>
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>