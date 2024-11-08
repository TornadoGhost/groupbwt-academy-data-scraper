<?php

namespace App\Exports;

use App\Services\Contracts\MetricServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class MetricsExport implements FromCollection, WithHeadings, WithTitle, WithColumnWidths, ShouldQueue
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
    )
    {
    }

    public function collection(): Collection
    {
        $avgRating = $this->scrapedDataService->avgRating(
            $this->products,
            $this->retailers,
            $this->startDate,
            $this->endDate,
            $this->userId,
        );
        $avgPrice = $this->scrapedDataService->avgPrice(
            $this->products,
            $this->retailers,
            $this->startDate,
            $this->endDate,
            $this->userId,
        );
        $avgImages = $this->scrapedDataService->avgImages(
            $this->products,
            $this->retailers,
            $this->startDate,
            $this->endDate,
            $this->userId,
        );

        $avgPriceMap = $avgPrice->keyBy('retailer_id')->toArray();
        $avgImagesMap = $avgImages->keyBy('retailer_id')->toArray();

        $mergedData = collect($this->metricService->getAvgData($avgRating, $avgPriceMap, $avgImagesMap));

        return $mergedData->map(function ($metric) {
            return [
                'Retailer name' => $metric['retailer_name'],
                'Average product rating' => $metric['average_product_rating'],
                'Average product price' => $metric['average_product_price'],
                'Average images per product' => $metric['average_images_per_product'],
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Retailer name',
            'Average product rating',
            'Average product price',
            'Average images per product',
        ];
    }

    public function columnWidths(): array
    {
        return array(
            'A' => 28,
            'B' => 20,
            'C' => 20,
            'D' => 25,
        );
    }

    public function title(): string
    {
        return 'Metrics';
    }
}
