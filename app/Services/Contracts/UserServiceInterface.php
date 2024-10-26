<?php

namespace App\Services\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface extends BaseCrudServiceInterface
{
    public function findByEmail($email);
    public function prepareUsers(Collection $users);
}
