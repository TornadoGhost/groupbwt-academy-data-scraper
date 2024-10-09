<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapedDataImages extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function scrapedData(): BelongsTo
    {
        return $this->belongsTo(ScrapedData::class);
    }
}
