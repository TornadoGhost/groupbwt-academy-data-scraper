<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateRetailerAccessRequest;
use App\Http\Resources\RetailerResource;
use App\Models\Retailer;
use App\Services\Contracts\RetailerServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RetailerAccessController extends Controller
{
    use JsonResponseHelper;

    public function __construct(protected RetailerServiceInterface $retailerService)
    {
    }

    public function grandAccess(UpdateRetailerAccessRequest $request, int $retailer_id): JsonResponse
    {
        if (auth()->user()->cannot('grandAccess', Retailer::class)) {
            return $this->unauthorizedResponse();
        }

        $this->retailerService->grandAccess($retailer_id, $request['users_id']);

        return $this->successResponse("Access Granted");
    }

    public function revokeAccess(UpdateRetailerAccessRequest $request, int $retailer_id): JsonResponse
    {
        if (auth()->user()->cannot('revokeAccess', Retailer::class)) {
            return $this->unauthorizedResponse();
        }

        $this->retailerService->revokeAccess($retailer_id, $request['users_id']);

        return $this->successResponse("Access Revoked");
    }
}
