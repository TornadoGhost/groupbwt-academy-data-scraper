<?php

namespace App\Repositories\Contracts;

interface ImageProductRepositoryInterface
{
    public function findById(int $id);
    public function delete(int $id);
}
