<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Voucher</title>

```
<style>

    @page {
        size: A4;
        margin: 15mm;
    }

    body{
        font-family: Arial, Helvetica, sans-serif;
        font-size:12px;
        color:#000;
        margin:0;
        padding:0;
    }

    .container{
        width:100%;
    }

    .header{
        text-align:center;
        border-bottom:2px solid #000;
        padding-bottom:10px;
        margin-bottom:20px;
    }

    .company-name{
        font-size:24px;
        font-weight:bold;
    }

    .company-subtitle{
        font-size:12px;
        margin-top:4px;
    }

    .voucher-title{
        text-align:center;
        font-size:18px;
        font-weight:bold;
        margin:15px 0;
        border:1px solid #000;
        padding:8px;
    }

    table{
        width:100%;
        border-collapse:collapse;
    }

    .details td{
        padding:6px;
        border:1px solid #ddd;
    }

    .label{
        font-weight:bold;
        width:220px;
        background:#f5f5f5;
    }

    .amount-box{
        margin-top:20px;
    }

    .amount-box th{
        background:#e5e7eb;
        border:1px solid #000;
        padding:8px;
        text-align:left;
    }

    .amount-box td{
        border:1px solid #000;
        padding:8px;
    }

    .totals{
        font-weight:bold;
        background:#f3f4f6;
    }

    .approval{
        margin-top:60px;
    }

    .approval td{
        width:33%;
        text-align:center;
        padding-top:50px;
    }

    .line{
        border-top:1px solid #000;
        width:90%;
        margin:auto;
        padding-top:5px;
    }

    .footer{
        margin-top:30px;
        text-align:center;
        font-size:10px;
        color:#555;
    }

    @media print{
        .no-print{
            display:none;
        }
    }

</style>
```

</head>
<body>

<div class="container">

```
<div class="header">

    <div class="company-name">
        {{ config('app.company_name', 'BRILLIANT & CO. LTD') }}
    </div>

    <div class="company-subtitle">
        Electrical Engineering | Power Systems | Renewable Energy | ICT Solutions
    </div>

</div>

<div class="voucher-title">
    PAYMENT VOUCHER
</div>

<table class="details">

    <tr>
        <td class="label">Voucher Number</td>
        <td>{{ $voucher->voucher_number }}</td>

        <td class="label">Voucher Date</td>
        <td>{{ $voucher->voucher_date }}</td>
    </tr>

    <tr>
        <td class="label">Payee</td>
        <td>{{ $voucher->payee_name }}</td>

        <td class="label">Payment Method</td>
        <td>{{ $voucher->payment_method }}</td>
    </tr>

    <tr>
        <td class="label">Reference Number</td>
        <td>{{ $voucher->reference_no }}</td>

        <td class="label">Status</td>
        <td>{{ strtoupper($voucher->status) }}</td>
    </tr>

    <tr>
        <td class="label">Payment Type</td>
        <td>
            {{ ucwords(str_replace('_',' ',$voucher->payment_type)) }}
        </td>

        <td class="label">Expense Category</td>
        <td>
            {{ $voucher->category->name ?? '-' }}
        </td>
    </tr>

    @if($voucher->project)
    <tr>
        <td class="label">Project</td>
        <td colspan="3">
            {{ $voucher->project->project_name }}
        </td>
    </tr>
    @endif

    <tr>
        <td class="label">Narration</td>
        <td colspan="3">
            {{ $voucher->narration }}
        </td>
    </tr>

</table>

<table class="amount-box">

    <thead>
    <tr>
        <th>Description</th>
        <th style="width:200px;">Amount (GHS)</th>
    </tr>
    </thead>

    <tbody>

    <tr>
        <td>Amount To Pay</td>
        <td>{{ number_format($voucher->gross_amount,2) }}</td>
    </tr>

    <tr>
        <td>VAT (15%)</td>
        <td>{{ number_format($voucher->vat_amount,2) }}</td>
    </tr>

    <tr>
        <td>GETFund (2.5%)</td>
        <td>{{ number_format($voucher->getfund_amount,2) }}</td>
    </tr>

    <tr>
        <td>NHIL (2.5%)</td>
        <td>{{ number_format($voucher->nhil_amount,2) }}</td>
    </tr>

    <tr>
        <td>Withholding Tax</td>
        <td>
            ({{ number_format($voucher->withholding_tax,2) }})
        </td>
    </tr>

    <tr class="totals">
        <td>NET PAYMENT</td>
        <td>{{ number_format($voucher->net_payment,2) }}</td>
    </tr>

    </tbody>

</table>

<div style="margin-top:20px;">

    <strong>Amount in Words:</strong>

    <br><br>

    {{ $voucher->amount_in_words ?? '_______________________________' }}

</div>

<table class="approval">

    <tr>
        <td>
            <div class="line">
                Prepared By
                <br>
                {{ $voucher->prepared_by }}
            </div>
        </td>

        <td>
            <div class="line">
                Approved By
                <br>
                {{ $voucher->approved_by }}
            </div>
        </td>

        <td>
            <div class="line">
                Received By
            </div>
        </td>
    </tr>

</table>

<div class="footer">
    Generated on {{ now()->format('d M Y H:i') }}
</div>
```

</div>

<script>
window.onload = function () {
    window.print();
};
</script>

</body>
</html>
