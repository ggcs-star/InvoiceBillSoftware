<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'order_id' => $this->order_id,
            'subtotal' => (float) $this->subtotal,
            'tax_total' => (float) $this->tax_total,
            'discount_total' => (float) $this->discount_total,
            'grand_total' => (float) $this->grand_total,
            'payment_status' => $this->payment_status->value,
            'payment_status_label' => $this->payment_status->label(),
            'printed_count' => $this->printed_count,
            'last_printed_at' => $this->last_printed_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
