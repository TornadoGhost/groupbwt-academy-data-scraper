<?php

namespace Database\Factories;

use App\Models\Retailer;
use App\Models\SessionStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScrapingSession>
 */
class ScrapingSessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $statuses = collect(SessionStatus::all()->modelKeys());
        $retailers = collect(Retailer::all()->modelKeys());

        return [
            'status_id' => $statuses->random(),
            'retailer_id' => $retailers->random(),
            'started_at' => Carbon::now(),
            'ended_at' => Carbon::now()->addDay(),
        ];
    }
}
