<?php

namespace App\Services\Contracts;

interface ImageServiceInterface
{
    public function saveImage(string $path);
    public function deleteImageByPath(string $path);
    public function deleteImageById(int $id);
}
