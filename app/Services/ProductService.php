<?php

namespace App\Services;

use App\Models\Product;
use App\Jobs\LogToMongo;
//use Illuminate\Container\Attributes\Storage;
use Illuminate\Support\Facades\Storage;



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

        LogToMongo::dispatch([
            'section' => 'products',
            'action' => 'Product Created',
            'user_id' => null,
            'data' => [
                'product_id' => $product_id,
                'product' => $product->toArray()
            ]
        ]);

        return response()->json([
            'message' => 'Product Has Created',
            'product' => $product
        ]);


    }

    public function update($product_id, $data)
    {
        $product = Product::findOrFail($product_id);

        $nonFileData = $data->only([
            'title',
            'stock',
            'main_price',
            'final_price',
            'attributes',
            'category_id',
        ]);

        $product->update($nonFileData);

        if ($data->hasFile('main_images')) {
            Storage::disk('public')->delete($product->main_images);
        }

        if ($data->hasFile('gallery_images')) {
            Storage::disk('public')->delete($product->gallery_images);
        }


        $images = [];

        if ($data->hasFile('main_images')) {
            $main_image_name = $data->file('main_images')->hashName();
            $images['main_images'] = $data
                ->file('main_images')
                ->storeAs("images/products/{$product_id}", $main_image_name, 'public');
        }

        if ($data->hasFile('gallery_images')) {
            $galleryImagesPaths = [];

            foreach ($data->file('gallery_images') as $item) {
                $item_name = $item->hashName();
                $galleryImagesPaths[] = $item->storeAs(
                    "images/products/{$product_id}",
                    $item_name,
                    'public'
                );
            }

            $images['gallery_images'] = $galleryImagesPaths;
        }

        if (!empty($images)) {
            $product->update($images);
        }

        LogToMongo::dispatch([
            'section' => 'products',
            'action' => "Product with ID: {$product_id} Updated",
            'user_id' => null,
            'data' => "Product: {$product->toArray()}"
        ]);

        return response()->json([
            'message' => 'Product Has Updated',
            'product' => $product
        ]);
    }


    public function remove($product_id)
    {

        $product = Product::findOrFail($product_id);

        if ($product->main_images) {
            Storage::disk('public')->delete($product->main_images);
        }

        if ($product->gallery_images) {
            Storage::disk('public')->delete($product->gallery_images);
        }

        $product->delete();

        LogToMongo::dispatch([
            'section' => 'products',
            'action' => 'Product Has Removed',
            'user_id' => null,
            'data' => [
                'product_id' => $product_id,
                'product' => $product->toArray()
            ]
        ]);

        return response()->json([
            'message' => "Product With ID: {$product_id} Has Removed",
        ]);

    }

    public function getAll()
    {
        return Product::paginate(10);
    }

    public function getByID($product_id)
    {
        $product = Product::findOrFail($product_id);

        return response()->json([
            'Product ID: ' => $product_id,
            'Product: ' => $product
        ]);
    }
}