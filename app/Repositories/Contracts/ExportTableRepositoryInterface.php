<?php

namespace App\Repositories\Contracts;

interface ExportTableRepositoryInterface
{
    public function getExportedFiles($userId);
    public function create(string $userId, string $fileName, string $filePath);
    public function show(int $id);
    public function delete(int $id);
}
