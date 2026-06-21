<?php

namespace App\Services;

use App\Models\Product;
use App\Jobs\LogToMongo;



class ProductService
{
    public function add($data)
    {
        $title = $data['title'];
        $stock = $data['stock'];
        $main_price = $data['main_price'];
        $final_price = $data['final_price'];
        $attributes = $data['attributes'];
        $category_id = $data['category_id'];

        $product = Product::create([
            'title' => $title,
            'stock' => $stock,
            'main_price' => $main_price,
            'final_price' => $final_price,
            'attributes' => $attributes,
            'category_id' => $category_id,
        ]);

        $product_id = $product->id;

        $main_image_path = null;

        if ($data->hasFile('main_images')) {
            $main_image = $data->file('main_images');

            $main_image_name = $main_image->hashName();
            $main_image_path = $main_image->storeAs(
                "images/products/{$product_id}",
                $main_image_name,
                'public'
            );
        }

        $gallery_images_paths = [];

        if ($data->hasFile('gallery_images')) {
            $gallery_images = $data->file('gallery_images');

            foreach ($gallery_images as $item) {
                $item_name = $item->hashName();
                $path = $item->storeAs(
                    "images/products/{$product_id}",
                    $item_name,
                    'public'
                );
                $gallery_images_paths[] = $path;
            }
        }

        $product->main_images = $main_image_path;
        $product->gallery_images = $gallery_images_paths;
        $product->save();


    }

    public function update($user_id, $order_id, $status)
    {

    }


    public function remove($user_id, $order_id)
    {
        /*Order::where('user_id', $user_id)->where('id', $order_id)->delete();

        LogToMongo::dispatch([
            'section' => 'order',
            'action' => 'Order has removed',
            'user_id' => $user_id,
            'data' => $order_id,
        ]);

        return 'Order Has Removed';*/
    }

    public function index($user_id)
    {
        /*$orders = Order::where('user_id', $user_id)->get();

        if ($orders->isEmpty()) {
            return 'There is no Order';
        }

        return [
            'User' => $user_id,
            'Orders' => $orders,
        ];*/
    }

    public function show($user_id, $order_id)
    {
        /*$orders = Order::where('user_id', $user_id)->where('id', $order_id)->get();

        if ($orders->isEmpty()) {
            return 'There is no Order';
        }

        return [
            'User' => $user_id,
            'Orders' => $orders,
        ];*/
    }
}