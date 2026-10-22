<?php

namespace App\Http\Requests\Admin;

use App\Enums\PrinterConnectionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PrinterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'connection_type' => ['required', new Enum(PrinterConnectionType::class)],
            'ip_address' => ['nullable', 'required_if:connection_type,network', 'ip'],
            'port' => ['nullable', 'integer', 'min:1', 'max:65535'],
            'mac_address' => ['nullable', 'string', 'max:255'],
            'is_default' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
