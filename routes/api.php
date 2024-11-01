<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ImageProductController;
use App\Http\Controllers\Api\MetricController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\RegionController;
use App\Http\Controllers\Api\RetailerAccessController;
use App\Http\Controllers\Api\RetailerController;
use App\Http\Controllers\Api\ScrapedDataController;
use App\Http\Controllers\Api\ScrapingSessionController;
use App\Http\Controllers\Api\SessionStatusController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:api')->group(function () {
    Route::apiResources([
        'users' => UserController::class,
        'products' => ProductController::class,
        'retailers' => RetailerController::class,
        'regions' => RegionController::class,
        'scraping-sessions' => ScrapingSessionController::class,
        'scraped-data' => ScrapedDataController::class,
    ]);

    Route::controller(RetailerController::class)->group(function () {
        Route::patch('/retailers/{id}/restore', 'restore');
        Route::get('retailers/{retailer_id}/users', 'getWithUsers');
    });

    Route::controller(RetailerAccessController::class)->group(function () {
        Route::patch('/retailers/{retailer_id}/grand-access', 'grandAccess');
        Route::patch('/retailers/{retailer_id}/revoke-access', 'revokeAccess');
    });


    Route::controller(MetricController::class)->group(function () {
        Route::get('metrics', 'index');
        Route::get('metrics/products', 'getProducts');
        Route::get('metrics/retailers', 'getRetailers');
    });

    Route::controller(ImageProductController::class)->group(function () {
        Route::post('/images', 'store');
        Route::delete('/images/{id}', 'destroy');
    });

    Route::post('products/import', [ProductController::class, 'import']);

    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('login', [AuthController::class, 'login']);
