<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegionRequest;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use App\Services\Contracts\RegionServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class RegionController extends Controller
{
    use JsonResponseHelper;

    public function __construct(protected RegionServiceInterface $regionService)
    {
    }

    public function index(): JsonResponse|AnonymousResourceCollection
    {
        if (auth()->user()->cannot('viewAll', Region::class)) {
            return $this->unauthorizedResponse();
        }

        $regions = $this->regionService->all();

        return RegionResource::collection($regions);
    }

    public function store(RegionRequest $request): JsonResponse
    {
        if (auth()->user()->cannot('create', Region::class)) {
            return $this->unauthorizedResponse();
        }

        $region = $this->regionService->create($request->validated());

        return $this->successResponse("Region created", 201, RegionResource::make($region));
    }

    public function show(int $id): JsonResponse
    {
        $region = $this->regionService->find($id);

        if (auth()->user()->cannot('view', $region)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse("Region received", data: RegionResource::make($region));
    }

    public function update(RegionRequest $request, string $id): JsonResponse
    {
        if (auth()->user()->cannot('update', Region::class)) {
            return $this->unauthorizedResponse();
        }

        $region = $this->regionService->update($id, $request->validated());

        return $this->successResponse("Region updated", data: RegionResource::make($region));
    }

    public function destroy(string $id): JsonResponse
    {
        if (auth()->user()->cannot('delete', Region::class)) {
            return $this->unauthorizedResponse();
        }

        $this->regionService->delete($id);

        return $this->successResponse("Region deleted");
    }
}
