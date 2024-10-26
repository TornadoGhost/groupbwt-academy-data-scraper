<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\UserServiceInterface;
use Illuminate\Http\Request;

class RetailerController extends Controller
{
    public function __construct(protected RetailerServiceInterface $retailerService, protected UserServiceInterface $userService)
    {
    }

    public function index()
    {
        $users = $this->userService->all();
        $preparedUsers = $this->userService->prepareUsers($users);

        return view('retailers.index', compact('users', 'preparedUsers'));
    }

    public function create()
    {
        return view('retailers.create');
    }

    public function show(int $id)
    {
        $retailer = $this->retailerService->find($id);

        return view('retailers.show', compact('retailer'));
    }
}
