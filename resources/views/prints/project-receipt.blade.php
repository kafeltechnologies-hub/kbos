<!DOCTYPE html>
<html>
<head>
    <title>Receipt Voucher - {{ $receipt->receipt_number }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            color: #111827;
            margin: 40px;
            font-size: 13px;
        }

        .print-btn {
            margin-bottom: 20px;
            padding: 8px 14px;
            border: 1px solid #111827;
            background: #111827;
            color: white;
            cursor: pointer;
        }

        .header {
            border-bottom: 3px solid #111827;
            padding-bottom: 12px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .company {
            font-size: 22px;
            font-weight: 800;
        }

        .muted {
            color: #6b7280;
            font-size: 12px;
            margin-top: 4px;
        }

        .document-badge {
            border: 1px solid #111827;
            padding: 8px 14px;
            font-weight: 800;
            text-transform: uppercase;
            font-size: 12px;
        }

        .title {
            text-align: center;
            font-size: 19px;
            font-weight: 800;
            margin: 24px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 18px;
        }

        td, th {
            border: 1px solid #d1d5db;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background: #f3f4f6;
            text-align: left;
            width: 24%;
        }

        .amount {
            font-size: 20px;
            font-weight: 800;
            color: #065f46;
        }

        .words {
            font-size: 14px;
            font-weight: 800;
            color: #064e3b;
            line-height: 1.6;
        }

        .status {
            font-weight: 800;
            text-transform: uppercase;
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
            font-size: 11px;
            color: #6b7280;
            border-top: 1px solid #d1d5db;
            padding-top: 10px;
        }

        @media print {
            .print-btn {
                display: none;
            }

            body {
                margin: 20px;
            }
        }
    </style>
</head>

<body>

    <button class="print-btn" onclick="window.print()">Print Receipt</button>

    <div class="header">

    <div style="display:flex;align-items:center;gap:20px">

        @if($receipt->project?->company?->logo)
            <img
                src="{{ asset('storage/'.$receipt->project->company->logo) }}"
                style="height:80px;width:auto;">
        @endif

        <div>
            <div class="company">
                {{ $receipt->project?->company?->name }}
            </div>

            <div class="muted">
                {{ $receipt->project?->company?->address }}
            </div>

            <div class="muted">
                {{ $receipt->project?->company?->phone }}
            </div>

            <div class="muted">
                {{ $receipt->project?->company?->email }}
            </div>
        </div>

    </div>

    <div class="document-badge">
        RECEIPT VOUCHER
    </div>

</div>

    <div class="title">Official Receipt Voucher</div>

    <table>
        <tr>
            <th>Receipt No.</th>
            <td>{{ $receipt->receipt_number }}</td>
            <th>Date Received</th>
            <td>{{ $receipt->date_received }}</td>
        </tr>

        <tr>
            <th>Receipt Code</th>
            <td>{{ $receipt->receipt_code }}</td>
            <th>Status</th>
            <td class="status">{{ strtoupper($receipt->status) }}</td>
        </tr>

        <tr>
            <th>Project</th>
            <td>{{ $receipt->project?->project_name }}</td>
            <th>Project Code</th>
            <td>{{ $receipt->project?->project_code }}</td>
        </tr>

        <tr>
            <th>Received From</th>
            <td>{{ $receipt->received_from }}</td>
            <th>Payer Phone</th>
            <td>{{ $receipt->payer_phone }}</td>
        </tr>

        <tr>
            <th>Payer TIN</th>
            <td>{{ $receipt->payer_tin }}</td>
            <th>Receipt Method</th>
            <td>{{ $receipt->receipt_method }}</td>
        </tr>

        <tr>
            <th>Transaction Reference</th>
            <td colspan="3">{{ $receipt->transaction_reference }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Contract Value</th>
            <td>GHS {{ number_format((float) $receipt->contract_value, 2) }}</td>
        </tr>

        <tr>
            <th>Total Received Before</th>
            <td>GHS {{ number_format((float) $receipt->total_received_before, 2) }}</td>
        </tr>

        <tr>
            <th>Amount Received</th>
            <td class="amount">GHS {{ number_format((float) $receipt->amount_received, 2) }}</td>
        </tr>

        <tr>
            <th>Amount In Words</th>
            <td class="words">{{ $receipt->amount_in_words }}</td>
        </tr>

        <tr>
            <th>Outstanding Balance</th>
            <td>GHS {{ number_format((float) $receipt->outstanding_balance, 2) }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Receipt Narration</th>
            <td>{{ $receipt->receipt_narration }}</td>
        </tr>

        <tr>
            <th>Remarks</th>
            <td>{{ $receipt->remarks }}</td>
        </tr>
    </table>

    <div class="signature-grid">
        <div class="signature">
            Prepared By<br>
            {{ $receipt->prepared_by ?: '________________' }}
        </div>

        <div class="signature">
            Approved By<br>
            {{ $receipt->approved_by ?: '________________' }}
        </div>

        <div class="signature">
            Received / Confirmed By<br>
            __________________
        </div>

        <div class="signature">
            Official Stamp<br>
            __________________
        </div>
    </div>

    <div class="footer">
        This receipt was generated from Kafel ERP. It is valid only when supported by the appropriate approval and payment reference.
    </div>

</body>
</html>