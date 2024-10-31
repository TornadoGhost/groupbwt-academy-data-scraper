<?php

namespace App\Services;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class ProductService extends BaseCrudService implements ProductServiceInterface
{
    protected function getRepositoryClass(): string
    {
        return ProductRepositoryInterface::class;
    }

    public function findByMpn(string $mpn): Product
    {
        return $this->repository()->findById($id);
    }
}
