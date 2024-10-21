<?php

namespace App\Http\Requests;

use App\Enums\ScrapingSessionStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateScrapingSessionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'retailer_id' => 'int|exists:retailers,id',
            'status_code' => [
                'required',
                'integer',
                Rule::enum(ScrapingSessionStatus::class)->only([ScrapingSessionStatus::COMPLETED, ScrapingSessionStatus::FAILED]),
            ],
            'started_at' => 'date',
            'ended_at' => 'date'
        ];
    }
}
