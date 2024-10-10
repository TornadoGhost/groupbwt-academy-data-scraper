<?php

namespace Database\Seeders;

use App\Models\Region;
use App\Models\Retailer;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superUser = User::query()->where('isAdmin', 1)->first();
        if (!$superUser) {
            User::factory()->create([
                'email' => 'superuser@gmail.com',
                'username' => 'Super User',
                'isAdmin' => 1,
                'region_id' => null
            ]);
        }

        $retailers = Retailer::all();
        if ($retailers->count() < 1) {
            $retailers = Retailer::factory(10)->create();
        }

        $region = Region::all();
        if ($region->count() < 1) {
            $this->call(RegionSeeder::class);
        }

        // isAdmin equal 0 by default
        User::factory(10)
            ->create([
                'region_id' => Region::query()->inRandomOrder()->first()->id
            ])
            ->each(function ($user) use ($retailers) {
                $randomRetailer = $retailers->random(rand(1, 10));
                $user->retailers()->attach($randomRetailer);
            });
    }
}
