# User Balance System - Quick Reference

## Files Created (11 total)

### Models
```
app/Models/UserBalance.php
app/Models/UserRecharge.php
app/Models/UserBalanceTransaction.php
app/Models/BalanceSuspensionLog.php
```

### Meta Classes
```
app/Models/UserBalance_Meta.php
app/Models/UserRecharge_Meta.php
app/Models/UserBalanceTransaction_Meta.php
app/Models/BalanceSuspensionLog_Meta.php
```

### Service
```
app/Services/BalanceService.php
```

### Migration
```
database/migrations/2024_01_20_create_user_balance_tables.php
```

### Documentation
```
BALANCE_SYSTEM_INTEGRATION.md
BALANCE_SYSTEM_SUMMARY.md
BALANCE_SYSTEM_IMPLEMENTATION_CHECKLIST.md
balance_system_demo.php
```

## Core Classes

### BalanceService
```php
// Recharge
BalanceService::createRecharge($userId, $amount, $paymentMethod);
BalanceService::completeRecharge($rechargeId, $gatewayResponse);

// Service charge
BalanceService::chargeService($userId, $amount, $serviceType, $description, $refModel, $refId);

// Status
BalanceService::getBalanceInfo($userId);
BalanceService::getTransactionHistory($userId, $limit);
BalanceService::getRechargeHistory($userId, $limit);

// Suspension
BalanceService::checkAndSuspendIfNeeded($userId);
BalanceService::suspendServices($userId, $reason);
BalanceService::resumeServicesIfEligible($userId);
```

### Models
```php
// UserBalance
$balance->hasEnoughBalance($amount);
$balance->isFrozen();

// UserRecharge
$recharge->isExpired();
$recharge->markAsCompleted($gatewayResponse);

// UserBalanceTransaction
UserBalanceTransaction::createTransaction($userId, $type, $amount, $description, $serviceType, $refModel, $refId);
$transaction->reverse($reason);

// BalanceSuspensionLog
$suspension->isActive();
$suspension->resume();
```

## Database Tables

### user_balance
```
id, user_id (unique), balance, total_recharged, total_spent,
status, is_frozen, frozen_reason, low_balance_threshold,
last_low_balance_alert, last_transaction_at, created_at, updated_at
```

### user_recharge
```
id, user_id, amount, payment_method, transaction_code, status,
paid_at, completed_at, expired_at, gateway_response, ip_address,
created_at, updated_at
```

### user_balance_transaction
```
id, user_id, transaction_type, service_type, reference_model,
reference_id, related_recharge_id, amount, balance_before, balance_after,
description, status, is_reversed, reversed_at, reversed_reason,
transaction_date, created_at, updated_at
```

### balance_suspension_log
```
id, user_id, reason, suspended_at, resumed_at, duration_minutes,
balance_at_suspension, created_at, updated_at
```

## Basic Usage

```php
// 1. User nạp tiền
$recharge = BalanceService::createRecharge(auth()->id(), 500000, 'zalo_pay');

// 2. Payment gateway callback
BalanceService::completeRecharge($recharge->id, $gatewayData);

// 3. Check balance
$info = BalanceService::getBalanceInfo(auth()->id());
// Returns: ['balance' => 500000, 'total_recharged' => 500000, 'total_spent' => 0, ...]

// 4. Charge for service usage
try {
    BalanceService::chargeService(
        userId: 1,
        amount: 1500,
        serviceType: 'vps',
        description: 'VPS per-minute charge',
        referenceModel: 'VpsInstance',
        referenceId: 123
    );
} catch (\Exception $e) {
    // Balance insufficient - service suspended automatically
}

// 5. Get transaction history
$transactions = BalanceService::getTransactionHistory(1, 50);
```

## Transaction Types
- `recharge` - User nạp tiền
- `service_fee` - Chi phí dịch vụ (VPS, Hosting, etc.)
- `refund` - Hoàn lại
- `adjustment` - Điều chỉnh thủ công
- `penalty` - Phạt

## Service Types
- `vps` - Virtual Private Server
- `hosting` - Web Hosting
- `email` - Email Service
- `cdn` - CDN Service
- `ssl` - SSL Certificate
- etc.

## HTTP Status Codes
- `200 OK` - Success
- `402 Payment Required` - Insufficient balance
- `400 Bad Request` - Invalid parameters
- `409 Conflict` - Account frozen/suspended

## Admin Routes (Auto-generated)
- `/_admin/user-balance` - List balances
- `/_admin/user-balance/{id}` - View balance
- `/_admin/user-recharge` - List recharges
- `/_admin/user-balance-transaction` - List transactions
- `/_admin/balance-suspension-log` - List suspensions

## Setup
```bash
# 1. Run migration
php artisan migrate

# 2. Initialize balances for existing users
php artisan tinker
> \App\Models\User::all()->each(fn($u) => \App\Models\UserBalance::firstOrCreate(['user_id' => $u->id], ['balance' => 0]));

# 3. Test
php balance_system_demo.php
```

## Common Queries

```php
// Current balance
$balance = UserBalance::where('user_id', 1)->first()->balance;

// Total spent by service
UserBalanceTransaction::where('user_id', 1)
    ->where('transaction_type', 'service_fee')
    ->groupBy('service_type')
    ->selectRaw('service_type, SUM(ABS(amount)) as total')
    ->get();

// Active suspensions
BalanceSuspensionLog::where('user_id', 1)
    ->whereNull('resumed_at')
    ->get();

// Recent transactions
UserBalanceTransaction::where('user_id', 1)
    ->orderBy('transaction_date', 'desc')
    ->limit(20)
    ->get();
```

## Error Messages

| Error | Cause | Fix |
|-------|-------|-----|
| "User balance not found" | No balance record | Run initialization |
| "Số dư không đủ" | Insufficient funds | User must recharge |
| "Tài khoản đang bị khóa" | Admin frozen account | Admin must unfreeze |
| "Giao dịch đã được hoàn tất" | Duplicate completion | Check recharge status first |

## Integration Checklist

- [ ] Run migration: `php artisan migrate`
- [ ] Initialize user balances
- [ ] Test with demo script: `php balance_system_demo.php`
- [ ] Implement payment gateway callbacks
- [ ] Create API endpoints for recharge
- [ ] Integrate with VPS usage cron job
- [ ] Create email notifications
- [ ] Test end-to-end flow
- [ ] Deploy to production

## Next Steps

1. **Immediate**: Run migration and initialize balances
2. **Week 1**: Implement payment gateway integration
3. **Week 2**: Integrate with VPS cron for per-minute charges
4. **Week 3**: Create admin dashboard and user views
