<?php

namespace App\Providers;

use App\Models\User;
use App\Services\AuthService;
use App\Services\Contracts\AuthServiceInterface;
use App\Services\Contracts\MetricServiceInterface;
use App\Services\Contracts\NotificationServiceInterface;
use App\Services\MetricService;
use App\Services\NotificationService;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();

        // Gates
        Gate::define('isAdmin', function (User $user) {
            return $user->isAdmin;
        });
    }
}
