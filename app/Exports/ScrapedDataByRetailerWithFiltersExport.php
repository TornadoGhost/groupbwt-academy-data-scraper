<?php

namespace App\Exports;

use App\Exports\Sheets\ScrapedDataByRetailerFiltersSheet;
use App\Exports\Sheets\ScrapedDataByRetailerSheet;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ScrapedDataByRetailerWithFiltersExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    public function __construct(
        protected int $retailerId,
        protected string $date,
        protected ScrapedDataServiceInterface $scrapedDataService,
        protected array $filters,
        protected RetailerServiceInterface $retailerService,
    )
    {
    }

    public function sheets(): array
    {
        return [
            new ScrapedDataByRetailerFiltersSheet($this->filters, $this->retailerService),
            new ScrapedDataByRetailerSheet($this->retailerId, $this->date, $this->scrapedDataService),
        ];
    }
}
