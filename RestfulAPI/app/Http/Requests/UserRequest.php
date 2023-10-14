<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->id),
            ],
            'password_inicial' => 'sometimes|required',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' =>  'O nome é obrigatório',
            'name.string' =>  'O nome tem de ser uma string',
            'name.max' =>  'O nome tem de ter no máximo 255 caracteres',
            'email.required' => 'O email é obrigatório',
            'email.email' =>    'O formato do email é inválido',
            'email.unique' =>   'O email tem que ser único',
            'email.max' =>      'O email tem de ter no máximo 255 caracteres',

            'password_inicial.required' => 'A password inicial é obrigatória',
        ];
    }
}
