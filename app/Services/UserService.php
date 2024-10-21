<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use App\Services\Contracts\UserServiceInterface;

class UserService extends BaseCrudService implements UserServiceInterface
{
    public function findByEmail($email) {
        return $this->repository()->findByEmail($email);
    }

    protected function getRepositoryClass(): string
    {
        return UserRepositoryInterface::class;
    }
}
