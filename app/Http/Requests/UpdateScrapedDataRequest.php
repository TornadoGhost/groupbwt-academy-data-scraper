<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScrapedDataRequest extends FormRequest
{
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
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'session_id' => ['required', 'integer', 'exists:scraping_sessions,id'],
            'mpn' => ['string', 'max:50'],

        ];
    }
}
