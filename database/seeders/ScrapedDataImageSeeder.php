<?php

namespace Database\Seeders;

use App\Models\ScrapedData;
use App\Models\ScrapedDataImage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ScrapedDataImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $faker = Faker::create();
        $scrapedData = ScrapedData::query()->limit(200000)->get()->modelKeys();

        foreach ($scrapedData as $d) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $data[] = [
                    'path' => $faker->imageUrl(),
                    'scraped_data_id' => $d,
                ];
            }
        }
        $chunks = array_chunk($data, 4000);
        foreach ($chunks as $chunk) {
            DB::transaction(function () use ($chunk) {
                DB::table('scraped_data_images')->insert($chunk);
            });
        }
    }
}
