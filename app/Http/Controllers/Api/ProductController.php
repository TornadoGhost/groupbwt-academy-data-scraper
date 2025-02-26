<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductImportRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    use JsonResponseHelper;

    public function __construct
    (
        protected ProductServiceInterface     $productService,
        protected ExportTableServiceInterface $exportTableService,
    )
    {
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        return ProductResource::collection(
            $this->productService->allPaginate(auth()->user()->isAdmin, $request->all())
        );
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        return $this->successResponse(
            'Product created',
            201,
            ProductResource::make($this->productService->create($request->validated()))
        );
    }

    public function show(int $id): JsonResponse
    {
        if (auth()->user()->cannot('view', $this->productService->find($id))) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            "Product received",
            data: ProductResource::make($this->productService->find($id))
        );
    }

    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        if (auth()->user()->cannot('update', $this->productService->find($id))) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            "Product updated",
            data: ProductResource::make($this->productService->update($id, $request->validated())));
    }

    public function destroy(int $id): JsonResponse
    {
        if (auth()->user()->cannot('delete', $this->productService->find($id))) {
            return $this->unauthorizedResponse();
        }

        $this->productService->delete($id);

        return $this->successResponse('Product deleted');
    }

    public function import(ProductImportRequest $request): JsonResponse
    {
        return $this->productService->import($request->validated('csv_file'), $request->user());
    }

    public function export(): JsonResponse
    {
        return $this->productService->exportExcel(request()->user());
    }
}
