<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImageProductRequest;
use App\Models\Product;
use App\Services\Contracts\ImageServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageProductController extends Controller
{
    use JsonResponseHelper;
    public function __construct(
        protected ImageServiceInterface   $imageService,
        protected ProductServiceInterface $productService,
    )
    {
    }

    public function store(ImageProductRequest $request): JsonResponse
    {
        $product = $this->productService->findById($request->validated('product_id'));
        $images = [];
        foreach ($request->validated('images') as $image) {
            $path = $this->imageService->saveImage($image);
            $images[] = $product->images()->create(['path' => $path]);
        }

        return $this->successResponse('Image saved', 201, $images);
    }

    public function destroy(string $id): JsonResponse
    {
        $this->imageService->deleteImageById($id);

        return $this->successResponse('Image deleted');
    }
}
