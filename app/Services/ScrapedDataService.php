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

    public function avgRating(
        array $products = [],
        array $retailers = [],
        string $startDate = '',
        string $endDate = '',
        int $userId = 0
    ): Collection
    {
        return $this->repository()->avgRating(
            $products,
            $retailers,
            $startDate,
            $endDate,
            $userId
        );
    }

    public function avgPrice(
        array $products = [],
        array $retailers = [],
        string $startDate = '',
        string $endDate = '',
        int $userId = 0
    ): Collection
    {
        return $this
            ->repository()
            ->avgPrice(
                $products,
                $retailers,
                $startDate,
                $endDate,
                $userId
            );
    }

    public function avgImages(
        array $products = [],
        array $retailers = [],
        string $startDate = '',
        string $endDate = '',
        int $userId = 0
    ): Collection
    {
        return $this
            ->repository()
            ->avgImages($products, $retailers, $startDate, $endDate, $userId);
    }
}
