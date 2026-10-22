@extends('layouts.app')

@section('title', $invoice->invoice_number)
@section('header', 'Invoice ' . $invoice->invoice_number)

@section('content')
<div x-data="invoiceApp({{ $invoice->id }})" class="max-w-xl">
    <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-6">
        <div class="flex justify-between text-sm text-ink-500 mb-4">
            <span>Order {{ $receipt['order_number'] }}</span>
            <span>{{ $receipt['date'] }}</span>
        </div>

        <table class="w-full text-sm mb-4">
            <thead class="text-left text-ink-500 border-b">
                <tr><th class="py-1">Item</th><th class="py-1">Qty</th><th class="py-1 text-right">Total</th></tr>
            </thead>
            <tbody>
                @foreach ($receipt['items'] as $item)
                    <tr class="border-b last:border-0">
                        <td class="py-1">{{ $item['name'] }}</td>
                        <td class="py-1">{{ $item['quantity'] }}</td>
                        <td class="py-1 text-right">₹{{ number_format($item['line_total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="space-y-1 text-sm border-t pt-3">
            <div class="flex justify-between"><span>Subtotal</span><span>₹{{ number_format($receipt['subtotal'], 2) }}</span></div>
            <div class="flex justify-between"><span>Tax</span><span>₹{{ number_format($receipt['tax_total'], 2) }}</span></div>
            @if ($receipt['discount_total'] > 0)
                <div class="flex justify-between"><span>Discount</span><span>-₹{{ number_format($receipt['discount_total'], 2) }}</span></div>
            @endif
            <div class="flex justify-between font-semibold text-base"><span>Total</span><span>₹{{ number_format($receipt['grand_total'], 2) }}</span></div>
        </div>

        <div class="mt-4 text-sm text-ink-500">
            Payment status: {{ $receipt['payment_status'] }} · Printed {{ $invoice->printed_count }} time(s)
        </div>

        <div class="mt-6 flex gap-3">
            <button @click="printToPrinter()" :disabled="loading"
                    class="rounded-lg bg-brand-600 px-4 py-2 text-white text-sm font-medium hover:bg-brand-700 disabled:opacity-50">
                <span x-show="!loading">Print Bill</span>
                <span x-show="loading">Printing…</span>
            </button>
            <a href="{{ route('billing.invoices.print', $invoice) }}" target="_blank"
               class="rounded-lg border px-4 py-2 text-sm font-medium">
                Open Print Preview
            </a>
        </div>
        <p x-show="message" x-text="message" class="mt-3 text-sm"></p>
    </div>
</div>
@endsection

@section('scripts')
<script>
function invoiceApp(invoiceId) {
    return {
        loading: false,
        message: null,
        async printToPrinter() {
            this.loading = true;
            this.message = null;
            try {
                await axios.post(`/api/v1/invoices/${invoiceId}/print`);
                this.message = 'Sent to the branch printer.';
            } catch (e) {
                const fallbackUrl = e.response?.data?.fallback_url;
                this.message = e.response?.data?.message || 'Could not reach the printer.';
                if (fallbackUrl) {
                    window.open(fallbackUrl, '_blank');
                }
            } finally {
                this.loading = false;
            }
        },
    };
}
</script>
@endsection
