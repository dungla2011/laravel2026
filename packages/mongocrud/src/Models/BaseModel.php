<?php

namespace YourCompany\MongoCrud\Models;

use MongoDB\Laravel\Eloquent\Model;

abstract class BaseModel extends Model
{
    protected $connection = 'mongodb';
    
    protected $fillable = [];
    protected $guarded = [];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the connection name for the model.
     */
    public function getConnectionName()
    {
        return config('mongocrud.connection', 'mongodb');
    }

    /**
     * Get collection name with prefix
     */
    public function getTable()
    {
        $prefix = config('mongocrud.collection_prefix', '');
        return $prefix . $this->collection;
    }

    /**
     * Scope for pagination
     */
    public function scopePaginated($query, $perPage = null)
    {
        $perPage = $perPage ?: config('mongocrud.pagination.per_page', 20);
        $maxPerPage = config('mongocrud.pagination.max_per_page', 100);
        
        if ($perPage > $maxPerPage) {
            $perPage = $maxPerPage;
        }
        
        return $query->paginate($perPage);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $field, $value)
    {
        if (empty($value)) {
            return $query;
        }
        
        return $query->where($field, 'like', "%{$value}%");
    }

    /**
     * Scope for date range
     */
    public function scopeDateRange($query, $field, $startDate, $endDate)
    {
        if ($startDate && $endDate) {
            return $query->whereBetween($field, [$startDate, $endDate]);
        } elseif ($startDate) {
            return $query->where($field, '>=', $startDate);
        } elseif ($endDate) {
            return $query->where($field, '<=', $endDate);
        }
        
        return $query;
    }
} 