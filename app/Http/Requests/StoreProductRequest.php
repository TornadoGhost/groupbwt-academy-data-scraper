<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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
                Rule::unique('products')->where(function ($query) {
                    return $query->where('user_id', auth()->user()->id);
                }),
                'min:3',
                'max:50'
            ],
            'pack_size' => 'required|string|min:3|max:20',
            'retailers' => 'required|array',
            'retailers.*.retailer_id' => 'required|distinct|exists:retailers,id',
            'retailers.*.product_url' => 'required|string|min:5|max:255',
            'images' => 'array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048'
        ];
    }

    public function messages()
    {
        return [
            'retailers.*.retailer_id.required' => 'The retailer field is required.',
            'retailers.*.retailer_id.distinct' => 'The retailer must be unique in the list.',
            'retailers.*.retailer_id.exists' => 'The selected retailer does not exist in the database.',

            'retailers.*.product_url.required' => 'The product URL is required.',
            'retailers.*.product_url.string' => 'The product URL must be a string.',
            'retailers.*.product_url.min' => 'The product URL must be at least :min characters.',
            'retailers.*.product_url.max' => 'The product URL may not be greater than :max characters.',
        ];
    }
}
