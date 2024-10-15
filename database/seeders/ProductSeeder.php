<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Retailer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $retailers = collect(Retailer::all()->modelKeys());

        Product::factory(1000)
            ->create()
            ->each(function ($product) use ($retailers, $faker) {
                $randomRetailers = $retailers->random(rand(1, 10));
                foreach ($randomRetailers as $retailer) {
                    $product->retailers()->attach($retailer, [
                        'product_url' => $faker->url(),
                    ]);
                }

                ProductImage::factory(rand(1,3))->create([
                    'product_id' => $product->id,
                ]);
            });
    }
}
