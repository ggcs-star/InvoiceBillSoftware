@extends('layouts.app')

@section('title', 'Billing')
@section('header', 'Billing — ' . $branch->restaurant->name . ' (' . $branch->name . ')')

@section('content')
<div x-data="billingApp({{ json_encode(['activeOrder' => $activeOrder ?? null]) }})" x-init="init()" class="grid grid-cols-3 gap-6">

    {{-- Menu --}}
    <div class="col-span-2 space-y-6">
        @if ($openOrders->count())
            <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4">
                <div class="text-sm font-medium text-ink-500 mb-2">Open Orders</div>
                <div class="flex gap-2 flex-wrap">
                    @foreach ($openOrders as $open)
                        <a href="{{ route('billing.orders.show', $open) }}"
                           class="px-3 py-1.5 rounded-lg text-sm border {{ isset($activeOrder) && $activeOrder->id === $open->id ? 'border-brand-600 text-brand-600' : 'border-ink-100' }}">
                            {{ $open->order_number }}
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        @foreach ($categories as $category)
            @if ($category->menuItems->count())
                <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4">
                    <div class="font-medium mb-3">{{ $category->name }}</div>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach ($category->menuItems as $item)
                            <button @click="addItem({{ $item->id }})"
                                    class="text-left rounded-lg border p-3 hover:border-brand-500 hover:bg-brand-50">
                                <div class="font-medium text-sm">{{ $item->name }}</div>
                                <div class="text-brand-600 text-sm mt-1">₹{{ number_format($item->price, 2) }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    {{-- Cart --}}
    <div class="col-span-1">
        <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4 sticky top-6">
            <template x-if="!order">
                <button @click="startOrder()" class="w-full rounded-lg bg-ink-900 text-white py-2.5 font-medium">
                    + New Order
                </button>
            </template>

            <template x-if="order">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <div class="font-medium" x-text="order.order_number"></div>
                        <button @click="order = null" class="text-xs text-ink-400">clear</button>
                    </div>

                    <div class="space-y-2 max-h-80 overflow-y-auto">
                        <template x-for="item in order.items" :key="item.id">
                            <div class="flex items-center justify-between text-sm border-b pb-2">
                                <div class="flex-1">
                                    <div x-text="item.name"></div>
                                    <div class="text-xs text-ink-400" x-text="'₹' + item.unit_price + ' each'"></div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button @click="updateQty(item, item.quantity - 1)" class="w-6 h-6 rounded border">-</button>
                                    <span x-text="item.quantity"></span>
                                    <button @click="updateQty(item, item.quantity + 1)" class="w-6 h-6 rounded border">+</button>
                                </div>
                                <div class="w-16 text-right" x-text="'₹' + item.line_total"></div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-4 space-y-1 text-sm border-t pt-3">
                        <div class="flex justify-between"><span>Subtotal</span><span x-text="'₹' + order.subtotal"></span></div>
                        <div class="flex justify-between"><span>Tax (GST)</span><span x-text="'₹' + order.tax_total"></span></div>
                        <div class="flex justify-between font-semibold text-base"><span>Total</span><span x-text="'₹' + order.grand_total"></span></div>
                    </div>

                    <div class="mt-4">
                        <label class="block text-xs font-medium text-ink-500 mb-1">Payment Method</label>
                        <select x-model="paymentMethod" class="w-full rounded-lg border-ink-200 shadow-sm text-sm">
                            <option value="cash">Cash</option>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <button @click="checkout()" :disabled="loading || !order.items.length"
                            class="mt-4 w-full rounded-lg bg-brand-600 text-white py-2.5 font-medium disabled:opacity-50">
                        <span x-show="!loading">Checkout &amp; Generate Invoice</span>
                        <span x-show="loading">Processing…</span>
                    </button>

                    <p x-show="error" x-text="error" class="mt-2 text-sm text-red-600"></p>
                    <p x-show="success" x-text="success" class="mt-2 text-sm text-green-600"></p>
                </div>
            </template>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function billingApp(initial) {
    return {
        order: initial.activeOrder,
        loading: false,
        paymentMethod: 'cash',
        error: null,
        success: null,
        invoice: null,

        init() {},

        async startOrder() {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.post('/api/v1/orders', {});
                this.order = data.data;
            } catch (e) {
                this.error = e.response?.data?.message || 'Could not start order.';
            } finally {
                this.loading = false;
            }
        },

        async addItem(menuItemId) {
            if (!this.order) {
                await this.startOrder();
            }
            try {
                const { data } = await axios.post(`/api/v1/orders/${this.order.id}/items`, {
                    menu_item_id: menuItemId,
                    quantity: 1,
                });
                this.order = data.data;
            } catch (e) {
                this.error = e.response?.data?.message || 'Could not add item.';
            }
        },

        async updateQty(item, qty) {
            if (qty < 1) {
                return this.removeItem(item);
            }
            const { data } = await axios.put(`/api/v1/orders/${this.order.id}/items/${item.id}`, {
                quantity: qty,
                notes: item.notes,
            });
            this.order = data.data;
        },

        async removeItem(item) {
            const { data } = await axios.delete(`/api/v1/orders/${this.order.id}/items/${item.id}`);
            this.order = data.data;
        },

        async checkout() {
            this.loading = true;
            this.error = null;
            this.success = null;
            try {
                const { data } = await axios.post(`/api/v1/orders/${this.order.id}/checkout`, {
                    payment_method: this.paymentMethod,
                    amount: this.order.grand_total,
                });
                this.invoice = data.data;
                await this.printInvoice();
                this.success = `Invoice ${this.invoice.invoice_number} generated.`;
                this.order = null;
            } catch (e) {
                this.error = e.response?.data?.message || 'Checkout failed.';
            } finally {
                this.loading = false;
            }
        },

        async printInvoice() {
            try {
                await axios.post(`/api/v1/invoices/${this.invoice.id}/print`);
            } catch (e) {
                const fallbackUrl = e.response?.data?.fallback_url;
                if (fallbackUrl) {
                    window.open(fallbackUrl, '_blank');
                }
            }
        },
    };
}
</script>
@endsection
