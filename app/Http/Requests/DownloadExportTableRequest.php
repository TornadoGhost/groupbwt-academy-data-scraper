<?php

namespace App\Http\Requests;

use App\Models\ExportTable;
use Illuminate\Foundation\Http\FormRequest;

class DownloadExportTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        $get = ExportTable::where('user_id', auth()->id())
            ->where('path', $this->input('file_path'))
            ->get();

        if (!$get->isEmpty()) {
            return true;
        }

        return false;
    }

    public function rules(): array
    {
        return [
            'file_path' => ['required','string'],
        ];
    }
}
