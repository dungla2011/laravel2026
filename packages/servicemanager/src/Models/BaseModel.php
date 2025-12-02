<?php

namespace YourCompany\ServiceManager\Models;

use MongoDB\Laravel\Eloquent\Model;

abstract class BaseModel extends Model
{
    protected $connection = 'mongodb';

    protected $dates = ['created_at', 'updated_at'];

    protected $guarded = ['_id'];
}
