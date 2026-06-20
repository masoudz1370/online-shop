<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Log extends Model
{
    protected $connection = 'mongodb';

    protected $collection = 'logs';

    protected $fillable = [
        'user_id',
        'section',
        'action',
        'data',
    ];
}
