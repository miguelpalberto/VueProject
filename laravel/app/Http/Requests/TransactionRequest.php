<?php

namespace App\Http\Requests;

use App\Rules\PaymentReferenceValidationRule;
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
            'vcard' => 'required|string|exists:vcards,phone_number,deleted_at,NULL',
            'type' => 'required|in:C,D',
            'value' => 'required|decimal:0,2|min:0.01',
            'payment_reference' => [
                'required',
                new PaymentReferenceValidationRule()
            ],
            'pair_vcard' => 'sometimes|nullable|required_if:payment_type,VCARD|string|different:vcard|exists:vcards,phone_number,deleted_at,NULL',
            'payment_type' => 'required|in:VISA,MB,IBAN,PAYPAL,MBWAY,VCARD',
            'category_id' => 'sometimes|nullable|integer|exists:categories,id',
            'description' => 'sometimes|nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'The transaction type is required',
            'type.in' => 'The type must be C (Credit) or D (Debit)',
            'vcard.string' => 'The vCard must be a string',
            'vcard.exists' => 'Vcard was not found',
            'value.required' => 'The value is required',
            'value.decimal' => 'The value must be a decimal number with 2 decimal places',
            'value.min' => 'The value must be greater than 0.01',
            'pair_vcard.required_if' => 'The payment type is VCARD, so the destination vCard is required',
            'pair_vcard.string' => 'The pair vcard must be a string',
            'pair_vcard.different' => 'The destination vCard must be different from the origin vCard',
            'pair_vcard.exists' => 'Destination vCard was not found',
            'payment_type.required' => 'The payment type is required',
            'payment_type.in' => 'The payment type must be VISA, MB, IBAN, PAYPAL, MBWAY or VCARD',
            'category_id.integer' => 'The category id must be an integer',
            'category_id.exists' => 'Category was not found',
            'description.string' => 'The description must be a string',
            'description.max' => 'The maximum character limit for the description is 255 characters',
        ];
    }
}
