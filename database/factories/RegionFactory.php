<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Region>
 */
class RegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $regions = [
            'North America',
            'Latin America',
            'Northern Europe',
            'Western Europe',
            'Southern Europe',
            'Eastern Europe',
            'Central Asia',
            'Eastern Asia',
        ];
        return [
            'name' => array_rand($regions)
        ];
    }
}
