<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\NotificationServiceInterface;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function __construct
    (
        protected NotificationServiceInterface $notificationService
    )
    {
    }

    public function get(Request $request): array
    {
        $notifications = auth()->user()->notifications;

        $dropdownHtml = '';

        foreach ($notifications as $key => $not) {
            $icon = "<i class='mr-2 fas fa-fw fa-file text-success'></i>";

            $time = "<span class='float-right text-muted text-sm'>"
                   . $this->notificationService->getRightTime($not->created_at) .
                "</span>";

            $dropdownHtml .= "<a href=" . route('exportTables.index') . " class='dropdown-item'>
                            {$icon}{$not['data']['message']}{$time}
                          </a>";

            if ($key < count($notifications) - 1) {
                $dropdownHtml .= "<div class='dropdown-divider'></div>";
            }
        }

        // Return the new notification data.

        return [
            'label' => count($notifications),
            'label_color' => 'danger',
            'icon_color' => 'dark',
            'dropdown' => $dropdownHtml,
        ];
    }
}
