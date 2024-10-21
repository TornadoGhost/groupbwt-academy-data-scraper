<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Services\Contracts\UserServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\TokenRepository;

class AuthController extends Controller
{
    use JsonResponseHelper;

    public function __construct(protected UserServiceInterface $userService)
    {
    }

    public function login(LoginUserRequest $request)
    {

        if (Auth::guard('api')->user()) {
            return $this->errorResponse('Authenticated users cannot access the login page', 403);
        }

        $user = $this->userService->findByEmail($request->email);

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->errorResponse('User unauthorized', 401);
        }

        $token = $user->createToken('main')->accessToken;

        return $this->successResponse('Login successful', data: [
            'id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'accessToken' => $token
        ]);
    }

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            $user->token()->revoke();

            return $this->successResponse('You have been successfully logged out!');
        }

        return $this->errorResponse('User unauthorized', 401);
    }
}
