<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'menu_item_id' => $this->menu_item_id,
            'menu_item_variation_id' => $this->menu_item_variation_id,
            'name' => $this->item_name,
            'unit_price' => (float) $this->unit_price,
            'gst_percentage' => (float) $this->gst_percentage,
            'quantity' => $this->quantity,
            'tax_amount' => (float) $this->tax_amount,
            'line_total' => (float) $this->line_total,
            'notes' => $this->notes,
        ];
    }
}
