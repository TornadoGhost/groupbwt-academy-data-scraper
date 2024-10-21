<?php

namespace App\Policies;

use App\Models\Region;
use App\Models\User;

class RegionPolicy
{
    public function before(User $user)
    {
        if ($user->isAdmin) {
            return true;
        }

        return null;
    }

    public function viewAll()
    {
        return false;
    }

    public function create()
    {
        return false;
    }

    public function view(User $user, Region $region)
    {
        return $user->region_id === $region->id;
    }

    public function update()
    {
        return false;
    }

    public function delete()
    {
        return false;
    }
}
