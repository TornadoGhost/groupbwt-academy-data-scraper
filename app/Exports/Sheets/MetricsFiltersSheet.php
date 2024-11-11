<?php

namespace App\Exports\Sheets;

use App\Models\User;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class MetricsFiltersSheet implements FromCollection, WithHeadings, WithTitle, WithColumnWidths, ShouldQueue
{
    use Exportable, Queueable;
    public function __construct(
        protected array $filters,
        protected RetailerServiceInterface $retailerService,
        protected ProductServiceInterface $productService,
        protected User $user,
        protected ScrapingSessionServiceInterface $scrapingSessionService,
    )
    {
    }

    public function collection(): Collection
    {
        return collect($this->filters)->map(function ($value, $key) {
            if (is_array($value) && $key === 'retailers') {
                $retailers = [];
                foreach($value as $retailerId) {
                    $retailers[] = $this->retailerService->getNameById($retailerId);
                }

                return [
                    'Filter' => ucfirst(str_replace('_', ' ', $key)),
                    'Value' => implode(', ', $retailers),
                ];
            }

            if (is_array($value) && $key === 'products') {
                $products = [];
                foreach($value as $productId) {
                    $products[] = $this->productService->getNameById($productId);
                }

                return [
                    'Filter' => ucfirst(str_replace('_', ' ', $key)),
                    'Value' => implode(', ', $products),
                ];
            }

            if ($key === 'user_id') {
                if ($this->user->isAdmin) {
                    return [
                        'Filter' => ucfirst(str_replace('_', ' ', $key)),
                        'Value' => $value,
                    ];
                }

                return [];
            }

            if ($key === 'current_day') {
                return [
                    'Filter' => 'File Created',
                    'Value' => Carbon::parse($value)->format('Y-m-d H:m'),
                ];
            }

            return [
                'Filter' => ucfirst(str_replace('_', ' ', $key)),
                'Value' => empty($value) ? Carbon::parse($this->scrapingSessionService->getLatestScrapingSession())->format('Y-m-d') : $value,
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
