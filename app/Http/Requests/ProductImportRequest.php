<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductImportRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'csv_file' => 'required|file|mimes:csv,txt'
        ];
    }
}
