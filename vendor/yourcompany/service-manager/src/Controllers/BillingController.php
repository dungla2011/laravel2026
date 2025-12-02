<?php

namespace YourCompany\ServiceManager\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use YourCompany\ServiceManager\Models\UserBalance;
use YourCompany\ServiceManager\Models\BillingRecord;
use YourCompany\ServiceManager\Models\BalanceTransaction;
use YourCompany\ServiceManager\Services\BillingService;

class BillingController extends Controller
{
    protected $billingService;

    public function __construct(BillingService $billingService)
    {
        $this->billingService = $billingService;
    }

    /**
     * Get user balance
     */
    public function balance(Request $request)
    {
        $balance = UserBalance::getOrCreateForUser($request->user()->id);

        return response()->json([
            'success' => true,
            'data' => [
                'balance' => $balance->balance,
                'available_balance' => $balance->getAvailableBalance(),
                'reserved_amount' => $balance->reserved_amount,
                'currency' => $balance->currency
            ]
        ]);
    }

    /**
     * Add funds to user balance
     */
    public function addFunds(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'sometimes|string',
            'payment_method' => 'sometimes|string'
        ]);

        $balance = UserBalance::getOrCreateForUser($request->user()->id);
        
        try {
            $transaction = $balance->addFunds(
                $request->amount,
                $request->description ?? 'Funds added',
                [
                    'payment_method' => $request->payment_method,
                    'added_by' => $request->user()->id
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Funds added successfully',
                'data' => [
                    'transaction' => $transaction,
                    'new_balance' => $balance->fresh()->balance
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
     * Get balance transactions
     */
    public function transactions(Request $request)
    {
        $transactions = BalanceTransaction::byUser($request->user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }

    /**
     * Get billing records
     */
    public function billingRecords(Request $request)
    {
        $query = BillingRecord::byUser($request->user()->id);

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->byDateRange($request->start_date, $request->end_date);
        }

        $records = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $records
        ]);
    }

    /**
     * Get billing summary
     */
    public function summary(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $summary = $this->billingService->getUserBillingSummary(
            $request->user()->id,
            $startDate,
            $endDate
        );

        return response()->json([
            'success' => true,
            'data' => $summary
        ]);
    }

    /**
     * Pay a billing record
     */
    public function payBilling(Request $request, $billingId)
    {
        $billingRecord = BillingRecord::find($billingId);
        
        if (!$billingRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Billing record not found'
            ], 404);
        }

        // Check ownership
        if ($billingRecord->user_id != $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($billingRecord->status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => 'Billing record already paid'
            ], 400);
        }

        $userBalance = UserBalance::getOrCreateForUser($request->user()->id);

        if (!$userBalance->hasSufficientBalance($billingRecord->amount)) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient balance'
            ], 400);
        }

        try {
            // Deduct from balance
            $userBalance->deductFunds(
                $billingRecord->amount,
                "Payment for billing record #{$billingRecord->_id}",
                ['billing_record_id' => $billingRecord->_id]
            );

            // Mark as paid
            $billingRecord->markAsPaid(null, 'balance');

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => $billingRecord
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Get overdue billing records
     */
    public function overdue(Request $request)
    {
        $overdueRecords = BillingRecord::byUser($request->user()->id)
            ->overdue()
            ->orderBy('due_date', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $overdueRecords
        ]);
    }

    /**
     * Process billing cycle (Admin only)
     */
    public function processBillingCycle(Request $request)
    {
        try {
            $results = $this->billingService->processBillingCycle();

            return response()->json([
                'success' => true,
                'message' => 'Billing cycle processed',
                'data' => $results
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get billing statistics (Admin only)
     */
    public function statistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $totalRevenue = BillingRecord::paid()
            ->byDateRange($startDate, $endDate)
            ->sum('amount');

        $pendingAmount = BillingRecord::pending()
            ->byDateRange($startDate, $endDate)
            ->sum('amount');

        $overdueAmount = BillingRecord::overdue()
            ->sum('amount');

        $totalTransactions = BillingRecord::byDateRange($startDate, $endDate)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total_revenue' => $totalRevenue,
                'pending_amount' => $pendingAmount,
                'overdue_amount' => $overdueAmount,
                'total_transactions' => $totalTransactions,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate
                ]
            ]
        ]);
    }
} 