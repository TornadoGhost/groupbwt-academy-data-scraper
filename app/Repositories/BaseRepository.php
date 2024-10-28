<?php

namespace App\Repositories;

use App\Repositories\Contracts\BaseRepositoryInterface;
use App\Traits\JsonResponseHelper;
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

    public function model()
    {
        return $this->model;
    }

    public function all()
    {
        return $this->model()
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    public function find($uid)
    {
        return $this->model()->findOrFail($uid);
    }

    public function create($attributes)
    {
        return $this->model()->create($attributes);
    }

    public function update($uid, $attributes)
    {
        $record = $this->model()->findOrFail($uid);
        $record->update($attributes);

        return $record;
    }

    public function delete($uid)
    {
        return $this->model()->findOrFail($uid)->delete($uid);
    }

    abstract protected function getModelClass();
}
