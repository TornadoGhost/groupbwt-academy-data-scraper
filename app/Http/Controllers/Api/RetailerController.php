<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRetailerRequest;
use App\Http\Requests\UpdateRetailerRequest;
use App\Http\Resources\RetailerResource;
use App\Models\Retailer;
use App\Models\User;
use App\Services\Contracts\RetailerServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RetailerController extends Controller
{
    use JsonResponseHelper;
    public function __construct(protected RetailerServiceInterface $retailerService)
    {
    }

    public function index(): AnonymousResourceCollection
    {
        $retailers = $this->retailerService->all();

        return RetailerResource::collection($retailers);
    }

    public function store(StoreRetailerRequest $request): JsonResponse
    {
        if (auth()->user()->cannot('create', Retailer::class)) {
            return $this->unauthorizedResponse();
        }

        $retailer = $this->retailerService->create($request->validated());

        return $this->successResponse('Retailer created', 201, RetailerResource::make($retailer));
    }

    public function show(string $id): JsonResponse
    {
        $retailer = $this->retailerService->find($id);

        if (auth()->user()->cannot('view', $retailer)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse("Retailer received", data: RetailerResource::make($retailer));
    }

    public function update(UpdateRetailerRequest $request, string $id): JsonResponse
    {
        if (auth()->user()->cannot('update', Retailer::class)) {
            return $this->unauthorizedResponse();
        }

        $retailer = $this->retailerService->update($id, $request->validated());

        return $this->successResponse("Retailer updated", data: RetailerResource::make($retailer));
    }

    public function destroy(string $id): JsonResponse
    {
        if (auth()->user()->cannot('delete', Retailer::class)) {
            return $this->unauthorizedResponse();
        }

        $this->retailerService->delete($id);

        return $this->successResponse("Retailer deleted");
    }

    public function restore(int $id): JsonResponse
    {
        if (auth()->user()->cannot('restore', Retailer::class)) {
            return $this->unauthorizedResponse();
        }

        $this->retailerService->restore($id);

        return $this->successResponse("Retailer restored");
    }
}
