<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Http\JsonResponse;

interface ScrapedDataServiceInterface extends BaseCrudServiceInterface
{
    public function avgRating(array $products, array $retailers, string $startDate, string $endDate, int $userId);

    public function avgPrice(array $products, array $retailers, string $startDate, string $endDate, int $userId);

    public function avgImages(array $products, array $retailers, string $startDate, string $endDate, int $userId);

    public function exportByRetailer(int $retailer_id, string $date, User $user, array $filters): JsonResponse;
}
