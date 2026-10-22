@extends('layouts.print')

@section('title', $receipt['invoice_number'])

@section('content')
    <div class="text-center">
        <div class="font-bold text-sm">{{ $receipt['restaurant']['name'] }}</div>
        @if ($receipt['restaurant']['address'])
            <div>{{ $receipt['restaurant']['address'] }}</div>
        @endif
        @if ($receipt['restaurant']['phone'])
            <div>Ph: {{ $receipt['restaurant']['phone'] }}</div>
        @endif
        @if ($receipt['restaurant']['gstin'])
            <div>GSTIN: {{ $receipt['restaurant']['gstin'] }}</div>
        @endif
    </div>

    <div class="border-t border-dashed border-black my-2"></div>

    <div>Invoice: {{ $receipt['invoice_number'] }}</div>
    <div>Order: {{ $receipt['order_number'] }}</div>
    <div>Date: {{ $receipt['date'] }}</div>
    @if ($receipt['customer_name'])
        <div>Customer: {{ $receipt['customer_name'] }}</div>
    @endif

    <div class="border-t border-dashed border-black my-2"></div>

    @foreach ($receipt['items'] as $item)
        <div class="flex justify-between">
            <span>{{ $item['name'] }} x{{ $item['quantity'] }}</span>
            <span>{{ number_format($item['line_total'], 2) }}</span>
        </div>
    @endforeach

    <div class="border-t border-dashed border-black my-2"></div>

    <div class="flex justify-between"><span>Subtotal</span><span>{{ number_format($receipt['subtotal'], 2) }}</span></div>
    <div class="flex justify-between"><span>Tax</span><span>{{ number_format($receipt['tax_total'], 2) }}</span></div>
    @if ($receipt['discount_total'] > 0)
        <div class="flex justify-between"><span>Discount</span><span>-{{ number_format($receipt['discount_total'], 2) }}</span></div>
    @endif
    <div class="flex justify-between font-bold"><span>TOTAL</span><span>{{ number_format($receipt['grand_total'], 2) }}</span></div>

    @foreach ($receipt['payments'] as $payment)
        <div class="flex justify-between"><span>Paid via {{ $payment['method'] }}</span><span>{{ number_format($payment['amount'], 2) }}</span></div>
    @endforeach

    <div class="border-t border-dashed border-black my-2"></div>
    <div class="text-center">Thank you, visit again!</div>
@endsection
