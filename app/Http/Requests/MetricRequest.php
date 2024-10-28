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
            'product_id' => 'nullable|int|exists:products,id',
            'manufacturer_part_number' => 'nullable|string|exists:products,manufacturer_part_number',
            'retailer_id' => 'nullable|int|exists:retailers,id',
        ];
    }
}
