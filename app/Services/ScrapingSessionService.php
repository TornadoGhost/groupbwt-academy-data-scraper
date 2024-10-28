<?php

namespace App\Services;

use App\Repositories\Contracts\ScrapingSessionRepositoryInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;

class ScrapingSessionService extends BaseCrudService implements ScrapingSessionServiceInterface
{
    public function getLatestScrapingSession(): string
    {
        return $this->repository()->getLatestScrapingSession();
    }
    public function getFirstScrapingSession(): string
    {
        return $this->repository()->getFirstScrapingSession();
    }
    protected function getRepositoryClass()
    {
        return ScrapingSessionRepositoryInterface::class;
    }
}
