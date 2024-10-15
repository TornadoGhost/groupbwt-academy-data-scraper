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

        foreach($statuses as $index => $status) {
            SessionStatus::factory()->create(
                [
                    'code' => ++$index,
                    'code_name' => $status
                ]
            );
        }
    }
}
