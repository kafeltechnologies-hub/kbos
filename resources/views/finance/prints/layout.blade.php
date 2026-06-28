<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'Finance Print')</title>

    <style>
        @page { size: A4; margin: 14mm; }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 0;
            padding-bottom: 155px;
        }

        .no-print { margin-bottom: 12px; }

        .print-btn {
            background: #065f46;
            color: #fff;
            border: 0;
            padding: 8px 18px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
        }

        .company-header {
            display: flex;
            justify-content: space-between;
            border-bottom: 3px solid #0f172a;
            padding-bottom: 14px;
            margin-bottom: 18px;
        }

        .company-left { width: 74%; }
        .company-right { width: 24%; text-align: right; }

        .company-left h1 {
            margin: 0;
            font-size: 26px;
            color: #0f172a;
            text-transform: uppercase;
        }

        .company-tagline {
            margin-top: 4px;
            font-size: 11px;
            color: #047857;
            font-weight: bold;
            text-transform: uppercase;
        }

        .company-info {
            margin-top: 8px;
            font-size: 11px;
            line-height: 18px;
            color: #374151;
        }

        .company-right img {
            max-width: 135px;
            max-height: 85px;
            object-fit: contain;
        }

        .print-content {
            padding-bottom: 170px;
        }

        .approval-bottom {
            position: fixed;
            left: 14mm;
            right: 14mm;
            bottom: 42px;
            background: white;
        }

        .signature-section {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 35px;
        }

        .signature-box { text-align: center; }

        .signature-line {
            border-top: 1px solid #111827;
            margin-bottom: 6px;
        }

        .signature-title {
            font-size: 11px;
            color: #374151;
            margin: 0;
        }

        .signature-name {
            font-size: 12px;
            font-weight: bold;
            margin-top: 3px;
        }

        .signature-date {
            font-size: 10px;
            color: #6b7280;
            margin-top: 4px;
        }

        .footer {
            position: fixed;
            left: 14mm;
            right: 14mm;
            bottom: 12px;
            border-top: 1px solid #d1d5db;
            padding-top: 7px;
            display: grid;
            grid-template-columns: 1fr 1fr 1.4fr;
            gap: 10px;
            font-size: 10px;
            color: #4b5563;
            background: white;
        }

        .footer div:nth-child(2) { text-align: center; }
        .footer div:nth-child(3) { text-align: right; }

        @media print {
            .no-print { display: none; }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>

    @yield('styles')
</head>

<body>
@php
    $companyName = $company->company_name ?? $company->name ?? config('app.name', 'Kafel ERP');
    $postalAddress = $company->postal_address ?? '';
    $physicalAddress = $company->physical_address ?? $company->address ?? '';
    $telephone = $company->telephone ?? $company->phone ?? '';
    $telephone2 = $company->telephone2 ?? $company->mobile ?? '';
    $email = $company->email ?? '';
    $website = $company->website ?? '';
    $taxNumber = $company->tax_number ?? $company->tin ?? $company->tax_identification_number ?? '';
    $vatNumber = $company->vat_number ?? $company->vat_registration_number ?? '';
    $registrarNumber = $company->registrar_number ?? $company->registration_number ?? '';
    $ssnitNumber = $company->ssnit_number ?? '';
    $logo = $company->logo ?? $company->logo_path ?? null;

    $preparedBy = $company->prepared_by ?? $company->default_prepared_by ?? auth()->user()->name ?? 'System User';
    $reviewedBy = $company->reviewed_by ?? $company->default_reviewed_by ?? 'Finance Manager';
    $approvedBy = $company->approved_by ?? $company->default_approved_by ?? 'Managing Director';
@endphp

<div class="no-print">
    <button onclick="window.print()" class="print-btn">Print</button>
</div>

<div class="company-header">
    <div class="company-left">
        <h1>{{ $companyName }}</h1>

        <div class="company-tagline">
            Electrical Contractors | Power Engineering | Renewable Energy | ICT Solutions
        </div>

        <div class="company-info">
            @if($postalAddress) {{ $postalAddress }}<br> @endif
            @if($physicalAddress) {{ $physicalAddress }}<br> @endif

            @if($telephone || $telephone2)
                Tel: {{ $telephone }}
                @if($telephone2) / {{ $telephone2 }} @endif
                <br>
            @endif

            @if($email) Email: {{ $email }}<br> @endif
            @if($website) Website: {{ $website }}<br> @endif
            @if($taxNumber) Tax Identification No: <strong>{{ $taxNumber }}</strong><br> @endif
            @if($vatNumber) VAT Registration No: <strong>{{ $vatNumber }}</strong><br> @endif
            @if($registrarNumber) Registrar General No: <strong>{{ $registrarNumber }}</strong><br> @endif
            @if($ssnitNumber) SSNIT Employer No: <strong>{{ $ssnitNumber }}</strong><br> @endif
        </div>
    </div>

    <div class="company-right">
        @if($logo)
            <img src="{{ asset('storage/'.$logo) }}" alt="Company Logo">
        @endif
    </div>
</div>

<div class="print-content">
    @yield('content')
</div>

<div class="approval-bottom">
    <div class="signature-section">
        <div class="signature-box">
            <div class="signature-line"></div>
            <p class="signature-title">Prepared By</p>
            <div class="signature-name">{{ $preparedBy }}</div>
            <div class="signature-date">Date: __________________</div>
        </div>

        <div class="signature-box">
            <div class="signature-line"></div>
            <p class="signature-title">Reviewed By</p>
            <div class="signature-name">{{ $reviewedBy }}</div>
            <div class="signature-date">Date: __________________</div>
        </div>

        <div class="signature-box">
            <div class="signature-line"></div>
            <p class="signature-title">Approved By</p>
            <div class="signature-name">{{ $approvedBy }}</div>
            <div class="signature-date">Date: __________________</div>
        </div>
    </div>
</div>

<div class="footer">
    <div>Printed By: <strong>{{ auth()->user()->name ?? 'System User' }}</strong></div>
    <div>Printed On: <strong>{{ now()->format('d M Y H:i') }}</strong></div>
    <div>{{ $company->report_footer ?? 'Generated from Kafel ERP Finance Operations Centre' }}</div>
</div>

</body>
</html>