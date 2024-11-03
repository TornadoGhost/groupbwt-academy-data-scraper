<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProductsImport implements ToModel, WithBatchInserts, WithChunkReading, WithValidation
{
    public function model(array $row): Product|null
    {
        return new Product([
            'title' => $row[0],
            'manufacturer_part_number' => $row[1],
            'pack_size' => $row[2],
            'user_id' => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:3', 'max:100'],
            'manufacturer_part_number' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('products')->where(fn ($query) => $query->where('user_id', auth()->id())),
            ],
            'pack_size' => ['required', 'string', 'min:3', 'max:20'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'manufacturer_part_number.required' => 'MPN field is required.',
            'manufacturer_part_number.string' => 'MPN field should be a string.',
            'manufacturer_part_number.min' => 'MPN field should be at least 3 characters.',
            'manufacturer_part_number.max' => 'MPN field should be less than 50 characters.',
            'manufacturer_part_number.unique' => 'This MPN already exists.',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
