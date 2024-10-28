<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRetailerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'reference' => ['required', 'string', 'min:5', 'max:255'],
            'currency' => ['required', 'string', 'min:2', 'max:20'],
            'logo_path' => ['required', 'string', 'min:5', 'max:255'],
            'isActive' => ['int'],
        ];
    }
}
