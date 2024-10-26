<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|unique:users|min:5|max:75',
            'username' => 'required|string|min:3|max:50',
            'password' => 'required|min:6|max:255',
            'isAdmin' => 'numeric',
            'region_id' => 'required|numeric|exists:regions,id'
        ];
    }
}
