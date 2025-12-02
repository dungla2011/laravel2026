<?php

namespace YourCompany\ServiceManager\Services;

use YourCompany\ServiceManager\Models\Service;
use YourCompany\ServiceManager\Models\ServicePlan;
use YourCompany\ServiceManager\Models\UserBalance;
use Carbon\Carbon;

class ServiceProvisioningService
{
    protected $billingService;
    protected $resourceCalculator;

    public function __construct(BillingService $billingService, ResourceCalculatorService $resourceCalculator)
    {
        $this->billingService = $billingService;
        $this->resourceCalculator = $resourceCalculator;
    }

    /**
     * Create a new service for a user
     */
    public function createService($userId, $planId, array $customResources = null, $billingPeriod = 'month', array $metadata = [])
    {
        $plan = ServicePlan::find($planId);
        if (!$plan || !$plan->status) {
            throw new \Exception('Service plan not found or inactive');
        }

        // Use custom resources or default plan resources
        $resources = $customResources ?? $plan->resources;

        // Validate resource configuration
        $validation = $this->resourceCalculator->validateResourceConfiguration($resources, $plan);
        if (!$validation['valid']) {
            throw new \Exception('Invalid resource configuration: ' . implode(', ', $validation['errors']));
        }

        // Calculate initial cost
        $initialCost = $plan->calculatePrice($billingPeriod, $resources);

        // Check user balance
        $userBalance = UserBalance::getOrCreateForUser($userId);
        if (!$userBalance->hasSufficientBalance($initialCost)) {
            throw new \Exception('Insufficient balance to create service');
        }

        // Calculate billing dates
        $billingPeriodMinutes = config('servicemanager.billing_periods.' . $billingPeriod, 43200);
        $nextBillingDate = now()->addMinutes($billingPeriodMinutes);

        // Create service
        $service = Service::create([
            'user_id' => $userId,
            'plan_id' => $planId,
            'name' => $metadata['name'] ?? "Service - {$plan->name}",
            'description' => $metadata['description'] ?? $plan->description,
            'status' => 'provisioning',
            'current_resources' => $resources,
            'billing_period' => $billingPeriod,
            'next_billing_date' => $nextBillingDate,
            'last_billing_date' => null,
            'total_cost' => $initialCost,
            'metadata' => $metadata,
            'provisioning_data' => [
                'created_at' => now()->toISOString(),
                'initial_resources' => $resources,
                'initial_cost' => $initialCost
            ]
        ]);

        // Charge initial cost
        $userBalance->deductFunds(
            $initialCost,
            "Initial charge for service: {$service->name}",
            ['service_id' => $service->_id, 'type' => 'initial_charge']
        );

        // Start provisioning process
        $this->startProvisioning($service);

        return $service;
    }

    /**
     * Start the provisioning process
     */
    protected function startProvisioning(Service $service)
    {
        // Update provisioning data
        $service->update([
            'provisioning_data' => array_merge($service->provisioning_data ?? [], [
                'provisioning_started_at' => now()->toISOString(),
                'provisioning_steps' => [
                    'resource_allocation' => 'pending',
                    'configuration' => 'pending',
                    'activation' => 'pending'
                ]
            ])
        ]);

        // Here you would integrate with your actual provisioning system
        // For now, we'll simulate the process
        $this->simulateProvisioning($service);
    }

    /**
     * Simulate provisioning process (replace with actual provisioning logic)
     */
    protected function simulateProvisioning(Service $service)
    {
        // In a real implementation, this would:
        // 1. Allocate resources on your infrastructure
        // 2. Configure the service
        // 3. Activate the service
        // 4. Update the service status

        $service->update([
            'status' => 'active',
            'provisioning_data' => array_merge($service->provisioning_data ?? [], [
                'provisioning_completed_at' => now()->toISOString(),
                'provisioning_steps' => [
                    'resource_allocation' => 'completed',
                    'configuration' => 'completed',
                    'activation' => 'completed'
                ],
                'service_details' => [
                    'server_ip' => '192.168.1.100', // Example
                    'access_credentials' => 'Generated credentials',
                    'control_panel_url' => 'https://panel.example.com'
                ]
            ])
        ]);
    }

    /**
     * Get service provisioning status
     */
    public function getProvisioningStatus(Service $service)
    {
        $provisioningData = $service->provisioning_data ?? [];
        
        return [
            'service_id' => $service->_id,
            'status' => $service->status,
            'provisioning_data' => $provisioningData,
            'current_resources' => $service->current_resources,
            'next_billing_date' => $service->next_billing_date,
            'total_cost' => $service->total_cost
        ];
    }

    /**
     * Get services summary for a user
     */
    public function getUserServicesSummary($userId)
    {
        $services = Service::byUser($userId)->get();
        
        $summary = [
            'total_services' => $services->count(),
            'active_services' => $services->where('status', 'active')->count(),
            'suspended_services' => $services->where('status', 'suspended')->count(),
            'terminated_services' => $services->where('status', 'terminated')->count(),
            'total_monthly_cost' => 0,
            'services' => []
        ];

        foreach ($services as $service) {
            $monthlyCost = $service->billing_period === 'month' 
                ? $service->calculateCurrentCost()
                : $this->resourceCalculator->convertBillingPeriod(
                    $service->calculateCurrentCost(),
                    $service->billing_period,
                    'month'
                );

            $summary['total_monthly_cost'] += $monthlyCost;
            $summary['services'][] = [
                'id' => $service->_id,
                'name' => $service->name,
                'status' => $service->status,
                'resources' => $service->current_resources,
                'monthly_cost' => $monthlyCost,
                'next_billing_date' => $service->next_billing_date
            ];
        }

        return $summary;
    }
} 