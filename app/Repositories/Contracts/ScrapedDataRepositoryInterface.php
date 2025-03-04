<?php

namespace App\Repositories\Contracts;

interface ScrapedDataRepositoryInterface extends BaseRepositoryInterface
{
    public function avgRating(array $products, array $retailers, string $startDate, string $endDate, int $userId);

    public function avgPrice(array $products, array $retailers, string $startDate, string $endDate, int $userId);

    public function avgImages(array $products, array $retailers, string $startDate, string $endDate, int $userId);
    public function scrapedDataByRetailer(int $retailerId, string $date);
}
