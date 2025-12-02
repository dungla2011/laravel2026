<?php

namespace YourCompany\ServiceManager\Models;

class ServicePlan extends BaseModel
{
    protected $collection = 'service_plans';

    protected $fillable = [
        'name',
        'description',
        'category',
        'status',
        'resources',
        'pricing',
        'metadata',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'resources' => 'array',
        'pricing' => 'array',
        'metadata' => 'array',
        'status' => 'boolean'
    ];

    /**
     * Get services using this plan
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'plan_id');
    }

    /**
     * Calculate price for specific billing period
     */
    public function calculatePrice($billingPeriod = 'month', $customResources = null)
    {
        $resources = $customResources ?? $this->resources;
        $totalPrice = 0;

        foreach ($resources as $resourceType => $quantity) {
            if (isset($this->pricing[$resourceType][$billingPeriod])) {
                $totalPrice += $this->pricing[$resourceType][$billingPeriod] * $quantity;
            }
        }

        return $totalPrice;
    }

    /**
     * Get available billing periods for this plan
     */
    public function getAvailableBillingPeriods()
    {
        $periods = [];
        foreach ($this->pricing as $resourceType => $pricing) {
            $periods = array_merge($periods, array_keys($pricing));
        }
        return array_unique($periods);
    }

    /**
     * Scope for active plans
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope by category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }
} 