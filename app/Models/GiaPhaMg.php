<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use MongoDB\Laravel\Eloquent\Model as Mongo1; // Assuming this is the correct namespace for your MongoDB model base class

class GiaPhaMg extends Mongo1
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'giaphamg';
    
    // Allow all fields to be mass assignable since we're importing from MySQL
    protected $guarded = [];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'idsql' => 'integer', // ID gốc từ MySQL
    ];

    /**
     * Get the original MySQL ID
     */
    public function getSqlIdAttribute()
    {
        return $this->attributes['idsql'] ?? null;
    }

    /**
     * Scope to find by MySQL ID
     */
    public function scopeBySqlId($query, $sqlId)
    {
        return $query->where('idsql', $sqlId);
    }

    /**
     * Scope to find by original MySQL ID (alias for compatibility)
     */
    public function scopeByMysqlId($query, $mysqlId)
    {
        return $query->where('idsql', $mysqlId);
    }
}
