<div class="footer">
    <div>
        Printed By:
        <strong>{{ auth()->user()->name ?? 'System User' }}</strong>
    </div>

    <div>
        Printed On:
        <strong>{{ now()->format('d M Y H:i') }}</strong>
    </div>

    <div>
        {{ $company->report_footer ?? 'Generated from Kafel ERP Finance Operations Centre' }}
    </div>
</div>