<?php

namespace App\Services\Contracts;

use App\Http\Requests\MetricRequest;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

interface MetricServiceInterface
{
    public function getAvgData(
        Collection $avgRating, array $avgPrice, array $avgImages
    ): Collection|\Illuminate\Support\Collection;
    public function exportExcel(array $requestData, User $user): JsonResponse;
    public function getMetrics(
        MetricRequest $request, User|Authenticatable $user
    ): Collection|\Illuminate\Support\Collection;
    public function prepareDataForIndexPage(User|Authenticatable $user): array;
}
