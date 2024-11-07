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
    public function getExportedFiles(int $userId): Collection
    {
        return $this->exportTable->where("user_id", $userId)->get();
    }

    public function getLatestExportedFiles(int $userId): Collection
    {
        return $this->exportTable->where("user_id", $userId)->latest()->get();
    }

    public function create(string $userId, string $fileName, string $filePath): ExportTable
    {
        return $this->exportTable->create([
            'file_name' => $fileName,
            'path' => $filePath,
            'user_id' => $userId,
        ]);
    }

    public function findOrFail(int $id): ExportTable
    {
        return $this->exportTable->findOrFail($id);
    }

    public function delete(int $id): bool
    {
        return $this->exportTable->findOrFail($id)->delete();
    }
}
