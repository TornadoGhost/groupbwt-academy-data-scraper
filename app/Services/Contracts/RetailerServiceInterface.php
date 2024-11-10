<?php

namespace App\Services\Contracts;

interface RetailerServiceInterface extends BaseCrudServiceInterface
{
    public function grandAccess(int $retailer_id, array $users_id);
    public function revokeAccess(int $retailer_id, array $users_id);
    public function restore(int $uid);
    public function findWithUsers(int $id);
    public function list();
    public function retailersForMetrics(int $userId);
    public function getNameById(int $retailerId);
    public function prepareDataForIndexView(): array;
}
