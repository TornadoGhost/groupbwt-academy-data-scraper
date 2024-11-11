<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    protected $productService;
    protected $productRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->productRepository = Mockery::mock(ProductRepository::class);
        $this->app->instance(ProductRepositoryInterface::class, $this->productRepository);
        $this->productService = app(ProductServiceInterface::class);
    }

    public function testDownloadExampleImportFile()
    {
        $mockFile = Mockery::mock(StreamedResponse::class);
        Storage::shouldReceive('download')->once()
            ->with('/excel/import/example.csv', 'import_products_example.csv')
            ->andReturn($mockFile);

        $result = $this->productService->downloadExampleImportFile();

        $this->assertEquals($mockFile, $result);
    }

    public function testDownloadExampleImportFileWillDownloadTheFile()
    {
        $expectedFileName = 'import_products_example.csv';
        $expectedFilePath = '/excel/import/example.csv';

        Storage::shouldReceive('download')
            ->once()
            ->with($expectedFilePath, $expectedFileName)
            ->andReturn(new StreamedResponse());

        $result = $this->productService->downloadExampleImportFile();

        $this->assertInstanceOf(StreamedResponse::class, $result);
    }

    public function testProductsForMetricsWhenNoProducts()
    {
        $userId = 3;
        $this->productRepository->shouldReceive('productsForMetrics')->with($userId)->andReturn();
        $result = $this->productService->productsForMetrics($userId);
        $this->assertInstanceOf(Collection::class, $result);
    }

    public function testAllLatestWhenUserHasProducts()
    {
        $user = User::factory()->create();
        $products = Product::factory()->count(3)->make(); // Create a collection of 3 products

        $this->productRepository->shouldReceive('allLatest')->with($user)->andReturn($products);

        $result = $this->productService->allLatest($user);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);  // Ensure the collection has 3 products
    }

    public function testAllLatestWhenUserDoesNotHaveProducts()
    {
        $user = new User();
        $this->productRepository->shouldReceive('allLatest')->with($user)->andReturn(new Collection());
        $result = $this->productService->allLatest($user);
        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals(0, $result->count());
    }

    public function testNameByIdWhenIdExists()
    {
        $productId = rand(2,50);
        $productName = 'Test Product';
        $this->productRepository->shouldReceive('getNameById')->with($productId)->andReturn($productName);
        $result = $this->productService->getNameById($productId);
        $this->assertEquals($productName, $result);
    }

    public function testNameByIdWhenIdDoesNotExist()
    {
        $productId = 0;
        $this->productRepository->shouldReceive('getNameById')->with($productId)->andReturnNull();
        $result = $this->productService->getNameById($productId);
        $this->assertNull($result);
    }


    /*public function testExportExcelWhenUserHasNoProducts()
    {
        $user = User::factory()->create();

        // Mock ProductRepository
        $productRepositoryMock = \Mockery::mock(ProductRepository::class);
        $productRepositoryMock->shouldReceive('all')->andReturn(collect());

        // Bind the mock to the service container
        $this->app->instance(ProductRepository::class, $productRepositoryMock);

        // Mock Excel store method
        $excelMock = \Mockery::mock('alias:' . Excel::class);
        $excelMock->shouldReceive('store')
            ->once()
            ->with(\Mockery::type(ProductsExport::class), 'exports/products_' . $user->id . '.xlsx', 'local')
            ->andReturn(true);

        // Fake the queue and local storage
        Queue::fake();
        Storage::fake('local');

        // Call the service method
        $response = $this->productService->exportExcel($user);

        // Convert the JsonResponse to a TestResponse for further assertions
        $testResponse = TestResponse::fromBaseResponse($response);

        // Assertions on the response
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $testResponse->assertJson(['message' => 'Products exportation started']);

        // Validate the queue jobs were pushed
        Queue::assertPushed(NotifyUserOfCompletedExport::class, function ($job) use ($user) {
            return $job->user->is($user);
        });

        Queue::assertPushed(SaveExportTableData::class, function ($job) use ($user) {
            return $job->filename === 'exports/products_' . $user->id . '.xlsx';
        });

        // Manually check storage
        $filepath = 'exports/products_'.$user->id.'.xlsx';
        Storage::disk('local')->assertExists($filepath);
    }*/

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
