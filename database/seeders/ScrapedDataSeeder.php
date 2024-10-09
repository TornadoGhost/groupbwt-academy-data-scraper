<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\ScrapedData;
use App\Models\ScrapedDataImages;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScrapedDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $startDate = Carbon::now()->subYear();
        $currentDate = Carbon::now();
        $retailers = Retailer::all();

        if ($retailers->count() < 1) {
            $retailers = Retailer::factory(10)->create();
        }

        if ($retailers->random()->products()->count() < 1) {
            $this->call(ProductSeeder::class);
        }

        while ($startDate <= $currentDate) {
            foreach ($retailers as $retailer) {
                for ($i = 0; $i < $retailer->products()->count(); $i++) {
                    $scrapedData = ScrapedData::factory()->create([
                        'retailer_id' => $retailer->id,
                        'created_at' => $startDate,
                        'updated_at' => $startDate
                    ]);

                    ScrapedDataImages::factory(rand(1, 3))->create([
                        'scraped_data_id' => $scrapedData->id,
                    ]);
                }
            }
            $startDate->addDay();
        }

        /*for ($i = 0; $i < 365; $i++) {
            foreach ($products as $product) {
                $data = ScrapedData::factory()->create([
                    'product_id' => $product->id,
                    'retailer_id' => Retailer::query()->inRandomOrder()->first()->id,
                    'created_at' => $startDate,
                    'updated_at' => $startDate
                ]);

                ScrapedDataImages::factory(rand(1, 3))->create([
                    'scraped_data_id' => $data->id,
                ]);
            }
            $startDate->addDay();
        }*/
    }
}
