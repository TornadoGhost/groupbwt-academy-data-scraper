<?php

namespace App\Jobs;

use App\Models\ExportTable;
use App\Models\User;
use App\Services\Contracts\ExportTableServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SaveExportTableData implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string                      $fileName,
        public string                      $filePath,
        public User                        $user,
        public ExportTableServiceInterface $exportTableService
    )
    {
    }

    public function handle(): void
    {
        $this->exportTableService->create(
            $this->user->id,
            $this->fileName,
            $this->filePath,
        );
    }
}
