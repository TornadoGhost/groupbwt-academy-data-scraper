<?php

namespace App\Repositories;

use App\Models\ScrapingSession;
use App\Repositories\Contracts\ScrapingSessionRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class ScrapingSessionRepository extends BaseRepository implements ScrapingSessionRepositoryInterface
{
    public function all(): Collection
    {
        return $this->model()
            ->with('retailer')
            ->orderByDesc('started_at')
            ->orderByDesc('id')
            ->get();
    }

    public function create($attributes): Model
    {
        $creatDay = Carbon::parse($attributes['started_at'])->format('Y-m-d');
        $exist = $this->model()->where('started_at', 'like', $creatDay . '%')->first();

        if ($exist) {
            return false;
        } else {
            return $this->model()->create($attributes);
        }
    }

    public function getLatestScrapingSession(): string
    {
        return $this->model()->select('started_at')
            ->whereNotNull('ended_at')
            ->latest('started_at')
            ->first()
            ->started_at;
    }

    public function getFirstScrapingSession(): string
    {
        return $this->model()->select('started_at')
            ->whereNotNull('ended_at')
            ->oldest('started_at')
            ->first()
            ->started_at;
    }

    protected function getModelClass(): string
    {
        return ScrapingSession::class;
    }
}
