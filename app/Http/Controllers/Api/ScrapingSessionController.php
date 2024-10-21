<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreScrapingSessionRequest;
use App\Http\Requests\UpdateScrapingSessionRequest;
use App\Http\Resources\ScrapingSessionResource;
use App\Models\ScrapingSession;
use App\Services\Contracts\ScrapingSessionServiceInterface;
use App\Traits\JsonResponseHelper;

class ScrapingSessionController extends Controller
{
    use JsonResponseHelper;

    public function __construct(protected ScrapingSessionServiceInterface $sessionService)
    {
    }

    public function index()
    {
        if (auth()->user()->cannot('viewAll', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        $sessions = $this->sessionService->all();

        return ScrapingSessionResource::collection($sessions);
    }

    public function store(StoreScrapingSessionRequest $request)
    {
        if (auth()->user()->cannot('create', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        $session = $this->sessionService->create($request->validated());

        if (!$session) {
            return $this->errorResponse('Session for this retailer_id already created for this date', 422);
        }

        return $this->successResponse("Scraping session created", 201, ScrapingSessionResource::make($session));
    }

    public function show(string $id)
    {
        if (auth()->user()->cannot('view', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        $session = $this->sessionService->find($id);

        return $this->successResponse("Scraping session received", data: ScrapingSessionResource::make($session));
    }

    public function update(UpdateScrapingSessionRequest $request, string $id)
    {
        if (auth()->user()->cannot('update', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        $session = $this->sessionService->update($id, $request->validated());

        return $this->successResponse("Scraping session updated", data: ScrapingSessionResource::make($session));
    }

    public function destroy(string $id)
    {
        if (auth()->user()->cannot('delete', ScrapingSession::class)) {
            return $this->unauthorizedResponse();
        }

        $this->sessionService->delete($id);

        return $this->successResponse("Scraping session deleted");
    }
}
