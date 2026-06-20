<?php

namespace App\Services;

use App\Http\Resources\CartItemsResource;
use App\Jobs\LogCartToMongo;
use App\Jobs\LogToMongo;
use App\Models\CartItems;
use App\Models\Product;

class CartItemsService
{
    public function add($user_id, $data)
    {
        $product_id = $data['product_id'];
        $quantity = $data['quantity'];

        if ($quantity <= 0)
            return 'Invalid Quantity';

        $product = Product::find($product_id);
        $cartItem = CartItems::where('user_id', $user_id)->where('product_id', $product_id)->first();

        if (!$product)
            return 'Product Not Found';

        $currentQuantity = $cartItem ? $cartItem->quantity : 0;

        if ($currentQuantity + $quantity > $product->stock)
            return 'Out Of Stock';

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
            return $cartItem->fresh();
        }

        $cartItem = CartItems::create([
            'user_id' => $user_id,
            'product_id' => $product_id,
            'quantity' => $quantity,
        ]);
        LogToMongo::dispatch([
            'section' => 'cart_items',
            'action' => 'Add product to cart items',
            'user_id' => $user_id,
            'data' => $product->toArray(),
        ]);

        return $cartItem;
    }

    public function updateCartItem($user_id, $data)
    {
        $product_id = $data['product_id'];
        $quantity = $data['quantity'];

        $cartItem = CartItems::where('user_id', $user_id)->where('product_id', $product_id)->firstOrFail();
        $product = Product::findOrFail($product_id);

        if ($quantity == 0) {
            $cartItem->delete();
            LogToMongo::dispatch([
                'section' => 'cart_items',
                'action' => 'The product removed from cart items',
                'user_id' => $user_id,
                'data' => $product->toArray(),
            ]);
            return 'Cart Item Removed';
        }

        if ($quantity > $product->stock)
            return 'Out Of Stock';

        $cartItem->update([
            'quantity' => $quantity,
        ]);

        LogToMongo::dispatch([
            'section' => 'cart_items',
            'action' => 'Cart item list has updated',
            'user_id' => $user_id,
            'data' => $product->toArray(),
        ]);

        return $cartItem;
    }

    public function removeCartItem($user_id, $product_id)
    {
        $cartItem = CartItems::where('user_id', $user_id)->where('product_id', $product_id)->firstOrFail();

        LogToMongo::dispatch([
            'section' => 'cart_items',
            'action' => 'The product removed from cart items',
            'user_id' => $user_id,
            'data' => $product_id,
        ]);

        $cartItem->delete();
        return 'Cart Item Removed';
    }

    public function index($user_id)
    {
        $items = CartItems::where('user_id', $user_id)->with('product')->get();

        if ($items->isEmpty())
            return 'Cart Items Epmty';

        $total_price = 0;

        foreach ($items as $item) {
            $item_price = $item->product->final_price ?: $item->product->main_price;
            $total_price += ($item_price * $item->quantity);
        }

        $responce = [
            'items' => CartItemsResource::collection($items),
            'total_price' => $total_price,
        ];

        return $responce;
    }
}