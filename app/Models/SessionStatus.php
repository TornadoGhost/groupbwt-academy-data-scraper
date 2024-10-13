<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SessionStatus extends Model
{
    use HasFactory;

    public $timestamps = false;

    public function scrapingSessions(): HasMany
    {
        return $this->hasMany(ScrapingSession::class);
    }
}
