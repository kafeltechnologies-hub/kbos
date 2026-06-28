@extends('finance.prints.layout')

@section('title', 'Receipt '.$payment->payment_no)

@section('content')
@php $status = strtolower((string) $payment->status); @endphp

@if(in_array($status, ['draft', 'cancelled', 'reversed']))
    <div class="watermark">{{ strtoupper($status) }}</div>
@endif

<div class="doc-title">
    <h2>OFFICIAL RECEIPT</h2>
    <div class="doc-meta">
        <strong>Receipt No:</strong> {{ $payment->payment_no }}
        &nbsp;&nbsp;&nbsp;
        <strong>Date:</strong> {{ $payment->payment_date?->format('d M Y') }}
        &nbsp;&nbsp;&nbsp;
        <span class="status-badge status-{{ $status }}">{{ strtoupper($payment->status) }}</span>
    </div>
</div>

<div class="box">
    <div class="box-title">Receipt Summary</div>
    <table class="info-table">
        <tr><td>Received From</td><td>{{ $payment->party_name }}</td></tr>
        <tr><td>Amount Received</td><td><strong>{{ number_format((float)$payment->gross_amount, 2) }}</strong></td></tr>
        <tr><td>Receipt Type</td><td>{{ ucwords(str_replace('_', ' ', $payment->transaction_subtype ?? $payment->purpose)) }}</td></tr>
        <tr><td>Payment Method</td><td>{{ ucwords(str_replace('_', ' ', $payment->payment_method ?? '-')) }}</td></tr>
        <tr><td>External Reference</td><td>{{ $payment->external_reference ?? '-' }}</td></tr>
        <tr><td>Invoice</td><td>{{ $payment->document?->document_no ?? '-' }}</td></tr>
        <tr><td>Project</td><td>{{ $payment->project?->project_name ?? '-' }}</td></tr>
        <tr><td>Receiving Account</td><td>{{ $payment->cashAccount?->account_name ?? '-' }}</td></tr>
    </table>
</div>

<div class="amount-words">
    Amount in Words: {{ $payment->amount_in_words ?? 'Ghana Cedis '.number_format((float)$payment->gross_amount, 2).' Only' }}
</div>

<div class="box" style="margin-top:14px;">
    <div class="box-title">Narration</div>
    {{ $payment->narration ?: 'Funds received and acknowledged.' }}
</div>
@endsection