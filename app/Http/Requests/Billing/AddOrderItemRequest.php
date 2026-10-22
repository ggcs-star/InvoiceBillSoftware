<?php

namespace App\Http\Requests\Billing;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_item_id' => [
                'required',
                Rule::exists('menu_items', 'id')->where('branch_id', $this->route('order')->branch_id),
            ],
            'quantity' => ['required', 'integer', 'min:1', 'max:999'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
