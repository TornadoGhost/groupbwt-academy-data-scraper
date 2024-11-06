<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\DownloadExportTableRequest;
use App\Http\Resources\ExportTableResource;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

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
        $files = $this->exportTableService->getExportedFiles(auth()->id());

        return $this->successResponse('Exported files received', data: ExportTableResource::collection($files));
    }

    public function store(Request $request): JsonResponse
    {
        $result = $this->exportTableService->create(auth()->id(), $request->get('file_name'), $request->get('path'));

        return $this->successResponse('Export file stored', data: $result);
    }

    public function destroy(int $id): JsonResponse
    {
        $data = $this->exportTableService->show($id);

        if (!$this->exportTableService->checkFileExistence($data->path)) {
            return $this->errorResponse('File not found', 404);
        }

        $res = Storage::delete($data->path);

        if ($res) {
            $this->exportTableService->delete($id);
            return $this->successResponse('File deleted');
        } else {
            return $this->errorResponse('File deleted, but file in database was not deleted', 20);
        }
    }
}
