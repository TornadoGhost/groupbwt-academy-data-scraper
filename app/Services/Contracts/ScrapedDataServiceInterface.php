<?php

namespace App\Services\Contracts;

interface ScrapedDataServiceInterface extends BaseCrudServiceInterface
{
    public function avgRating(int $productId, string $mpn, int $retailerId, string $startDate, string $endDate, int $userId);

    public function avgPrice(int $productId, string $mpn, int $retailerId, string $startDate, string $endDate, int $userId);

    public function avgImages(int $productId, string $mpn, int $retailerId, string $startDate, string $endDate, int $userId);
}
