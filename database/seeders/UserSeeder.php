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

        $retailers = collect(Retailer::all()->modelKeys());
        $regions = collect(Region::all()->modelKeys());

        // isAdmin equal 0 by default
        User::factory(5)
            ->create([
                'region_id' => $regions->random()
            ])
            ->each(function ($user) use ($retailers) {
                $user->retailers()->attach($retailers->random(rand(1, 10)));
            });
    }
}
