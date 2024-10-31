<?php

namespace App\Services\Contracts;

use App\Services\Contracts\BaseCrudServiceInterface;

interface ProductServiceInterface extends BaseCrudServiceInterface
{
    public function findByMpn(string $mpn);

    public function productsForMetrics(int $userId);
}
