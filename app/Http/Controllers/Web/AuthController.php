<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthenticateRequest;
use App\Services\Contracts\AuthServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected AuthServiceInterface $authService,
    )
    {
    }

    public function index(): View
    {
        return view('auth.login');
    }

    public function login(StoreAuthenticateRequest $request): RedirectResponse
    {
        return $this->authService->loginWeb($request);
    }

    public function logout(Request $request): RedirectResponse
    {
        return $this->authService->logoutWeb($request);
    }
}
