<?php

namespace App\Http\Requests;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;

class ImageProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        $product = Product::findOrFail($this->product_id);
        if ($product->user_id === auth()->id()) {
            return true;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|int|exists:products,id',
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ];
    }
}
