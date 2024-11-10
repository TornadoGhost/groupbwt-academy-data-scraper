<?php

namespace App\Services\Contracts;

use Illuminate\Http\JsonResponse;

interface AuthServiceInterface
{
    public function login($request): JsonResponse;
    public function logout(): JsonResponse;
}