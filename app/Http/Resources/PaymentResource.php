<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'method' => $this->method->value,
            'method_label' => $this->method->label(),
            'amount' => (float) $this->amount,
            'status' => $this->status->value,
            'transaction_reference' => $this->transaction_reference,
            'paid_at' => $this->paid_at?->toIso8601String(),
        ];
    }
}
