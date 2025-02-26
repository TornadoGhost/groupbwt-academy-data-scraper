<?php

namespace App\Services\Contracts;

use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

interface ProductServiceInterface extends BaseCrudServiceInterface
{
    public function productsForMetrics(int $userId): Collection;
    public function import(UploadedFile $file, User $user): JsonResponse;
    public function downloadExampleImportFile(): StreamedResponse;
    public function exportExcel(User $user): JsonResponse;
    public function getNameById(int $id): ?string;
    public function allLatest(User $user): Collection| \Illuminate\Support\Collection;
    public function allPaginate(bool $isAdmin, array $filters): LengthAwarePaginator;
}
