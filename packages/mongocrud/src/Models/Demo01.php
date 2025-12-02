<?php

namespace YourCompany\MongoCrud\Models;

class Demo01 extends BaseModel
{
    protected $collection = 'demo01';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'age',
        'status',
        'description',
        'metadata',
        'tags'
    ];

    protected $casts = [
        'age' => 'integer',
        'status' => 'boolean',
        'metadata' => 'array',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope for active records
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for inactive records
     */
    public function scopeInactive($query)
    {
        return $query->where('status', false);
    }

    /**
     * Scope by age range
     */
    public function scopeAgeRange($query, $minAge = null, $maxAge = null)
    {
        if ($minAge && $maxAge) {
            return $query->whereBetween('age', [$minAge, $maxAge]);
        } elseif ($minAge) {
            return $query->where('age', '>=', $minAge);
        } elseif ($maxAge) {
            return $query->where('age', '<=', $maxAge);
        }
        
        return $query;
    }

    /**
     * Scope by tags
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->where('tags', $tag);
    }

    /**
     * Get full name attribute
     */
    public function getFullInfoAttribute()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'age' => $this->age,
            'status' => $this->status ? 'Active' : 'Inactive'
        ];
    }
} 