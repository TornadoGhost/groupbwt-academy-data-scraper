<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MetricRequest;
use App\Services\Contracts\MetricServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Traits\JsonResponseHelper;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class MetricController extends Controller
{
    use JsonResponseHelper;

    public function __construct(
        protected ScrapedDataServiceInterface $scrapedDataService,
        protected ScrapingSessionServiceInterface $scrapingSessionService,
        protected MetricServiceInterface $metricService,
    )
    {
    }

    public function __invoke(MetricRequest $request): JsonResponse
    {
        $productId = $request->product_id ?? 0;
        $mpn = $request->manufacturer_part_number ?? 0;
        $retailerId = $request->retailer_id ?? 0;
        $startDate = $request->start_date ?? Carbon::parse($this->scrapingSessionService->getLatestScrapingSession())->format('Y-m-d');
        $endDate = $request->end_date ?? '';

        if (auth()->user()->isAdmin) {
            $userId = $request->userId ?? 0;
        } else {
            $userId = auth()->id();
        }

        $avgRating = $this->scrapedDataService->avgRating($productId, $mpn, $retailerId, $startDate, $endDate, $userId);
        $avgPrice = $this->scrapedDataService->avgPrice($productId, $mpn, $retailerId, $startDate, $endDate, $userId);
        $avgImages = $this->scrapedDataService->avgImages($productId, $mpn, $retailerId, $startDate, $endDate, $userId);
        $avgPriceMap = $avgPrice->keyBy('retailer_id')->toArray();
        $avgImagesMap = $avgImages->keyBy('retailer_id')->toArray();

        $mergedData = $this->metricService->getAvgData($avgRating, $avgPriceMap, $avgImagesMap);

        return $this->successResponse("Metrics data received", data: $mergedData);
    }
}
