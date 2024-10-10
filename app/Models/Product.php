<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function retailers(): BelongsToMany
    {
        return $this->belongsToMany(Retailer::class, 'product_retailer');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scraped_data(): HasMany
    {
        return $this->hasMany(ScrapedData::class);
    }
}
