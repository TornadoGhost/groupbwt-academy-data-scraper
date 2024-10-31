<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    use JsonResponseHelper;
    public function __construct(protected ProductServiceInterface $productService)
    {
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->all();

        return $this->successResponse('Products list received', data: ProductResource::collection($products));
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
        $product = $this->productService->find($mpn);

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
}
