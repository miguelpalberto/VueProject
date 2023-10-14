<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionRequest extends FormRequest
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
            'vcard' => 'required|string|exists:vcards,phone_number',
            'type' => 'required|in:C,D',
            'value' => 'required|float|min:0.01',
            'payment_type' => 'required|in:VISA,MB,IBAN,PAYPAL,MBWAY,VCARD',
            'category_id' => 'integer|exists:categories,id',
            'description' => 'string|max:255',
        ];
    }

    public function messages(): array//////
    {
        return [
            'type.required' => 'The transaction type is required',
            'type.in' => 'The type must be C (Credit) or D (Debit)',
            'vcard.string' => 'The vcard must be a string',
            'vcard.exists' => 'Vcard not found on the DB',
            'value.required' => 'The value is required',
            'value.float' => 'The value must be a float',
            'value.min' => 'The value must be greater than 0.01',
            'payment_type.required' => 'The payment type is required',
            'payment_type.in' => 'The payment type must be VISA, MB, IBAN, PAYPAL, MBWAY or VCARD',
            'category_id.integer' => 'The category id must be an integer',
            'category_id.exists' => 'Category not found on the DB',
            'description.string' => 'The description must be a string',
            'description.max' => 'The maximum character limit for the description is 255 characters',



        ];
    }
}
