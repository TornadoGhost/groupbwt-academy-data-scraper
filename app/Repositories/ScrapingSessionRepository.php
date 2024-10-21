<?php

namespace App\Repositories;

use App\Models\ScrapingSession;
use App\Repositories\Contracts\ScrapingSessionRepositoryInterface;
use Carbon\Carbon;

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

    public function create($attributes)
    {
        $creatDay = Carbon::parse($attributes['started_at'])->format('Y-m-d');
        $exist = $this->model()->where('started_at', 'like', $creatDay . '%')->first();

        if ($exist) {
            return false;
        } else {
            return $this->model()->create($attributes);
        }
    }

    protected function getModelClass()
    {
        return ScrapingSession::class;
    }
}
