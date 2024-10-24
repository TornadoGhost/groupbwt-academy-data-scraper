<?php

namespace App\Services\Contracts;

interface RetailerServiceInterface extends BaseCrudServiceInterface
{
    public function grandAccess(int $retailer_id, array $users_id);
    public function revokeAccess(int $retailer_id, array $users_id);
    public function restore(int $uid);
}
