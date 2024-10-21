<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ScrapedData;
use App\Models\ScrapingSession;
use App\Repositories\Contracts\ScrapedDataRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ScrapedDataRepository extends BaseRepository implements ScrapedDataRepositoryInterface
{

    public function all($perPage = 15)
    {
        if (auth()->user()->isAdmin) {
            return $this->model()
                ->with('scrapedDataImages')
                ->latest('session_id')
                ->paginate($perPage);
        }

        $userId = auth()->id();

        return $this->model()
            ->where('user_id', $userId)
            ->latest('session_id')
            ->paginate($perPage);
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

    public function getMetricData($query, $productId = 0, $mpn = '', $retailerId = 0, $date = '', $userId = 0)
    {
        $dateFormat = Carbon::parse($date)->format('Y-m-d');

        if ($date) {
            $query->where('created_at', 'like', $dateFormat . '%');
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

    public function avgRating($productId = 0, $mpn = '', $retailerId = 0, $date = '', $userId = 0)
    {
        $query = $this->model()->query()->select(
            'retailer_id',
            DB::raw('AVG(avg_rating) as average_product_rating'));

        return $this->getMetricData($query, $productId, $mpn, $retailerId, $date, $userId);
    }

    public function avgPrice($productId = 0, $mpn = '', $retailerId = 0, $date = '', $userId = 0)
    {
        $query = $this->model()->query()->select(
            'retailer_id',
            DB::raw('AVG(price) as average_product_price'));
        return $this->getMetricData($query, $productId, $mpn, $retailerId, $date, $userId);
    }

    public function avgImages($productId = 0, $mpn = '', $retailerId = 0, $date = '', $userId = 0)
    {
        $query = $this->model()->query()
            ->leftJoin('scraped_data_images', 'scraped_data.id', '=', 'scraped_data_images.scraped_data_id')
            ->select(
            'retailer_id',
            DB::raw('COUNT(scraped_data_images.id) / COUNT(DISTINCT scraped_data.id) as average_images_per_product')
        );

        return $this->getMetricData($query, $productId, $mpn, $retailerId, $date, $userId);
    }

    public function getLatestScrapedData()
    {
        return ScrapingSession::select('started_at')
            ->where('retailer_id', 10)
            ->whereNotNull('ended_at')
            ->groupBy('started_at')
            ->latest('started_at')
            ->first()
            ->started_at;
    }

    protected function getProduct(string $mpn)
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
