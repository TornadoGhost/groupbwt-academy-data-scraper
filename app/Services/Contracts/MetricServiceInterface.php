<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

interface MetricServiceInterface
{
    public function getAvgData(Collection $avgRating, array $avgPrice, array $avgImages): Collection|\Illuminate\Support\Collection;
    public function exportExcel($startDate, $endDate, User $user): JsonResponse;
}
