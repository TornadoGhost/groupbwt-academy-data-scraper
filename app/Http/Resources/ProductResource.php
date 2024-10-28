<?php

namespace App\Http\Resources;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'manufacturer_part_number' => $this->manufacturer_part_number,
            'pack_size' => $this->pack_size,
            'created_at' => $this->created_at->format('d-m-Y'),
            'updated_at' => $this->updated_at->format('d-m-Y'),
            'retailers' => $this->retailers->map(function ($retailer) {
                return [
                    'id' => $retailer->id,
                    'name' => $retailer->name,
                    'currency' => $retailer->currency,
                    'logo_path' => $retailer->logo_path,
                    'isActive' => $retailer->isActive,
                    'product_url' => $retailer->pivot->product_url,
                ];
            }),
            'images' => $this->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'path' => $image->path,
                ];
            }),
        ];
    }
}
