<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ImageServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(protected ImageServiceInterface $imageService)
    {
        parent::__construct();
    }

    public function all(): Collection
    {
        if (auth()->user()->isAdmin) {
            return $this->model()
                ->with('retailers')
                ->with('user')
                ->with('images')
                ->get();
        }

        return $this->model()
            ->with('retailers')
            ->where('user_id', auth()->id())
            ->get();
    }

    public function allLatest(User $user): Collection
    {
        if ($user->isAdmin) {
            $model = $this->model()
                ->with('retailers')
                ->with('user')
                ->with('images');

            return $this->getLatestData($model);
        }

        $model = $this->model()
            ->with('retailers')
            ->where('user_id', $user->id);

        return $this->getLatestData($model);
    }

    public function create(array $attributes): Model
    {
        return DB::transaction(function () use ($attributes) {
            $product = $this->model()->create([
                'title' => $attributes['title'],
                'manufacturer_part_number' => $attributes['manufacturer_part_number'],
                'pack_size' => $attributes['pack_size'],
                'user_id' => auth()->user()->id,
            ]);
            foreach ($attributes['retailers'] as $retailer) {
                if ($retailer['product_url']) {
                    $product->retailers()->attach($retailer['retailer_id'], [
                        'product_url' => $retailer['product_url']
                    ]);
                }
            }

            if ($attributes['images'] ?? null) {
                foreach ($attributes['images'] as $image) {
                    $path = $this->imageService->saveImage($image);
                    $product->images()->create([
                        'path' => $path
                    ]);
                }
            }

            return $product;
        });
    }

    public function find(int $id): Model
    {
        if (auth()->user()->isAdmin) {
            return $this->model
                ->with('retailers')
                ->with('images')
                ->findOrFail($id);
        }

        return $this->model
            ->with('retailers')
            ->with('images')
            ->findOrFail($id);
    }

    public function update(int $id, array $attributes): Model
    {
        $product = $this->model
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        $retailers = $attributes['retailers'] ?? [];
        $filteredRetailers = array_filter($retailers, function ($item) {
            return $item['product_url'] !== null;
        });

        $attributes['retailers'] = $filteredRetailers;

        return DB::transaction(function () use ($product, $filteredRetailers, $attributes) {
            if (!empty($attributes['retailers'])) {
                $product->retailers()->sync($attributes['retailers']);
            }

            if ($attributes['images'] ?? null) {
                foreach ($attributes['images'] as $image) {
                    $path = $this->imageService->saveImage($image);
                    $product->images()->create([
                        'path' => $path
                    ]);
                }
            }

            $product->update($attributes);

            return $product;
        });
    }

    public function delete(int $id): bool
    {
        $product = $this->model
            ->with('images')
            ->findOrFail($id);

        $images = $product->images;

        return DB::transaction(function () use ($product, $images) {
            foreach ($images as $image) {
                $this->imageService->deleteImageByPath($image->path);
            }
            return $product->delete();
        });
    }

    protected function getModelClass(): string
    {
        return Product::class;
    }

    protected function getLatestData($model): Collection
    {
        return $model
            ->latest('created_at')
            ->latest('id')
            ->get();
    }

    public function productsForMetrics(int $userId): Collection
    {
        return $this->model()
            ->where('user_id', $userId)
            ->get([
                'title',
                'manufacturer_part_number',
                'id'
            ]);
    }

    public function getNameById(int $id): ?string
    {
        return $this->model()->query()->find($id, 'title')->title;
    }
}
