<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Retailer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference',
        'currency',
        'logo_path',
        'isActive',
    ];

    public function scrapedData(): HasMany
    {
        return $this->hasMany(ScrapedData::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)->withPivot('isActive', 'product_url');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_retailer');
    }

    public function scrapingSessions(): HasMany
    {
        return $this->hasMany(ScrapingSession::class);
    }
}
