<?php

namespace App\Repositories;

use App\Models\Region;
use App\Repositories\Contracts\RegionRepositoryInterface;

class RegionRepository extends BaseRepository implements RegionRepositoryInterface
{

    protected function getModelClass(): string
    {
        return Region::class;
    }
}
