<?php

namespace App\Exports;

use App\Exports\Sheets\ProductsFiltersSheet;
use App\Exports\Sheets\ProductsSheet;
use App\Exports\Sheets\ScrapedDataByRetailerFiltersSheet;
use App\Exports\Sheets\ScrapedDataByRetailerSheet;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProductsExport implements WithMultipleSheets, ShouldQueue
{
    use Exportable, Queueable;

    public function __construct(
        protected array                   $filters,
        protected ProductServiceInterface $productService,
    )
    {
    }

    public function sheets(): array
    {
        return [
            new ProductsFiltersSheet($this->filters),
            new ProductsSheet($this->productService),
        ];
    }
}
