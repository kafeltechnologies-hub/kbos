@php
    $preparedBy = $company->prepared_by ?? $company->default_prepared_by ?? auth()->user()->name ?? 'Prepared By';
    $reviewedBy = $company->reviewed_by ?? $company->default_reviewed_by ?? 'Reviewed By';
    $approvedBy = $company->approved_by ?? $company->default_approved_by ?? 'Approved By';
@endphp

<div class="signatures">
    <div class="signature-box">
        <div class="signature-line"></div>
        <p>Prepared By</p>
        <strong>{{ $preparedBy }}</strong>
    </div>

    <div class="signature-box">
        <div class="signature-line"></div>
        <p>Reviewed By</p>
        <strong>{{ $reviewedBy }}</strong>
    </div>

    <div class="signature-box">
        <div class="signature-line"></div>
        <p>Approved By</p>
        <strong>{{ $approvedBy }}</strong>
    </div>
</div>