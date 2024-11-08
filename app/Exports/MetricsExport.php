<?php

namespace App\Exports;

use App\Exports\Sheets\MetricsFiltersSheet;
use App\Exports\Sheets\MetricsSheet;
use App\Models\User;
use App\Services\Contracts\MetricServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class MetricsExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    public function __construct(
        protected ScrapedDataServiceInterface $scrapedDataService,
        protected MetricServiceInterface      $metricService,
        protected array                       $products,
        protected array                       $retailers,
        protected string                      $startDate,
        protected string                      $endDate,
        protected int                         $userId,
        protected array                       $filters,
        protected RetailerServiceInterface    $retailerService,
        protected ProductServiceInterface     $productService,
        protected User                        $user,
    )
    {
    }

    public function sheets(): array
    {
        return [
            new MetricsFiltersSheet($this->filters, $this->retailerService, $this->productService, $this->user),
            new MetricsSheet(
                $this->scrapedDataService,
                $this->metricService,
                $this->products,
                $this->retailers,
                $this->startDate,
                $this->endDate,
                $this->userId,
            ),
        ];
    }
}
