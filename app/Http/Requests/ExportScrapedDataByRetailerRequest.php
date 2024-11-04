<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportScrapedDataByRetailerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'retailer_id' => ['required', 'integer', 'exists:retailers,id'],
            'date' => ['required', 'date', 'date_format:Y-m-d'],
        ];
    }
}
