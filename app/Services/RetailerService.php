<?php

namespace App\Services;

use App\Models\Retailer;
use App\Repositories\Contracts\RetailerRepositoryInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

class RetailerService extends BaseCrudService implements RetailerServiceInterface
{
    public function __construct(
        protected UserServiceInterface $userService,
        protected ScrapingSessionServiceInterface $scrapingSessionService,
    )
    {
        parent::__construct();
    }

    public function grandAccess(int $retailer_id, array $users_id): bool
    {
        return $this->repository()->grandAccess($retailer_id, $users_id);
    }

    public function revokeAccess(int $retailer_id, array $users_id): bool
    {
        return $this->repository()->revokeAccess($retailer_id, $users_id);
    }

    public function restore(int $uid): bool
    {
        return $this->repository()->restore($uid);
    }

    public function findWithUsers(int $id): Retailer
    {
        return $this->repository()->findWithUsers($id);
    }

    public function list(): Collection
    {
        return $this->repository()->list();
    }

    public function retailersForMetrics(int $userId): Collection
    {
        return $this->repository()->retailersForMetrics($userId);
    }

    public function getNameById(int $retailerId): ?string
    {
        return $this->repository()->getNameById($retailerId);
    }

    public function prepareDataForIndexView(): array
    {
        $users = $this->userService->all();
        $preparedUsers = $this->userService->prepareUsers($users);
        $firstScrapedData = $this->scrapingSessionService->getFirstScrapingSession();
        $lastScrapedDate = $this->scrapingSessionService->getLatestScrapingSession();
        $firstDate = Carbon::parse($firstScrapedData)->format('Y-m-d');
        $lastDate = Carbon::parse($lastScrapedDate)->format('Y-m-d');

        return [
            'users' => $users,
            'preparedUsers' => $preparedUsers,
            'firstScrapedData' => $firstScrapedData,
            'lastScrapedDate' => $lastScrapedDate,
            'firstDate' => $firstDate,
            'lastDate' => $lastDate,
        ];
    }

    protected function getRepositoryClass()
    {
        return RetailerRepositoryInterface::class;
    }
}
