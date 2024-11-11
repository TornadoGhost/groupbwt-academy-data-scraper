<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use Illuminate\Contracts\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProductController extends Controller
{

    public function __construct(
        protected ProductServiceInterface  $productService,
        protected RetailerServiceInterface $retailerService
    )
    {
    }

    public function index(): View
    {
        return view('products.index');
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function show(int $id): View
    {
        $product = $this->productService->find($id);

        return view('products.show', compact('product'));
    }

    public function edit(int $id): View
    {
        $product = $this->productService->find($id);

        return view('products.edit', compact('product'));
    }

    public function getExampleCsv(): StreamedResponse
    {
        return $this->productService->downloadExampleImportFile();
    }
}
