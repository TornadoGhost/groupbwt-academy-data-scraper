<?php

namespace App\Repositories;

use App\Repositories\Contracts\ImageProductRepositoryInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class ImageProductRepository implements ImageProductRepositoryInterface
{
    protected string $table;
    public function __construct(){
        $this->table = 'product_images';
    }

    public function table(): Builder
    {
        return DB::table($this->table);
    }
    public function findById(int $id): \stdClass
    {
        return $this->table()->where('id', $id)->firstOrFail();
    }

    public function delete(int $id): bool
    {
        return $this->table()->where('id', $id)->delete();
    }
}
