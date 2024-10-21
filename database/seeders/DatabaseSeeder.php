<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RegionSeeder::class,
            RetailerSeeder::class,
            UserSeeder::class,
            ProductSeeder::class,
            ScrapingSessionSeeder::class,
//            ScrapedDataSeeder::class,
//            ScrapedDataImageSeeder::class
        ]);
    }
}
