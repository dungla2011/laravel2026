<?php

namespace YourCompany\ServiceManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use YourCompany\ServiceManager\Models\Service;
use YourCompany\ServiceManager\Models\ServicePlan;
use YourCompany\ServiceManager\Services\ServiceProvisioningService;
use YourCompany\ServiceManager\Services\BillingService;
use YourCompany\ServiceManager\Services\ResourceCalculatorService;

class ServiceController extends Controller
{
    protected $provisioningService;
    protected $billingService;
    protected $resourceCalculator;

    public function __construct(
        ServiceProvisioningService $provisioningService,
        BillingService $billingService,
        ResourceCalculatorService $resourceCalculator
    ) {
        $this->provisioningService = $provisioningService;
        $this->billingService = $billingService;
        $this->resourceCalculator = $resourceCalculator;
    }

    /**
     * Get user's services
     */
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $services = Service::byUser($userId)
            ->with('plan')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $services
        ]);
    }

    /**
     * Get service details
     */
    public function show($id)
    {
        $service = Service::with('plan')->find($id);
        
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        // Get provisioning status
        $provisioningStatus = $this->provisioningService->getProvisioningStatus($service);

        return response()->json([
            'success' => true,
            'data' => array_merge($service->toArray(), [
                'provisioning_status' => $provisioningStatus,
                'current_cost' => $service->calculateCurrentCost()
            ])
        ]);
    }

    /**
     * Create a new service
     */
    public function store(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:service_plans,_id',
            'billing_period' => 'required|in:minute,hour,day,month,year',
            'custom_resources' => 'sometimes|array',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string'
        ]);

        try {
            $service = $this->provisioningService->createService(
                $request->user()->id,
                $request->plan_id,
                $request->custom_resources,
                $request->billing_period,
                [
                    'name' => $request->name,
                    'description' => $request->description
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Service created successfully',
                'data' => $service
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update service resources
     */
    public function updateResources(Request $request, $id)
    {
        $request->validate([
            'resources' => 'required|array'
        ]);

        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        // Check ownership
        if ($service->user_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            // Calculate billing impact first
            $billing = $this->billingService->calculateProratedBilling($service, $request->resources);
            
            // Process the resource change
            $result = $this->billingService->processResourceChangeBilling($service, $request->resources);

            return response()->json([
                'success' => true,
                'message' => 'Resources updated successfully',
                'data' => [
                    'service' => $service->fresh(),
                    'billing_impact' => $result
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Calculate resource change cost
     */
    public function calculateResourceChangeCost(Request $request, $id)
    {
        $request->validate([
            'resources' => 'required|array'
        ]);

        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        // Check ownership
        if ($service->user_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $billing = $this->billingService->calculateProratedBilling($service, $request->resources);

        return response()->json([
            'success' => true,
            'data' => $billing
        ]);
    }

    /**
     * Suspend service
     */
    public function suspend(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        // Check ownership
        if ($service->user_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $service->suspend($request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Service suspended successfully',
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Reactivate service
     */
    public function reactivate(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        // Check ownership
        if ($service->user_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $service->reactivate();

            return response()->json([
                'success' => true,
                'message' => 'Service reactivated successfully',
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Terminate service
     */
    public function terminate(Request $request, $id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        // Check ownership
        if ($service->user_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        try {
            $service->terminate($request->reason);

            return response()->json([
                'success' => true,
                'message' => 'Service terminated successfully',
                'data' => $service
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get service billing history
     */
    public function billingHistory($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $history = $this->billingService->getServiceBillingHistory($id);

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Get resource usage history
     */
    public function resourceUsageHistory($id)
    {
        $service = Service::find($id);
        if (!$service) {
            return response()->json([
                'success' => false,
                'message' => 'Service not found'
            ], 404);
        }

        $history = $service->resourceUsageHistory()
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    /**
     * Get user services summary
     */
    public function summary(Request $request)
    {
        $summary = $this->provisioningService->getUserServicesSummary($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }
} 