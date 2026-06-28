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
    $logo = $company->logo ?? $company->logo_path ?? null;
@endphp

<div class="company-header">
    <div class="company-left">
        <h1>{{ $companyName }}</h1>

        <div class="company-info">
            @if($postalAddress)
                {{ $postalAddress }}<br>
            @endif

            @if($physicalAddress)
                {{ $physicalAddress }}<br>
            @endif

            @if($telephone || $telephone2)
                Tel: {{ $telephone }}
                @if($telephone2)
                    / {{ $telephone2 }}
                @endif
                <br>
            @endif

            @if($email)
                Email: {{ $email }}<br>
            @endif

            @if($website)
                Website: {{ $website }}<br>
            @endif

            @if($taxNumber)
                Tax Identification No: <strong>{{ $taxNumber }}</strong><br>
            @endif

            @if($vatNumber)
                VAT Registration No: <strong>{{ $vatNumber }}</strong><br>
            @endif
        </div>
    </div>

    <div class="company-right">
        @if($logo)
            <img src="{{ asset('storage/'.$logo) }}" alt="Logo">
        @endif
    </div>
</div>