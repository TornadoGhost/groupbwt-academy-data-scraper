<?php

namespace App\Services\Contracts;

interface ScrapingSessionServiceInterface extends BaseCrudServiceInterface
{
    public function getLatestScrapingSession(): string;

    public function getFirstScrapingSession(): string;
}
