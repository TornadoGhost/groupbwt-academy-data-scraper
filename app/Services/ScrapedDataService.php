<?php

namespace App\Services;

use App\Repositories\Contracts\ScrapedDataRepositoryInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ScrapedDataService extends BaseCrudService implements ScrapedDataServiceInterface
{


    protected function getRepositoryClass(): string
    {
        return ScrapedDataRepositoryInterface::class;
    }

    public function avgRating(int $productId = 0, string $mpn = '', int $retailerId = 0, string $startDate = '', string $endDate = '', int $userId = 0): Collection
    {
        return $this->repository()->avgRating($productId, $mpn, $retailerId, $startDate, $endDate, $userId);
    }

    public function avgPrice(int $productId = 0, string $mpn = '', int $retailerId = 0, string $startDate = '', string $endDate = '', int $userId = 0): Collection
    {
        return $this->repository()->avgPrice($productId, $mpn, $retailerId, $startDate, $endDate, $userId);
    }

    public function avgImages(int $productId = 0, string $mpn = '', int $retailerId = 0, string $startDate = '', string $endDate = '', int $userId = 0): Collection
    {
        return $this->repository()->avgImages($productId, $mpn, $retailerId, $startDate, $endDate, $userId);
    }
}
