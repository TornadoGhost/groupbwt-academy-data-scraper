<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScrapingSession extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(Retailer::class);
    }

    public function scrapedData(): HasMany
    {
        return $this->hasMany(ScrapedData::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(SessionStatus::class);
    }
}
