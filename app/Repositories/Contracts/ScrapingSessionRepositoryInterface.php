<?php

namespace App\Repositories\Contracts;

interface ScrapingSessionRepositoryInterface extends BaseRepositoryInterface
{
    public function getLatestScrapingSession();

    public function getFirstScrapingSession();
}
