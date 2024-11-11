<?php

namespace App\Policies;

use App\Models\ScrapedData;
use App\Models\User;

class ScrapedDataPolicy
{
    public function before(User $user)
    {
        if ($user->isAdmin) {
            return true;
        }

        return null;
    }

    public function create()
    {
        return false;
    }

    public function view(User $user, ScrapedData $scrapedData)
    {
        return $user->id === $scrapedData->user_id;
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
