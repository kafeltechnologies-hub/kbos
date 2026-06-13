<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice - {{ $invoice->invoice_number }}</title>

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
        th { background: #f3f4f6; text-align: left; }

        .items th { background: #111827; color: white; }
        .right { text-align: right; }
        .center { text-align: center; }

        .summary {
            width: 45%;
            margin-left: auto;
        }

        .amount {
            font-size: 18px;
            font-weight: 800;
            color: #3730a3;
        }

        .words {
            font-weight: 800;
            color: #312e81;
            line-height: 1.6;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
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

<button class="print-btn" onclick="window.print()">Print Invoice</button>

<div class="header">
    <div>
        <div class="company">
            {{ $invoice->company?->name ?? $invoice->project?->company?->name ?? config('app.company_name', 'Company Name') }}
        </div>
        <div class="muted">Official Client Invoice</div>
        <div class="muted">Electrical Engineering • ICT • Projects • Operations</div>
    </div>

    <div class="badge">Invoice</div>
</div>

<div class="title">Official Invoice</div>

<table>
    <tr>
        <th>Invoice No.</th>
        <td>{{ $invoice->invoice_number }}</td>
        <th>Invoice Date</th>
        <td>{{ $invoice->invoice_date }}</td>
    </tr>

    <tr>
        <th>Due Date</th>
        <td>{{ $invoice->due_date }}</td>
        <th>Status</th>
        <td>{{ strtoupper($invoice->status) }}</td>
    </tr>

    <tr>
        <th>Client</th>
        <td>{{ $invoice->client_name }}</td>
        <th>Phone</th>
        <td>{{ $invoice->client_phone }}</td>
    </tr>

    <tr>
        <th>Email</th>
        <td>{{ $invoice->client_email }}</td>
        <th>TIN</th>
        <td>{{ $invoice->client_tin }}</td>
    </tr>

    <tr>
        <th>Address</th>
        <td colspan="3">{{ $invoice->client_address }}</td>
    </tr>

    <tr>
        <th>Project</th>
        <td colspan="3">{{ $invoice->project_title }}</td>
    </tr>

    <tr>
        <th>Scope of Work</th>
        <td colspan="3">{{ $invoice->scope_of_work }}</td>
    </tr>
</table>

<table class="items">
    <thead>
        <tr>
            <th style="width:5%;">#</th>
            <th style="width:13%;">Code</th>
            <th>Description</th>
            <th style="width:10%;">Unit</th>
            <th style="width:10%;" class="right">Qty</th>
            <th style="width:14%;" class="right">Unit Price</th>
            <th style="width:14%;" class="right">Line Total</th>
        </tr>
    </thead>

    <tbody>
        @foreach($invoice->items as $index => $item)
            <tr>
                <td class="center">{{ $index + 1 }}</td>
                <td>{{ $item->item_code }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->unit }}</td>
                <td class="right">{{ number_format((float) $item->quantity, 2) }}</td>
                <td class="right">{{ number_format((float) $item->unit_price, 2) }}</td>
                <td class="right">{{ number_format((float) $item->line_total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="summary">
    <tr>
        <th>Subtotal</th>
        <td class="right">GHS {{ number_format((float) $invoice->subtotal, 2) }}</td>
    </tr>

    <tr>
        <th>Labor Charge</th>
        <td class="right">GHS {{ number_format((float) $invoice->labor_charge, 2) }}</td>
    </tr>

    <tr>
        <th>Transport Charge</th>
        <td class="right">GHS {{ number_format((float) $invoice->transport_charge, 2) }}</td>
    </tr>

    <tr>
        <th>
            Other Charges
            @if($invoice->other_charges_description)
                <br><small>{{ $invoice->other_charges_description }}</small>
            @endif
        </th>
        <td class="right">GHS {{ number_format((float) $invoice->other_charges, 2) }}</td>
    </tr>

    <tr>
        <th>VAT 15%</th>
        <td class="right">GHS {{ number_format((float) $invoice->vat_amount, 2) }}</td>
    </tr>

    <tr>
        <th>GETFund 2.5%</th>
        <td class="right">GHS {{ number_format((float) $invoice->getfund_amount, 2) }}</td>
    </tr>

    <tr>
        <th>NHIL 2.5%</th>
        <td class="right">GHS {{ number_format((float) $invoice->nhil_amount, 2) }}</td>
    </tr>

    <tr>
        <th>Grand Total</th>
        <td class="right amount">GHS {{ number_format((float) $invoice->grand_total, 2) }}</td>
    </tr>
</table>

<table>
    <tr>
        <th>Amount In Words</th>
        <td class="words">{{ $invoice->amount_in_words }}</td>
    </tr>

    <tr>
        <th>Terms & Conditions</th>
        <td>{{ $invoice->terms_and_conditions }}</td>
    </tr>

    <tr>
        <th>Notes</th>
        <td>{{ $invoice->notes }}</td>
    </tr>
</table>

<div class="signature-grid">
    <div class="signature">
        Prepared By<br>
        {{ $invoice->prepared_by ?: '________________' }}
    </div>

    <div class="signature">
        Checked By<br>
        {{ $invoice->checked_by ?: '________________' }}
    </div>

    <div class="signature">
        Approved By<br>
        {{ $invoice->approved_by ?: '________________' }}
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