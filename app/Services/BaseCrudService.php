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

    public function all()
    {
        return $this->repository()->all();
    }

    public function find($attribute)
    {
        return $this->repository()->find($attribute);
    }

    public function create($attributes = [])
    {
        return $this->repository()->create($attributes);
    }

    public function update(int $id, array $attributes = [])
    {
        return $this->repository()->update($id, $attributes);
    }

    public function delete($id): bool
    {
        return $this->repository()->delete($id);
    }

    abstract protected function getRepositoryClass();
}
