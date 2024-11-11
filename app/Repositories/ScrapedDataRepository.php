<?php

namespace App\Repositories;

use App\Models\Product;
use App\Models\ScrapedData;
use App\Repositories\Contracts\ScrapedDataRepositoryInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Services\ImageService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Expression;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ScrapedDataRepository extends BaseRepository implements ScrapedDataRepositoryInterface
{
    public function __construct(
        protected ImageService $imageService,
        protected RetailerServiceInterface $retailerService,
        protected ProductServiceInterface $productService,
        protected ScrapingSessionServiceInterface $scrapingSessionService,
    )
    {
        parent::__construct();
    }

    public function all(): Collection
    {
        if (auth()->user()->isAdmin) {
            return $this->model()
                ->with('scrapedDataImages')
                ->latest('session_id')
                ->get();
        }

        return $this->model()
            ->where('user_id', auth()->id())
            ->latest('session_id')
            ->get();
    }

    public function find(int $id): Model
    {
        return $this->model()->with('scrapedDataImages')->findOrFail($id);
    }

    public function create($attributes): Model
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
                $path = $this->imageService->saveImage($image);
                $scrapedData->scrapedDataImages()->create([
                    'path' => $path,
                ]);
            }

            return $scrapedData;
        });
    }

    public function delete(int $id): bool
    {
        $scrapedData = $this->model
            ->with('scrapedDataImages')
            ->findOrFail($id);

        return DB::transaction(function () use ($scrapedData) {
            foreach ($scrapedData->scrapedDataImages as $image) {
                $this->imageService->deleteImageByPath($image->path);
            }
            $scrapedData->delete();
        });
    }

    protected function getMetricData(
        Builder $query,
        array   $products = [],
        array   $retailers = [],
        string  $startDate = '',
        string  $endDate = '',
        int     $userId = 0
    ): Collection
    {
        $startDateFormatted = Carbon::createFromFormat('Y-m-d', $startDate)->startOfDay();

        if ($startDate && !$endDate) {
            $query
                ->where(
                    'created_at',
                    'like',
                    $startDateFormatted->format('Y-m-d') . '%');
        } else if ($startDate && $endDate) {
            $endDateFormatted =
                Carbon::createFromFormat('Y-m-d', $endDate)
                    ->endOfDay()
                    ->format('Y-m-d H:i:s');
            $query
                ->whereBetween('created_at', [
                    $startDateFormatted->format('Y-m-d H:i:s'), $endDateFormatted
                ]);
        }

        if ($userId) {
            $query->where('user_id', $userId);
        }

        if (!empty($retailers)) {
            $query->whereIn('retailer_id', $retailers);
        } else {
            $query->whereIn('retailer_id', $this->getUserRetailersId());
        }

        if (!empty($products)) {
            $query->whereIn('product_id', $products);
        } else {
            $query->whereIn('product_id', $this->getUserProductsId());
        }

        $query->groupBy('retailer_id');

        return $query->get();
    }

    protected function getAvgMetricData(
        Expression $select,
        array      $products = [],
        array      $retailers = [],
        string     $startDate = '',
        string     $endDate = '',
        int        $userId = 0,
        bool       $isAvgImages = false
    ): Collection
    {
        $query = $this->model()->query();

        if ($isAvgImages) {
            $query
                ->leftJoin(
                    'scraped_data_images',
                    'scraped_data.id', '=', 'scraped_data_images.scraped_data_id');
        }

        $query
            ->with('retailer:id,name')
            ->select('retailer_id', $select);

        return $this
            ->getMetricData(
                $query,
                $products,
                $retailers,
                $startDate,
                $endDate,
                $userId
            );
    }

    public function avgRating(
        array  $products = [],
        array  $retailers = [],
        string $startDate = '',
        string $endDate = '',
        int    $userId = 0
    ): Collection
    {
        return $this
            ->getAvgMetricData(
                DB::raw('AVG(avg_rating) as average_product_rating'),
                $products,
                $retailers,
                $startDate,
                $endDate,
                $userId
            );
    }

    public function avgPrice(
        array  $products = [],
        array  $retailers = [],
        string $startDate = '',
        string $endDate = '',
        int    $userId = 0
    ): Collection
    {
        return $this
            ->getAvgMetricData(
                DB::raw('AVG(price) as average_product_price'),
                $products,
                $retailers,
                $startDate,
                $endDate,
                $userId);
    }

    public function avgImages(
        array  $products = [],
        array  $retailers = [],
        string $startDate = '',
        string $endDate = '',
        int    $userId = 0
    ): Collection
    {
        return $this
            ->getAvgMetricData(
                DB::raw('COUNT(scraped_data_images.id) / COUNT(DISTINCT scraped_data.id) as average_images_per_product'),
                $products,
                $retailers,
                $startDate,
                $endDate,
                $userId,
                true);
    }

    protected function getProduct(string $mpn): mixed
    {
        return Product::select(['id', 'user_id'])
            ->where('manufacturer_part_number', $mpn)
            ->firstOrFail();
    }

    protected function getUserRetailersId(): array
    {
        $userRetailers = $this->retailerService->all();
        $retailersId = [];
        foreach ($userRetailers as $retailer) {
            $retailersId[] = $retailer->id;
        }

        return $retailersId;
    }

    protected function getUserProductsId(): array
    {
        $userProducts = $this->productService->productsForMetrics(auth()->id());
        $productsId = [];
        foreach ($userProducts as $product) {
            $productsId[] = $product->id;
        }

        return $productsId;
    }

    public function scrapedDataByRetailer(int $retailerId, string $date): ?Collection
    {
        return $this->model()
            ->with('product')
            ->where('retailer_id', $retailerId)
            ->where('user_id', auth()->id())
            ->where('created_at','like', $date . '%')
            ->get();
    }

    protected function getModelClass(): string
    {
        return ScrapedData::class;
    }
}
