<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\ScrapedData;
use App\Models\ScrapedDataImage;
use App\Models\ScrapingSession;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ScrapedDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $data = [];
        $counter = 0;
        $startDate = Carbon::now()->subYear();
        $currentDate = Carbon::now();
        $retailers = Retailer::all();

        while ($startDate <= $currentDate) {
            foreach ($retailers as $retailer) {
                $counter += 1;
                foreach ($retailer->products as $product) {
                    $rating = [
                        'stars1' => rand(1, 50),
                        'stars2' => rand(1, 50),
                        'stars3' => rand(1, 50),
                        'stars4' => rand(1, 50),
                        'stars5' => rand(1, 50),
                    ];
                    $avg_rating = ((1 * $rating['stars1'] + 2 * $rating['stars2'] + 3 * $rating['stars3'] + 4 * $rating['stars4'] + 5 * $rating['stars5']) / array_sum($rating));
                    $data[] = [
                        'title' => $faker->word(),
                        'description' => $faker->sentence(),
                        'price' => $faker->numberBetween(100, 25000),
                        'avg_rating' => round($avg_rating, 1),
                        'stars_1' => $rating['stars1'],
                        'stars_2' => $rating['stars2'],
                        'stars_3' => $rating['stars3'],
                        'stars_4' => $rating['stars4'],
                        'stars_5' => $rating['stars5'],
                        'retailer_id' => $retailer->id,
                        'product_id' => $product->id,
                        'user_id' => $product->user->id,
                        'session_id' => $counter,
                        'created_at' => $startDate->copy(),
                        'updated_at' => $startDate->copy()
                    ];
                }
            }
            $startDate->addDay();
        }

        $chunks = array_chunk($data, 4000);
        foreach ($chunks as $chunk) {
            DB::transaction(function () use ($chunk) {
                DB::table('scraped_data')->insert($chunk);
            });
        }
    }
}
