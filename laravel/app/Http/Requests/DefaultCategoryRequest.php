<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DefaultCategoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;//
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|in:C,D',
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('name')->ignore($this->id),
            ]
        ];
    }


    public function messages(): array//////
    {
        return [
            'name.max' => 'The maximum character limit for the name is 255 characters',
            'name.required' => 'The name is required',
            'name.string' => 'The name must be a string',
            'name:unique' => 'The name must be unique',
            'type.required' => 'The transaction type is required',
            'type.in' => 'The type must be C (Credit) or D (Debit)',
        ];
    }
}
