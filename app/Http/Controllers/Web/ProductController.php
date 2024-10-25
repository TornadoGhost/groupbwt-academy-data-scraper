<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\Contracts\ProductServiceInterface;
use App\Services\Contracts\RetailerServiceInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function __construct(protected ProductServiceInterface $productService, protected RetailerServiceInterface $retailerService)
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

    public function show($mpn): View
    {
        $product = $this->productService->find($mpn);
        $retailers = $this->retailerService->all();

        return view('products.show', compact('product', 'retailers'));
    }
}
