# User Balance System - Integration Guide

## Overview

Complete balance management system for multi-service payment tracking and usage-based billing. Supports:
- Multiple payment methods (Bank Transfer, Credit Card, PayPal, Zalo Pay, Momo, etc.)
- Service-specific spending tracking (VPS, Hosting, Email, CDN, SSL, etc.)
- Transaction audit trail with debit/credit ledger
- Automatic service suspension on insufficient balance
- Balance threshold alerts

## Files Created

### Models (app/Models/)
1. **UserBalance.php** - Account balance tracking
   - Properties: balance, total_recharged, total_spent, is_frozen, low_balance_threshold
   - Methods: hasEnoughBalance(), isFrozen()
   - Relations: user, recharges, transactions

2. **UserRecharge.php** - Payment history
   - Properties: amount, payment_method, status, paid_at, completed_at, expired_at
   - Methods: isExpired(), markAsCompleted()
   - Status values: pending, processing, completed, failed, cancelled
   - Payment methods: bank_transfer, credit_card, paypal, zalo_pay, momo, etc.

3. **UserBalanceTransaction.php** - Transaction ledger (Debit/Credit)
   - Properties: user_id, transaction_type, service_type, amount, balance_before, balance_after
   - Methods: createTransaction(), reverse() (hoàn lại giao dịch)
   - Transaction types: recharge, service_fee, refund, adjustment, penalty
   - Service types: vps, hosting, email, cdn, ssl, etc.

4. **BalanceSuspensionLog.php** - Service suspension tracking
   - Properties: user_id, reason, suspended_at, resumed_at, duration_minutes, balance_at_suspension
   - Methods: isActive(), resume()

### Service Class (app/Services/)
**BalanceService.php** - Business logic for balance operations
```php
// Create recharge
BalanceService::createRecharge($userId, $amount, 'bank_transfer');

// Complete recharge (after payment gateway confirms)
BalanceService::completeRecharge($rechargeId, $gatewayResponse);

// Charge service usage
BalanceService::chargeService(
    userId: $userId,
    amount: $cost,
    serviceType: 'vps',
    description: 'VPS per-minute charge',
    referenceModel: 'VpsInstance',
    referenceId: $instanceId
);

// Get balance info
BalanceService::getBalanceInfo($userId);

// Get transaction history
BalanceService::getTransactionHistory($userId, 50);
```

### Migration (database/migrations/)
**2024_01_20_create_user_balance_tables.php**
Creates 4 tables:
- user_balance
- user_recharge
- user_balance_transaction
- balance_suspension_log

## Installation Steps

### 1. Run Migration
```bash
php artisan migrate
```

This creates:
- `user_balance` - Current account balances
- `user_recharge` - Recharge/payment history
- `user_balance_transaction` - Full transaction ledger
- `balance_suspension_log` - Service suspension records

### 2. Initialize User Balances
```php
// For existing users, create their balance records
$users = \App\Models\User::whereDoesntHave('userBalance')->get();

foreach ($users as $user) {
    \App\Models\UserBalance::create([
        'user_id' => $user->id,
        'balance' => 0,
        'status' => 1,
        'low_balance_threshold' => 10000,  // 10k VNĐ default
    ]);
}
```

## Usage Examples

### Recharge Flow (Nạp tiền)

```php
// 1. User initiates recharge request
$recharge = BalanceService::createRecharge(
    userId: auth()->id(),
    amount: 500000,  // 500k VNĐ
    paymentMethod: 'zalo_pay'
);
// Returns: UserRecharge object with status='pending'

// 2. Redirect user to payment gateway (e.g., Zalo Pay)
// ...handle payment gateway...

// 3. Payment gateway callback confirms payment
BalanceService::completeRecharge(
    rechargeId: $recharge->id,
    gatewayResponse: $response
);
// This automatically:
// - Updates recharge status to 'completed'
// - Creates UserBalanceTransaction (recharge type)
// - Updates UserBalance (adds amount to balance)
// - Resumes services if suspended

// Result: UserBalance.balance increases by 500,000đ
```

### Service Charge Flow (Chi phí dịch vụ)

```php
// When VPS instance starts or usage is recorded
$cost = 1500;  // 1500đ per minute

try {
    BalanceService::chargeService(
        userId: $vpsInstance->user_id,
        amount: $cost,
        serviceType: 'vps',
        description: "VPS Instance #{$vpsInstance->id} per-minute charge",
        referenceModel: 'VpsInstance',
        referenceId: $vpsInstance->id
    );
    // Success - transaction recorded, balance updated
} catch (\Exception $e) {
    if ($e->getMessage() == "Số dư không đủ") {
        // Suspend services automatically
        BalanceService::suspendServices(
            $vpsInstance->user_id,
            'Số dư không đủ - chi phí dịch vụ'
        );
        
        // Optionally turn off VPS instance
        $vpsInstance->update(['power_state' => 'powered_off']);
    }
}
```

### Check Before Usage

```php
// Before allowing VPS to run or any service usage:
$userBalance = \App\Models\UserBalance::where('user_id', $userId)->first();

if (!$userBalance->hasEnoughBalance(1500)) {
    // Insufficient balance
    return response()->json([
        'error' => 'Số dư không đủ để sử dụng dịch vụ',
        'balance' => $userBalance->balance,
        'required' => 1500,
        'short_of' => 1500 - $userBalance->balance,
    ], 402);  // 402 Payment Required
}

// OK to proceed with service
```

### VPS Usage Integration

Update your VPS cron job (that records `vps_usage` every minute):

```php
// In: Command for recording VPS usage
foreach ($vpsInstances as $instance) {
    if ($instance->power_state != 'powered_on') continue;
    
    // Check balance before charging
    $userBalance = UserBalance::where('user_id', $instance->user_id)->first();
    if (!$userBalance || !$userBalance->hasEnoughBalance($instance->price_per_minute)) {
        // Insufficient balance - suspend
        BalanceService::suspendServices(
            $instance->user_id,
            'Số dư âm - không đủ tiền cho VPS'
        );
        $instance->update(['power_state' => 'powered_off']);
        continue;
    }
    
    // Record usage
    $usage = \App\Models\VpsUsage::create([
        'vps_instance_id' => $instance->id,
        'user_id' => $instance->user_id,
        'timestamp' => now(),
        'cpu_usage' => $cpuUsage,
        'ram_usage' => $ramUsage,
        'disk_usage' => $diskUsage,
        'network_in' => $networkIn,
        'network_out' => $networkOut,
    ]);
    
    // Charge the user
    try {
        BalanceService::chargeService(
            userId: $instance->user_id,
            amount: $instance->price_per_minute,
            serviceType: 'vps',
            description: "VPS usage - {$instance->name}",
            referenceModel: 'VpsUsage',
            referenceId: $usage->id
        );
    } catch (\Exception $e) {
        \Log::error("VPS charge failed", [
            'user_id' => $instance->user_id,
            'instance_id' => $instance->id,
            'error' => $e->getMessage(),
        ]);
    }
}
```

## API Endpoints (To Be Implemented)

### User Recharge
```
POST /api/user-balance/recharge
{
    "amount": 500000,
    "payment_method": "zalo_pay"
}
Response: { id, amount, status, payment_url }
```

### Recharge Callback
```
POST /api/user-balance/recharge-callback
{
    "recharge_id": 123,
    "status": "success",
    "transaction_code": "ZP12345",
    "signature": "..."
}
```

### Get Balance Info
```
GET /api/user-balance/info
Response: {
    balance: 500000,
    total_recharged: 5000000,
    total_spent: 4500000,
    is_suspended: false,
    low_balance_threshold: 10000
}
```

### Transaction History
```
GET /api/user-balance/transactions?limit=50&offset=0
Response: [
    {
        id, transaction_type, service_type, amount,
        balance_before, balance_after, description,
        transaction_date
    }
]
```

## Database Queries Reference

### Get user's current balance
```sql
SELECT balance FROM user_balance WHERE user_id = 1;
```

### Get total spent by service
```sql
SELECT service_type, SUM(ABS(amount)) as total
FROM user_balance_transaction
WHERE user_id = 1 AND transaction_type = 'service_fee'
GROUP BY service_type;
```

### Check if suspended
```sql
SELECT id, reason, suspended_at
FROM balance_suspension_log
WHERE user_id = 1 AND resumed_at IS NULL;
```

### Transaction history with service reference
```sql
SELECT t.id, t.transaction_type, t.service_type, t.amount,
       t.description, t.transaction_date,
       u.name as user_name
FROM user_balance_transaction t
JOIN users u ON t.user_id = u.id
WHERE t.user_id = 1
ORDER BY t.transaction_date DESC
LIMIT 50;
```

## Error Codes

- `402 Payment Required` - Insufficient balance
- `400 Bad Request` - Invalid amount or payment method
- `409 Conflict` - Account frozen or suspended

## Future Enhancements

1. **Email Notifications**
   - Low balance alerts
   - Successful recharge confirmation
   - Service suspension notice

2. **Admin Dashboard**
   - User balance management (view, adjust, freeze)
   - Recharge approval for bank transfers
   - Transaction audit
   - Suspension management

3. **Automated Features**
   - Recurring charges for subscriptions
   - Auto-retry on failed charges
   - Usage forecast and warnings

4. **Integration Points**
   - Payment gateway webhooks (Zalo Pay, Momo, etc.)
   - Email/SMS notifications
   - Service suspension logic for all service types

5. **Reporting**
   - Revenue by payment method
   - Usage by service type
   - Churn analysis (suspended accounts)
   - Top up trends
