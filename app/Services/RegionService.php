<?php

namespace App\Services;

use App\Repositories\Contracts\RegionRepositoryInterface;
use App\Services\Contracts\RegionServiceInterface;

class RegionService extends BaseCrudService implements RegionServiceInterface
{

    protected function getRepositoryClass()
    {
        return RegionRepositoryInterface::class;
    }
}
