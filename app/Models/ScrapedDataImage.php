<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScrapedDataImage extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'path',
        'scraped_data_id'
    ];

    public function scrapedData(): BelongsTo
    {
        return $this->belongsTo(ScrapedData::class);
    }
}
