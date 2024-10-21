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

    protected $fillable = [
        'title',
        'description',
        'price',
        'avg_rating',
        'stars_1',
        'stars_2',
        'stars_3',
        'stars_4',
        'stars_5',
        'retailer_id',
        'product_id',
        'user_id',
        'session_id'
    ];

    public function scrapedDataImages(): HasMany
    {
        return $this->hasMany(ScrapedDataImage::class);
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

    public function scrapingSession(): BelongsTo
    {
        return $this->belongsTo(ScrapingSession::class, 'session_id');
    }
}
