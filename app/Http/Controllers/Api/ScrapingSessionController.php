<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScrapingSessionRequest;
use App\Http\Requests\UpdateScrapingSessionRequest;
use App\Http\Resources\ScrapingSessionResource;
use App\Models\ScrapingSession;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;

class ScrapingSessionController extends Controller
{
    use JsonResponseHelper;

    public function __construct(
        protected ScrapingSessionServiceInterface $sessionService
    )
    {
    }

    public function index(): JsonResponse
    {
        if (auth()->user()->cannot('viewAll', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'Scraping sessions list received',
            data: ScrapingSessionResource::collection($this->sessionService->all()));
    }

    public function store(StoreScrapingSessionRequest $request): JsonResponse
    {
        if (auth()->user()->cannot('create', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'Scraping session created',
            201,
            ScrapingSessionResource::make($this->sessionService->create($request->validated())));
    }

    public function show(string $id): JsonResponse
    {
        if (auth()->user()->cannot('view', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'Scraping session received',
            data: ScrapingSessionResource::make($this->sessionService->find($id)));
    }

    public function update(UpdateScrapingSessionRequest $request, string $id): JsonResponse
    {
        if (auth()->user()->cannot('update', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'Scraping session updated',
            data: ScrapingSessionResource::make($this->sessionService->update($id, $request->validated())));
    }

    public function destroy(string $id): JsonResponse
    {
        if (auth()->user()->cannot('delete', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        $this->sessionService->delete($id);

        return $this->successResponse("Scraping session deleted");
    }
}
