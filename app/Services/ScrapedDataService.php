<?php

namespace App\Services;

use App\Exports\ScrapedDataByRetailerWithFiltersExport;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\SaveExportTableData;
use App\Models\User;
use App\Repositories\Contracts\ScrapedDataRepositoryInterface;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;

class ScrapedDataService extends BaseCrudService implements ScrapedDataServiceInterface
{
    use JsonResponseHelper;
    public function __construct(
        protected ExportTableServiceInterface $exportTableService,
        protected RetailerServiceInterface $retailerService,
    )
    {
        return parent::__construct();
    }

    protected function getRepositoryClass(): string
    {
        return ScrapedDataRepositoryInterface::class;
    }

    public function avgRating(
        array $products = [],
        array $retailers = [],
        string $startDate = '',
        string $endDate = '',
        int $userId = 0
    ): Collection
    {
        return $this->repository()->avgRating(
            $products,
            $retailers,
            $startDate,
            $endDate,
            $userId
        );
    }

    public function avgPrice(
        array $products = [],
        array $retailers = [],
        string $startDate = '',
        string $endDate = '',
        int $userId = 0
    ): Collection
    {
        return $this
            ->repository()
            ->avgPrice(
                $products,
                $retailers,
                $startDate,
                $endDate,
                $userId
            );
    }

    public function avgImages(
        array $products = [],
        array $retailers = [],
        string $startDate = '',
        string $endDate = '',
        int $userId = 0
    ): Collection
    {
        return $this
            ->repository()
            ->avgImages($products, $retailers, $startDate, $endDate, $userId);
    }

    public function scrapedDataByRetailer(int $retailerId, string $date) {
        return $this->repository()->scrapedDataByRetailer($retailerId, $date);
    }

    public function exportByRetailer(int $retailer_id, string $date, User $user): JsonResponse
    {
        $retailerName = $this->retailerService->getNameById($retailer_id);
        $fileName = 'Scraped Data - ' . $retailerName;
        $filePath = $this->exportTableService->setPath($fileName, $user->id);
        (new ScrapedDataByRetailerExport($retailer_id, $date, $this))
            ->store($filePath)->chain([
                new NotifyUserOfCompletedExport($user, "Scraped Data"),
                new SaveExportTableData($fileName, $filePath, $user, $this->exportTableService
                ),
            ]);

        return $this->successResponse('Scraped data export for retailer ' . $retailerName . ' started');
    }
}
