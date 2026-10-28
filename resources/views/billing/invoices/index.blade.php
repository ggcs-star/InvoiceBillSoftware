@extends('layouts.app')

@section('title', 'Invoices')
@section('header', 'Invoices')

@section('content')
    <div class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-x-auto">
        <table class="w-full text-sm whitespace-nowrap">
            <thead class="text-left text-ink-500 border-b">
                <tr>
                    <th class="px-5 py-3">Invoice #</th>
                    <th class="px-5 py-3">Order #</th>
                    <th class="px-5 py-3">Status</th>
                    <th class="px-5 py-3 text-right">Total</th>
                    <th class="px-5 py-3">Date</th>
                    <th class="px-5 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr class="border-b last:border-0">
                        <td class="px-5 py-3 font-medium">{{ $invoice->invoice_number }}</td>
                        <td class="px-5 py-3">{{ $invoice->order->order_number }}</td>
                        <td class="px-5 py-3">{{ $invoice->payment_status->label() }}</td>
                        <td class="px-5 py-3 text-right">₹{{ number_format($invoice->grand_total, 2) }}</td>
                        <td class="px-5 py-3 text-ink-500">{{ $invoice->created_at->format('d M, h:i A') }}</td>
                        <td class="px-5 py-3 text-right">
                            <a href="{{ route('billing.invoices.show', $invoice) }}" class="text-brand-600 hover:underline">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-6 text-center text-ink-400">No invoices yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <x-pagination :paginator="$invoices" />
@endsection
