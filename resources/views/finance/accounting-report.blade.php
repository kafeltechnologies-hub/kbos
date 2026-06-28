<!DOCTYPE html>
<html>
<head>
<title>{{ $reportTitle }}</title>
<style>
body{font-family:Arial,sans-serif;margin:24px;font-size:10px;color:#111827}
table{width:100%;border-collapse:collapse} th,td{border:1px solid #cbd5e1;padding:5px} th{background:#f1f5f9}
.header{text-align:center;border-bottom:2px solid #111827;padding-bottom:8px;margin-bottom:10px}.title{font-size:16px;font-weight:bold}
</style>
</head>
<body>
<div class="header">
<div class="title">{{ $company?->name ?? config('app.name') }}</div>
<div>{{ $company?->address ?? '' }}</div>
<h2>{{ strtoupper($reportTitle) }}</h2>
<div>Generated: {{ now()->format('d M Y H:i') }}</div>
</div>
<table>
<thead><tr><th>Date</th><th>Account</th><th>Reference</th><th>Narration</th><th style="text-align:right">Debit</th><th style="text-align:right">Credit</th><th>Source</th></tr></thead>
<tbody>
@foreach($entries as $entry)
<tr>
<td>{{ $entry->entry_date?->format('d M Y') ?? $entry->transaction_date?->format('d M Y') }}</td>
<td>{{ $entry->account_name ?? $entry->account }}</td>
<td>{{ $entry->reference }}</td>
<td>{{ $entry->description ?? $entry->narration }}</td>
<td style="text-align:right">{{ number_format((float)($entry->debit ?? $entry->debit_amount ?? 0), 2) }}</td>
<td style="text-align:right">{{ number_format((float)($entry->credit ?? $entry->credit_amount ?? 0), 2) }}</td>
<td>{{ $entry->source_module }} / {{ $entry->source_type }}</td>
</tr>
@endforeach
</tbody>
<tfoot><tr><th colspan="4" style="text-align:right">Totals</th><th style="text-align:right">{{ number_format($totalDebit, 2) }}</th><th style="text-align:right">{{ number_format($totalCredit, 2) }}</th><th></th></tr></tfoot>
</table>
</body>
</html>
