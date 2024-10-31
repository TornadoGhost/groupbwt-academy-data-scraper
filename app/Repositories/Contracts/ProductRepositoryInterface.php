<?php

namespace App\Repositories\Contracts;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    public function findByMpn(string $mpn);
    public function productsForMetrics(int $userId);
}
