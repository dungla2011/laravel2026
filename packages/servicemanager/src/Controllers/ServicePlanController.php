<?php

namespace YourCompany\ServiceManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use YourCompany\ServiceManager\Models\ServicePlan;

class ServicePlanController extends Controller
{
    /**
     * Get all service plans
     */
    public function index(Request $request)
    {
        $query = ServicePlan::active();

        // Filter by category
        if ($request->has('category')) {
            $query->byCategory($request->category);
        }

        $plans = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $plans
        ]);
    }

    /**
     * Get service plan details
     */
    public function show($id)
    {
        $plan = ServicePlan::find($id);
        
        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Service plan not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $plan
        ]);
    }

    /**
     * Create new service plan (Admin only)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|string',
            'resources' => 'required|array',
            'pricing' => 'required|array',
            'metadata' => 'sometimes|array'
        ]);

        $plan = ServicePlan::create([
            'name' => $request->name,
            'description' => $request->description,
            'category' => $request->category,
            'status' => true,
            'resources' => $request->resources,
            'pricing' => $request->pricing,
            'metadata' => $request->metadata ?? [],
            'created_by' => $request->user() ? $request->user()->id : 1
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Service plan created successfully',
            'data' => $plan
        ], 201);
    }

    /**
     * Update service plan (Admin only)
     */
    public function update(Request $request, $id)
    {
        $plan = ServicePlan::find($id);
        
        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Service plan not found'
            ], 404);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'category' => 'sometimes|string',
            'resources' => 'sometimes|array',
            'pricing' => 'sometimes|array',
            'metadata' => 'sometimes|array',
            'status' => 'sometimes|boolean'
        ]);

        $plan->update(array_merge(
            $request->only(['name', 'description', 'category', 'resources', 'pricing', 'metadata', 'status']),
            ['updated_by' => $request->user() ? $request->user()->id : 1]
        ));

        return response()->json([
            'success' => true,
            'message' => 'Service plan updated successfully',
            'data' => $plan
        ]);
    }

    /**
     * Delete service plan (Admin only)
     */
    public function destroy($id)
    {
        $plan = ServicePlan::find($id);
        
        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Service plan not found'
            ], 404);
        }

        // Check if plan has active services
        $activeServices = $plan->services()->whereIn('status', ['active', 'suspended'])->count();
        
        if ($activeServices > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete plan with active services'
            ], 400);
        }

        $plan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Service plan deleted successfully'
        ]);
    }

    /**
     * Calculate plan price for specific resources and billing period
     */
    public function calculatePrice(Request $request, $id)
    {
        $request->validate([
            'resources' => 'required|array',
            'billing_period' => 'required|string'
        ]);

        $plan = ServicePlan::find($id);
        
        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Service plan not found'
            ], 404);
        }

        $price = $plan->calculatePrice($request->billing_period, $request->resources);

        return response()->json([
            'success' => true,
            'data' => [
                'price' => $price,
                'currency' => config('servicemanager.billing.currency', 'VND'),
                'billing_period' => $request->billing_period,
                'resources' => $request->resources
            ]
        ]);
    }

    /**
     * Get plan categories
     */
    public function categories()
    {
        $categories = ServicePlan::active()
            ->select('category')
            ->distinct()
            ->pluck('category');

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    /**
     * Get available billing periods for a plan
     */
    public function billingPeriods($id)
    {
        $plan = ServicePlan::find($id);
        
        if (!$plan) {
            return response()->json([
                'success' => false,
                'message' => 'Service plan not found'
            ], 404);
        }

        $periods = $plan->getAvailableBillingPeriods();

        return response()->json([
            'success' => true,
            'data' => $periods
        ]);
    }
} 