<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'bank_id' => 'required',
            'beneficiary_id' => 'nullable',
            'manual_data.account_name' => 'required_if:beneficiary_id,null',
            'manual_data.account_number' => 'required_if:beneficiary_id,null',
            'amount' => ['required', 'regex:/^[0-9]+(\.[0-9][0-9]?)?$/'],
            'wallet_type' => 'nullable',
            'purpose' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'manual_data.*.required_if' => __('Select beneficiary or fill up account name, number, branch name.'),
        ];
    }
}
