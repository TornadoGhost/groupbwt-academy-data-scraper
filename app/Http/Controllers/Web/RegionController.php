<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\RegionServiceInterface;
use Illuminate\Contracts\View\View;

class RegionController extends Controller
{
    public function __construct(
        protected RegionServiceInterface $regionService
    )
    {
    }

    public function index(): View
    {
        return view('regions.index');
    }

    public function create(): View
    {
        return view('regions.create');
    }

    public function show(int $id): View
    {
        $region = $this->regionService->find($id);
        return view('regions.show', compact('region'));
    }
}
