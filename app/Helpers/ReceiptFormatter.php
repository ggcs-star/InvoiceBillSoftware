<?php

namespace App\Helpers;

use App\Models\Invoice;

/**
 * Builds one plain data structure describing a receipt. Both the ESC/POS
 * printer service and the browser print-preview view render from this same
 * structure, so the two outputs can never drift out of sync with each other.
 */
class ReceiptFormatter
{
    public static function build(Invoice $invoice): array
    {
        $order = $invoice->order->loadMissing(['items', 'cashier', 'payments']);
        $branch = $invoice->branch->loadMissing('restaurant');

        return [
            'restaurant' => [
                'name' => $branch->restaurant->name . ($branch->name ? " \u{2014} {$branch->name}" : ''),
                'address' => $branch->address,
                'phone' => $branch->phone,
                'gstin' => $branch->gstin,
            ],
            'invoice_number' => $invoice->invoice_number,
            'order_number' => $order->order_number,
            'date' => $invoice->created_at->format('d-M-Y h:i A'),
            'cashier' => $order->cashier?->name,
            'customer_name' => $order->customer_name,
            'customer_phone' => $order->customer_phone,
            'items' => $order->items->map(fn ($item) => [
                'name' => $item->item_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'gst_percentage' => $item->gst_percentage,
                'tax_amount' => $item->tax_amount,
                'line_total' => $item->line_total,
                'notes' => $item->notes,
            ])->all(),
            'subtotal' => $invoice->subtotal,
            'tax_total' => $invoice->tax_total,
            'discount_total' => $invoice->discount_total,
            'grand_total' => $invoice->grand_total,
            'payment_status' => $invoice->payment_status->label(),
            'payments' => $order->payments->map(fn ($payment) => [
                'method' => $payment->method->label(),
                'amount' => $payment->amount,
            ])->all(),
        ];
    }
}
