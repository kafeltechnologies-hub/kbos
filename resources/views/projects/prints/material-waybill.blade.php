<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Transport Waybill - {{ $waybill->waybill_no }}</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            color: #0f172a;
            margin: 20px;
            position: relative;
            min-height: 100vh;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #cbd5e1;
            padding: 6px;
            vertical-align: top;
        }

        th {
            background: #e2e8f0;
            font-size: 9px;
            text-transform: uppercase;
        }

        .header {
            border-bottom: 3px solid #0f172a;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .company {
            font-size: 17px;
            font-weight: 900;
            text-transform: uppercase;
        }

        .muted {
            color: #64748b;
        }

        .title {
            margin-top: 8px;
            font-size: 13px;
            font-weight: 900;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-bottom: 14px;
        }

        .box {
            border: 1px solid #cbd5e1;
            padding: 8px;
        }

        .label {
            font-size: 9px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
        }

        .value {
            margin-top: 3px;
            font-weight: 700;
        }

        .qty {
            text-align: right;
            font-family: monospace;
        }

        .footer-space {
            height: 120px;
        }

        .signatures {
            position: fixed;
            bottom: 20px;
            left: 20px;
            right: 20px;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 25px;
            background: white;
        }

        .signature {
            border-top: 1px solid #0f172a;
            padding-top: 6px;
            text-align: center;
            font-weight: 700;
            font-size: 10px;
        }

        .signature small {
            display: block;
            margin-top: 3px;
            font-weight: normal;
            color: #64748b;
        }

        .no-print {
            margin-bottom: 15px;
        }

        button {
            background: #0f172a;
            color: white;
            border: 0;
            padding: 8px 14px;
            font-weight: 700;
            cursor: pointer;
            font-size: 10px;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                margin: 18px;
            }

            .signatures {
                bottom: 18px;
                left: 18px;
                right: 18px;
            }
        }
    </style>
</head>

<body>

<div class="no-print">
    <button onclick="window.print()">Print Transport Waybill</button>
</div>

<div class="header">
    <div class="company">
        {{ $waybill->transaction?->project?->company?->name ?? 'Company Name' }}
    </div>

    <div class="muted">
        {{ $waybill->transaction?->project?->company?->address ?? 'Company Address' }}<br>
        Tel: {{ $waybill->transaction?->project?->company?->phone ?? '-' }}
        |
        Email: {{ $waybill->transaction?->project?->company?->email ?? '-' }}
    </div>

    <div class="title">TRANSPORT WAYBILL</div>
</div>

<div class="grid">
    <div class="box">
        <div class="label">Waybill No.</div>
        <div class="value">{{ $waybill->waybill_no }}</div>
    </div>

    <div class="box">
        <div class="label">Issue Voucher No.</div>
        <div class="value">{{ $waybill->transaction?->transaction_no }}</div>
    </div>

    <div class="box">
        <div class="label">Project</div>
        <div class="value">{{ $waybill->transaction?->project?->project_name ?? '-' }}</div>
    </div>

    <div class="box">
        <div class="label">Destination</div>
        <div class="value">{{ $waybill->delivery_location ?? '-' }}</div>
    </div>

    <div class="box">
        <div class="label">Transporter</div>
        <div class="value">{{ $waybill->transporter_name ?? '-' }}</div>
    </div>

    <div class="box">
        <div class="label">Vehicle Number</div>
        <div class="value">{{ $waybill->vehicle_number ?? '-' }}</div>
    </div>

    <div class="box">
        <div class="label">Driver Name</div>
        <div class="value">{{ $waybill->driver_name ?? '-' }}</div>
    </div>

    <div class="box">
        <div class="label">Driver Phone</div>
        <div class="value">{{ $waybill->driver_phone ?? '-' }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>Code</th>
            <th>Material Loaded</th>
            <th>Unit</th>
            <th>Quantity</th>
        </tr>
    </thead>

    <tbody>
        @foreach($waybill->transaction?->lines ?? [] as $line)
            <tr>
                <td>{{ $line->material?->material_code }}</td>
                <td>
                    <strong>{{ $line->material?->name }}</strong><br>
                    <span class="muted">{{ $line->material?->description }}</span>
                </td>
                <td>{{ $line->material?->unit }}</td>
                <td class="qty">{{ number_format((float) $line->quantity, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<div class="footer-space"></div>

<div class="signatures">
    <div class="signature">
        Prepared By
        <small>Stores Department</small>
    </div>

    <div class="signature">
        Loaded By
        <small>{{ $waybill->loaded_by }}</small>
    </div>

    <div class="signature">
        Driver
        <small>{{ $waybill->driver_name }}</small>
    </div>

    <div class="signature">
        Received By
        <small>{{ $waybill->received_by }}</small>
    </div>
</div>

</body>
</html>