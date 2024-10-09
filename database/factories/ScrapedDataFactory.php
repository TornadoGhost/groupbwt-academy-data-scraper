<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Retailer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScrapedData>
 */
class ScrapedDataFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 25000),
            'retailer_id' => Retailer::query()->inRandomOrder()->first()->id,
        ];
    }
}


//'retailer_id' => Retailer::query()->inRandomOrder()->first()->id,
//'product_id' => Product::query()->inRandomOrder()->first()->id
