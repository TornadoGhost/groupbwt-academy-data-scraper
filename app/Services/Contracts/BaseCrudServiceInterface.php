<?php

namespace App\Services\Contracts;

interface BaseCrudServiceInterface
{
    public function all(int $perPage);

    public function find(string $attribute);

    public function create(array $attributes);

    public function update(string $uid, array $attributes);

    public function delete(string $uid);
}
