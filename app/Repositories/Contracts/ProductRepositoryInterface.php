<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function productsForMetrics(int $userId);
    public function getNameById(int $id): ?string;
    public function allLatest(User $user): Collection;
}
