<?php

namespace App\Http\Controllers\Api;

use App\Exports\MetricsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\MetricExportRequest;
use App\Http\Requests\MetricRequest;
use App\Http\Resources\MetricProductResource;
use App\Http\Resources\MetricRetailerResource;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\SaveExportTableData;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\MetricServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Traits\JsonResponseHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class MetricController extends Controller
{
    use JsonResponseHelper;
    protected readonly int $userId;

    public function __construct(
        protected ScrapedDataServiceInterface $scrapedDataService,
        protected ScrapingSessionServiceInterface $scrapingSessionService,
        protected MetricServiceInterface $metricService,
        protected ProductServiceInterface $productService,
        protected RetailerServiceInterface $retailerService,
        protected ExportTableServiceInterface  $exportTableService,
    )
    {
        $this->userId = auth()->id();
    }

    public function index(MetricRequest $request): JsonResponse
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

        $mergedData = $this->metricService->getAvgData($avgRating, $avgPriceMap, $avgImagesMap);

        return $this->successResponse("Metrics data received", data: $mergedData);
    }

    public function getProducts(): JsonResponse
    {
        $products = $this->productService->productsForMetrics($this->userId);

        return $this->successResponse("Metrics products data received", data: MetricProductResource::collection($products));
    }

    public function getRetailers(): JsonResponse
    {
        $retailers = $this->retailerService->retailersForMetrics($this->userId);

        return $this->successResponse("Metrics retailers data received", data: MetricRetailerResource::collection($retailers));
    }

    public function export(MetricExportRequest $request): JsonResponse
    {
        return $this->metricService->exportExcel(
            $request->validated(),
            request()->user()
        );
    }
}
