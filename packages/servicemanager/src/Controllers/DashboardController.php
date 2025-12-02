<?php

namespace YourCompany\ServiceManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use YourCompany\ServiceManager\Models\Service;
use YourCompany\ServiceManager\Models\ServicePlan;
use YourCompany\ServiceManager\Models\BillingRecord;
use YourCompany\ServiceManager\Models\UserBalance;
use YourCompany\ServiceManager\Models\BalanceTransaction;
use YourCompany\ServiceManager\Services\BillingService;

class DashboardController extends Controller
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Dashboard chính
     */
    public function index(Request $request)
    {
        // Thống kê tổng quan
        $stats = $this->getOverviewStats();

        // Dịch vụ gần đây
        $recentServices = Service::with('plan')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Billing records gần đây
        $recentBilling = BillingRecord::orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Doanh thu theo tháng (6 tháng gần đây)
        $monthlyRevenue = $this->getMonthlyRevenue();

        return view('servicemanager::dashboard.index', compact(
            'stats',
            'recentServices',
            'recentBilling',
            'monthlyRevenue'
        ));
    }

    /**
     * Thống kê dịch vụ
     */
    public function services(Request $request)
    {
        $services = Service::with('plan')
            ->when($request->status, function($query, $status) {
                return $query->where('status', $status);
            })
            ->when($request->search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $serviceStats = [
            'total' => Service::count(),
            'active' => Service::where('status', 'active')->count(),
            'suspended' => Service::where('status', 'suspended')->count(),
            'terminated' => Service::where('status', 'terminated')->count(),
        ];

        return view('servicemanager::dashboard.services', compact('services', 'serviceStats'));
    }

    /**
     * Thống kê billing
     */
    public function billing(Request $request)
    {

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $billingStats = [
            'total_revenue' => BillingRecord::paid()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'pending_amount' => BillingRecord::pending()
                ->whereBetween('created_at', [$startDate, $endDate])
                ->sum('amount'),
            'overdue_amount' => BillingRecord::overdue()->sum('amount'),
            'total_transactions' => BillingRecord::whereBetween('created_at', [$startDate, $endDate])->count(),
        ];

        $billingRecords = BillingRecord::whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('servicemanager::dashboard.billing', compact('billingStats', 'billingRecords', 'startDate', 'endDate'));
    }

    /**
     * Quản lý service plans
     */
    public function plans(Request $request)
    {
        $plans = ServicePlan::when($request->category, function($query, $category) {
                return $query->where('category', $category);
            })
            ->when($request->search, function($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Manually count services for each plan (MongoDB compatible)
        foreach ($plans as $plan) {
            $plan->services_count = Service::where('plan_id', $plan->_id)->count();
        }

        $categories = ServicePlan::distinct()->pluck('category');

        return view('servicemanager::dashboard.plans', compact('plans', 'categories'));
    }

    /**
     * Thống kê tổng quan
     */
    private function getOverviewStats()
    {
        return [
            'total_services' => Service::count(),
            'active_services' => Service::where('status', 'active')->count(),
            'total_plans' => ServicePlan::where('status', true)->count(),
            'total_revenue_month' => BillingRecord::paid()
                ->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])
                ->sum('amount'),
            'pending_billing' => BillingRecord::pending()->sum('amount'),
            'overdue_billing' => BillingRecord::overdue()->sum('amount'),
            'total_users' => Service::distinct('user_id')->count(),
        ];
    }

    /**
     * Doanh thu theo tháng
     */
    private function getMonthlyRevenue()
    {
        $months = [];
        $revenue = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $monthRevenue = BillingRecord::paid()
                ->whereBetween('created_at', [$date->startOfMonth(), $date->endOfMonth()])
                ->sum('amount');

            $revenue[] = $monthRevenue;
        }

        return [
            'months' => $months,
            'revenue' => $revenue
        ];
    }

    /**
     * API endpoint cho dashboard stats
     */
    public function apiStats()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getOverviewStats()
        ]);
    }

    /**
     * API endpoint cho monthly revenue
     */
    public function apiMonthlyRevenue()
    {
        return response()->json([
            'success' => true,
            'data' => $this->getMonthlyRevenue()
        ]);
    }
}
