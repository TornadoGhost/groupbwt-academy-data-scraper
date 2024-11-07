<?php

namespace App\Services\Contracts;

use App\Models\ExportTable;
use Illuminate\Http\JsonResponse;

interface ExportTableServiceInterface
{
    public function getExportedFiles($userId);
    public function create(string $userId, string $fileName, string $filePath);
    public function setPath(string $fileName, int $userId);
    public function findOrFail(int $id): ExportTable;
    public function delete(int $id);
    public function checkFileExistence(string $path);
    public function downloadFile(string $path, string $fileName);
    public function getLatestExportedFiles(int $userId);
    public function deleteFile(ExportTable $file): JsonResponse;
}
