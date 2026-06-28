<!DOCTYPE html>
<html>
<head>
    <title>Receipt {{ $reference }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #111827; }
        .page { width: 800px; margin: 20px auto; border: 1px solid #cbd5e1; padding: 30px; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 15px; }
        .title { font-size: 20px; font-weight: 900; margin-top: 15px; }
        .meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 25px 0; }
        .box { border: 1px solid #cbd5e1; padding: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px; }
        th { background: #f1f5f9; text-align: left; }
        .right { text-align: right; }
        .total { font-size: 16px; font-weight: 900; }
        .signatures { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px; margin-top: 60px; }
        .sig { border-top: 1px solid #111827; padding-top: 8px; text-align: center; }
        @media print { button { display: none; } .page { margin: 0 auto; } }
    </style>
</head>
<body>
    <button onclick="window.print()">Print</button>

    <div class="page">
        <div class="header">
            <h2>KAFEL TECHNOLOGIES</h2>
            <p>Finance Operations Centre</p>
            <div class="title">OFFICIAL RECEIPT</div>
        </div>

        <div class="meta">
            <div class="box"><b>Receipt No:</b> {{ $reference }}</div>
            <div class="box"><b>Date:</b> {{ $header->entry_date?->format('d M Y') }}</div>
            <div class="box"><b>Received From:</b> {{ str_replace('Receipt from ', '', $header->narration ?? '') }}</div>
            <div class="box"><b>Project:</b> {{ $header->project?->project_name ?? 'N/A' }}</div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Account</th>
                    <th>Description</th>
                    <th class="right">Debit</th>
                    <th class="right">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($entries as $entry)
                    <tr>
                        <td>{{ $entry->account_code }} — {{ $entry->account_name }}</td>
                        <td>{{ $entry->description ?? $entry->narration }}</td>
                        <td class="right">{{ number_format((float) $entry->debit, 2) }}</td>
                        <td class="right">{{ number_format((float) $entry->credit, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="right total">Amount Received</td>
                    <td colspan="2" class="right total">{{ number_format($total, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="signatures">
            <div class="sig">Prepared By</div>
            <div class="sig">Received By</div>
            <div class="sig">Approved By</div>
        </div>
    </div>
</body>
</html>