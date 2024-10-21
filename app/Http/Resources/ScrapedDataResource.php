<?php

namespace App\Http\Resources;

use App\Models\Retailer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScrapedDataResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'avg_rating' => $this->avg_rating,
            'stars_1' => $this->stars_1,
            'stars_2' => $this->stars_2,
            'stars_3' => $this->stars_3,
            'stars_4' => $this->stars_4,
            'stars_5' => $this->stars_5,
            'retailer' => [
                'id' => $this->retailer->id,
                'name' => $this->retailer->name,
                'currency' => $this->retailer->currency,
                'logo_path' => $this->retailer->logo_path,
                'isActive' => $this->retailer->isActive,
            ],
            'product' => [
                'id' => $this->product->id,
                'title' => $this->product->title,
                'manufacturer_part_number' => $this->product->manufacturer_part_number,
                'pack_size' => $this->product->pack_size,
                'product_url' => $this->product->retailers()?->where('retailer_id', $this->retailer->id)->first()->pivot->product_url
            ],
            'images' => $this->scrapedDataImages->map(function ($image) {
                return [
                    'path' => $image->path,
                ];
            }),
            'create_at' => $this->created_at->format('d-m-Y'),
        ];
    }
}
