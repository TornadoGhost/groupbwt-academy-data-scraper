<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function all($perPage = 15)
    {
        if (auth()->user()->isAdmin) {
            $model = $this->model()
                ->with('retailers')
                ->with('users')
                ->with('images');

            return $this->getLatestPaginatedData($model, $perPage);

        }
        $model = $this->model()
            ->with('retailers')
            ->where('user_id', auth()->id());

        return $this->getLatestPaginatedData($model, $perPage);

    }

    public function create($attributes)
    {
        return DB::transaction(function () use ($attributes) {
            $product = $this->model()->create([
                'title' => $attributes['title'],
                'manufacturer_part_number' => $attributes['manufacturer_part_number'],
                'pack_size' => $attributes['pack_size'],
                'user_id' => auth()->user()->id,
            ]);
            foreach ($attributes['retailers'] as $retailer) {
                $product->retailers()->attach($retailer['retailer_id'], [
                    'product_url' => $retailer['product_url']
                ]);
            }

            if ($attributes['images'] ?? null) {
                foreach ($attributes['images'] as $image) {
                    $path = $this->saveImage($image);
                    $product->images()->create([
                        'path' => $path
                    ]);
                }
            }


            return $product;
        });

    }

    public function find($uid)
    {
        return $this->model
            ->with('retailers')
            ->with('images')
            ->where('manufacturer_part_number', $uid)
            ->where('user_id', auth()->id())
            ->firstOrFail();
    }

    public function update($uid, $attributes)
    {
        $product = $this->model
            ->where('manufacturer_part_number', $uid)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return DB::transaction(function () use ($product, $uid, $attributes) {
            if (isset($attributes['retailers'])) {
                $product->retailers()->sync($attributes['retailers']);
            }

            $product->update($attributes);

            return $product;
        });
    }

    public function delete($uid)
    {
        $product = $this->model
            ->with('images')
            ->where('manufacturer_part_number', $uid)
            ->firstOrFail();

        $images = $product->images;

        return DB::transaction(function () use ($product, $images) {
            foreach ($images as $image) {
                $this->deleteImage($image->path);
            }
            $product->delete();
        });
    }

    protected function getModelClass(): string
    {
        return Product::class;
    }

    protected function getLatestPaginatedData($model, $perPage)
    {
        return $model
            ->latest('created_at')
            ->latest('id')
            ->paginate($perPage);
    }
}
