<?php

namespace App\Http\Controllers\Api;

use App\Exports\ScrapedDataByRetailerExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\ExportScrapedDataByRetailerRequest;
use App\Http\Requests\StoreScrapedDataRequest;
use App\Http\Requests\UpdateScrapedDataRequest;
use App\Http\Resources\ScrapedDataResource;
use App\Jobs\NotifyUserOfCompletedExport;
use App\Jobs\SaveExportTableData;
use App\Models\ScrapedData;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use App\Services\Contracts\ScrapedDataServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

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
        if (auth()->user()->cannot('viewAll', ScrapedData::class)) {
            return $this->unauthorizedResponse();
        }

        //TODO rework with paginate() instead of get(), because with get() method you can't get response with this amount of data
        $scrapedData = $this->scrapedDataService->all();

        return $this->successResponse('Scraped data list received', data: ScrapedDataResource::collection($scrapedData));
    }

    public function store(StoreScrapedDataRequest $request): JsonResponse
    {
        if (auth()->user()->cannot('create', ScrapedData::class)) {
            return $this->unauthorizedResponse();
        }

        $scrapedData = $this->scrapedDataService->create($request->validated());

        return $this->successResponse('Scraped data created', 201, ScrapedDataResource::make($scrapedData));
    }

    public function show(int $id): JsonResponse
    {
        $scrapedData = $this->scrapedDataService->find($id);

        if (auth()->user()->cannot('view', $scrapedData)) {
            return $this->unauthorizedResponse();
        }

        return $this->successResponse("Scraped data received", data: ScrapedDataResource::make($scrapedData));
    }

    public function update(UpdateScrapedDataRequest $request, int $id): JsonResponse
    {
        if (auth()->user()->cannot('update', ScrapedData::class)) {
            return $this->unauthorizedResponse();
        }

        $scrapedData = $this->scrapedDataService->update($id, $request->validated());

        return $this->successResponse("Scraped data updated", data: ScrapedDataResource::make($scrapedData));
    }

    public function destroy(int $id): JsonResponse
    {
        if (auth()->user()->cannot('delete', ScrapedData::class)) {
            return $this->unauthorizedResponse();
        }

        $this->scrapedDataService->delete($id);

        return $this->successResponse("Scraped data deleted");
    }

    public function exportByRetailer(ExportScrapedDataByRetailerRequest $request): JsonResponse
    {
        $fileData = $this->exportTableService->setPath("scraped_data_retailer{$request->validated('retailer_id')}");

        (new ScrapedDataByRetailerExport(
            $request->validated('retailer_id'),
            $request->validated('date'),
            $this->scrapedDataService,
        ))
            ->store($fileData['filePath'])->chain([
                new NotifyUserOfCompletedExport(request()->user(), "Scraped Data"),
                new SaveExportTableData(
                    $fileData['fileName'],
                    $fileData['filePath'],
                    request()->user(),
                    $this->exportTableService
                ),
            ]);

        return $this->successResponse('Products exportation started');
    }
}
