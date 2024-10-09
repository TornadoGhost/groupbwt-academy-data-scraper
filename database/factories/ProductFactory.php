<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductFactory extends Factory
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
            'manufacturer_part_number' => strtoupper($this->faker->unique()->lexify('????-????-????')),
            'pack_size' => $this->faker->randomElement(['case', 'each'])
        ];
    }
}
