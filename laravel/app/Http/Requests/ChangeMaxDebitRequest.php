<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeMaxDebitRequest extends FormRequest
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
            'max_debit' => 'required|numeric|min:20.0|max:10000.0',
        ];
    }

    public function messages(): array
    {
        return [
            'max_debit.required' => 'Max debit is required',
            'max_debit.numeric' => 'Max debit must be a number',
            'max_debit.min' => 'Max debit must be at least 20.0',
            'max_debit.max' => 'Max debit must be at most 10000.0',
        ];
    }
}
