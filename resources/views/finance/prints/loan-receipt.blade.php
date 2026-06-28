@extends('finance.prints.layout')

@section('title', 'Loan Receipt '.$payment->payment_no)

@section('content')
@php $status = strtolower((string) $payment->status); @endphp

@if(in_array($status, ['draft', 'cancelled', 'reversed']))
    <div class="watermark">{{ strtoupper($status) }}</div>
@endif

<div class="doc-title">
    <h2>LOAN / CAPITAL RECEIPT</h2>
    <div class="doc-meta">
        <strong>Loan Receipt No:</strong> {{ $payment->payment_no }}
        &nbsp;&nbsp;&nbsp;
        <strong>Date:</strong> {{ $payment->payment_date?->format('d M Y') }}
        &nbsp;&nbsp;&nbsp;
        <span class="status-badge status-{{ $status }}">{{ strtoupper($payment->status) }}</span>
    </div>
</div>

<div class="box">
    <div class="box-title">Loan / Capital Summary</div>
    <table class="info-table">
        <tr><td>Lender / Financier</td><td>{{ $payment->lender_name ?? $payment->party_name }}</td></tr>
        <tr><td>Amount Received</td><td><strong>{{ number_format((float)$payment->gross_amount, 2) }}</strong></td></tr>
        <tr><td>Loan Type</td><td>{{ ucwords(str_replace('_', ' ', $payment->transaction_subtype ?? 'loan')) }}</td></tr>
        <tr><td>Interest Rate</td><td>{{ number_format((float)($payment->interest_rate ?? 0), 2) }}% {{ ($payment->interest_period ?? 'monthly') === 'annual' ? 'per annum' : 'per month' }}</td></tr>
        <tr><td>Start Date</td><td>{{ $payment->loan_start_date?->format('d M Y') ?? '-' }}</td></tr>
        <tr><td>Due Date</td><td>{{ $payment->loan_due_date?->format('d M Y') ?? '-' }}</td></tr>
        <tr><td>Receiving Account</td><td>{{ $payment->cashAccount?->account_name ?? '-' }}</td></tr>
        <tr><td>External Reference</td><td>{{ $payment->external_reference ?? '-' }}</td></tr>
    </table>
</div>

<div class="amount-words">
    Amount in Words: {{ $payment->amount_in_words ?? 'Ghana Cedis '.number_format((float)$payment->gross_amount, 2).' Only' }}
</div>

<div class="box" style="margin-top:14px;">
    <div class="box-title">Narration</div>
    {{ $payment->narration ?: 'Loan / capital funds received and recorded for accounting review.' }}
</div>
@endsection