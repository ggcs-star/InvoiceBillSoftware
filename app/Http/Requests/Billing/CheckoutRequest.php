<?php

namespace App\Http\Requests\Billing;

use App\Enums\PaymentMethod;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', new Enum(PaymentMethod::class)],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'transaction_reference' => ['nullable', 'string', 'max:255'],
        ];
    }
}
