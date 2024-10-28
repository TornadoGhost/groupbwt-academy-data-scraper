<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'min:5', 'max:75'],
            'username' => ['required', 'string', 'min:3', 'max:50'],
            'password' => ['min:6', 'max:255'],
            'isAdmin' => ['numeric'],
            'region_id' => ['numeric', 'exists:regions,id'],
        ];
    }
}
