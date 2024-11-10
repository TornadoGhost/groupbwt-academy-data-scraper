<?php

namespace Tests\Unit;

use App\Imports\ProductsImport;
use App\Models\Product;
use App\Models\User;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\HeadingRowImport;
use Mockery;
use Tests\TestCase;

class ProductServiceTest extends TestCase
{
    protected ProductServiceInterface $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = app(ProductServiceInterface::class);
    }

    public function test_all_returns_all_products_for_admin_user()
    {
        // Імітуємо адміністратора
        $admin = $this->mock(User::class);

        $admin->shouldReceive('getAttribute')->with('isAdmin')->andReturn(true);
        Auth::shouldReceive('user')->andReturn($admin);

        // Імітуємо модель Product
        $productMock = $this->mock(Product::class);
        $productMock->shouldReceive('with')
            ->with('retailers')
            ->andReturnSelf();
        $productMock->shouldReceive('with')
            ->with('user')
            ->andReturnSelf();
        $productMock->shouldReceive('with')
            ->with('images')
            ->andReturnSelf();
        $productMock->shouldReceive('get')
            ->andReturn(new Collection(['product1', 'product2', 'product3']));

        // Викликаємо метод all()
        $result = $this->productService->all();

        $this->assertCount(3, $result);
    }

    public function test_all_returns_only_user_products_for_non_admin_user()
    {
        // Імітуємо звичайного користувача
        $user = $this->mock(User::class);
        $user->shouldReceive('getAttribute')->with('isAdmin')->andReturn(false);
        $user->shouldReceive('getAuthIdentifier')->andReturn(1);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('id')->andReturn(1);

        // Імітуємо модель Product
        $productMock = $this->mock(Product::class);
        $productMock->shouldReceive('with')
            ->with('retailers')
            ->andReturnSelf();
        $productMock->shouldReceive('where')
            ->with('user_id', 1)
            ->andReturnSelf();
        $productMock->shouldReceive('get')
            ->andReturn(new Collection(['userProduct1']));

        // Викликаємо метод all()
        $result = $this->productService->all();

        $this->assertCount(1, $result);
    }

    public function test_import_returns_error_when_headers_are_invalid()
    {
        // Імітуємо файл CSV з неправильними заголовками
        $file = UploadedFile::fake()->createWithContent('products.csv', "wrong_header,another_wrong_header\n");

        $user = Mockery::mock(User::class);

        // Мокаємо HeadingRowImport для перевірки заголовків
        $headingRowImport = Mockery::mock(HeadingRowImport::class);
        $headingRowImport->shouldReceive('toArray')->andReturn([['wrong_header', 'another_wrong_header']]);

        // Мокаємо ProductsImport для перевірки рядків
        $productImport = Mockery::mock(ProductsImport::class, [$user]);

        // Ініціюємо сервіс
        $service = $this->productService;

        // Викликаємо метод і перевіряємо результат
        $response = $service->import($file, $user);

        $this->assertEquals(422, $response->getStatusCode());
        $this->assertEquals('Wrong headings in CSV file', $response->getData()->message);
    }

    public function test_import_queues_job_on_valid_data()
    {
        Storage::fake('local');
        Queue::fake();

        // Створюємо фейковий CSV файл з коректними заголовками
        $file = UploadedFile::fake()->createWithContent('products.csv', "title,manufacturer_part_number,pack_size\n");

        // Створюємо мок користувача
        $user = Mockery::mock(User::class);
        $user->shouldReceive('getAttribute')->with('id')->andReturn(1);

        // Мокаємо HeadingRowImport для перевірки заголовків
        $headingRowImport = Mockery::mock(HeadingRowImport::class);
        $headingRowImport->shouldReceive('toArray')->andReturn([['title', 'manufacturer_part_number', 'pack_size']]);

        // Створюємо інстанс імпорту і додаємо користувача
        $productImport = new ProductsImport($user);

        // Ініціалізуємо сервіс
        $service = $this->productService;

        // Викликаємо метод import і перевіряємо, що черга була додана
        $response = $service->import($file, $user);

        // Перевіряємо, що задача була додана в чергу
        Queue::assertPushed(ProductsImport::class, function ($job) use ($user) {
            return $job->user->is($user);  // Перевіряємо, чи передано правильного користувача в job
        });

        // Перевіряємо статус код і повідомлення
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('CSV data imported successfully', $response->getData()->message);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
