<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExportScrapedDataByRetailerRequest;
use App\Http\Requests\StoreScrapedDataRequest;
use App\Http\Requests\UpdateScrapedDataRequest;
use App\Http\Resources\ScrapedDataResource;
use App\Models\ScrapedData;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;

class ScrapedDataController extends Controller
{
    use JsonResponseHelper;

    public function __construct(
        protected ScrapedDataServiceInterface $scrapedDataService,
        protected ExportTableServiceInterface $exportTableService,
        protected RetailerServiceInterface $retailerService,
    )
    {
    }

    public function index(): JsonResponse
    {
        //TODO rework with paginate() instead of get(), because with get() method you can't get response with this amount of data
        $scrapedData = $this->scrapedDataService->all();

        return $this->successResponse('Scraped data list received', data: ScrapedDataResource::collection($scrapedData));
    }

    public function store(StoreScrapedDataRequest $request): JsonResponse
    {
        if (auth()->user()->cannot('create', ScrapedData::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'Scraped data created',
            201,
            ScrapedDataResource::make($this->scrapedDataService->create($request->validated())));
    }

    public function show(int $id): JsonResponse
    {
        if (auth()->user()->cannot('view', $this->scrapedDataService->find($id))) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'Scraped data received',
            data: ScrapedDataResource::make($this->scrapedDataService->find($id))
        );
    }

    public function update(UpdateScrapedDataRequest $request, int $id): JsonResponse
    {
        if (auth()->user()->cannot('update', ScrapedData::class)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse(
            'Scraped data updated',
            data: ScrapedDataResource::make($this->scrapedDataService->update($id, $request->validated()))
        );
    }

    public function destroy(int $id): JsonResponse
    {
        if (auth()->user()->cannot('delete', ScrapedData::class)) {
            return $this->unauthorizedResponse();
        }

        $this->scrapedDataService->delete($id);

        return $this->successResponse('Scraped data deleted');
    }

    public function exportByRetailer(ExportScrapedDataByRetailerRequest $request): JsonResponse
    {
        return $this->scrapedDataService->exportByRetailer(
            $request->validated('retailer_id'),
            $request->validated('date'),
            request()->user(),
            array_merge($request->validated(), ['current_day' => now()]),
        );
    }
}
