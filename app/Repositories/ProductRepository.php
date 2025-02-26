<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ImageServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    private const PER_PAGE = 10;
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

    public function allPaginate(bool $isAdmin, array $filters): LengthAwarePaginator
    {
        $qb = $this->model()->with('retailers');

        if ($filters['sort_id'] ?? null) {
            $qb->orderBy('id', $filters['sort_id']);
        }

        if ($filters['sort_title'] ?? null) {
            $qb->orderBy('title', $filters['sort_title']);
        }

        if ($filters['sort_manufacturer_part_number'] ?? null) {
            $qb->orderBy('manufacturer_part_number', $filters['sort_manufacturer_part_number']);
        }

        if ($filters['sort_pack_size'] ?? null) {
            $qb->orderBy('pack_size', $filters['sort_pack_size']);
        }

        if ($filters['sort_created_at'] ?? null) {
            $qb->orderBy('created_at', $filters['sort_created_at']);
        }


        if ($filters['search'] ?? null) {
            $search = '%' . $filters['search'] . '%';
            $qb->orWhere('id', 'like', $search)
                ->orWhere('title', 'like', $search)
                ->orWhere('manufacturer_part_number', 'like', $search)
                ->orWhere('pack_size', 'like', $search)
                ->orWhere('created_at', 'like', $search)
            ;
        }

        $page = $filters['page'] ?? 1;

        if ($isAdmin) {
            $qb->with('user')->with('images');

            if ($filters['per_page'] ?? null) {
                return $qb->paginate($filters['per_page'], page: $page);
            }

            return $qb->paginate(self::PER_PAGE, page: $page);
        }


        $qb->where('user_id', auth()->id());

        if ($filters['per_page'] ?? null) {
            return $qb->paginate($filters['per_page']);
        }

        return $qb->paginate(self::PER_PAGE);
    }

    public function allLatest(User $user): Collection| \Illuminate\Support\Collection
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
