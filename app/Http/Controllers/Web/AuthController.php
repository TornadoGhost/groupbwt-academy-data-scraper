<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAuthenticateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function index(): View
    {
        return view('auth.login');
    }

    public function login(StoreAuthenticateRequest $request)
    {
        if (Auth::attempt($request->validated())) {
            $request->session()->regenerate();
            $token = Auth::user()->createToken('main')->accessToken;
            $cookie = Cookie::make('laravel_token', $token, config('session.lifetime'), '/', config('url'), false, false);

            return redirect()->route('home')->cookie($cookie);
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::user()->tokens()->delete();
        Auth::logout();
        $request->session()->invalidate();
        Cookie::forget('laravel_token');

        return redirect()->route('login');
    }
}
