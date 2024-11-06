<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\DownloadExportTableRequest;
use App\Services\Contracts\ExportTableServiceInterface;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportTableController extends Controller
{
    public function __construct(
        protected ExportTableServiceInterface $exportTableService
    )
    {
    }

    public function index(): View
    {
        return view('exportTables.index');
    }

    public function download(DownloadExportTableRequest $request): StreamedResponse
    {
        return $this->exportTableService->downloadFile(
            $request->validated('file_path'),
            $request->validated('file_name')
        );
    }
}
