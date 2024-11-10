<?php

namespace App\Services;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\SaveExportTableData;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\HeadingRowImport;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductService extends BaseCrudService implements ProductServiceInterface
{
    use JsonResponseHelper;

    public function __construct(
        protected ExportTableServiceInterface $exportTableService,
    )
    {
        parent::__construct();
    }

    protected function getRepositoryClass(): string
    {
        return ProductRepositoryInterface::class;
    }

    public function productsForMetrics(int $userId): Collection
    {
        return $this->repository()->productsForMetrics($userId);
    }

    public function import(UploadedFile $file, User $user): JsonResponse
    {
        $productImport = (new ProductsImport($user));
        $headings = (new HeadingRowImport)->toArray($file)[0][0];
        $expectedHeadings = ['title', 'manufacturer_part_number', 'pack_size'];

        $headersValidation = $this->importHeadersValidation($headings, $expectedHeadings);
        $rowsValidation = $this->importRowsValidation($productImport, $file, $user);

        if (!empty($headersValidation)) {
            return $this->errorResponse('Wrong headings in CSV file', 422, $headersValidation);

        }

        if (!empty($rowsValidation)) {
            return $this->errorResponse('Validation error', 422, $rowsValidation);
        }

        (new ProductsImport(request()->user()))->queue($file);

        return $this->successResponse('CSV data imported successfully', 201);
    }

    protected function importHeadersValidation(array $headings, array $expectedHeadings): array
    {
        $res = array_diff($headings, $expectedHeadings);
        $errors = [];
        foreach ($res as $key => $value) {
            $errors[] = "Wrong header, received '$value', must be '$expectedHeadings[$key]'";
        }

        return $errors;
    }

    protected function importRowsValidation(object $productImport, UploadedFile|string $file, User $user): array
    {
        $rows = Excel::toArray($productImport, $file);
        $rules = [
            'title' => ['required', 'string', 'min:3', 'max:100'],
            'manufacturer_part_number' => [
                'required',
                'string',
                'min:3',
                'max:50',
                Rule::unique('products')->where(fn($query) => $query->where('user_id', $user->id)),
            ],
            'pack_size' => ['required', 'string', 'min:3', 'max:20'],
        ];
        $validationErrors = [];
        $manufacturerPartNumbers = [];

        foreach ($rows[0] as $index => $row) {
            $validator = Validator::make($row, $rules);

            $manufacturerPartNumber = $row['manufacturer_part_number'];
            if (in_array($manufacturerPartNumber, $manufacturerPartNumbers)) {
                $validationErrors[] = 'Row №' . ($index + 2) . ' - ' . "Duplicate manufacturer_part_number found";
            } else {
                $manufacturerPartNumbers[] = $manufacturerPartNumber;
            }

            if ($validator->fails()) {
                foreach ($validator->errors()->messages() as $messages) {
                    foreach ($messages as $message) {
                        $validationErrors[] = 'Row №' . ($index + 2) . ' - ' . $message;
                    }
                }
            }
        }

        return $validationErrors;
    }

    public function downloadExampleImportFile(): StreamedResponse
    {
        return Storage::download('/excel/import/example.csv', 'import_products_example.csv');
    }

    public function exportExcel(User $user): JsonResponse
    {
        $fileName = 'Product Data';
        $filePath = 'excel/export/' . $user->id . '/products/' . md5($fileName . now()) . '.xlsx';

        (new ProductsExport($this))->store($filePath)->chain([
            new NotifyUserOfCompletedExport($user, 'Products'),
            new SaveExportTableData($fileName, $filePath, $user, $this->exportTableService)
        ]);

        return $this->successResponse('Products exportation started');
    }

    public function getNameById(int $id): ?string
    {
        return $this->repository()->getNameById($id);
    }

    public function allLatest(User $user): Collection
    {
        return $this->repository()->allLatest($user);
    }
}
