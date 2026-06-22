<?php

namespace App\Services;

use App\Models\Order;
use App\Models\CartItems;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\OrderStatus;
use App\Jobs\LogToMongo;



class OrderService
{
    public function add($user_id, $status)
    {
        $items = CartItems::where('user_id', $user_id)->get();
        $temp_items = [];

        $order_price = null;

        $order_items = null;
        $order_status = null;

        if ($items->isEmpty()) {
            return 'No Order';
        }

        $responceArray = [];

        foreach ($items as $item) {
            $product_id = $item->product_id;
            $quantity = $item->quantity;

            $product = Product::find($product_id);

            if (!$product) {
                $responceArray[] = 'Product Not Found!';
                continue;
            }

            if ($product->stock <= 0) {
                CartItems::where('user_id', $user_id)->where('product_id', $product_id)->delete();
                $responceArray[] = $product->title . ' Is Out Of Stock';
                continue;
            }

            if ($quantity > $product->stock) {
                $responceArray[] = 'This Amount Of ' . $product->title . ' Is Not Availaibe';
                continue;
            }

            $temp_items[] = $item;
        }

        if (empty($temp_items)) {
            return [
                'Message' => 'No valid items for order',
                'Response' => $responceArray,
            ];
        }

        $order = Order::create([
            'user_id' => $user_id,
            'status' => $status,
        ]);

        if (!$order) {
            return [
                'Error' => 'Internal Server Error',
                'Code' => 500,
                'Responce' => $responceArray,
            ];
        }

        foreach ($temp_items as $temp_item) {
            $temp_product = Product::findOrFail($temp_item->product_id);
            $product_price = $temp_product->final_price ?? $temp_product->main_price;
            $order_items = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $temp_item->product_id,
                'quantity' => $temp_item->quantity,
                'order_price' => $product_price * $temp_item->quantity,
            ]);

            Product::where('id', $temp_item->product_id)->decrement('stock', $temp_item->quantity);
        }

        $order_status = OrderStatus::create([
            'order_id' => $order->id,
            'status' => $status,
        ]);

        CartItems::where('user_id', $user_id)->delete();

        LogToMongo::dispatch([
            'user_id' => $user_id,
            'section' => 'Order',
            'action' => 'Order Create',
            'data' => [
                'order_details' => $order,
                'responce' => $responceArray,
            ]
        ]);

        return [
            'Message' => 'Order Created Succesfully',
            'Status' => $status,
            'Responce' => $responceArray,
            'Order' => $order,
        ];

    }

    public function update($user_id, $order_id, $status)
    {
        $old_status = null;
        $order = Order::where('user_id', $user_id)
            ->where('id', $order_id)
            ->first();

        if (!$order) {
            return [
                'Message' => 'Order not found',
                'Code' => 404,
            ];
        }

        $old_status = $order->status;

        $order->update([
            'status' => $status,
        ]);

        OrderStatus::where('order_id', $order->id)->update([
            'status' => $status,
        ]);

        LogToMongo::dispatch([
            'section' => 'order',
            'action' => 'Order Status Changed From ' . $old_status . ' To ' . $status,
            'user_id' => $user_id,
            'data' => $order->toArray(),
        ]);

        return [
            'Message' => 'Order Status Changed',
            'Status' => $status,
            'Order' => $order,
        ];
    }


    public function remove($user_id, $order_id)
    {
        Order::where('user_id', $user_id)->where('id', $order_id)->delete();

        LogToMongo::dispatch([
            'section' => 'order',
            'action' => 'Order has removed',
            'user_id' => $user_id,
            'data' => $order_id,
        ]);

        return 'Order Has Removed';
    }

    public function index($user_id)
    {
        $orders = Order::with('items', 'status')->where('user_id', $user_id)->get();

        if ($orders->isEmpty()) {
            return 'There is no Order';
        }

        return [
            'User' => $user_id,
            'Orders' => $orders,
        ];
    }

    public function show($user_id, $order_id)
    {
        $orders = Order::where('user_id', $user_id)->where('id', $order_id)->get();

        if ($orders->isEmpty()) {
            return 'There is no Order';
        }

        return Order::with('items', 'status')->where('user_id', $user_id)->where('id', $order_id)->get();

    }
}