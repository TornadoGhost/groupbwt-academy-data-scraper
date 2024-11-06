<?php

namespace App\Http\Controllers\Api;

use App\Exports\ProductsExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImportRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Imports\ProductsImport;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\SaveExportTableData;
use App\Models\Product;
use App\Notifications\ProductsExportReady;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    use JsonResponseHelper;

    public function __construct
    (
        protected ProductServiceInterface $productService,
        protected ExportTableServiceInterface $exportTableService,
    )
    {
    }

    public function index(): JsonResponse
    {
        /*$length = $request->input('length') ?? 10;
        $start = $request->input('start') ?? 0;
        $search = $request->input('search.value');

        $query = Product::query();
        if ($search) {
            $query->where('title', 'like', "%$search%")
                ->orWhere('manufacturer_part_number', 'like', "%$search%");
        }

        $total = $query->count();
        $users = $query->skip($start)->take($length)->get();

        return response()->json([
            "draw" => intval($request->input('draw')),
            "recordsTotal" => $total,
            "recordsFiltered" => $total,
            "data" => $users,
        ]);*/


        $products = $this->productService->all();

        return $this->successResponse('Products list received', data: ProductResource::collection($products));
//        return $this->successResponseWithPagination(ProductResource::collection($products), 'Products retrieved successfully.');
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        if (auth()->user()->cannot('create', Product::class)) {
            return $this->unauthorizedResponse();
        }

        $product = $this->productService->create($request->validated());

        return $this->successResponse('Product created', 201, ProductResource::make($product));
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->find($id);

        if (auth()->user()->cannot('view', $product)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse("Product received", data: ProductResource::make($product));
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->find($id);

        if (auth()->user()->cannot('update', $product)) {
            return $this->unauthorizedResponse();
        }

        $product = $this->productService->update($id, $request->validated());

        return $this->successResponse("Product updated", data: ProductResource::make($product));
    }

    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->find($id);

        if (auth()->user()->cannot('delete', $product)) {
            return $this->unauthorizedResponse();
        }

        $this->productService->delete($id);

        return $this->successResponse('Product deleted');
    }

    public function import(ProductImportRequest $request): JsonResponse
    {
        try {
//            Excel::queueImport(new ProductsImport, $request->validated('csv_file'));
            Excel::import(new ProductsImport, $request->validated('csv_file'));

            return $this->successResponse('CSV data imported successfully', 201);

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            return $this->errorResponse('Validation errors', 422, $failures);
        }
    }

    public function export(): JsonResponse
    {
        $fileName = 'products_' . md5(now());
        $filePath = 'excel/export/' . auth()->id() . '/products/' . $fileName . '.xlsx';
        (new ProductsExport($this->productService,))->store($filePath)->chain([
            new NotifyUserOfCompletedExport(request()->user(), 'Products'),
            new SaveExportTableData($fileName, $filePath, request()->user(), $this->exportTableService)
        ]);

        return $this->successResponse('Products exportation started');
    }

    public function importExampleFile(): JsonResponse
    {
        return $this->productService->downloadExampleImportFile();
    }
}
