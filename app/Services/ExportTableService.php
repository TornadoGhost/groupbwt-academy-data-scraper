<?php

namespace App\Services;

use App\Models\ExportTable;
use App\Repositories\Contracts\ExportTableRepositoryInterface;
use App\Repositories\ExportTableRepository;
use App\Services\Contracts\ExportTableServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;

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

    public function show(int $id): ExportTable
    {
        return $this->repository->show($id);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function setPath(string $prefix): array
    {
        $fileName = "{$prefix}_" . md5(now());
        $filePath = 'excel/export/' . auth()->id() . "/{$prefix}/" . $fileName . '.xlsx';

        return [
            'fileName' => $fileName,
            'filePath' => $filePath,
        ];
    }

    public function checkFileExistence(string $path): bool
    {
        return Storage::exists($path);
    }

    public function getFile(string $path): ?string
    {
        return Storage::get($path);
    }

    public function deleteFile(string $path): bool
    {
        return Storage::delete($path);
    }
}
