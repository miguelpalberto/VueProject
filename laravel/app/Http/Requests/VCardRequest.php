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
            'phone_number' => [
                'required',
                'string',
                'size:9',
                'regex:/^9\d{8}$/',
                Rule::unique('vcards', 'phone_number')->ignore($this->id),
            ],
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('vcards', 'email')->ignore($this->id),
            ],
            'password' => 'required|string',
            'confirmation_code' => 'required|string|size:4', //mudar para o enunciado
            'custom_options' => 'sometimes|json',
            'custom_data' => 'sometimes|json',
            'photo_file' =>         'sometimes|image|max:4096' // maxsize = 4Mb
        ];
    }

    public function messages(): array//////
    {
        return [
            'phone_number.required' => 'Phone number is required',
            'phone_number.string' => 'Phone number must be a string',
            'phone_number.unique' => 'Phone number must be unique',
            'phone_number.regex' => 'Phone number format is invalid. It must a standard Portuguese mobile phone number',
            'phone_number.size' => 'Phone number must have exactly 9 characters',
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
            'confirmation_code.size' => 'Confirmation code must have exactly 4 characters',
            'photo_file.image' =>    'The photo file must be an image',
            'photo_file.size' =>     'The photo file must be smaller than 4Mb',
            'custom_options.json' => 'Custom options must be a valid JSON',
            'custom_data.json' =>    'Custom data must be a valid JSON'
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'custom_options' => json_encode($this->custom_options),
            'custom_data' => json_encode($this->custom_data)
        ]);
    }
}
