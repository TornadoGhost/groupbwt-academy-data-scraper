<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserServiceInterface extends BaseCrudServiceInterface
{
    public function findByEmail($email): User;
    public function prepareUsers(Collection $users): array;
}
