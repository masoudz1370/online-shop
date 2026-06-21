<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $casts = [
        'gallery_images' => 'array',
        'attributes' => 'array'
    ];

    protected $fillable = [
        'title',
        'stock',
        'main_price',
        'final_price',
        'attributes',
        'category_id',
        'main_images',
        'gallery_images',
    ];

}
