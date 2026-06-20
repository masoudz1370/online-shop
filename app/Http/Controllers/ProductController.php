<?php

namespace App\Http\Controllers;

use App\Http\Requests\Products\CreateProductRequest;
use App\Models\Product;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function store(CreateProductRequest $request)
    {
        $data = $request->validated();

        Product::create($data);

        return 'Product Created';
    }
}
