<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\ScrapingSession;
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
        $randomRetailer = collect(Retailer::all()->modelKeys());
        $randomProduct = collect(Product::all()->modelKeys());
        $randomUser = collect(User::all()->modelKeys());
        $randomSessionId = collect(ScrapingSession::all()->modelKeys());
        $stars_1 = rand(1, 50);
        $stars_2 = rand(1, 50);
        $stars_3 = rand(1, 50);
        $stars_4 = rand(1, 50);
        $stars_5 = rand(1, 50);
        $avg_rating =  ($stars_1 + $stars_2 + $stars_3 + $stars_4 + $stars_5) / 5;

        return [
            'title' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'price' => $this->faker->numberBetween(100, 25000),
            'avg_rating' => $avg_rating,
            'stars_1' => $stars_1,
            'stars_2' => $stars_2,
            'stars_3' => $stars_3,
            'stars_4' => $stars_4,
            'stars_5' => $stars_5,
            'retailer_id' => $randomRetailer->random(),
            'product_id' => $randomProduct->random(),
            'user_id' => $randomUser->random(),
            'session_id' => $randomSessionId->random()
        ];
    }
}
