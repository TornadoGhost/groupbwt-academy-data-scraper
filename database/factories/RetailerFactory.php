<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Retailer>
 */
class RetailerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->company(),
            'reference' => $this->faker->unique()->url(),
            'currency' => $this->faker->currencyCode(),
            'logo_path' => $this->faker->unique()->imageUrl(100,100,'business'),
            'isActive' => 1,
        ];
    }
}
