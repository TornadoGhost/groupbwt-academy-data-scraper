<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductsExportReady extends Notification
{
    use Queueable;

    public function __construct(
        public string $prefix,
    )
    {

    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'status' => 'success',
            'message' => "{$this->prefix} export completed.",
        ];
    }
}
