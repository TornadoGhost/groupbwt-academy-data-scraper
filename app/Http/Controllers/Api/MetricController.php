<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    use JsonResponseHelper;
    public function __construct(protected ScrapedDataServiceInterface $scrapedDataService)
    {
    }

    public function __invoke(Request $request)
    {

        $productId = $request->product_id ? $request->product_id : 0;
        $mpn = $request->manufacturer_part_number ? $request->manufacturer_part_number : 0;
        $retailerId = $request->retailer_id ? $request->retailer_id : 0;
        $date = $request->date ? $request->date : $this->scrapedDataService->getLatestScrapedData();

        if (auth()->user()->isAdmin) {
            $userId = $request->userId ? $request->userId : 0;
        } else {
            $userId = auth()->id();
        }

        $avgRating = $this->scrapedDataService->avgRating($productId, $mpn, $retailerId, $date, $userId);
        $avgPrice = $this->scrapedDataService->avgPrice($productId, $mpn, $retailerId, $date, $userId);
        $avgImages = $this->scrapedDataService->avgImages($productId, $mpn, $retailerId, $date, $userId);

        $mergedData = $avgRating->map(function ($item) use ($avgPrice, $avgImages) {
            $retailerId = $item['retailer_id'];

            return [
                'retailer_id' => $retailerId,
                'average_product_rating' => round($item['average_product_rating'], 4),
                'average_product_price' => round($avgPrice->firstWhere('retailer_id', $retailerId)['average_product_price'], 4),
                'average_images_per_product' => round((float)$avgImages->firstWhere('retailer_id', $retailerId)['average_images_per_product'], 4),
            ];
        });

        return $this->successResponse("Metrics data received", data: $mergedData);
    }
}
