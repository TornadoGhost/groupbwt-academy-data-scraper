<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use App\Services\Contracts\BaseCrudServiceInterface;

abstract class BaseCrudService implements BaseCrudServiceInterface
{
    protected BaseRepository $repository;

    public function __construct()
    {
        $this->repository = app($this->getRepositoryClass());
    }

    public function repository()
    {
        return $this->repository;
    }

    public function all($perPage = 15)
    {
        return $this->repository()->all($perPage);
    }

    public function find($attribute)
    {
        return $this->repository()->find($attribute);
    }

    public function create($attributes = [])
    {
        return $this->repository()->create($attributes);
    }

    public function update($uid, $attributes = [])
    {
        return $this->repository()->update($uid, $attributes);
    }

    public function delete($uid)
    {
        return $this->repository()->delete($uid);
    }

    abstract protected function getRepositoryClass();
}
