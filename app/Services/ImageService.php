<?php

namespace App\Services;

use App\Http\Requests\ImageProductRequest;
use App\Repositories\Contracts\ImageProductRepositoryInterface;
use App\Services\Contracts\ImageServiceInterface;
use Illuminate\Http\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImageService implements ImageServiceInterface
{
    const IMAGE_PATH = "images/";

    public function __construct(
        protected ImageProductRepositoryInterface $imageProductRepository,
    )
    {
    }

    public function saveImage(string $path): false|string
    {
        $imagePath = self::IMAGE_PATH . auth()->id();

        return Storage::disk('public')->putFile($imagePath, new File($path), 'public');
    }

    public function deleteImageByPath(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }

    public function deleteImageById(int $id): null|string
    {
        return DB::transaction(function () use ($id) {
            $image = $this->imageProductRepository->findById($id);
            $this->deleteImageByPath($image->path);
            $this->imageProductRepository->delete($id);
        });
    }

    public function store(ImageProductRequest $request): array
    {
        $product = $this->productService->find($request->validated('product_id'));
        $images = [];
        foreach ($request->validated('images') as $image) {
            $path = $this->imageService->saveImage($image);
            $images[] = $product->images()->create(['path' => $path]);
        }

        return $images;
    }
}
