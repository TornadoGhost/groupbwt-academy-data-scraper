<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRetailerRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:100',
            'reference' => 'required|string|min:5|max:255',
            'currency' => 'required|string|min:2|max:20',
            'logo_path' => 'required|string|min:5|max:255',
            'isActive' => 'int'
        ];
    }
}
