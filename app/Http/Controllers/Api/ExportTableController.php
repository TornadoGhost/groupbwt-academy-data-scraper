<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\DownloadExportTableRequest;
use App\Http\Resources\ExportTableResource;
use App\Models\ExportTable;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ExportTableController
{
    use JsonResponseHelper;

    public function __construct(
        protected ExportTableServiceInterface $exportTableService
    )
    {
    }

    public function index(): JsonResponse
    {
        $files = $this->exportTableService->getLatestExportedFiles(auth()->id());

        return $this->successResponse('Exported files received', data: ExportTableResource::collection($files));
    }

    public function store(Request $request): JsonResponse
    {
        $result = $this->exportTableService->create(auth()->id(), $request->get('file_name'), $request->get('path'));

        return $this->successResponse('Export file stored', data: $result);
    }

    public function destroy(ExportTable $file): JsonResponse
    {
        return $this->exportTableService->deleteFile($file);
    }
}
