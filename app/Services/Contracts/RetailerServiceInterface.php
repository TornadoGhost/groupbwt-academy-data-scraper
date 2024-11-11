<?php

namespace App\Services\Contracts;

use App\Models\Retailer;
use Illuminate\Database\Eloquent\Collection;

interface RetailerServiceInterface extends BaseCrudServiceInterface
{
    public function grandAccess(int $retailer_id, array $users_id): bool;
    public function revokeAccess(int $retailer_id, array $users_id): bool;
    public function restore(int $uid): bool;
    public function findWithUsers(int $id): Retailer;
    public function list(): Collection;
    public function retailersForMetrics(int $userId): Collection;
    public function getNameById(int $retailerId): ?string;
    public function prepareDataForIndexView(): array;
}
