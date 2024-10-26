<?php

use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\RetailerController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products.index');
        Route::get('/products/create', 'create')->name('products.create');
        Route::get('/products/{mpn}', 'show')->name('products.show');
    });
    Route::controller(RetailerController::class)->group(function () {
        Route::get('/retailers', 'index')->name('retailers.index');
        Route::get('/retailers/create', 'create')->name('retailers.create');
        Route::get('/retailers/{id}', 'show')->name('retailers.show');
    });
    Route::controller(UserController::class)->middleware(AuthenticateAdmin::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::get('/users/create', 'create')->name('users.create');
        Route::get('/users/{id}', 'show')->name('users.show');
    });


    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::controller(AuthController::class)->middleware('guest')->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'login')->name('login.store');
});
