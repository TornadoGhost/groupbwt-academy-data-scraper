<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ScrapingSession;
use App\Services\Contracts\MetricServiceInterface;
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
        protected MetricServiceInterface $metricService,
    )
    {
    }

    public function index(): View
    {
        return view('metrics.index', $this->metricService->prepareDataForIndexPage(auth()->user()));
    }
}
