<?php

namespace YourCompany\ServiceManager\Models;

class BillingRecord extends BaseModel
{
    protected $collection = 'billing_records';

    protected $fillable = [
        'user_id',
        'service_id',
        'amount',
        'currency',
        'billing_period',
        'billing_start_date',
        'billing_end_date',
        'status',
        'payment_method',
        'transaction_id',
        'description',
        'metadata',
        'paid_at',
        'due_date'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'billing_start_date' => 'datetime',
        'billing_end_date' => 'datetime',
        'paid_at' => 'datetime',
        'due_date' => 'datetime',
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
     * Mark as paid
     */
    public function markAsPaid($transactionId = null, $paymentMethod = null)
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'transaction_id' => $transactionId,
            'payment_method' => $paymentMethod
        ]);

        return $this;
    }

    /**
     * Mark as failed
     */
    public function markAsFailed($reason = null)
    {
        $this->update([
            'status' => 'failed',
            'metadata' => array_merge($this->metadata ?? [], [
                'failure_reason' => $reason,
                'failed_at' => now()->toISOString()
            ])
        ]);

        return $this;
    }

    /**
     * Check if overdue
     */
    public function isOverdue()
    {
        return $this->due_date && $this->due_date->isPast() && $this->status !== 'paid';
    }

    /**
     * Scope for paid records
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    /**
     * Scope for pending records
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for overdue records
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->where('status', '!=', 'paid');
    }

    /**
     * Scope by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('billing_start_date', [$startDate, $endDate]);
    }
} 