<?php

namespace App\Repositories\Contracts;

interface BaseRepositoryInterface
{
    public function all(int $perPage);

    public function find(string $uid);

    public function create(array $attributes);

    public function update(string $uid, array $attributes);

    public function delete(string $uid);
}
