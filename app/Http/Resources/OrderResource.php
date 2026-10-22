<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'customer_name' => $this->customer_name,
            'customer_phone' => $this->customer_phone,
            'subtotal' => (float) $this->subtotal,
            'tax_total' => (float) $this->tax_total,
            'discount_total' => (float) $this->discount_total,
            'grand_total' => (float) $this->grand_total,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'invoice' => new InvoiceResource($this->whenLoaded('invoice')),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
