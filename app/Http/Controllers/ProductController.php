<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\CreateProductRequest;
use App\Models\Product;
use App\Services\ProductService;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(CreateProductRequest $request, ProductService $productService)
    {
        return $productService->add($request->validated());
    }

    public function update($product_id, CreateProductRequest $request, ProductService $productService)
    {
        return $productService->update($product_id, $request);
    }

    public function destroy($product_id, ProductService $productService)
    {
        return $productService->remove($product_id);
    }
    public function index(ProductService $productService)
    {
        return $productService->getAll();
    }

    public function show($product_id, ProductService $productService)
    {
        return $productService->getByID($product_id);
    }
}
