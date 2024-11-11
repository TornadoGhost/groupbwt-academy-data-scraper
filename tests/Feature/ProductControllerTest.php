<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Retailer;
use App\Models\User;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_index_method_returns_correct_json()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $productService = \Mockery::mock(ProductServiceInterface::class);
        $productService->shouldReceive('allLatest')->once()->andReturn(collect([$product]));

        $this->app->instance(ProductServiceInterface::class, $productService);

        Passport::actingAs($user);

        $response = $this->getJson('/api/products');

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'manufacturer_part_number',
                        'pack_size',
                        'created_at',
                        'updated_at',
                        'retailers',
                        'images'
                    ]
                ]
            ]);;
    }

    public function test_store_method_creates_product_and_returns_correct_json()
    {
        $user = User::factory()->create();

        $retailer1 = Retailer::factory()->create();
        $retailer2 = Retailer::factory()->create();

        $productData = [
            'title' => 'Test Product',
            'manufacturer_part_number' => '12345',
            'pack_size' => 'each',
            'retailers' => [
                ['retailer_id' => $retailer1->id],
                ['retailer_id' => $retailer2->id],
            ]
        ];

        $productService = \Mockery::mock(ProductServiceInterface::class);
        $productService->shouldReceive('create')->once()->andReturn(new Product($productData));

        $this->app->instance(ProductServiceInterface::class, $productService);

        Passport::actingAs($user);

        $response = $this->postJson('/api/products', $productData);

        $response->assertCreated()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'title',
                    'manufacturer_part_number',
                    'pack_size',
                    'created_at',
                    'updated_at',
                    'retailers',
                    'images'
                ]
            ]);
    }

    public function test_show_method_returns_correct_json()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);


        $productService = \Mockery::mock(ProductServiceInterface::class);
        $productService->shouldReceive('find')->atLeast()->once()->with($product->id)->andReturn($product);

        $this->app->instance(ProductServiceInterface::class, $productService);

        Passport::actingAs($user);

        $response = $this->getJson('/api/products/' . $product->id);

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'title',
                    'manufacturer_part_number',
                    'pack_size',
                    'created_at',
                    'updated_at',
                    'retailers',
                    'images'
                ]
            ]);
    }

    public function test_update_method_updates_product_and_returns_correct_json()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $productData = [
            'title' => 'Updated Product',
            'manufacturer_part_number' => $product->manufacturer_part_number,
            'pack_size' => $product->pack_size,
        ];

        $productService = \Mockery::mock(ProductServiceInterface::class);

        $productService->shouldReceive('find')->once()->with($product->id)->andReturn($product);

        $updatedProduct = (object)array_merge($product->toArray(), $productData, [
            'retailers' => $product->retailers,
            'images' => $product->images,
        ]);

        $productService->shouldReceive('update')->once()->with($product->id, $productData)->andReturn($updatedProduct);

        $this->app->instance(ProductServiceInterface::class, $productService);

        Passport::actingAs($user);

        $response = $this->patchJson('/api/products/' . $product->id, $productData);

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'title',
                    'manufacturer_part_number',
                    'pack_size',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }

    public function test_destroy_method_deletes_product_and_returns_correct_json()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['user_id' => $user->id]);

        $productService = \Mockery::mock(ProductServiceInterface::class);
        $productService->shouldReceive('find')->once()->with($product->id)->andReturn($product);
        $productService->shouldReceive('delete')->once()->with($product->id)->andReturn(response()->json(['message'=>'CSV data imported successfully']), 201);

        $this->app->instance(ProductServiceInterface::class, $productService);

        Passport::actingAs($user);

        $response = $this->deleteJson('/api/products/' . $product->id);

        $response->assertOk()
            ->assertJsonStructure([
                'status',
                'message'
            ]);
    }

    // TODO not working, can't see example file
    public function test_import_method_returns_correct_json()
    {
        $user = User::factory()->create();

        $csvData = "title,manufacturer_part_number,pack_size\ntest_title_1,asd-qwe-asd,each\ntest_title_2,asd-qwe-asd1,each";

        $filePath = storage_path('app/private/excel/example.csv');
        file_put_contents($filePath, $csvData);

        // Створення об'єкта UploadedFile
        $file = new UploadedFile($filePath, 'example.csv', 'text/csv', null, true);

        // Мокаємо ProductServiceInterface
        $productService = \Mockery::mock(ProductServiceInterface::class);
        $productService->shouldReceive('import')->once()->andReturn(
            response()->json([
                'status' => 'Success',
                'message' => 'CSV data imported successfully'
            ], 201)
        );

        $this->app->instance(ProductServiceInterface::class, $productService);

        // Аутентифікація користувача
        Passport::actingAs($user);

        // Виконання POST запиту
        $response = $this->postJson('/api/import/products', ['csv_file' => $file]);

        // Перевірка відповіді
        $response->assertStatus(201)
            ->assertJson([
                'status' => 'Success',
                'message' => 'CSV data imported successfully',
            ]);

        // Видалення тимчасового файлу
        unlink($filePath);
    }

    public function test_export_method_returns_correct_json()
    {
        $user = User::factory()->create();

        $productService = \Mockery::mock(ProductServiceInterface::class);
        $productService->shouldReceive('exportExcel')->once()->andReturn(response()->json(
            [
                'status' => 'Success',
                'message' => 'Products exportation started'
            ]
        ));

        $this->app->instance(ProductServiceInterface::class, $productService);

        Passport::actingAs($user);

        $response = $this->getJson('/api/export/products');

        $response->assertOk()
            ->assertJson([
                'status' => 'Success',
                'message' => 'Products exportation started',
            ]);
    }
}
