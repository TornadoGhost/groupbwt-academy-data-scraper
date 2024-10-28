<?php

namespace App\Services;

use App\Services\Contracts\MetricServiceInterface;
use Illuminate\Database\Eloquent\Collection;

class MetricService implements MetricServiceInterface
{
    public function getAvgData(Collection $avgRating,array $avgPrice,array $avgImages)
    {
        return $avgRating->map(function ($item) use ($avgPrice, $avgImages) {
            $retailerId = $item['retailer_id'];

            return [
                'retailer_id' => $retailerId,
                'retailer_name' => $item['retailer']['name'],
                'average_product_rating' => round($item['average_product_rating'], 2),
                'average_product_price' => round($avgPrice[$retailerId]['average_product_price'] ?? 0, 2),
                'average_images_per_product' => round((float)($avgImages[$retailerId]['average_images_per_product'] ?? 0), 2),
            ];
        });
    }
}
