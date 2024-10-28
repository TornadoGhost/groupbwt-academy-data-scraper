<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\MetricRequest;
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
        protected ScrapingSessionServiceInterface $scrapingSessionService)
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
        $mergedData = $avgRating->map(function ($item) use ($avgPrice, $avgImages) {
            $retailerId = $item['retailer_id'];

            return [
                'retailer_id' => $retailerId,
                'retailer_name' => $item['retailer']['name'],
                'average_product_rating' => round($item['average_product_rating'], 2),
                'average_product_price' => round($avgPrice->firstWhere('retailer_id', $retailerId)['average_product_price'], 2),
                'average_images_per_product' => round((float)$avgImages->firstWhere('retailer_id', $retailerId)['average_images_per_product'], 2),
            ];
        });

        return $this->successResponse("Metrics data received", data: $mergedData);
    }
}
