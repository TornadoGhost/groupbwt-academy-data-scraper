<?php

namespace App\Services;

use App\Exports\MetricsExport;
use App\Http\Requests\MetricRequest;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\SaveExportTableData;
use App\Models\User;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\MetricServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Traits\JsonResponseHelper;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class MetricService implements MetricServiceInterface
{
    use JsonResponseHelper;

    public function __construct(
        protected ExportTableServiceInterface     $exportTableService,
        protected ScrapingSessionServiceInterface $scrapingSessionService,
        protected ScrapedDataServiceInterface     $scrapedDataService,
        protected RetailerServiceInterface        $retailerService,
        protected ProductServiceInterface         $productService,
    )
    {
    }

    public function getAvgData(Collection $avgRating, array $avgPrice, array $avgImages): Collection|\Illuminate\Support\Collection
    {
        return $avgRating->map(function ($item) use ($avgPrice, $avgImages) {
            $retailerId = $item['retailer_id'];

            return [
                'retailer_id' => $retailerId,
                'retailer_name' => $item['retailer']['name'],
                'average_product_rating' => round($item['average_product_rating'], 2),
                'average_product_price' => round($avgPrice[$retailerId]['average_product_price'] ?? 0, 2),
                'average_images_per_product' => round((float)($avgImages[$retailerId]['average_images_per_product'] ?? 0), 2),
            ];
        });
    }

    public function exportExcel(array $requestData, User $user): JsonResponse
    {
        $fileName = 'Metrics';
        $filePath = $this->exportTableService->setPath($fileName, $user->id);
        $products = $requestData['products'] ?? [];
        $retailers = $requestData['retailers'] ?? [];
        $startDate = $requestData['start_date'] ?? Carbon::parse($this->scrapingSessionService->getLatestScrapingSession())
            ->format('Y-m-d');
        $endDate = $requestData['end_date'] ?? '';
        $userId = $requestData['user_id'] ?? 0;

        (new MetricsExport(
            $this->scrapedDataService,
            $this,
            $products,
            $retailers,
            $startDate,
            $endDate,
            $userId,
            $requestData,
            $this->retailerService,
            $this->productService,
            request()->user(),
        ))
            ->store($filePath)
            ->chain([
                new NotifyUserOfCompletedExport($user, 'Metrics'),
                new SaveExportTableData($fileName, $filePath, $user, $this->exportTableService)
            ]);

        return $this->successResponse('Metrics data export started');
    }

    public function getMetrics(MetricRequest $request, User|Authenticatable $user): Collection|\Illuminate\Support\Collection
    {
        $products = $request->products ?? [];
        $retailers = $request->retailers ?? [];
        $startDate = $request->start_date ?? Carbon::parse($this->scrapingSessionService->getLatestScrapingSession())->format('Y-m-d');
        $endDate = $request->end_date ?? '';

        if (auth()->user()->isAdmin) {
            $userId = $request->userId ?? 0;
        } else {
            $userId = auth()->id();
        }

        $avgRating = $this->scrapedDataService->avgRating($products, $retailers, $startDate, $endDate, $userId);
        $avgPrice = $this->scrapedDataService->avgPrice($products, $retailers, $startDate, $endDate, $userId);
        $avgImages = $this->scrapedDataService->avgImages($products, $retailers, $startDate, $endDate, $userId);
        $avgPriceMap = $avgPrice->keyBy('retailer_id')->toArray();
        $avgImagesMap = $avgImages->keyBy('retailer_id')->toArray();

        return $this->getAvgData($avgRating, $avgPriceMap, $avgImagesMap);
    }
}
