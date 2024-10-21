<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
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

    public function view()
    {
        return false;
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
