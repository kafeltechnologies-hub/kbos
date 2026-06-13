<!DOCTYPE html>
<html>
<head>
    <title>Quotation - {{ $quotation->quotation_number }}</title>

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
        }

        .items th {
            background: #111827;
            color: white;
        }

        .right {
            text-align: right;
        }

        .center {
            text-align: center;
        }

        .amount {
            font-size: 18px;
            font-weight: 800;
            color: #1d4ed8;
        }

        .words {
            font-size: 14px;
            font-weight: 800;
            color: #1e3a8a;
            line-height: 1.6;
        }

        .status {
            font-weight: 800;
            text-transform: uppercase;
        }

        .summary {
            width: 45%;
            margin-left: auto;
        }

        .signature-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
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

    <button class="print-btn" onclick="window.print()">Print Quotation</button>

    <div class="header">
        <div style="display:flex; align-items:center; gap:18px;">

            @if($quotation->company?->logo)
                <img
                    src="{{ asset('storage/' . $quotation->company->logo) }}"
                    style="height:75px; width:auto;">
            @endif

            <div>
                <div class="company">
                    {{ $quotation->company?->name ?? 'Company Name' }}
                </div>

                @if($quotation->company?->address)
                    <div class="muted">
                        {{ $quotation->company->address }}
                    </div>
                @endif

                @if($quotation->company?->phone)
                    <div class="muted">
                        Tel: {{ $quotation->company->phone }}
                    </div>
                @endif

                @if($quotation->company?->email)
                    <div class="muted">
                        Email: {{ $quotation->company->email }}
                    </div>
                @endif

                @if($quotation->company?->tin_number)
                    <div class="muted">
                        TIN: {{ $quotation->company->tin_number }}
                    </div>
                @endif
            </div>
        </div>

        <div class="document-badge">
            Quotation
        </div>
    </div>

    <div class="title">Official Project Quotation</div>

    <table>
        <tr>
            <th>Quotation No.</th>
            <td>{{ $quotation->quotation_number }}</td>
            <th>Quotation Date</th>
            <td>{{ $quotation->quotation_date }}</td>
        </tr>

        <tr>
            <th>Quotation Code</th>
            <td>{{ $quotation->quotation_code }}</td>
            <th>Valid Until</th>
            <td>{{ $quotation->valid_until }}</td>
        </tr>

        <tr>
            <th>Status</th>
            <td class="status">{{ strtoupper($quotation->status) }}</td>
            <th>Project</th>
            <td>{{ $quotation->project_title }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Client Name</th>
            <td>{{ $quotation->client_name }}</td>
            <th>Client Phone</th>
            <td>{{ $quotation->client_phone }}</td>
        </tr>

        <tr>
            <th>Client Email</th>
            <td>{{ $quotation->client_email }}</td>
            <th>Client TIN</th>
            <td>{{ $quotation->client_tin }}</td>
        </tr>

        <tr>
            <th>Client Address</th>
            <td colspan="3">{{ $quotation->client_address }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Scope of Work</th>
        </tr>

        <tr>
            <td>{{ $quotation->scope_of_work }}</td>
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
            @foreach($quotation->items as $index => $item)
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
            <td class="right">GHS {{ number_format((float) $quotation->subtotal, 2) }}</td>
        </tr>

        <tr>
            <th>VAT 15%</th>
            <td class="right">GHS {{ number_format((float) $quotation->vat_amount, 2) }}</td>
        </tr>

        <tr>
            <th>GETFund 2.5%</th>
            <td class="right">GHS {{ number_format((float) $quotation->getfund_amount, 2) }}</td>
        </tr>

        <tr>
            <th>NHIL 2.5%</th>
            <td class="right">GHS {{ number_format((float) $quotation->nhil_amount, 2) }}</td>
        </tr>

        <tr>
            <th>Grand Total</th>
            <td class="right amount">GHS {{ number_format((float) $quotation->grand_total, 2) }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Amount In Words</th>
            <td class="words">{{ $quotation->amount_in_words }}</td>
        </tr>
    </table>

    <table>
        <tr>
            <th>Terms & Conditions</th>
        </tr>

        <tr>
            <td>{{ $quotation->terms_and_conditions }}</td>
        </tr>

        <tr>
            <th>Notes</th>
        </tr>

        <tr>
            <td>{{ $quotation->notes }}</td>
        </tr>
    </table>

    <div class="signature-grid">
        <div class="signature">
            Prepared By<br>
            {{ $quotation->prepared_by ?: '________________' }}
        </div>

        <div class="signature">
            Approved By<br>
            {{ $quotation->approved_by ?: '________________' }}
        </div>

        <div class="signature">
            Client Acceptance / Signature<br>
            __________________
        </div>
    </div>

    <div class="footer">
        This quotation was generated from Kafel ERP. It is subject to the stated validity period, terms, and company approval.
    </div>

</body>
</html>