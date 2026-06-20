<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemsRequest;
use App\Services\CartItemsService;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

class CartItemsController extends Controller
{
    public function store(CartItemsRequest $request, CartItemsService $cartItemsService)
    {
        return $cartItemsService->add(auth()->id(), $request->validated());
    }

    public function update(CartItemsRequest $request, CartItemsService $cartItemsService)
    {
        return $cartItemsService->updateCartItem(auth()->id(), $request->validated());
    }

    public function destroy($product_id, CartItemsService $cartItemsService)
    {
        return $cartItemsService->removeCartItem(auth()->id(), $product_id);
    }

    public function index(CartItemsService $cartItemsService)
    {
        return $cartItemsService->index(auth()->id());
    }
}
