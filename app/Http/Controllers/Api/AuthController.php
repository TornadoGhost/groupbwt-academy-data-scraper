<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Services\Contracts\UserServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    use JsonResponseHelper;

    public function __construct(protected UserServiceInterface $userService)
    {
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        if (Auth::guard('api')->user()) {
            return $this->errorResponse('Authenticated users cannot access the login page', 403);
        }

        if (Auth::attempt($request->validated())) {
            $user = auth()->user();
            $token = $user->createToken('main')->accessToken;
            $cookie = Cookie::make('laravel_token', $token, 500000, '/', config('url'), false, false);

            return $this->successResponse('Login successful', data: [
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
                'token' => $token
            ])->cookie($cookie);
        } else {
            return $this->errorResponse('User unauthorized', 401);
        }
    }

    public function logout(): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();
            Cookie::forget('laravel_token');
            return $this->successResponse('You have been successfully logged out!')->withCookie(Cookie::forget('laravel_token'));
        }

        return $this->errorResponse('User unauthorized', 401);
    }
}
