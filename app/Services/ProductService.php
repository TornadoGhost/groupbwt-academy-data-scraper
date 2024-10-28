<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;

class ProductService extends BaseCrudService implements ProductServiceInterface
{
    protected function getRepositoryClass(): string
    {
        return ProductRepositoryInterface::class;
    }

    public function findById(int $id): Product
    {
        return $this->repository()->findById($id);
    }
}
