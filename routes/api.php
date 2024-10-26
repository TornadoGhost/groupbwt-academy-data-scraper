<?php

use App\Http\Controllers\Api\AuthController;
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

    Route::patch('/retailers/{id}/restore', [RetailerController::class, 'restore']);

    Route::controller(RetailerAccessController::class)->group(function() {
        Route::patch('/retailers/{retailer_id}/grand-access', 'grandAccess');
        Route::patch('/retailers/{retailer_id}/revoke-access', 'revokeAccess');
    });

    Route::get('retailers/{retailer_id}/users', [RetailerController::class, 'getWithUsers']);
    Route::get('metrics', MetricController::class);
    Route::post('logout', [AuthController::class, 'logout']);
});

Route::post('login', [AuthController::class, 'login']);
