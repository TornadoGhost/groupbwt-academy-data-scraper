<?php

namespace App\Exports\Sheets;

use App\Services\Contracts\RetailerServiceInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ScrapedDataByRetailerFiltersSheet implements FromCollection, WithHeadings, WithTitle, WithColumnWidths, ShouldQueue
{
    use Exportable, Queueable;

    public function __construct(
        protected array                    $filters,
        protected RetailerServiceInterface $retailerService,
    )
    {
    }

    public function collection(): Collection
    {
        return collect($this->filters)->map(function ($value, $key) {
            if ($key === 'retailer_id') {
                return [
                    'Filter' => 'Retailer name',
                    'Value' => $this->retailerService->getNameById($value),
                ];
            }

            if ($key === 'date') {
                return [
                    'Filter' => 'Scraped Data',
                    'Value' => $value,
                ];
            }

            if ($key === 'current_day') {
                return [
                    'Filter' => 'File Created',
                    'Value' => Carbon::parse($value)->format('Y-m-d H:m'),
                ];
            }

            return [
                'Filter' => ucfirst(str_replace('_', ' ', $key)),
                'Value' => empty($value) ? 'None' : $value,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Filter',
            'Value',
        ];
    }

    public function title(): string
    {
        return 'Filters';
    }

    public function columnWidths(): array
    {
        return array(
            'A' => 12,
            'B' => 28,
        );
    }
}

