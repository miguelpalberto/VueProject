<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthUserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|email|max:255'
        ];
    }

    public function messages(): array//////
    {
        return [
            'name.min' => 'The minimum character limit for the name is 3 characters',
            'name.max' => 'The maximum character limit for the name is 255 characters',
            'name.required' => 'The name is required',
            'name.string' => 'The name must be a string',
            'email.required' => 'Email is required',
            'email.email' => 'Email format is invalid',
            'email.max' => 'Email must have a maximum of 255 characters',
        ];
    }
}
