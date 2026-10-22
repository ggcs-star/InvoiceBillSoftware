<?php

namespace App\Services;

use App\Enums\InvoicePaymentStatus;
use App\Enums\OrderStatus;
use App\Enums\SequenceType;
use App\Models\Invoice;
use App\Models\Order;

class InvoiceService
{
    public function __construct(
        private readonly SequenceGenerator $sequences,
        private readonly PaymentService $payments,
    ) {
    }

    public function generate(Order $order): Invoice
    {
        if ($order->invoice) {
            return $order->invoice;
        }

        $invoice = $order->invoice()->create([
            'branch_id' => $order->branch_id,
            'invoice_number' => $this->sequences->next($order->branch, SequenceType::Invoice),
            'subtotal' => $order->subtotal,
            'tax_total' => $order->tax_total,
            'discount_total' => $order->discount_total,
            'grand_total' => $order->grand_total,
            'payment_status' => $this->resolvePaymentStatus($order),
            'printed_count' => 0,
        ]);

        $order->update([
            'status' => OrderStatus::Completed,
            'completed_at' => now(),
        ]);

        return $invoice;
    }

    private function resolvePaymentStatus(Order $order): InvoicePaymentStatus
    {
        $paid = $this->payments->totalPaid($order);

        if (bccomp($paid, '0.00', 2) <= 0) {
            return InvoicePaymentStatus::Unpaid;
        }

        return $this->payments->isFullyPaid($order)
            ? InvoicePaymentStatus::Paid
            : InvoicePaymentStatus::PartiallyPaid;
    }
}
