<?php

namespace YourCompany\ServiceManager\Models;

class ResourceUsage extends BaseModel
{
    protected $collection = 'resource_usage';

    protected $fillable = [
        'service_id',
        'old_resources',
        'new_resources',
        'change_date',
        'cost_difference',
        'billing_period',
        'prorated_amount',
        'description',
        'metadata'
    ];

    protected $casts = [
        'old_resources' => 'array',
        'new_resources' => 'array',
        'change_date' => 'datetime',
        'cost_difference' => 'decimal:2',
        'prorated_amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    /**
     * Get the service
     */
    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    /**
     * Calculate prorated amount based on remaining time in billing period
     */
    public function calculateProratedAmount()
    {
        $service = $this->service;
        if (!$service || !$service->next_billing_date) {
            return $this->cost_difference;
        }

        $totalMinutesInPeriod = config('servicemanager.billing_periods.' . $this->billing_period, 43200);
        $remainingMinutes = now()->diffInMinutes($service->next_billing_date);
        
        if ($remainingMinutes <= 0 || $totalMinutesInPeriod <= 0) {
            return $this->cost_difference;
        }

        $proratedRatio = $remainingMinutes / $totalMinutesInPeriod;
        return $this->cost_difference * $proratedRatio;
    }

    /**
     * Get resource changes summary
     */
    public function getResourceChangesSummary()
    {
        $changes = [];
        $oldResources = $this->old_resources ?? [];
        $newResources = $this->new_resources ?? [];

        $allResourceTypes = array_unique(array_merge(array_keys($oldResources), array_keys($newResources)));

        foreach ($allResourceTypes as $resourceType) {
            $oldValue = $oldResources[$resourceType] ?? 0;
            $newValue = $newResources[$resourceType] ?? 0;
            $difference = $newValue - $oldValue;

            if ($difference != 0) {
                $changes[$resourceType] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                    'difference' => $difference,
                    'change_type' => $difference > 0 ? 'increase' : 'decrease'
                ];
            }
        }

        return $changes;
    }

    /**
     * Scope by service
     */
    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    /**
     * Scope by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('change_date', [$startDate, $endDate]);
    }

    /**
     * Scope for increases only
     */
    public function scopeIncreases($query)
    {
        return $query->where('cost_difference', '>', 0);
    }

    /**
     * Scope for decreases only
     */
    public function scopeDecreases($query)
    {
        return $query->where('cost_difference', '<', 0);
    }
} 