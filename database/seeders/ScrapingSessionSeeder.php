<?php

namespace Database\Seeders;

use App\Models\Retailer;
use App\Models\ScrapingSession;
use App\Models\SessionStatus;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScrapingSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        $startDate = Carbon::now()->subYear();
        $currentDate = Carbon::now();
        $retailers = Retailer::query()->pluck('id');

        while ($startDate <= $currentDate) {
            foreach($retailers as $retailer) {
                $data[] = [
                    'retailer_id' => $retailer,
                    'status_code' => 1,
                    'started_at' => $startDate->copy(),
                    'ended_at' => $startDate->copy(),
                ];
            }
            $startDate->addDay();
        }

        $chunks = array_chunk($data, 1000);
        foreach ($chunks as $chunk) {
            DB::transaction(function () use ($chunk) {
                DB::table('scraping_sessions')->insert($chunk);
            });
        }
    }
}
