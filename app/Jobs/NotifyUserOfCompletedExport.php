<?php

namespace App\Jobs;

use App\Models\User;
use App\Notifications\ProductsExportReady;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class NotifyUserOfCompletedExport implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public string $prefix,
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->notify(new ProductsExportReady($this->prefix));
    }
}
