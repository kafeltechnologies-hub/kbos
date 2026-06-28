@extends('finance.prints.layout')

@section('title', 'Invoice '.$document->document_no)

@section('content')
@php
    $status = strtolower((string) $document->status);
    $subtotal = (float)$document->materials_total + (float)$document->labour_cost + (float)$document->transport_cost + (float)$document->other_cost;
@endphp

@if(in_array($status, ['draft', 'cancelled', 'reversed']))
    <div class="watermark">{{ strtoupper($status) }}</div>
@endif

<div class="doc-title">
    <h2>INVOICE</h2>
    <div class="doc-meta">
        <strong>Invoice No:</strong> {{ $document->document_no }}
        &nbsp;&nbsp;&nbsp;
        <strong>Date:</strong> {{ $document->document_date?->format('d M Y') }}
        &nbsp;&nbsp;&nbsp;
        <span class="status-badge status-{{ $status }}">{{ strtoupper($document->status) }}</span>
    </div>
</div>

<div class="grid-2">
    <div class="box">
        <div class="box-title">Bill To</div>
        <table class="info-table">
            <tr><td>Customer</td><td>{{ $document->customer_name }}</td></tr>
            <tr><td>Project</td><td>{{ $document->project?->project_name ?? '-' }}</td></tr>
            <tr><td>Location</td><td>{{ $document->project?->location ?? $document->location ?? '-' }}</td></tr>
            <tr><td>Source Quotation</td><td>{{ $document->source_quotation_no ?? '-' }}</td></tr>
        </table>
    </div>

    <div class="box">
        <div class="box-title">Description / Scope</div>
        {{ $document->service_description ?: 'Invoice raised for goods/services supplied.' }}
    </div>
</div>

<table class="items-table">
    <thead>
    <tr>
        <th>#</th>
        <th>Code</th>
        <th>Description</th>
        <th>Unit</th>
        <th class="right">Qty</th>
        <th class="right">Unit Price</th>
        <th class="right">Amount</th>
    </tr>
    </thead>
    <tbody>
    @forelse($document->lines as $line)
        <tr>
            <td class="center">{{ $loop->iteration }}</td>
            <td>{{ $line->material?->material_code ?? '-' }}</td>
            <td>{{ $line->description }}</td>
            <td>{{ $line->unit }}</td>
            <td class="right">{{ number_format((float)$line->quantity, 2) }}</td>
            <td class="right">{{ number_format((float)$line->unit_price, 2) }}</td>
            <td class="right">{{ number_format((float)$line->amount, 2) }}</td>
        </tr>
    @empty
        <tr><td colspan="7" class="center">No invoice lines found.</td></tr>
    @endforelse
    </tbody>
</table>

<table class="totals">
    <tr><td>Subtotal</td><td class="right">{{ number_format($subtotal, 2) }}</td></tr>
    <tr><td>Discount</td><td class="right">{{ number_format((float)$document->discount_amount, 2) }}</td></tr>
    <tr><td>Tax</td><td class="right">{{ number_format((float)$document->tax_amount, 2) }}</td></tr>
    <tr class="grand"><td>Total Due</td><td class="right">{{ number_format((float)$document->grand_total, 2) }}</td></tr>
</table>

<div class="amount-words">
    Amount in Words: {{ $document->amount_in_words ?? 'Ghana Cedis '.number_format((float)$document->grand_total, 2).' Only' }}
</div>

<div class="box" style="margin-top:14px;">
    <div class="box-title">Narration</div>
    {{ $document->narration ?: 'Kindly make payment using the approved company payment details.' }}
</div>
@endsection