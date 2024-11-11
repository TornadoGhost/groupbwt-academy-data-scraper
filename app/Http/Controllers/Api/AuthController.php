<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    use JsonResponseHelper;

    public function __construct(
        protected UserServiceInterface $userService,
        protected AuthServiceInterface $authService,
    )
    {
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        return $this->authService->loginApi($request->validated());
    }

    public function logout(): JsonResponse
    {
        return $this->authService->logoutApi();
    }
}
