<!DOCTYPE html>
<html>
<head>
    <title>{{ $voucherTitle ?? 'MATERIAL RECEIPT VOUCHER' }}</title>
    <style>
        body{font-family:Arial,sans-serif;margin:28px;font-size:10px;color:#111827}
        table{width:100%;border-collapse:collapse}
        th,td{border:1px solid #cbd5e1;padding:6px}
        th{background:#f1f5f9}
        .header{text-align:center;border-bottom:2px solid #111827;padding-bottom:10px;margin-bottom:14px}
        .title{font-size:16px;font-weight:bold}
        .meta td{border:none;padding:3px}
        .footer{position:fixed;bottom:20px;left:28px;right:28px}
        @media print{button{display:none}.footer{position:fixed}}
    </style>
</head>
<body>
@php
    $voucherTitle = match($transaction->transaction_type) {
        'receive' => 'GOODS RECEIVED NOTE',
        'purchase_resale' => 'STOCK PURCHASE RECEIPT',
        'return_project' => 'PROJECT MATERIAL RETURN VOUCHER',
        'return_account' => 'BORROWED STOCK RETURN VOUCHER',
        default => 'MATERIAL RECEIPT VOUCHER',
    };
@endphp

<div class="header">
    <div class="title">{{ $company?->name ?? config('app.name') }}</div>
    <div>{{ $company?->address ?? '' }}</div>
    <h2>{{ $voucherTitle }}</h2>
</div>

<table class="meta">
    <tr>
        <td><strong>Voucher No:</strong> {{ $transaction->transaction_no }}</td>
        <td><strong>Date:</strong> {{ $transaction->transaction_date?->format('d M Y') }}</td>
        <td><strong>Status:</strong> {{ strtoupper($transaction->status ?? '-') }}</td>
    </tr>
    <tr>
        <td colspan="3">
            @if($transaction->transaction_type === 'return_project')
                <strong>Project Returning Stock:</strong> {{ $transaction->fromProject?->project_name }}
            @elseif($transaction->transaction_type === 'return_account')
                <strong>Borrower:</strong> {{ $transaction->account_holder_name }} |
                <strong>Phone:</strong> {{ $transaction->account_holder_phone }}
            @elseif($transaction->transaction_type === 'purchase_resale')
                <strong>Payment Voucher:</strong> {{ $transaction->paymentVoucher?->voucher_no ?? $transaction->payment_voucher_id }}
            @else
                <strong>Project:</strong> {{ $transaction->project?->project_name ?? 'General Stock' }}
            @endif
        </td>
    </tr>
    <tr><td colspan="3"><strong>Reference:</strong> {{ $transaction->reference ?? '-' }}</td></tr>
    <tr><td colspan="3"><strong>Remarks:</strong> {{ $transaction->remarks ?? '-' }}</td></tr>
</table>

<br>

<table>
    <thead>
    <tr>
        <th>Code</th>
        <th>Material</th>
        <th>Unit</th>
        <th style="text-align:right">Qty</th>
        <th style="text-align:right">Unit Cost</th>
        <th style="text-align:right">Total</th>
    </tr>
    </thead>
    <tbody>
    @foreach($transaction->lines as $line)
        <tr>
            <td>{{ $line->material?->material_code }}</td>
            <td>{{ $line->material?->name }}</td>
            <td>{{ $line->material?->unit }}</td>
            <td style="text-align:right">{{ number_format((float)$line->quantity,2) }}</td>
            <td style="text-align:right">{{ number_format((float)$line->unit_cost,2) }}</td>
            <td style="text-align:right">{{ number_format((float)$line->line_total,2) }}</td>
        </tr>
    @endforeach
    </tbody>
    <tfoot>
    <tr>
        <th colspan="5" style="text-align:right">Total Value</th>
        <th style="text-align:right">{{ number_format((float)$transaction->lines->sum('line_total'),2) }}</th>
    </tr>
    </tfoot>
</table>

<div class="footer">
    <table>
        <tr>
            <td>Prepared By<br><br>__________________</td>
            <td>Checked By<br><br>__________________</td>
            <td>Approved By<br><br>__________________</td>
            <td>Received By<br><br>__________________</td>
        </tr>
    </table>
</div>

@if(request('print'))<script>window.onload=function(){window.print()}</script>@endif
</body>
</html>
