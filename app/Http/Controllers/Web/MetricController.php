<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ScrapingSession;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MetricController extends Controller
{
    public function __construct(
        protected ScrapingSessionServiceInterface $scrapingSessionService,
        protected RetailerServiceInterface $retailerService,
        protected ProductServiceInterface $productService,
        protected UserServiceInterface $userService,
    )
    {
    }

    public function index(): View
    {
        $firstScrapedData = $this->scrapingSessionService->getFirstScrapingSession();
        $lastScrapedDate = $this->scrapingSessionService->getLatestScrapingSession();
        $firstDate = Carbon::parse($firstScrapedData)->format('Y-m-d');
        $lastDate = Carbon::parse($lastScrapedDate)->format('Y-m-d');
        $retailers = $this->retailerService->all();
        $products = $this->productService->all();
        $users = null;
        if (auth()->user()->isAdmin) {
            $users = $this->userService->all();
        }

        return view('metrics.index', compact('firstDate','lastDate', 'retailers', 'products', 'users'));
    }
}
