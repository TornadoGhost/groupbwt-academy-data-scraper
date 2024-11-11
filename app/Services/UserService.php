<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class UserService extends BaseCrudService implements UserServiceInterface
{
    public function findByEmail($email): User
    {
        return $this->repository()->findByEmail($email);
    }

    public function prepareUsers(Collection $users): array
    {
        $preparedUsers = [];
        foreach ($users as $user) {
            $preparedUsers[$user->id] = "$user->username ($user->email)";
        };

        return $preparedUsers;
    }

    protected function getRepositoryClass(): string
    {
        return UserRepositoryInterface::class;
    }
}
