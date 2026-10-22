<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'category_name' => $this->category?->name,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'gst_percentage' => (float) $this->gst_percentage,
            'is_available' => $this->is_available,
            'image_url' => $this->image_path ? asset('storage/' . $this->image_path) : null,
        ];
    }
}
