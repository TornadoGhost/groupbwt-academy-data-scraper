<?php

namespace Tests\Feature;

use App\Exports\ProductsExport;
use App\Models\Product;
use App\Models\User;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\ProductRepository;
use App\Services\Contracts\ExportTableServiceInterface;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\JsonResponseHelper;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\Passport;
use Mockery;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions, JsonResponseHelper;

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

    public
    function testExportExcelProduct()
    {
        $user = User::factory()->create();
        Passport::actingAs($user);

        $this->productService = Mockery::mock(ProductServiceInterface::class)
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $this->productService->shouldReceive('exportExcel')
            ->once()
            ->with($user)
            ->andReturn($this->successResponse('Products exportation started'));

        $result = $this->productService->exportExcel($user);

        $response = $this->successResponse('Products exportation started');
        $this->assertEquals($response, $result);
    }


    public function testProductsForMetrics()
    {
        $userId = 1;
        $productCollection = new Collection([new Product(), new Product()]);
        $this->productRepository->shouldReceive('productsForMetrics')->with($userId)->andReturn($productCollection);
        $products = $this->productService->productsForMetrics($userId);
        $this->assertInstanceOf(Collection::class, $products);
        $this->assertCount(2, $products);
    }

    public function testProductsForMetricsWhenNoProducts()
    {
        $userId = 3;
        $this->productRepository->shouldReceive('productsForMetrics')->with($userId)->andReturn();
        $result = $this->productService->productsForMetrics($userId);
        $this->assertInstanceOf(Collection::class, $result);
    }

    protected function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
