<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuthenticated
{
    public function handle(Request $request, Closure $next): Response
    {
        $accessToken = $_COOKIE['laravel_token'] ?? null;

        if ($accessToken) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
