<?php

namespace Database\Seeders;

use App\Models\SessionStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SessionStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['In Progress', 'Done', 'Error'];

        foreach($statuses as $status) {
            SessionStatus::factory()->create(
                ['name' => $status]
            );
        }
    }
}
