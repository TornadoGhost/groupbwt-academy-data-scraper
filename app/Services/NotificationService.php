<?php

namespace App\Services;

use App\Services\Contracts\NotificationServiceInterface;
use Carbon\Carbon;

class NotificationService implements NotificationServiceInterface
{
    public function getRightTime(string $time): string
    {
        $creationDate = Carbon::parse($time);
        $now = Carbon::now();

        if ($creationDate->diffInSeconds($now) < 60) {
            $diff = round($creationDate->diffInSeconds($now));
            return "{$diff} sec ago";
        } elseif ($creationDate->diffInMinutes($now) < 60) {
            $diff = round($creationDate->diffInMinutes($now))   ;
            return "{$diff} min ago";
        } else {
            // Повертаємо кількість годин
            $diff = round($creationDate->diffInHours($now));
            return "{$diff} hours ago";
        }
    }
}
