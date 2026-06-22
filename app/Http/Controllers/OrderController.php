<?php

namespace App\Http\Controllers;

use App\Http\Requests\OrderRequest;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(OrderRequest $orderRequest, OrderService $orderService)
    {
        return $orderService->add(auth()->id(), $orderRequest->validated()['status']);
    }

    public function update(OrderRequest $orderRequest, OrderService $orderService)
    {
        return $orderService->update(auth()->id(), $orderRequest->order_id, $orderRequest->status);
    }

    public function show($order_id, OrderService $orderService)
    {
        return $orderService->show(auth()->id(), $order_id);
    }

    public function index(OrderService $orderService)
    {
        return $orderService->index(auth()->id());
    }

    public function destroy($order_id, OrderService $orderService)
    {
        return $orderService->remove(auth()->id(), $order_id);
    }
}
