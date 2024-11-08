<?php

namespace App\Exports;

use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProductsExport implements FromCollection, WithHeadings, WithColumnWidths, ShouldQueue
{
    use Exportable, Queueable;

    public function __construct(
        protected ProductServiceInterface $productService,
    )
    {
    }

    public function collection(): AnonymousResourceCollection|Collection
    {
        return $this->productService->all()->flatMap(function ($product) {
            return $product->retailers->map(function ($retailer) use ($product) {
                return [
                    'Product ID' => $product->id,
                    'Product Name' => $product->title,
                    'Product Manufacturer Part Number' => $product->manufacturer_part_number,
                    'Product Pack Size' => $product->pack_size,
                    'Product Added' => $product->created_at,
                    'Product Url' => $retailer->pivot->product_url,
                    'Retailer Name' => $retailer->name,
                    'Retailer Currency' => $retailer->currency,
                    'Retailer Reference' => $retailer->reference,
                ];
            });
        });
    }

    public function headings(): array
    {
        return [
            'Product ID',
            'Product Name',
            'Product Manufacturer Part Number',
            'Product Pack Size',
            'Product Added',
            'Product Url',
            'Retailer Name',
            'Retailer Currency',
            'Retailer Reference',
        ];
    }

    public function columnWidths(): array
    {
        return array(
            'A' => 10,
            'B' => 15,
            'C' => 31,
            'D' => 16,
            'E' => 19,
            'F' => 80,
            'G' => 28,
            'H' => 16,
            'I' => 74,
        );
    }
}
