<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangeVCardConfirmationCodeRequest extends FormRequest
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
            'password' => 'required|string',
            'confirmation_code' => 'required|string|size:4',
            'confirmation_code_confirmation' => 'required|string|same:confirmation_code'
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'The password is required',
            'password.string' => 'The password must be a string',
            'confirmation_code.required' => 'The confirmation code is required',
            'confirmation_code.string' => 'The confirmation code must be a string',
            'confirmation_code.size' => 'The confirmation code must have exactly 4 characters',
            'confirmation_code_confirmation.required' => 'The confirmation code confirmation is required',
            'confirmation_code_confirmation.string' => 'The confirmation code confirmation must be a string',
            'confirmation_code_confirmation.same' => 'The confirmation code confirmation must be equal to the confirmation code'
        ];
    }
}
