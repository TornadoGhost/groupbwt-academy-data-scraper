<?php

namespace App\Policies;

use App\Models\Retailer;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RetailerPolicy
{
    public function before(User $user, string $ability): bool|null
    {
        if ($user->isAdmin) {
            return true;
        }

        return null;
    }

    public function view(User $user, Retailer $retailer)
    {
        return $user->retailers->contains($retailer->id);
    }

    public function create(): bool
    {
        return false;
    }

    public function update(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }

    public function restore()
    {
        return false;
    }

    public function grandAccess()
    {
        return false;
    }

    public function revokeAccess()
    {
        return false;
    }
}
