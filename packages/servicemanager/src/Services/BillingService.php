<?php

namespace YourCompany\ServiceManager\Services;

use YourCompany\ServiceManager\Models\Service;
use YourCompany\ServiceManager\Models\BillingRecord;
use YourCompany\ServiceManager\Models\UserBalance;
use YourCompany\ServiceManager\Models\ResourceUsage;
use Carbon\Carbon;

class BillingService
{
    /**
     * Process billing for all services due for billing
     */
    public function processBillingCycle()
    {
        $servicesDue = Service::dueForBilling()->get();
        $results = [];

        foreach ($servicesDue as $service) {
            try {
                $result = $this->processServiceBilling($service);
                $results[] = [
                    'service_id' => $service->_id,
                    'status' => 'success',
                    'amount' => $result['amount'],
                    'billing_record_id' => $result['billing_record']->_id
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'service_id' => $service->_id,
                    'status' => 'error',
                    'error' => $e->getMessage()
                ];
            }
        }

        return $results;
    }

    /**
     * Process billing for a specific service
     */
    public function processServiceBilling(Service $service)
    {
        $amount = $service->calculateCurrentCost();
        $userBalance = UserBalance::getOrCreateForUser($service->user_id);

        // Create billing record
        $billingRecord = BillingRecord::create([
            'user_id' => $service->user_id,
            'service_id' => $service->_id,
            'amount' => $amount,
            'currency' => config('servicemanager.billing.currency', 'VND'),
            'billing_period' => $service->billing_period,
            'billing_start_date' => $service->last_billing_date ?? $service->created_at,
            'billing_end_date' => $service->next_billing_date,
            'status' => 'pending',
            'description' => "Billing for service: {$service->name}",
            'due_date' => now()->addDays(7) // 7 days to pay
        ]);

        // Try to charge from user balance
        if ($userBalance->hasSufficientBalance($amount)) {
            $userBalance->deductFunds(
                $amount,
                "Service billing: {$service->name}",
                ['service_id' => $service->_id, 'billing_record_id' => $billingRecord->_id]
            );

            $billingRecord->markAsPaid(null, 'balance');
            
            // Update service billing dates
            $this->updateServiceBillingDates($service);
        } else {
            // Insufficient funds - suspend service if configured
            if (config('servicemanager.billing.auto_suspend_on_insufficient_funds', true)) {
                $service->suspend('Insufficient funds for billing');
            }
        }

        return [
            'amount' => $amount,
            'billing_record' => $billingRecord,
            'paid' => $billingRecord->status === 'paid'
        ];
    }

    /**
     * Calculate prorated billing for resource changes
     */
    public function calculateProratedBilling(Service $service, array $newResources)
    {
        $costDifference = $service->calculateResourceChangeCost($newResources);
        
        if ($costDifference == 0) {
            return ['amount' => 0, 'description' => 'No cost change'];
        }

        // Calculate prorated amount based on remaining time in billing period
        $totalMinutesInPeriod = config('servicemanager.billing_periods.' . $service->billing_period, 43200);
        $remainingMinutes = now()->diffInMinutes($service->next_billing_date);
        
        if ($remainingMinutes <= 0) {
            $proratedAmount = $costDifference;
        } else {
            $proratedRatio = $remainingMinutes / $totalMinutesInPeriod;
            $proratedAmount = $costDifference * $proratedRatio;
        }

        return [
            'amount' => $proratedAmount,
            'cost_difference' => $costDifference,
            'prorated_ratio' => $proratedRatio ?? 1,
            'remaining_minutes' => $remainingMinutes,
            'description' => $costDifference > 0 ? 'Resource upgrade charge' : 'Resource downgrade credit'
        ];
    }

    /**
     * Process resource change billing
     */
    public function processResourceChangeBilling(Service $service, array $newResources)
    {
        $billing = $this->calculateProratedBilling($service, $newResources);
        
        if ($billing['amount'] == 0) {
            return $billing;
        }

        $userBalance = UserBalance::getOrCreateForUser($service->user_id);

        if ($billing['amount'] > 0) {
            // Charge for upgrade
            if (!$userBalance->hasSufficientBalance($billing['amount'])) {
                throw new \Exception('Insufficient balance for resource upgrade');
            }

            $userBalance->deductFunds(
                $billing['amount'],
                $billing['description'],
                [
                    'service_id' => $service->_id,
                    'resource_change' => true,
                    'old_resources' => $service->current_resources,
                    'new_resources' => $newResources
                ]
            );
        } else {
            // Credit for downgrade
            $userBalance->addFunds(
                abs($billing['amount']),
                $billing['description'],
                [
                    'service_id' => $service->_id,
                    'resource_change' => true,
                    'old_resources' => $service->current_resources,
                    'new_resources' => $newResources
                ]
            );
        }

        // Update service resources
        $service->updateResources($newResources);

        return array_merge($billing, ['processed' => true]);
    }

    /**
     * Update service billing dates
     */
    private function updateServiceBillingDates(Service $service)
    {
        $billingPeriodMinutes = config('servicemanager.billing_periods.' . $service->billing_period, 43200);
        
        $service->update([
            'last_billing_date' => $service->next_billing_date ?? now(),
            'next_billing_date' => now()->addMinutes($billingPeriodMinutes)
        ]);
    }

    /**
     * Get billing summary for a user
     */
    public function getUserBillingSummary($userId, $startDate = null, $endDate = null)
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $billingRecords = BillingRecord::byUser($userId)
            ->byDateRange($startDate, $endDate)
            ->get();

        $summary = [
            'total_amount' => $billingRecords->sum('amount'),
            'paid_amount' => $billingRecords->where('status', 'paid')->sum('amount'),
            'pending_amount' => $billingRecords->where('status', 'pending')->sum('amount'),
            'overdue_amount' => $billingRecords->filter->isOverdue()->sum('amount'),
            'record_count' => $billingRecords->count(),
            'records' => $billingRecords
        ];

        return $summary;
    }

    /**
     * Get service billing history
     */
    public function getServiceBillingHistory($serviceId, $limit = 50)
    {
        return BillingRecord::where('service_id', $serviceId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
} 