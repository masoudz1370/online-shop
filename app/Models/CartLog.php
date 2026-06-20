<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class CartLog extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'cart_logs';

    protected $fillable = [
        'user_id',
        'items',
        'total_price'
    ];
}
