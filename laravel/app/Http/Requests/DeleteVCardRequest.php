<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeleteVCardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->user();

        if ($user->user_type == 'A') {
            return [];
        } 

        return [
            'password' => 'required|string',
            'confirmation_code' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'password.required' => 'The password field is required.',
            'password.string' => 'The password field must be a string.',
            'confirmation_code.required' => 'The confirmation code field is required.',
            'confirmation_code.string' => 'The confirmation code field must be a string.',
        ];
    }
}
