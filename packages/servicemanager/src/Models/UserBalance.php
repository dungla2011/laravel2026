<?php

namespace YourCompany\ServiceManager\Models;

class UserBalance extends BaseModel
{
    protected $collection = 'user_balances';

    protected $fillable = [
        'user_id',
        'balance',
        'currency',
        'reserved_amount',
        'last_transaction_id',
        'metadata'
    ];

    protected $casts = [
        'balance' => 'decimal:2',
        'reserved_amount' => 'decimal:2',
        'metadata' => 'array'
    ];

    /**
     * Get balance transactions
     */
    public function transactions()
    {
        return $this->hasMany(BalanceTransaction::class, 'user_id', 'user_id');
    }

    /**
     * Get available balance (balance - reserved)
     */
    public function getAvailableBalance()
    {
        return $this->balance - $this->reserved_amount;
    }

    /**
     * Add funds to balance
     */
    public function addFunds($amount, $description = null, $metadata = [])
    {
        $transaction = BalanceTransaction::create([
            'user_id' => $this->user_id,
            'type' => 'credit',
            'amount' => $amount,
            'balance_before' => $this->balance,
            'balance_after' => $this->balance + $amount,
            'description' => $description ?? 'Funds added',
            'metadata' => $metadata
        ]);

        $this->increment('balance', $amount);
        $this->update(['last_transaction_id' => $transaction->_id]);

        return $transaction;
    }

    /**
     * Deduct funds from balance
     */
    public function deductFunds($amount, $description = null, $metadata = [])
    {
        if ($this->getAvailableBalance() < $amount) {
            throw new \Exception('Insufficient balance');
        }

        $transaction = BalanceTransaction::create([
            'user_id' => $this->user_id,
            'type' => 'debit',
            'amount' => $amount,
            'balance_before' => $this->balance,
            'balance_after' => $this->balance - $amount,
            'description' => $description ?? 'Funds deducted',
            'metadata' => $metadata
        ]);

        $this->decrement('balance', $amount);
        $this->update(['last_transaction_id' => $transaction->_id]);

        return $transaction;
    }

    /**
     * Reserve funds (for pending charges)
     */
    public function reserveFunds($amount, $description = null)
    {
        if ($this->getAvailableBalance() < $amount) {
            throw new \Exception('Insufficient balance to reserve');
        }

        $this->increment('reserved_amount', $amount);

        return BalanceTransaction::create([
            'user_id' => $this->user_id,
            'type' => 'reserve',
            'amount' => $amount,
            'balance_before' => $this->balance,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Funds reserved',
            'metadata' => ['reserved_amount' => $amount]
        ]);
    }

    /**
     * Release reserved funds
     */
    public function releaseReservedFunds($amount, $description = null)
    {
        $releaseAmount = min($amount, $this->reserved_amount);
        $this->decrement('reserved_amount', $releaseAmount);

        return BalanceTransaction::create([
            'user_id' => $this->user_id,
            'type' => 'release',
            'amount' => $releaseAmount,
            'balance_before' => $this->balance,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Reserved funds released',
            'metadata' => ['released_amount' => $releaseAmount]
        ]);
    }

    /**
     * Convert reserved funds to actual charge
     */
    public function chargeReservedFunds($amount, $description = null, $metadata = [])
    {
        $chargeAmount = min($amount, $this->reserved_amount);
        
        $this->decrement('reserved_amount', $chargeAmount);
        $this->decrement('balance', $chargeAmount);

        return BalanceTransaction::create([
            'user_id' => $this->user_id,
            'type' => 'charge',
            'amount' => $chargeAmount,
            'balance_before' => $this->balance + $chargeAmount,
            'balance_after' => $this->balance,
            'description' => $description ?? 'Charged from reserved funds',
            'metadata' => array_merge($metadata, ['charged_from_reserved' => true])
        ]);
    }

    /**
     * Check if user has sufficient balance
     */
    public function hasSufficientBalance($amount)
    {
        return $this->getAvailableBalance() >= $amount;
    }

    /**
     * Get or create user balance
     */
    public static function getOrCreateForUser($userId, $currency = 'VND')
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'balance' => 0,
                'currency' => $currency,
                'reserved_amount' => 0,
                'metadata' => []
            ]
        );
    }
} 