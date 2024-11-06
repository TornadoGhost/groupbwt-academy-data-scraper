<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProductsImport implements ToModel, WithBatchInserts, WithChunkReading, WithHeadingRow, ShouldQueue
{
    use Importable;

    public function __construct(
        public User $user,
    )
    {
    }

    public function model(array $row): Product|null
    {
        return new Product([
            'title' => $row['title'],
            'manufacturer_part_number' => $row['manufacturer_part_number'],
            'pack_size' => $row['pack_size'],
            'user_id' => $this->user->id,
        ]);
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
