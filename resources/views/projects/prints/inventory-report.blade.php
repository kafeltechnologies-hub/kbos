<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Inventory Report</title>

    <style>
        @page {
            size: A4 landscape;
            margin: 14mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #0f172a;
            margin: 0;
            background: #ffffff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 5px;
            vertical-align: top;
        }

        th {
            background: #e2e8f0;
            font-size: 9px;
            text-transform: uppercase;
        }

        .no-print {
            margin-bottom: 15px;
        }

        .no-print button {
            background: #0f172a;
            color: white;
            border: 0;
            padding: 8px 14px;
            font-weight: 700;
            cursor: pointer;
            font-size: 10px;
        }

        .header {
            border-bottom: 3px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 12px;
        }

        .company {
            font-size: 18px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: .3px;
        }

        .company-details {
            margin-top: 3px;
            color: #64748b;
            line-height: 1.4;
        }

        .title {
            margin-top: 10px;
            font-size: 14px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 12px;
        }

        .meta-box {
            border: 1px solid #cbd5e1;
            padding: 8px;
            background: #f8fafc;
        }

        .meta-label {
            font-size: 8px;
            text-transform: uppercase;
            color: #475569;
            font-weight: 700;
        }

        .meta-value {
            margin-top: 3px;
            font-weight: 800;
        }

        .summary-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            margin-bottom: 14px;
        }

        .summary-card {
            border: 1px solid #cbd5e1;
            padding: 9px;
            background: #ffffff;
        }

        .summary-title {
            font-size: 8px;
            color: #64748b;
            text-transform: uppercase;
            font-weight: 800;
        }

        .summary-value {
            margin-top: 4px;
            font-size: 15px;
            font-weight: 900;
            font-family: monospace;
        }

        .muted {
            color: #64748b;
        }

        .money,
        .qty {
            text-align: right;
            font-family: monospace;
        }

        .status-ok {
            font-weight: 800;
            color: #15803d;
        }

        .status-low {
            font-weight: 800;
            color: #b91c1c;
        }

        .footer {
            margin-top: 18px;
            border-top: 1px solid #cbd5e1;
            padding-top: 8px;
            color: #64748b;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

@php
    $reportTitle = strtoupper(str_replace('_', ' ', $type ?? 'stock_summary'));

    $visibleRows = collect();
    $totalStockQty = 0;
    $totalStockValue = 0;
    $lowStockCount = 0;

    foreach ($materials as $material) {
        $stock = \App\Http\Controllers\Projects\InventoryReportPrintController::stockQuantity($material->id);
        $value = $stock * (float) ($material->standard_price ?? 0);
        $isLow = $stock <= (float) ($material->reorder_level ?? 0);

        if (($type ?? 'stock_summary') === 'low_stock' && ! $isLow) {
            continue;
        }

        $totalStockQty += $stock;
        $totalStockValue += $value;

        if ($isLow) {
            $lowStockCount++;
        }

        $visibleRows->push([
            'material' => $material,
            'stock' => $stock,
            'value' => $value,
            'isLow' => $isLow,
        ]);
    }
@endphp

<div class="no-print">
    <button onclick="window.print()">Print Report</button>
</div>

<div class="header">
    <div class="company">
        {{ $company->name ?? 'Company Name' }}
    </div>

    <div class="company-details">
        {{ $company->address ?? 'Company Address' }}<br>
        Tel: {{ $company->phone ?? '-' }}
        |
        Email: {{ $company->email ?? '-' }}
    </div>

    <div class="title">
        {{ $reportTitle }}
    </div>
    <div style="margin-top:8px;">
        <strong>Report Filters:</strong>
        {{ $filterDescription ?: 'No Filters Applied' }}
    </div>
</div>

<div class="meta-grid">
    <div class="meta-box">
        <div class="meta-label">Report Type</div>
        <div class="meta-value">{{ $reportTitle }}</div>
    </div>

    <div class="meta-box">
        <div class="meta-label">Search Filter</div>
        <div class="meta-value">{{ $search ?: 'All Materials' }}</div>
    </div>

    <div class="meta-box">
        <div class="meta-label">Generated Date</div>
        <div class="meta-value">{{ now()->format('d M Y') }}</div>
    </div>

    <div class="meta-box">
        <div class="meta-label">Generated Time</div>
        <div class="meta-value">{{ now()->format('h:i A') }}</div>
    </div>
</div>

<div class="summary-grid">
    <div class="summary-card">
        <div class="summary-title">Total Materials</div>
        <div class="summary-value">{{ $visibleRows->count() }}</div>
    </div>

    <div class="summary-card">
        <div class="summary-title">Total Stock Qty</div>
        <div class="summary-value">{{ number_format((float) $totalStockQty, 2) }}</div>
    </div>

    <div class="summary-card">
        <div class="summary-title">Total Stock Value</div>
        <div class="summary-value">{{ number_format((float) $totalStockValue, 2) }}</div>
    </div>

    <div class="summary-card">
        <div class="summary-title">Low Stock Items</div>
        <div class="summary-value">{{ $lowStockCount }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th style="width: 9%;">Code</th>
            <th style="width: 25%;">Material</th>
            <th style="width: 12%;">Category</th>
            <th style="width: 7%;">Unit</th>
            <th style="width: 9%;">Stock</th>
            <th style="width: 9%;">Cost</th>
            <th style="width: 10%;">Value</th>
            <th style="width: 9%;">Reorder</th>
            <th style="width: 10%;">Status</th>
        </tr>
    </thead>

    <tbody>
        @forelse($visibleRows as $row)
            @php
                $material = $row['material'];
                $stock = $row['stock'];
                $value = $row['value'];
                $isLow = $row['isLow'];
            @endphp

            <tr>
                <td>{{ $material->material_code }}</td>

                <td>
                    <strong>{{ $material->name }}</strong><br>
                    <span class="muted">{{ $material->description }}</span>
                </td>

                <td>{{ $material->category?->category_name ?? '-' }}</td>

                <td>{{ $material->unit ?? '-' }}</td>

                <td class="qty">{{ number_format((float) $stock, 2) }}</td>

                <td class="money">{{ number_format((float) $material->standard_price, 2) }}</td>

                <td class="money">{{ number_format((float) $value, 2) }}</td>

                <td class="qty">{{ number_format((float) $material->reorder_level, 2) }}</td>

                <td>
                    @if($isLow)
                        <span class="status-low">LOW STOCK</span>
                    @else
                        <span class="status-ok">OK</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="9" style="text-align:center; padding:20px;">
                    No inventory records found for this report.
                </td>
            </tr>
        @endforelse
    </tbody>

    <tfoot>
        <tr>
            <th colspan="4" style="text-align:right;">Grand Total</th>
            <th class="qty">{{ number_format((float) $totalStockQty, 2) }}</th>
            <th></th>
            <th class="money">{{ number_format((float) $totalStockValue, 2) }}</th>
            <th colspan="2"></th>
        </tr>
    </tfoot>
</table>

<div class="footer">
    <div>
        Generated by KBOS Inventory Module
    </div>

    <div>
        Printed: {{ now()->format('d M Y, h:i A') }}
    </div>
</div>

@if(request('print'))
    <script>
        window.onload = function () {
            window.print();
        }
    </script>
@endif

</body>
</html>