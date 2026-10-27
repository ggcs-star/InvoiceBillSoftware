@extends('layouts.app')

@section('title', 'POS')
@section('header', 'POS — ' . $branch->restaurant->name . ' (' . $branch->name . ')')

@section('content')
@php
    // No photo uploaded yet — show a dummy picture that at least hints at
    // what the dish is, instead of one generic icon for every single item.
    $dummyEmoji = [
        'pizza' => '🍕', 'burger' => '🍔', 'sandwich' => '🥪', 'roll' => '🌯', 'wrap' => '🌯',
        'pasta' => '🍝', 'noodle' => '🍜', 'ramen' => '🍜', 'soup' => '🍲',
        'rice' => '🍚', 'biryani' => '🍛', 'curry' => '🍛', 'dal' => '🍛',
        'paneer' => '🧀', 'tikka' => '🍢', 'kebab' => '🍢', 'tandoori' => '🍢', 'grill' => '🍢',
        'salad' => '🥗', 'fruit' => '🍓',
        'fries' => '🍟', 'chips' => '🍟',
        'cake' => '🍰', 'chocolate' => '🍫', 'pastry' => '🧁', 'dessert' => '🍨', 'ice cream' => '🍨',
        'coffee' => '☕', 'tea' => '🍵', 'juice' => '🧃', 'shake' => '🥤', 'smoothie' => '🥤',
        'soda' => '🥤', 'cold drink' => '🥤', 'drink' => '🥤', 'lassi' => '🥤',
        'bread' => '🍞', 'naan' => '🫓', 'egg' => '🍳', 'omelette' => '🍳',
        'momo' => '🥟', 'dumpling' => '🥟', 'chicken' => '🍗', 'fish' => '🐟',
    ];
    $resolveDummyEmoji = function (string $itemName, string $categoryName) use ($dummyEmoji) {
        $haystack = Str::lower($itemName . ' ' . $categoryName);
        foreach ($dummyEmoji as $keyword => $emoji) {
            if (Str::contains($haystack, $keyword)) {
                return $emoji;
            }
        }
        return '🍽️';
    };

    $itemsMeta = $categories->flatMap(fn ($category) => $category->menuItems->map(fn ($item) => [
        'id' => $item->id,
        'name' => Str::lower($item->name),
        'display_name' => $item->name,
        'description' => $item->description,
        'category_id' => $item->category_id,
        'image_url' => $item->image_path ? asset('storage/' . $item->image_path) : null,
        'emoji' => $resolveDummyEmoji($item->name, $category->name),
        'variations' => $item->variations->map(fn ($variation) => [
            'id' => $variation->id,
            'name' => $variation->name,
            'price' => (float) $variation->price,
        ])->values(),
    ]))->values();
@endphp
<div x-data="billingApp(@js(['activeOrder' => $activeOrder ?? null, 'items' => $itemsMeta]))" x-init="init()">

    <div class="grid grid-cols-3 gap-6">
        {{-- Menu --}}
        <div class="col-span-2 space-y-4">
            <div class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[220px]">
                    <svg viewBox="0 0 24 24" class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-ink-400" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="7"/>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" x-model="search" placeholder="Search menu items..."
                           class="w-full rounded-full border-ink-200 bg-white pl-10 pr-4 py-2.5 text-sm shadow-sm focus:border-brand-300 focus:ring-brand-300">
                </div>

                @if ($openOrders->count())
                    <div class="flex gap-2 flex-wrap">
                        @foreach ($openOrders as $open)
                            <a href="{{ route('billing.orders.show', $open) }}"
                               class="px-3 py-1.5 rounded-full text-xs font-medium border {{ isset($activeOrder) && $activeOrder->id === $open->id ? 'border-brand-600 text-brand-600 bg-brand-50' : 'border-ink-200 text-ink-500 hover:bg-ink-50' }}">
                                {{ $open->order_number }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="flex gap-2 overflow-x-auto pb-1">
                <button @click="activeCategory = null"
                        :class="activeCategory === null ? 'bg-brand-600 text-white border-brand-600' : 'border-ink-200 text-ink-600 hover:bg-ink-50'"
                        class="shrink-0 rounded-full border px-4 py-1.5 text-sm font-medium transition">
                    All
                </button>
                @foreach ($categories as $category)
                    @if ($category->menuItems->count())
                        <button @click="activeCategory = {{ $category->id }}"
                                :class="activeCategory === {{ $category->id }} ? 'bg-brand-600 text-white border-brand-600' : 'border-ink-200 text-ink-600 hover:bg-ink-50'"
                                class="shrink-0 rounded-full border px-4 py-1.5 text-sm font-medium transition">
                            {{ $category->name }}
                        </button>
                    @endif
                @endforeach
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach ($categories as $category)
                    @foreach ($category->menuItems as $item)
                        <div x-show="isVisible({{ $item->id }})"
                             class="bg-white rounded-2xl border border-ink-100 shadow-card overflow-hidden flex flex-col">
                            <div class="h-24 bg-brand-50/60">
                                @if ($item->image_path)
                                    <img src="{{ asset('storage/' . $item->image_path) }}" class="h-full w-full object-cover">
                                @else
                                    <div class="h-full w-full flex items-center justify-center text-4xl">
                                        {{ $resolveDummyEmoji($item->name, $category->name) }}
                                    </div>
                                @endif
                            </div>
                            <div class="p-3 flex-1 flex flex-col">
                                <div class="flex items-center gap-1.5">
                                    <x-food-type-icon :type="$item->food_type" />
                                    <span class="font-medium text-sm text-ink-900 truncate">{{ $item->name }}</span>
                                </div>
                                <div class="text-brand-600 text-sm font-semibold mt-1">₹{{ number_format($item->price, 2) }}</div>
                                <button @click="chooseItem({{ $item->id }})"
                                        class="mt-auto w-full rounded-lg bg-brand-600 text-white text-xs font-medium py-2 hover:bg-brand-700">
                                    + Add
                                </button>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>

        {{-- Cart --}}
        <div class="col-span-1">
            <div class="bg-white rounded-2xl border border-ink-100 shadow-card p-4 sticky top-6">
                <div class="space-y-3 mb-3 pb-3 border-b">
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Customer Name (optional)</label>
                        <input type="text" x-model="customerName" @blur="saveCustomer()" placeholder="Walk-in"
                               class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-ink-500 mb-1">Phone (optional)</label>
                        <input type="text" x-model="customerPhone" @blur="saveCustomer()" placeholder="98765 43210"
                               class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                    </div>
                </div>

                <template x-if="!order">
                    <button @click="startOrder()" class="w-full rounded-lg bg-brand-600 text-white py-2.5 font-medium hover:bg-brand-700">
                        + New Order
                    </button>
                </template>

                <template x-if="order">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <div class="font-medium">Current Order <span class="text-ink-400 font-normal" x-text="'(' + order.items.length + ')'"></span></div>
                            <button @click="order = null; customerName = ''; customerPhone = ''; transactionReference = ''" class="text-xs font-medium text-brand-600 hover:underline">Clear All</button>
                        </div>

                        <div class="space-y-2 max-h-80 overflow-y-auto">
                            <template x-for="item in order.items" :key="item.id">
                                <div class="flex items-center justify-between text-sm border-b pb-2">
                                    <div class="flex items-center gap-2 flex-1 min-w-0">
                                        <span class="shrink-0 flex h-8 w-8 items-center justify-center rounded-full bg-brand-50 text-base overflow-hidden">
                                            <template x-if="itemsMeta[item.menu_item_id]?.image_url">
                                                <img :src="itemsMeta[item.menu_item_id].image_url" class="h-full w-full object-cover">
                                            </template>
                                            <template x-if="!itemsMeta[item.menu_item_id]?.image_url">
                                                <span x-text="itemsMeta[item.menu_item_id]?.emoji ?? '🍽️'"></span>
                                            </template>
                                        </span>
                                        <div class="min-w-0">
                                            <div class="truncate" x-text="item.name"></div>
                                            <div class="text-xs text-ink-400" x-text="'₹' + item.unit_price + ' each'"></div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-1.5 shrink-0">
                                        <button @click="updateQty(item, item.quantity - 1)" class="w-6 h-6 rounded border text-ink-500 hover:bg-ink-50">-</button>
                                        <span class="w-4 text-center" x-text="item.quantity"></span>
                                        <button @click="updateQty(item, item.quantity + 1)" class="w-6 h-6 rounded border text-ink-500 hover:bg-ink-50">+</button>
                                    </div>
                                    <div class="w-16 text-right shrink-0" x-text="'₹' + item.line_total"></div>
                                    <button @click="removeItem(item)" class="shrink-0 text-ink-300 hover:text-red-500" title="Remove">
                                        <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                        </svg>
                                    </button>
                                </div>
                            </template>

                            <p x-show="!order.items.length" class="text-sm text-ink-400 text-center py-6">No items yet — add something from the menu.</p>
                        </div>

                        <div class="mt-4 space-y-1 text-sm border-t pt-3">
                            <div class="flex justify-between"><span>Sub Total</span><span x-text="'₹' + order.subtotal"></span></div>
                            <div class="flex justify-between"><span>Tax (GST)</span><span x-text="'₹' + order.tax_total"></span></div>
                            <div class="flex justify-between font-semibold text-base"><span>Total</span><span x-text="'₹' + order.grand_total"></span></div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-xs font-medium text-ink-500 mb-1">Payment Method</label>
                            <select x-model="paymentMethod" class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                                <option value="cash">Cash</option>
                                <option value="upi">UPI</option>
                                <option value="card">Card</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div class="mt-3" x-show="paymentMethod === 'upi'">
                            <label class="block text-xs font-medium text-ink-500 mb-1">UPI ID</label>
                            <input type="text" x-model="transactionReference" placeholder="name@bank"
                                   class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                        </div>
                        <div class="mt-3" x-show="paymentMethod === 'card'">
                            <label class="block text-xs font-medium text-ink-500 mb-1">Card Details (last 4 digits / ref. no.)</label>
                            <input type="text" x-model="transactionReference" placeholder="•••• 4242"
                                   class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                        </div>
                        <div class="mt-3" x-show="paymentMethod === 'other'">
                            <label class="block text-xs font-medium text-ink-500 mb-1">Reference (optional)</label>
                            <input type="text" x-model="transactionReference" placeholder="Reference number"
                                   class="w-full rounded-lg border-ink-200 px-3 py-2 shadow-sm text-sm">
                        </div>

                        <button @click="checkout()" :disabled="loading || !order.items.length"
                                class="mt-4 w-full rounded-lg bg-brand-600 text-white py-2.5 font-medium disabled:opacity-50">
                            <span x-show="!loading">Place Order</span>
                            <span x-show="loading">Processing…</span>
                        </button>

                        <p x-show="error" x-text="error" class="mt-2 text-sm text-red-600"></p>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Item Variations --}}
    <div x-show="variationItem" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden" @click.outside="variationItem = null">
            <div class="flex items-center justify-between px-6 py-4 border-b border-ink-100">
                <h3 class="text-lg font-bold text-ink-900">Item Variations</h3>
                <button @click="variationItem = null" class="text-ink-400 hover:text-ink-700">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <div class="px-6 pt-4">
                <div class="font-semibold text-ink-900" x-text="variationItem?.display_name"></div>
                <div class="text-sm text-ink-500" x-show="variationItem?.description" x-text="variationItem?.description"></div>
            </div>

            <div class="p-6">
                <div class="rounded-xl border border-ink-100 overflow-hidden">
                    <div class="grid grid-cols-3 bg-ink-50/60 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-ink-500">
                        <span>Item Name</span>
                        <span>Price</span>
                        <span class="text-right">Action</span>
                    </div>
                    <template x-for="variation in variationItem?.variations ?? []" :key="variation.id">
                        <div class="grid grid-cols-3 items-center px-4 py-3 border-t border-ink-100">
                            <span class="font-medium text-ink-900" x-text="variation.name"></span>
                            <span class="text-ink-600" x-text="'₹' + variation.price.toFixed(2)"></span>
                            <span class="text-right">
                                <button @click="addItem(variationItem.id, variation.id); variationItem = null"
                                        class="rounded-lg bg-brand-600 px-4 py-1.5 text-sm font-medium text-white hover:bg-brand-700">
                                    Select
                                </button>
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    {{-- Receipt --}}
    <div x-show="receipt" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-ink-900/50 p-4">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden" @click.outside="receipt = null">
            <div class="p-6 text-center border-b border-dashed border-ink-200">
                <div class="font-bold text-lg text-ink-900">{{ $branch->restaurant->name }}</div>
                <div class="text-xs text-ink-500">{{ $branch->name }}@if($branch->address) — {{ $branch->address }}@endif</div>
                @if ($branch->gstin)
                    <div class="text-xs text-ink-400">GSTIN: {{ $branch->gstin }}</div>
                @endif
            </div>
            <div class="p-6 space-y-3 text-sm">
                <div class="flex justify-between text-ink-500">
                    <span>Invoice No.</span>
                    <span class="font-medium text-ink-900" x-text="receipt?.invoice_number"></span>
                </div>
                <div class="flex justify-between text-ink-500">
                    <span>Date</span>
                    <span class="font-medium text-ink-900" x-text="receipt?.date"></span>
                </div>
                <div x-show="receipt?.customer_name || receipt?.customer_phone" class="flex justify-between text-ink-500">
                    <span>Customer</span>
                    <span class="font-medium text-ink-900" x-text="[receipt?.customer_name, receipt?.customer_phone].filter(Boolean).join(' · ')"></span>
                </div>

                <div class="border-t pt-3 space-y-1.5">
                    <template x-for="item in receipt?.items ?? []" :key="item.id">
                        <div class="flex justify-between">
                            <span x-text="item.name + ' x' + item.quantity"></span>
                            <span x-text="'₹' + item.line_total"></span>
                        </div>
                    </template>
                </div>

                <div class="border-t pt-3 space-y-1">
                    <div class="flex justify-between text-ink-500"><span>Sub Total</span><span x-text="'₹' + receipt?.subtotal"></span></div>
                    <div class="flex justify-between text-ink-500"><span>Tax</span><span x-text="'₹' + receipt?.tax_total"></span></div>
                    <div class="flex justify-between font-bold text-base text-ink-900"><span>Total</span><span x-text="'₹' + receipt?.grand_total"></span></div>
                </div>

                <div class="text-center text-xs text-ink-400 pt-2">Thank you! Visit again.</div>
            </div>
            <div class="flex gap-3 p-4 bg-ink-50/60">
                <a :href="receipt ? '/invoices/' + receipt.invoice_id + '/print' : '#'" target="_blank"
                   class="flex-1 text-center rounded-lg border border-ink-200 bg-white px-4 py-2 text-sm font-medium text-ink-600 hover:bg-ink-50">
                    Print
                </a>
                <button @click="receipt = null; order = null" class="flex-1 rounded-lg bg-brand-600 px-4 py-2 text-sm font-medium text-white hover:bg-brand-700">
                    New Order
                </button>
            </div>
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
        transactionReference: '',
        error: null,
        receipt: null,
        variationItem: null,
        search: '',
        activeCategory: null,
        itemsMeta: {},
        customerName: '',
        customerPhone: '',

        init() {
            this.itemsMeta = Object.fromEntries((initial.items || []).map((i) => [i.id, i]));
            this.customerName = this.order?.customer_name ?? '';
            this.customerPhone = this.order?.customer_phone ?? '';
        },

        async saveCustomer() {
            if (!this.order) {
                return;
            }
            const { data } = await axios.put(`/api/v1/orders/${this.order.id}/customer`, {
                customer_name: this.customerName || null,
                customer_phone: this.customerPhone || null,
            });
            this.order = data.data;
        },

        isVisible(menuItemId) {
            const meta = this.itemsMeta[menuItemId];
            if (!meta) {
                return true;
            }
            const matchesCategory = this.activeCategory === null || meta.category_id === this.activeCategory;
            const matchesSearch = !this.search || meta.name.includes(this.search.toLowerCase());
            return matchesCategory && matchesSearch;
        },

        async startOrder() {
            this.loading = true;
            this.error = null;
            try {
                const { data } = await axios.post('/api/v1/orders', {
                    customer_name: this.customerName || null,
                    customer_phone: this.customerPhone || null,
                });
                this.order = data.data;
            } catch (e) {
                this.error = e.response?.data?.message || 'Could not start order.';
            } finally {
                this.loading = false;
            }
        },

        chooseItem(menuItemId) {
            const meta = this.itemsMeta[menuItemId];
            if (meta?.variations?.length) {
                this.variationItem = meta;
                return;
            }
            this.addItem(menuItemId);
        },

        async addItem(menuItemId, variationId = null) {
            if (!this.order) {
                await this.startOrder();
            }

            const existing = this.order.items.find((i) => i.menu_item_id === menuItemId && i.menu_item_variation_id === variationId);
            if (existing) {
                return this.updateQty(existing, existing.quantity + 1);
            }

            try {
                const { data } = await axios.post(`/api/v1/orders/${this.order.id}/items`, {
                    menu_item_id: menuItemId,
                    variation_id: variationId,
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
            if (this.loading || !this.order || !this.order.items.length) {
                return;
            }
            this.loading = true;
            this.error = null;
            const snapshot = this.order;
            try {
                const { data } = await axios.post(`/api/v1/orders/${this.order.id}/checkout`, {
                    payment_method: this.paymentMethod,
                    amount: this.order.grand_total,
                    transaction_reference: this.transactionReference || null,
                });
                const invoice = data.data;
                this.receipt = {
                    invoice_id: invoice.id,
                    invoice_number: invoice.invoice_number,
                    date: new Date().toLocaleString('en-IN', { dateStyle: 'medium', timeStyle: 'short' }),
                    customer_name: snapshot.customer_name,
                    customer_phone: snapshot.customer_phone,
                    items: snapshot.items,
                    subtotal: invoice.subtotal,
                    tax_total: invoice.tax_total,
                    grand_total: invoice.grand_total,
                };
                this.customerName = '';
                this.customerPhone = '';
                this.transactionReference = '';
                this.printInvoice(invoice.id);
            } catch (e) {
                this.error = e.response?.status === 403
                    ? 'This order was already placed.'
                    : (e.response?.data?.message || 'Checkout failed.');
            } finally {
                this.loading = false;
            }
        },

        async printInvoice(invoiceId) {
            try {
                await axios.post(`/api/v1/invoices/${invoiceId}/print`);
            } catch (e) {
                // Branch printer is USB/Bluetooth/unconfigured — the server can't reach it
                // directly, so open the receipt's print view and let the OS print dialog
                // (which lists the paired Bluetooth printer) take it from there.
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
