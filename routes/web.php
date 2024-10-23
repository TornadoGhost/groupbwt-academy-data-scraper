<?php

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\LoginController;
use App\Http\Middleware\CheckAuthenticated;
use App\Http\Middleware\CheckLogged;
use Illuminate\Support\Facades\Route;

Route::middleware([CheckAuthenticated::class])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
});

Route::get('/login', [LoginController::class, 'index'])->middleware(CheckLogged::class)->name('login');
