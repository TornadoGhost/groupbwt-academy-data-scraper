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

    public function get(): array
    {
        return $this->notificationService->getNotification(auth()->user());
    }
}
