<?php

namespace App\Providers;

use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\RegionRepositoryInterface;
use App\Repositories\Contracts\RetailerRepositoryInterface;
use App\Repositories\Contracts\ScrapedDataRepositoryInterface;
use App\Repositories\Contracts\ScrapingSessionRepositoryInterface;
use App\Repositories\Contracts\SessionStatusRepositoryInterface;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Repositories\RegionRepository;
use App\Repositories\RetailerRepository;
use App\Repositories\ScrapedDataRepository;
use App\Repositories\ScrapingSessionRepository;
use App\Repositories\SessionStatusRepository;
use App\Repositories\UserRepository;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RegionServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Services\Contracts\SessionStatusServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use App\Services\ProductService;
use App\Services\RegionService;
use App\Services\RetailerService;
use App\Services\ScrapedDataService;
use App\Services\ScrapingSessionService;
use App\Services\SessionStatusService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->singleton(ProductServiceInterface::class, ProductService::class);

        $this->app->singleton(UserRepositoryInterface::class, UserRepository::class);
        $this->app->singleton(UserServiceInterface::class, UserService::class);

        $this->app->singleton(RetailerRepositoryInterface::class, RetailerRepository::class);
        $this->app->singleton(RetailerServiceInterface::class, RetailerService::class);

        $this->app->singleton(RegionRepositoryInterface::class, RegionRepository::class);
        $this->app->singleton(RegionServiceInterface::class, RegionService::class);

        $this->app->singleton(ScrapingSessionRepositoryInterface::class, ScrapingSessionRepository::class);
        $this->app->singleton(ScrapingSessionServiceInterface::class, ScrapingSessionService::class);

        $this->app->singleton(ScrapedDataRepositoryInterface::class, ScrapedDataRepository::class);
        $this->app->singleton(ScrapedDataServiceInterface::class, ScrapedDataService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
