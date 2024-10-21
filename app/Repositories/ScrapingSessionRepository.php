<?php

namespace App\Repositories;

use App\Models\ScrapingSession;
use App\Repositories\Contracts\ScrapingSessionRepositoryInterface;

class ScrapingSessionRepository extends BaseRepository implements ScrapingSessionRepositoryInterface
{
    public function all($perPage)
    {
        return $this->model()
            ->with('retailer')
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }
    
    protected function getModelClass()
    {
        return ScrapingSession::class;
    }
}
