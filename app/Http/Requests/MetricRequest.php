<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetricRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'products' => ['nullable', 'array'],
            'products.*' => ['int', 'exists:products,id'],
            'retailers' => ['nullable', 'array'],
            'retailers.*' => ['int', 'exists:retailers,id'],
        ];
    }
}
