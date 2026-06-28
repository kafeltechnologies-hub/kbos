<!DOCTYPE html>
<html>
<head>
    <title>{{ $reportTitle ?? 'Inventory Report' }}</title>
    <style>
        body{font-family:Arial,sans-serif;margin:24px;font-size:10px;color:#111827}
        table{width:100%;border-collapse:collapse;margin-top:10px}
        th,td{border:1px solid #cbd5e1;padding:5px}
        th{background:#f1f5f9}
        .header{text-align:center;border-bottom:2px solid #111827;padding-bottom:8px;margin-bottom:10px}
        .title{font-size:16px;font-weight:bold}
        .filter{background:#f8fafc;border:1px solid #cbd5e1;padding:8px;margin-bottom:10px}
        @media print{button{display:none}}
    </style>
</head>
<body>
<div class="header">
    <div class="title">{{ $company?->name ?? config('app.name') }}</div>
    <div>{{ $company?->address ?? '' }}</div>
    <h2>{{ strtoupper($reportTitle ?? 'Inventory Report') }}</h2>
</div>

<div class="filter">
    <strong>Filters:</strong>
    {{ $filterDescription ?? 'No filters applied' }} |
    <strong>Generated:</strong> {{ now()->format('d M Y H:i') }}
</div>

@if(in_array($type, ['stock_summary','stock_valuation','low_stock','material_master']))
    <table>
        <thead>
            <tr>
                <th>Code</th><th>Material</th><th>Category</th><th>Unit</th>
                <th style="text-align:right">Cost</th>
                <th style="text-align:right">Selling Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($materials as $material)
                <tr>
                    <td>{{ $material->material_code }}</td>
                    <td>{{ $material->name }}</td>
                    <td>{{ $material->category?->category_name }}</td>
                    <td>{{ $material->unit }}</td>
                    <td style="text-align:right">{{ number_format((float)$material->standard_price,2) }}</td>
                    <td style="text-align:right">{{ number_format((float)$material->selling_price,2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if(in_array($type, ['material_movement','material_ledger','project_consumption','goods_receipt_register','material_issue_register','project_transfer_register','borrowed_stock_register']))
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>No.</th>
                <th>Type</th>
                <th>Movement</th>
                <th>Material</th>
                <th style="text-align:right">Qty</th>
                <th style="text-align:right">Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                @foreach($transaction->lines as $line)
                    <tr>
                        <td>{{ $transaction->transaction_date?->format('d M Y') }}</td>
                        <td>{{ $transaction->transaction_no }}</td>
                        <td>{{ strtoupper(str_replace('_',' ',$transaction->transaction_type)) }}</td>
                        <td>
                            @if($transaction->transaction_type === 'transfer_project')
                                {{ $transaction->fromProject?->project_name }} → {{ $transaction->toProject?->project_name }}
                            @elseif(in_array($transaction->transaction_type, ['issue_account','return_account']))
                                {{ $transaction->account_holder_name }} {{ $transaction->account_holder_phone ? '('.$transaction->account_holder_phone.')' : '' }}
                            @else
                                {{ $transaction->project?->project_name ?? $transaction->fromProject?->project_name ?? 'General Stock' }}
                            @endif
                        </td>
                        <td>{{ $line->material?->material_code }} — {{ $line->material?->name }}</td>
                        <td style="text-align:right">{{ number_format((float)$line->quantity,2) }}</td>
                        <td style="text-align:right">{{ number_format((float)$line->line_total,2) }}</td>
                        <td>{{ strtoupper($transaction->status ?? '-') }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endif

@if($type === 'waybill_register')
    <table>
        <thead>
            <tr>
                <th>Waybill No.</th><th>Issue No.</th><th>Project</th><th>Transporter</th><th>Driver</th><th>Vehicle</th><th>Destination</th>
            </tr>
        </thead>
        <tbody>
            @foreach($waybills as $waybill)
                <tr>
                    <td>{{ $waybill->waybill_no }}</td>
                    <td>{{ $waybill->transaction?->transaction_no }}</td>
                    <td>{{ $waybill->transaction?->project?->project_name }}</td>
                    <td>{{ $waybill->transporter_name }}</td>
                    <td>{{ $waybill->driver_name }}</td>
                    <td>{{ $waybill->vehicle_number }}</td>
                    <td>{{ $waybill->delivery_location }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

@if(request('print'))<script>window.onload=function(){window.print()}</script>@endif
</body>
</html>
