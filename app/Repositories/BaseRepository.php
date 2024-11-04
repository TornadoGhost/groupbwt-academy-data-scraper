<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;

abstract class BaseRepository implements BaseRepositoryInterface
{
    use JsonResponseHelper;

    protected Model $model;

    public function __construct()
    {
        $this->model = app($this->getModelClass());
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function all(): Collection
    {
        return $this->model()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    public function find(int $id): Model
    {
        return $this->model()->findOrFail($id);
    }

    public function create(array $attributes): Model
    {
        return $this->model()->create($attributes);
    }

    public function update(int $id, array $attributes): Model
    {
        $record = $this->model()->findOrFail($id);
        $record->update($attributes);

        return $record;
    }

    public function delete(int $id): bool
    {
        return $this->model()->findOrFail($id)->delete($id);
    }

    abstract protected function getModelClass(): string;
}
