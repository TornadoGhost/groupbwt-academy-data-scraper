<?php

namespace App\Services;

use App\Repositories\Contracts\ScrapedDataRepositoryInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;

class ScrapedDataService extends BaseCrudService implements ScrapedDataServiceInterface
{


    protected function getRepositoryClass()
    {
        return ScrapedDataRepositoryInterface::class;
    }

    public function avgRating(int $productId = 0, string $mpn = '', int $retailerId = 0, string $date = '', int $userId = 0)
    {
        return $this->repository()->avgRating($productId, $mpn, $retailerId, $date, $userId);
    }

    public function avgPrice(int $productId = 0, string $mpn = '', int $retailerId = 0, string $date = '', int $userId = 0)
    {
        return $this->repository()->avgPrice($productId, $mpn, $retailerId, $date, $userId);
    }

    public function avgImages(int $productId = 0, string $mpn = '', int $retailerId = 0, string $date = '', int $userId = 0)
    {
        return $this->repository()->avgImages($productId, $mpn, $retailerId, $date, $userId);
    }

    public function getLatestScrapedData()
    {
        return $this->repository()->getLatestScrapedData();
    }
}
