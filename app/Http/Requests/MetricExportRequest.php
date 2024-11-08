<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetricExportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'products' => ['nullable', 'array'],
            'products.*' => ['integer', 'exists:products,id'],
            'retailers' => ['nullable', 'array'],
            'retailers.*' => ['integer', 'exists:retailers,id'],
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
        ];
    }
}
