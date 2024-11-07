<?php

namespace App\Services;

use App\Repositories\BaseRepository;
use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Services\Contracts\BaseCrudServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

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

    public function all(): Collection
    {
        return $this->repository()->all();
    }

    public function find(int $id): Model
    {
        return $this->repository()->find($id);
    }

    public function create(array $attributes): Model
    {
        return $this->repository()->create($attributes);
    }

    public function update(int $id, array $attributes): Model
    {
        return $this->repository()->update($id, $attributes);
    }

    public function delete($id): bool
    {
        return $this->repository()->delete($id);
    }

    abstract protected function getRepositoryClass();
}
