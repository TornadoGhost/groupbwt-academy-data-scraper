<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExportTableRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'file_name' => ['required', 'string'],
            'path' => ['required', 'string'],
        ];
    }
}
