<?php

namespace App\Services\Contracts;

interface ExportTableServiceInterface
{
    public function getExportedFiles($userId);
    public function create(string $userId, string $fileName, string $filePath);
    public function setPath(string $prefix);
    public function show(int $id);
    public function delete(int $id);
    public function checkFileExistence(string $path);
    public function downloadFile(string $path, string $fileName);
    public function getLatestExportedFiles(int $userId);
}
