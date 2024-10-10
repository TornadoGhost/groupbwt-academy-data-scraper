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
                foreach ($retailer->products as $product) {
                    $scrapedData = ScrapedData::factory()->create([
                        'retailer_id' => $retailer->id,
                        'product_id' => $product->id,
                        'user_id' => $product->user->id,
                        'created_at' => $startDate,
                    ]);

                    ScrapedDataImages::factory(rand(1, 3))->create([
                        'scraped_data_id' => $scrapedData->id,
                    ]);
                }
            }
            $startDate->addDay();
        }
    }
}
