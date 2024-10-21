<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScrapingSessionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'retailer' => $this->retailer->name,
            'status_code' => (int)$this->status_code,
            'started_at' => Carbon::parse($this->started_at)->format('H:i d-m-Y'),
            'ended_at' => $this->ended_at ? Carbon::parse($this->ended_at)->format('H:i d-m-Y') : $this->ended_at,
        ];
    }
}
