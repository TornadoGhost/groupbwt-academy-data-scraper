<?php

namespace App\Services\Contracts;

interface ScrapingSessionServiceInterface extends BaseCrudServiceInterface
{
    public function getLatestScrapingSession();

    public function getFirstScrapingSession();
}
