<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface MetricServiceInterface
{
    public function getAvgData(Collection $avgRating, array $avgPrice, array $avgImages);
}
