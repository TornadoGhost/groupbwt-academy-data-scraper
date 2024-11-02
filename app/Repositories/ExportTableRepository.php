<?php

namespace App\Repositories;

use App\Models\ExportTable;
use App\Repositories\Contracts\ExportTableRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ExportTableRepository implements ExportTableRepositoryInterface
{
    protected ExportTable $exportTable;
    public function __construct(ExportTable $exportTable){
        $this->exportTable = $exportTable;
    }
    public function getExportedFiles($userId): Collection
    {
        return $this->exportTable->where("user_id", $userId)->get();
    }

    public function create(string $userId, string $fileName, string $filePath): ExportTable
    {
        return $this->exportTable->create([
            'file_name' => $fileName,
            'path' => $filePath,
            'user_id' => $userId,
        ]);
    }
}
