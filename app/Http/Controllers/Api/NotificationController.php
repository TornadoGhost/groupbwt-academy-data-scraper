<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Web\App;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    use JsonResponseHelper;
    public function markAllAsRead(): JsonResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return $this->successResponse('All notifications marked as read');
    }

    public function deleteAll(): JsonResponse
    {
        auth()->user()->notifications()->delete();

        return $this->successResponse('All notifications deleted');
    }
}
