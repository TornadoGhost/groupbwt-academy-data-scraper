<?php

namespace App\Http\Requests;

use App\Models\ExportTable;
use Illuminate\Foundation\Http\FormRequest;

class DownloadExportTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        $get = ExportTable::where('user_id', auth()->id())
            ->where('file_name', $this->input('file_name'))
            ->get();

        if (!$get->isEmpty()) {
            return true;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'file_name' => ['required','string'],
        ];
    }
}
