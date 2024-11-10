<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
class RetailerController extends Controller
{
    public function __construct(
        protected RetailerServiceInterface $retailerService,
        protected UserServiceInterface $userService,
        protected ScrapingSessionServiceInterface $scrapingSessionService,
    )
    {
    }

    public function index(): View
    {
        return view('retailers.index', $this->retailerService->prepareDataForIndexView());
    }

    public function create(): View
    {
        return view('retailers.create');
    }

    public function show(int $id): View
    {
        $retailer = $this->retailerService->find($id);

        return view('retailers.show', compact('retailer'));
    }
}
