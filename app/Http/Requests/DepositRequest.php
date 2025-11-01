<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'gateway_code' => 'required|exists:deposit_methods,gateway_code',
            'amount' => ['required', 'numeric'],
            'wallet_type' => 'nullable',
            'manual_data' => 'nullable|array',
        ];
    }
}
