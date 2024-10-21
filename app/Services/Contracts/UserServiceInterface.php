<?php

namespace App\Services\Contracts;

interface UserServiceInterface extends BaseCrudServiceInterface
{
    public function findByEmail($email);
}
