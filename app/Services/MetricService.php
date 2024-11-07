<?php

namespace App\Services;

use App\Exports\MetricsExport;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\SaveExportTableData;
use App\Models\User;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\MetricServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Traits\JsonResponseHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class MetricService implements MetricServiceInterface
{
    use JsonResponseHelper;

    public function __construct(
        protected ExportTableServiceInterface $exportTableService,
        protected ScrapingSessionServiceInterface $scrapingSessionService,
        protected ScrapedDataServiceInterface $scrapedDataService,
    )
    {
    }

    public function getAvgData(Collection $avgRating,array $avgPrice,array $avgImages): Collection|\Illuminate\Support\Collection
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

    public function exportExcel($startDate, $endDate, User $user): JsonResponse
    {
        $fileName = 'Metrics';
        $filePath = $this->exportTableService->setPath($fileName, $user->id);
        $startDate = $startDate ?? Carbon::parse($this->scrapingSessionService->getLatestScrapingSession())->format('Y-m-d');
        $endDate = $endDate ?? '';

        (new MetricsExport($this->scrapedDataService, $this, $startDate, $endDate))
            ->store($filePath)
            ->chain([
                new NotifyUserOfCompletedExport($user, 'Metrics'),
                new SaveExportTableData($fileName, $filePath, $user, $this->exportTableService)
            ]);

        return $this->successResponse('Metrics data export started');
    }
}
