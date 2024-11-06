<?php

use App\Http\Controllers\Web\ExportTableController;
use App\Http\Controllers\Web\HomeController;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\MetricController;
use App\Http\Controllers\Web\NotificationController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\RegionController;
use App\Http\Controllers\Web\RetailerController;
use App\Http\Controllers\Web\UserController;
use App\Http\Middleware\AuthenticateAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
//    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::redirect('/', '/products')->name('home');

    Route::controller(ProductController::class)->group(function () {
        Route::get('/products', 'index')->name('products.index');
        Route::get('/products/create', 'create')->name('products.create');
        Route::get('/products/{id}', 'show')->name('products.show');
        Route::get('/products/{id}/edit', 'edit')->name('products.show');
        Route::get('/products/import/example-csv', 'getExampleCsv')->name('products.exampleCsv');
    });
    Route::controller(RetailerController::class)->group(function () {
        Route::get('/retailers', 'index')->name('retailers.index');
        Route::get('/retailers/create', 'create')->name('retailers.create')->middleware(AuthenticateAdmin::class);
        Route::get('/retailers/{id}', 'show')->name('retailers.show');
    });
    Route::controller(UserController::class)->middleware(AuthenticateAdmin::class)->group(function () {
        Route::get('/users', 'index')->name('users.index');
        Route::get('/users/create', 'create')->name('users.create');
        Route::get('/users/{id}', 'show')->name('users.show');
    });
    Route::controller(RegionController::class)->group(function () {
        Route::get('/regions', 'index')->name('regions.index');
        Route::get('/regions/create', 'create')->name('regions.create');
        Route::get('/regions/{id}', 'show')->name('regions.show');
    });
    Route::get('/metrics', [MetricController::class, 'index'])->name('metrics.index');

    Route::controller(ExportTableController::class)->group(function () {
        Route::get('/export-tables', 'index')->name('exportTables.index');
        Route::get('/export-tables/download', 'download')->name('exportTables.download');
    });

    Route::get('/notifications/get', [NotificationController::class, 'get'])->name('notifications.get');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::controller(AuthController::class)->middleware('guest')->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'login')->name('login.store');
});
