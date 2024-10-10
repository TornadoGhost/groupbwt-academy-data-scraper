<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScrapedData extends Model
{
    use HasFactory;

    protected $table = 'scraped_data';
    public $timestamps = false;

    public function images(): HasMany
    {
        return $this->hasMany(ScrapedDataImages::class);
    }

    public function retailer(): BelongsTo
    {
        return $this->belongsTo(Retailer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
