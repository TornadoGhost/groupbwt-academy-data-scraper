<?php

namespace App\Repositories\Contracts;

interface RetailerRepositoryInterface extends BaseRepositoryInterface
{
    public function grandAccess(int $retailer_id, array $users_id);
    public function revokeAccess(int $retailer_id, array $users_id);
    public function restore(int $uid);
    public function findWithUsers(int $id);
}
