<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class VCardRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->id),
            ],
            'password' => 'required|string',
            'confirmation_code' => 'required|string',
            'balance' => 'required|float|min:0.00|max:0.00',
            'max_debit' => 'required|float|min:5000.00|max:5000.00',
        ];
    }

    public function messages(): array//////
    {
        return [
            'name.max' => 'The maximum character limit for the name is 255 characters',
            'name.required' => 'The name is required',
            'name.string' => 'The name must be a string',
            'email.required' => 'Email is required',
            'email.email' => 'Email format is invalid',
            'email.unique' => 'Email must be unique',
            'email.max' => 'Email must have a maximum of 255 characters',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string',
            'confirmation_code.required' => 'Confirmation code is required',
            'confirmation_code.string' => 'Confirmation code must be a string',
            'balance.required' => 'Balance is required',
            'balance.float' => 'Balance must be a float',
            'balance.min' => 'Default balance must be 0.00',
            'balance.max' => 'Default balance must be 0.00',
            'max_debit.required' => 'Max debit is required',
            'max_debit.float' => 'Max debit must be a float',
            'max_debit.min' => 'Default debit must be 5000.00',
            'max_debit.max' => 'Default debit must be 5000.00',




        ];
    }
}
