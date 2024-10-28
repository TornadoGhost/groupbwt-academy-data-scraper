<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRetailerAccessRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'users_id' => ['required', 'array'],
            'users_id.*' => ['exists:users,id'],
        ];
    }
}
