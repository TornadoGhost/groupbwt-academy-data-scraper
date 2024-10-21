<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:3|max:100',
            'manufacturer_part_number' => [
                'required',
                'string',
                'min:3',
                'max:50'
            ],
            'pack_size' => 'required|string|min:3|max:20',
            'retailers' => 'required|array',
            'retailers.retailer_id.*' => 'exists:retailers,id',
            'retailers.product_url.*' => 'string|min:5|max:255',
        ];
    }
}
