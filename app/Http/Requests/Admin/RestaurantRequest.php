<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'cuisine_type' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'owner_id' => [
                'nullable',
                Rule::exists('users', 'id'),
            ],
        ];
    }
}
