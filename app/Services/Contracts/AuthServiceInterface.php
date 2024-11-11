<?php

namespace App\Services\Contracts;

use App\Http\Requests\StoreAuthenticateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    public function loginApi($request): JsonResponse;
    public function logoutApi(): JsonResponse;
    public function loginWeb(StoreAuthenticateRequest $request): RedirectResponse;
    public function logoutWeb(Request $request): RedirectResponse;
}