<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use MongoDB\Laravel\Eloquent\Model as Mongo1; // Assuming this is the correct namespace for your MongoDB model base class

class TestMongo1 extends Mongo1
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb_testdb3';
    protected $collection = 'testmg1';
    protected $guarded = [];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'image',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];
}
