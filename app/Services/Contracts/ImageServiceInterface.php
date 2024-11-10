<?php

namespace App\Services\Contracts;

use App\Http\Requests\ImageProductRequest;

interface ImageServiceInterface
{
    public function saveImage(string $path): false|string;
    public function deleteImageByPath(string $path): bool;
    public function deleteImageById(int $id): null|string;
    public function store(ImageProductRequest $request): array;
}
