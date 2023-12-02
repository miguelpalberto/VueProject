<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'type' => 'required|in:C,D',
            'custom_options' => 'sometimes|json',
            'custom_data' => 'sometimes|json',
        ];
    }

    public function messages(): array//////
    {
        return [
            'name.max' => 'The maximum character limit for the name is 255 characters',
            'name.required' => 'The name is required',
            'name.string' => 'The name must be a string',
            'type.required' => 'The transaction type is required',
            'type.in' => 'The type must be C (Credit) or D (Debit)',
            'vcard.string' => 'The vcard must be a string',
            'vcard.exists' => 'Vcard not found on the DB',
            'custom_options.json' => 'The custom options must be a valid JSON',
            'custom_data.json' => 'The custom data must be a valid JSON',
        ];
    }
}
