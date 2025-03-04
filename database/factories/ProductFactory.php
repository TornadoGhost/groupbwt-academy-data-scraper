<?php

namespace Database\Factories;

use App\Models\User;
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
        $users = User::query()->pluck('id');

        return [
            'title' => $this->faker->word(),
            'manufacturer_part_number' => strtoupper($this->faker->unique()->lexify('????-????-????')),
            'pack_size' => $this->faker->randomElement(['case', 'each']),
            'user_id' => $users->random(),
        ];
    }
}
