<?php

namespace App\Services;

use App\Repositories\Contracts\ScrapingSessionRepositoryInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;

class ScrapingSessionService extends BaseCrudService implements ScrapingSessionServiceInterface
{
    protected function getRepositoryClass()
    {
        return ScrapingSessionRepositoryInterface::class;
    }
}
