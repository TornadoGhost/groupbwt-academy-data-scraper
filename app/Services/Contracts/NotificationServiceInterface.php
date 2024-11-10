<?php

namespace App\Services\Contracts;

use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;

interface NotificationServiceInterface
{
    public function getRightTime(string $time): string;
    public function getNotification(User|Authenticatable $user): array;
}
