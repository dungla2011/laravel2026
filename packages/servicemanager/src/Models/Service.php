<?php

namespace YourCompany\ServiceManager\Models;

use Carbon\Carbon;

class Service extends BaseModel
{
    protected $collection = 'services';

    protected $fillable = [
        'user_id',
        'plan_id',
        'name',
        'description',
        'status',
        'current_resources',
        'billing_period',
        'next_billing_date',
        'last_billing_date',
        'total_cost',
        'metadata',
        'provisioning_data',
        'suspended_at',
        'terminated_at'
    ];

    protected $casts = [
        'current_resources' => 'array',
        'metadata' => 'array',
        'provisioning_data' => 'array',
        'next_billing_date' => 'datetime',
        'last_billing_date' => 'datetime',
        'suspended_at' => 'datetime',
        'terminated_at' => 'datetime',
        'total_cost' => 'decimal:2'
    ];

    /**
     * Get the service plan
     */
    public function plan()
    {
        return $this->belongsTo(ServicePlan::class, 'plan_id');
    }

    /**
     * Get billing records
     */
    public function billingRecords()
    {
        return $this->hasMany(BillingRecord::class, 'service_id');
    }

    /**
     * Get resource usage history
     */
    public function resourceUsageHistory()
    {
        return $this->hasMany(ResourceUsage::class, 'service_id');
    }

    /**
     * Calculate current cost per billing period
     */
    public function calculateCurrentCost()
    {
        $plan = $this->plan;
        if (!$plan) {
            return 0;
        }

        return $plan->calculatePrice($this->billing_period, $this->current_resources);
    }

    /**
     * Calculate cost difference when changing resources
     */
    public function calculateResourceChangeCost($newResources)
    {
        $plan = $this->plan;
        if (!$plan) {
            return 0;
        }

        $currentCost = $plan->calculatePrice($this->billing_period, $this->current_resources);
        $newCost = $plan->calculatePrice($this->billing_period, $newResources);

        return $newCost - $currentCost;
    }

    /**
     * Update resources and calculate billing
     */
    public function updateResources($newResources, $effectiveDate = null)
    {
        $effectiveDate = $effectiveDate ?? now();
        $oldResources = $this->current_resources;
        
        // Create resource usage record
        ResourceUsage::create([
            'service_id' => $this->_id,
            'old_resources' => $oldResources,
            'new_resources' => $newResources,
            'change_date' => $effectiveDate,
            'cost_difference' => $this->calculateResourceChangeCost($newResources),
            'billing_period' => $this->billing_period
        ]);

        // Update current resources
        $this->update(['current_resources' => $newResources]);

        return $this;
    }

    /**
     * Suspend service
     */
    public function suspend($reason = null)
    {
        $this->update([
            'status' => 'suspended',
            'suspended_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], [
                'suspension_reason' => $reason
            ])
        ]);

        return $this;
    }

    /**
     * Reactivate service
     */
    public function reactivate()
    {
        $this->update([
            'status' => 'active',
            'suspended_at' => null,
            'metadata' => array_merge($this->metadata ?? [], [
                'reactivated_at' => now()->toISOString()
            ])
        ]);

        return $this;
    }

    /**
     * Terminate service
     */
    public function terminate($reason = null)
    {
        $this->update([
            'status' => 'terminated',
            'terminated_at' => now(),
            'metadata' => array_merge($this->metadata ?? [], [
                'termination_reason' => $reason
            ])
        ]);

        return $this;
    }

    /**
     * Check if service is active
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if service is suspended
     */
    public function isSuspended()
    {
        return $this->status === 'suspended';
    }

    /**
     * Check if service is terminated
     */
    public function isTerminated()
    {
        return $this->status === 'terminated';
    }

    /**
     * Scope for active services
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for services due for billing
     */
    public function scopeDueForBilling($query)
    {
        return $query->where('next_billing_date', '<=', now())
                    ->whereIn('status', ['active', 'suspended']);
    }

    /**
     * Scope by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
} 