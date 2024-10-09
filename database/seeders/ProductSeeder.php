<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Retailer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $retailers = Retailer::all();
        if ($retailers->count() < 1) {
            $retailers = Retailer::factory(10)->create();
        }
        Product::factory(1000)
            ->create()
            ->each(function ($product) use ($retailers) {
                $randomRetailers = $retailers->random(rand(1, 10));
                $product->retailers()->attach($randomRetailers);

                ProductImage::factory(rand(1,4))->create([
                    'product_id' => $product->id,
                ]);
            });
    }
}
