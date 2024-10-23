<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cookie;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login');
    }
}
