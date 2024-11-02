<?php

namespace App\Services;

use App\Models\ExportTable;
use App\Repositories\Contracts\ExportTableRepositoryInterface;
use App\Repositories\ExportTableRepository;
use App\Services\Contracts\ExportTableServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ExportTableService implements ExportTableServiceInterface
{
    protected ExportTableRepository $repository;

    public function __construct(ExportTableRepositoryInterface $repository){
        $this->repository = $repository;
    }

    public function getExportedFiles($userId): Collection
    {
        return $this->repository->getExportedFiles($userId);
    }

    public function create(string $userId, string $fileName, string $filePath): ExportTable
    {
        return $this->repository->create($userId, $fileName, $filePath);
    }
}
