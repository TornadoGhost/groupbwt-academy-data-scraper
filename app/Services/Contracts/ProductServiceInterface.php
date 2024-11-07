<?php

namespace App\Services\Contracts;

use App\Models\User;
use App\Services\Contracts\BaseCrudServiceInterface;
use Illuminate\Http\UploadedFile;

interface ProductServiceInterface extends BaseCrudServiceInterface
{
    public function findByMpn(string $mpn);
    public function productsForMetrics(int $userId);
    public function import(UploadedFile $file, User $user);
    public function downloadExampleImportFile();
    public function exportExcel(User $user);
}
