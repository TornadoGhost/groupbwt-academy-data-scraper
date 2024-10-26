<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\RegionServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class UserController extends Controller
{
    public function __construct(protected RegionServiceInterface $regionService, protected UserServiceInterface $userService)
    {
    }

    public function index(): View
    {
        return view('users.index');
    }

    public function create()
    {
        $regions = $this->regionService->all();

        return view('users.create', compact('regions'));
    }

    public function show(int $id)
    {
        $user = $this->userService->find($id)->toArray();
        $regions = $this->regionService->all();

        return view('users.show', compact('regions', 'user'));
    }
}
