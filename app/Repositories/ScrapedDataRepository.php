<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ScrapedData;
use App\Repositories\Contracts\ScrapedDataRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\DB;

class ScrapedDataRepository extends BaseRepository implements ScrapedDataRepositoryInterface
{

    public function all()
    {
        if (auth()->user()->isAdmin) {
            return $this->model()
                ->with('scrapedDataImages')
                ->latest('session_id')
                ->get();
        }

        $userId = auth()->id();

        return $this->model()
            ->where('user_id', $userId)
            ->latest('session_id')
            ->get();
    }

    public function find($uid)
    {
        return $this->model()->with('scrapedDataImages')->findOrFail($uid);
    }

    public function create($attributes)
    {
        $product = $this->getProduct($attributes['mpn']);

        return DB::transaction(function () use ($attributes, $product) {
            $scrapedData = $this->model()->create([
                'title' => $attributes['title'],
                'description' => $attributes['description'],
                'price' => $attributes['price'],
                'avg_rating' => $attributes['avg_rating'],
                'stars_1' => $attributes['stars_1'],
                'stars_2' => $attributes['stars_2'],
                'stars_3' => $attributes['stars_3'],
                'stars_4' => $attributes['stars_4'],
                'stars_5' => $attributes['stars_5'],
                'retailer_id' => $attributes['retailer_id'],
                'product_id' => $product->id,
                'user_id' => $product->user_id,
                'session_id' => $attributes['session_id']
            ]);

            foreach ($attributes['images'] as $image) {
                $path = $this->saveImage($image);
                $scrapedData->scrapedDataImages()->create([
                    'path' => $path,
                ]);
            }

            return $scrapedData;
        });
    }

    public function delete($uid)
    {
        $scrapedData = $this->model
            ->with('scrapedDataImages')
            ->findOrFail($uid);

        return DB::transaction(function () use ($scrapedData) {
            foreach ($scrapedData->scrapedDataImages as $image) {
                $this->deleteImage($image->path);
            }
            $scrapedData->delete();
        });
    }

    public function getMetricData(Builder $query, int $productId = 0, string $mpn = '', int $retailerId = 0, string $startDate = '', string $endDate = '', int $userId = 0): Collection
    {
        $startDateFormatted = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();

        if ($startDate && !$endDate) {
            $query->where('created_at', 'like', $startDateFormatted->format('Y-m-d') . '%');
        } else if ($startDate && $endDate) {
            $endDateFormatted = Carbon::createFromFormat('Y-m-d', $endDate)->endOfDay()->format('Y-m-d H:i:s');
            $query->whereBetween('created_at', [$startDateFormatted->format('Y-m-d H:i:s'), $endDateFormatted]);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if ($productId && $mpn) {
            $query->where('product_id', $productId)->where('product_id', $this->getProduct($mpn)->id);
        } else if ($productId) {
            $query->where('product_id', $productId);
        } else if ($mpn) {
            $query->where('product_id', $this->getProduct($mpn)->id);
        }

        if ($retailerId) {
            $query->where('retailer_id', $retailerId);
        }

        $query->groupBy('retailer_id');

        return $query->get();
    }

    protected function getAvgMetricData(Expression $select, int $productId = 0, string $mpn = '', int $retailerId = 0, string $startDate = '', string $endDate = '', int $userId = 0, bool $isAvgImages = false): Collection
    {
        $query = $this->model()->query();

        if ($isAvgImages) {
            $query->leftJoin('scraped_data_images', 'scraped_data.id', '=', 'scraped_data_images.scraped_data_id');
        }

        $query->with('retailer:id,name')->select('retailer_id', $select);

        return $this->getMetricData($query, $productId, $mpn, $retailerId, $startDate, $endDate, $userId);
    }

    public function avgRating(int $productId = 0, string $mpn = '', int $retailerId = 0, string $startDate = '', string $endDate = '', int $userId = 0): Collection
    {
        return $this->getAvgMetricData(DB::raw('AVG(avg_rating) as average_product_rating'), $productId, $mpn, $retailerId, $startDate, $endDate, $userId);
    }

    public function avgPrice(int $productId = 0, string $mpn = '', int $retailerId = 0, string $startDate = '', string $endDate = '', int $userId = 0): Collection
    {
        return $this->getAvgMetricData(DB::raw('AVG(price) as average_product_price'), $productId, $mpn, $retailerId, $startDate, $endDate, $userId);
    }

    public function avgImages(int $productId = 0, string $mpn = '', int $retailerId = 0, string $startDate = '', string $endDate = '', int $userId = 0): Collection
    {
        return $this->getAvgMetricData(DB::raw('COUNT(scraped_data_images.id) / COUNT(DISTINCT scraped_data.id) as average_images_per_product'), $productId, $mpn, $retailerId, $startDate, $endDate, $userId, true);
    }

    protected function getProduct(string $mpn): mixed
    {
        return Product::select(['id', 'user_id'])
            ->where('manufacturer_part_number', $mpn)
            ->firstOrFail();
    }

    protected function getModelClass(): string
    {
        return ScrapedData::class;
    }
}
