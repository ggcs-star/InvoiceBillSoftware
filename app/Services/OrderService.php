<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\SequenceType;
use App\Helpers\TaxCalculator;
use App\Models\Branch;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * The "cart" in this system IS the draft order: items are written straight
 * onto an Order in `open` status, so there is no separate cart representation
 * to keep in sync, and a draft can always be reopened exactly where it was left.
 */
class OrderService
{
    public function __construct(private readonly SequenceGenerator $sequences)
    {
    }

    public function start(Branch $branch, User $cashier, array $customer = []): Order
    {
        return Order::create([
            'branch_id' => $branch->id,
            'user_id' => $cashier->id,
            'order_number' => $this->sequences->next($branch, SequenceType::Order),
            'customer_name' => $customer['customer_name'] ?? null,
            'customer_phone' => $customer['customer_phone'] ?? null,
            'status' => OrderStatus::Open,
        ]);
    }

    public function addItem(Order $order, MenuItem $menuItem, int $quantity, ?string $notes = null): OrderItem
    {
        $item = $order->items()->create([
            'menu_item_id' => $menuItem->id,
            'item_name' => $menuItem->name,
            'unit_price' => $menuItem->price,
            'gst_percentage' => $menuItem->gst_percentage,
            'quantity' => $quantity,
            'tax_amount' => TaxCalculator::lineTax((string) $menuItem->price, $quantity, (string) $menuItem->gst_percentage),
            'line_total' => TaxCalculator::lineTotal((string) $menuItem->price, $quantity, (string) $menuItem->gst_percentage),
            'notes' => $notes,
        ]);

        $this->recalculateTotals($order);

        return $item;
    }

    public function updateItem(OrderItem $item, int $quantity, ?string $notes = null): OrderItem
    {
        $item->update([
            'quantity' => $quantity,
            'notes' => $notes,
            'tax_amount' => TaxCalculator::lineTax((string) $item->unit_price, $quantity, (string) $item->gst_percentage),
            'line_total' => TaxCalculator::lineTotal((string) $item->unit_price, $quantity, (string) $item->gst_percentage),
        ]);

        $this->recalculateTotals($item->order);

        return $item;
    }

    public function removeItem(OrderItem $item): void
    {
        $order = $item->order;
        $item->delete();
        $this->recalculateTotals($order);
    }

    public function applyDiscount(Order $order, string $discountAmount): Order
    {
        $order->update(['discount_total' => $discountAmount]);

        return $this->recalculateTotals($order);
    }

    public function cancel(Order $order): Order
    {
        $order->update(['status' => OrderStatus::Cancelled]);

        return $order;
    }

    public function recalculateTotals(Order $order): Order
    {
        return DB::transaction(function () use ($order) {
            $items = $order->items()->get();

            $subtotal = $items->reduce(
                fn ($carry, $item) => bcadd($carry, TaxCalculator::lineAmount((string) $item->unit_price, $item->quantity), 2),
                '0.00'
            );

            $taxTotal = $items->reduce(
                fn ($carry, $item) => bcadd($carry, (string) $item->tax_amount, 2),
                '0.00'
            );

            $discount = (string) $order->discount_total;
            $grandTotal = bcsub(bcadd($subtotal, $taxTotal, 2), $discount, 2);

            $order->update([
                'subtotal' => $subtotal,
                'tax_total' => $taxTotal,
                'grand_total' => max($grandTotal, '0.00'),
            ]);

            return $order->refresh();
        });
    }
}
