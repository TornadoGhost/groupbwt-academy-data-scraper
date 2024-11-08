<?php

namespace App\Exports;

use App\Services\Contracts\ScrapedDataServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ScrapedDataByRetailerSheet implements FromCollection, WithHeadings, WithTitle, WithColumnWidths, ShouldQueue
{
    use Exportable, Queueable;
    public function __construct(
        public int $retailerId,
        public string $date,
        public ScrapedDataServiceInterface $scrapedDataService,
    )
    {
    }

    public function collection(): Collection
    {
        return $this->scrapedDataService
            ->scrapedDataByRetailer($this->retailerId, $this->date)->map(function ($scrapedData) {
                return [
                    'Product name' => $scrapedData->title,
                    'Product manufacturer part number' => $scrapedData->product->manufacturer_part_number,
                    'Product description' => $scrapedData->description,
                    'Product price' => $scrapedData->price,
                    'Product pack size' => $scrapedData->product->pack_size,
                    'Product average rating' => $scrapedData->avg_rating,
                    'Product 1 star number' => $scrapedData->stars_1,
                    'Product 2 star number' => $scrapedData->stars_2,
                    'Product 3 star number' => $scrapedData->stars_3,
                    'Product 4 star number' => $scrapedData->stars_4,
                    'Product 5 star number' => $scrapedData->stars_5,
                    'Retailer name' => $scrapedData->retailer->name,
                    'Created at' => $scrapedData->created_at->format('Y-m-d H:i'),
                ];
            });
    }

    public function headings(): array
    {
        return [
            'Product name',
            'Product manufacturer part number',
            'Product description',
            'Product price',
            'Product pack size',
            'Product average rating',
            'Product 1 star number',
            'Product 2 star number',
            'Product 3 star number',
            'Product 4 star number',
            'Product 5 star number',
            'Retailer name',
            'Created at'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 16,
            'B' => 32,
            'C' => 54,
            'D' => 13,
            'E' => 17,
            'F' => 21,
            'G' => 21,
            'H' => 21,
            'I' => 21,
            'J' => 21,
            'K' => 21,
            'L' => 13,
            'M' => 16,
        ];
    }

    public function title(): string
    {
        return 'Scraped Data';
    }
}
