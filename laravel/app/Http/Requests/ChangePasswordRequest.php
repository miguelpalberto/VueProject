<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/|different:old_password|confirmed',
            'new_password_confirmation' => 'required|string|same:new_password'
        ];
    }

    public function messages(): array
    {
        return [
            'old_password.required' => 'The current password is required',
            'old_password.string' => 'The current password must be a string',
            'new_password.required' => 'The new password is required',
            'new_password.string' => 'The new password must be a string',
            'new_password.min' => 'The new password must have at least 8 characters',
            'new_password.regex' => 'The new password must contain at least one uppercase letter, one lowercase letter and one number',
            'new_password.different' => 'The new password must be different from the old password',
            'new_password.confirmed' => 'The new password confirmation does not match the new password',
            'new_password_confirmation.required' => 'The new password confirmation is required',
            'new_password_confirmation.string' => 'The new password confirmation must be a string',
            'new_password_confirmation.same' => 'The new password confirmation must be equal to the new password'
        ];
    }
}
