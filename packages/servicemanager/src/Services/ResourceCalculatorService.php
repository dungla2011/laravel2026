<?php

namespace YourCompany\ServiceManager\Services;

use YourCompany\ServiceManager\Models\ServicePlan;
use YourCompany\ServiceManager\Models\Service;

class ResourceCalculatorService
{
    /**
     * Calculate total cost for resources
     */
    public function calculateResourceCost(array $resources, array $pricing, $billingPeriod = 'month')
    {
        $totalCost = 0;

        foreach ($resources as $resourceType => $quantity) {
            if (isset($pricing[$resourceType][$billingPeriod])) {
                $unitPrice = $pricing[$resourceType][$billingPeriod];
                $totalCost += $unitPrice * $quantity;
            }
        }

        return $totalCost;
    }

    /**
     * Calculate cost difference between two resource configurations
     */
    public function calculateResourceDifference(array $oldResources, array $newResources, array $pricing, $billingPeriod = 'month')
    {
        $oldCost = $this->calculateResourceCost($oldResources, $pricing, $billingPeriod);
        $newCost = $this->calculateResourceCost($newResources, $pricing, $billingPeriod);

        return [
            'old_cost' => $oldCost,
            'new_cost' => $newCost,
            'difference' => $newCost - $oldCost,
            'percentage_change' => $oldCost > 0 ? (($newCost - $oldCost) / $oldCost) * 100 : 0
        ];
    }

    /**
     * Calculate prorated cost for partial billing period
     */
    public function calculateProratedCost($amount, $totalMinutes, $usedMinutes)
    {
        if ($totalMinutes <= 0 || $usedMinutes <= 0) {
            return 0;
        }

        $ratio = min($usedMinutes / $totalMinutes, 1);
        return $amount * $ratio;
    }

    /**
     * Get resource usage recommendations
     */
    public function getResourceRecommendations(Service $service, array $usageMetrics = [])
    {
        $currentResources = $service->current_resources;
        $recommendations = [];

        // CPU recommendations
        if (isset($usageMetrics['cpu_usage']) && isset($currentResources['cpu'])) {
            $cpuUsage = $usageMetrics['cpu_usage'];
            $currentCpu = $currentResources['cpu'];

            if ($cpuUsage > 80) {
                $recommendedCpu = ceil($currentCpu * 1.5);
                $recommendations['cpu'] = [
                    'current' => $currentCpu,
                    'recommended' => $recommendedCpu,
                    'reason' => 'High CPU usage detected',
                    'priority' => 'high'
                ];
            } elseif ($cpuUsage < 20 && $currentCpu > 1) {
                $recommendedCpu = max(1, floor($currentCpu * 0.7));
                $recommendations['cpu'] = [
                    'current' => $currentCpu,
                    'recommended' => $recommendedCpu,
                    'reason' => 'Low CPU usage, consider downgrading',
                    'priority' => 'low'
                ];
            }
        }

        // RAM recommendations
        if (isset($usageMetrics['ram_usage']) && isset($currentResources['ram'])) {
            $ramUsage = $usageMetrics['ram_usage'];
            $currentRam = $currentResources['ram'];

            if ($ramUsage > 85) {
                $recommendedRam = ceil($currentRam * 1.3);
                $recommendations['ram'] = [
                    'current' => $currentRam,
                    'recommended' => $recommendedRam,
                    'reason' => 'High RAM usage detected',
                    'priority' => 'high'
                ];
            } elseif ($ramUsage < 30 && $currentRam > 1) {
                $recommendedRam = max(1, floor($currentRam * 0.8));
                $recommendations['ram'] = [
                    'current' => $currentRam,
                    'recommended' => $recommendedRam,
                    'reason' => 'Low RAM usage, consider downgrading',
                    'priority' => 'low'
                ];
            }
        }

        // Disk recommendations
        if (isset($usageMetrics['disk_usage']) && isset($currentResources['disk'])) {
            $diskUsage = $usageMetrics['disk_usage'];
            $currentDisk = $currentResources['disk'];

            if ($diskUsage > 90) {
                $recommendedDisk = ceil($currentDisk * 1.5);
                $recommendations['disk'] = [
                    'current' => $currentDisk,
                    'recommended' => $recommendedDisk,
                    'reason' => 'Disk space running low',
                    'priority' => 'high'
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Calculate cost impact of recommendations
     */
    public function calculateRecommendationCost(Service $service, array $recommendations)
    {
        $plan = $service->plan;
        if (!$plan) {
            return null;
        }

        $currentResources = $service->current_resources;
        $recommendedResources = $currentResources;

        // Apply recommendations
        foreach ($recommendations as $resourceType => $recommendation) {
            $recommendedResources[$resourceType] = $recommendation['recommended'];
        }

        return $this->calculateResourceDifference(
            $currentResources,
            $recommendedResources,
            $plan->pricing,
            $service->billing_period
        );
    }

    /**
     * Validate resource configuration
     */
    public function validateResourceConfiguration(array $resources, ServicePlan $plan)
    {
        $errors = [];
        $warnings = [];

        foreach ($resources as $resourceType => $quantity) {
            // Check if resource type is supported by plan
            if (!isset($plan->pricing[$resourceType])) {
                $errors[] = "Resource type '{$resourceType}' is not supported by this plan";
                continue;
            }

            // Check minimum values
            if ($quantity < 0) {
                $errors[] = "Resource '{$resourceType}' cannot be negative";
            }

            // Check maximum values (if defined in plan metadata)
            if (isset($plan->metadata['max_resources'][$resourceType])) {
                $maxValue = $plan->metadata['max_resources'][$resourceType];
                if ($quantity > $maxValue) {
                    $errors[] = "Resource '{$resourceType}' exceeds maximum allowed value of {$maxValue}";
                }
            }

            // Check minimum values (if defined in plan metadata)
            if (isset($plan->metadata['min_resources'][$resourceType])) {
                $minValue = $plan->metadata['min_resources'][$resourceType];
                if ($quantity < $minValue) {
                    $errors[] = "Resource '{$resourceType}' is below minimum required value of {$minValue}";
                }
            }
        }

        // Check for required resources
        if (isset($plan->metadata['required_resources'])) {
            foreach ($plan->metadata['required_resources'] as $requiredResource) {
                if (!isset($resources[$requiredResource]) || $resources[$requiredResource] <= 0) {
                    $errors[] = "Resource '{$requiredResource}' is required for this plan";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Get resource utilization report
     */
    public function getResourceUtilizationReport($userId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->subDays(30);
        $endDate = $endDate ?? now();

        $services = Service::byUser($userId)->active()->get();
        $report = [];

        foreach ($services as $service) {
            $resourceUsage = $service->resourceUsageHistory()
                ->byDateRange($startDate, $endDate)
                ->get();

            $totalCostChange = $resourceUsage->sum('cost_difference');
            $upgradeCount = $resourceUsage->where('cost_difference', '>', 0)->count();
            $downgradeCount = $resourceUsage->where('cost_difference', '<', 0)->count();

            $report[] = [
                'service_id' => $service->_id,
                'service_name' => $service->name,
                'current_resources' => $service->current_resources,
                'current_cost' => $service->calculateCurrentCost(),
                'total_cost_change' => $totalCostChange,
                'upgrade_count' => $upgradeCount,
                'downgrade_count' => $downgradeCount,
                'change_count' => $resourceUsage->count(),
                'usage_history' => $resourceUsage
            ];
        }

        return $report;
    }

    /**
     * Convert between billing periods
     */
    public function convertBillingPeriod($amount, $fromPeriod, $toPeriod)
    {
        $periods = config('servicemanager.billing_periods');
        
        if (!isset($periods[$fromPeriod]) || !isset($periods[$toPeriod])) {
            throw new \InvalidArgumentException('Invalid billing period');
        }

        $fromMinutes = $periods[$fromPeriod];
        $toMinutes = $periods[$toPeriod];

        return ($amount / $fromMinutes) * $toMinutes;
    }
} 