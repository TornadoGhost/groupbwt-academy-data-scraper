<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|min:5|max:75',
            'username' => 'required|string|min:3|max:50',
            'password' => 'min:6|max:255',
            'isAdmin' => 'numeric',
            'region_id' => 'required|numeric'
        ];
    }
}
