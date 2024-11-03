<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MetricExportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'start_date' => ['nullable', 'date_format:Y-m-d'],
            'end_date' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
