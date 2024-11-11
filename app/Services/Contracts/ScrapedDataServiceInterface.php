<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

interface ScrapedDataServiceInterface extends BaseCrudServiceInterface
{
    public function avgRating(
        array $products, array $retailers, string $startDate, string $endDate, int $userId
    ): Collection;

    public function avgPrice(
        array $products, array $retailers, string $startDate, string $endDate, int $userId
    ): Collection;

    public function avgImages(
        array $products, array $retailers, string $startDate, string $endDate, int $userId
    ): Collection;

    public function scrapedDataByRetailer(int $retailerId, string $date): ?Collection;

    public function exportByRetailer(int $retailer_id, string $date, User $user, array $filters): JsonResponse;
}
