<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreScrapedDataRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric'],
            'avg_rating' => ['required', 'numeric'],
            'stars_1' => ['numeric'],
            'stars_2' => ['numeric'],
            'stars_3' => ['numeric'],
            'stars_4' => ['numeric'],
            'stars_5' => ['numeric'],
            'retailer_id' => ['required', 'integer', 'exists:retailers,id'],
            'session_id' => ['required', 'integer', 'exists:scraping_sessions,id'],
            'mpn' => ['required', 'string', 'max:50'],
            'images' => ['array'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
