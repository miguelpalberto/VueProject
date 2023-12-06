<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadPhotoRequest extends FormRequest
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
            'photo_file' => 'required|image|max:4096'
        ];
    }

    public function messages(): array
    {
        return [
            'photo_file.required' => 'The photo is required',
            'photo_file.image' => 'The photo must be an image',
            'photo_file.max' => 'The photo must be less than 4MB'
        ];
    }
}
