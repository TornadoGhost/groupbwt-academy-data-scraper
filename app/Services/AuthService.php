<?php

namespace App\Services;

use App\Http\Requests\StoreAuthenticateRequest;
use App\Services\Contracts\AuthServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthService implements AuthServiceInterface
{
    use JsonResponseHelper;

    public function loginApi($request): JsonResponse
    {
        if (Auth::guard('api')->user()) {
            return $this->errorResponse('Authenticated users cannot access the login page', 403);
        }

        if (Auth::attempt($request)) {
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

    public function logoutApi(): JsonResponse
    {
        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();
            Cookie::forget('laravel_token');
            return $this->successResponse('You have been successfully logged out!')->withCookie(Cookie::forget('laravel_token'));
        }

        return $this->errorResponse('User unauthorized', 401);
    }

    public function loginWeb(StoreAuthenticateRequest $request): RedirectResponse
    {
        if (Auth::attempt($request->validated())) {
            $request->session()->regenerate();
            
            return redirect()->route('home');
        } else {
            return redirect()->back()->withErrors(['errorLogin' => 'Wrong email or password'])->withInput();
        }
    }

    public function logoutWeb(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();

        return redirect()->route('login');
    }
}