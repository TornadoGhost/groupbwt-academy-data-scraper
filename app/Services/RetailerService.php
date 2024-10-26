<?php

namespace App\Services;

use App\Models\Retailer;
use App\Repositories\Contracts\RetailerRepositoryInterface;
use App\Services\Contracts\RetailerServiceInterface;

class RetailerService extends BaseCrudService implements RetailerServiceInterface
{
    public function grandAccess(int $retailer_id, array $users_id)
    {
        return $this->repository()->grandAccess($retailer_id, $users_id);
    }

    public function revokeAccess(int $retailer_id, array $users_id)
    {
        return $this->repository()->revokeAccess($retailer_id, $users_id);
    }

    public function restore(int $uid)
    {
        return $this->repository()->restore($uid);
    }

    public function findWithUsers(int $id): Retailer
    {
        return $this->repository()->findWithUsers($id);
    }

    protected function getRepositoryClass()
    {
        return RetailerRepositoryInterface::class;
    }
}
