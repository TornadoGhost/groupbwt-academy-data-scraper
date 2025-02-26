<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function productsForMetrics(int $userId);
    public function getNameById(int $id): ?string;
    public function allLatest(User $user): Collection| \Illuminate\Support\Collection;
    public function allPaginate(bool $isAdmin, array $filters): LengthAwarePaginator;
}
