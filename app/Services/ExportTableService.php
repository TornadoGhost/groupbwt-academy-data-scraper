<?php

namespace App\Services;

use App\Models\ExportTable;
use App\Repositories\Contracts\ExportTableRepositoryInterface;
use App\Repositories\ExportTableRepository;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportTableService implements ExportTableServiceInterface
{
    use JsonResponseHelper;

    public function __construct(
        protected ExportTableRepositoryInterface $repository
    )
    {
    }

    public function getExportedFiles($userId): Collection
    {
        return $this->repository->getExportedFiles($userId);
    }

    public function getLatestExportedFiles($userId): Collection
    {
        return $this->repository->getLatestExportedFiles($userId);
    }

    public function create(string $userId, string $fileName, string $filePath): ExportTable
    {
        return $this->repository->create($userId, $fileName, $filePath);
    }

    public function findOrFail(int $id): ExportTable
    {
        return $this->repository->show($id);
    }

    public function delete(int $id): bool
    {
        return $this->repository->delete($id);
    }

    public function setPath(string $fileName, int $userId): string
    {
        return 'excel/export/' . $userId . '/products/' . md5($fileName . now()) . '.xlsx';
    }

    public function checkFileExistence(string $path): bool
    {
        return Storage::exists($path);
    }

    public function downloadFile(string $path, string $fileName = 'export_file'): StreamedResponse
    {
        return Storage::download($path, $fileName);
    }

    public function deleteFile(ExportTable $file): JsonResponse
    {
        if (!$this->checkFileExistence($file->path)) {
            return $this->errorResponse('File not found', 404);
        }

        DB::beginTransaction();
        try {
            if (Storage::delete($file->path)) {
                $this->delete($file->id);
                DB::commit();
                return $this->successResponse('File deleted');
            } else {
                throw new \Exception('Failed to delete file from storage');
            }
        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse($e->getMessage(), $e->getCode());
        }
    }
}
