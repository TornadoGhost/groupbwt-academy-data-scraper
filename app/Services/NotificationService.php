<?php

namespace App\Services;

use App\Models\User;
use App\Services\Contracts\NotificationServiceInterface;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;

class NotificationService implements NotificationServiceInterface
{
    public function getNotification(User|Authenticatable $user): array
    {
        $notifications = $user->notifications;
        $unreadNotifications = $user->unreadNotifications;
        $dropdownHtml = '';

        foreach ($unreadNotifications as $key => $not) {
            $icon = "<i class='mr-2 fas fa-fw fa-file text-success'></i>";

            $time = "<span class='float-right text-muted text-sm'>"
                . $this->getRightTime($not->created_at) .
                "</span>";

            $dropdownHtml .= "<a href=" . route('exportTables.index') . " class='dropdown-item'>
                                {$icon}{$not['data']['message']}{$time}
                              </a>";

            if ($key < count($notifications) - 1) {
                $dropdownHtml .= "<div class='dropdown-divider'></div>";
            }
        }

        return [
            'label' => count($unreadNotifications),
            'label_color' => 'danger',
            'icon_color' => 'dark',
            'dropdown' => $dropdownHtml,
        ];
    }
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
