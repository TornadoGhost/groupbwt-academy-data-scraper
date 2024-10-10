<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\User;
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
        $randomRetailer = Retailer::query()->inRandomOrder()->first()->id;
        $randomProduct = Product::query()->inRandomOrder()->first()->id;
        $randomUser = User::query()->inRandomOrder()->first()->id;
        $randomSessionId = md5(time());

        if (!$randomRetailer) {
            $randomRetailer = Retailer::factory()->create();
        }

        if (!$randomProduct) {
            $randomProduct = Product::factory()->create();
        }

        if (!$randomUser) {
            $randomUser = Product::factory()->create();
        }

        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 25000),
            'retailer_id' => $randomRetailer,
            'product_id' => $randomProduct,
            'user_id' => $randomUser,
            'session_id' => $randomSessionId
        ];
    }
}
