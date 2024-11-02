<?php

namespace App\Services\Contracts;

interface ExportTableServiceInterface
{
    public function getExportedFiles($userId);
    public function create(string $userId, string $fileName, string $filePath);
}
