# User Balance System - Complete Implementation Summary

## ✅ Completed Tasks

### 1. Database Tables (Migration)
- ✅ File: `database/migrations/2024_01_20_create_user_balance_tables.php`
- Tables created:
  - `user_balance` - Current account balances
  - `user_recharge` - Payment history with gateway support
  - `user_balance_transaction` - Full transaction ledger (Debit/Credit)
  - `balance_suspension_log` - Service suspension tracking

### 2. Model Classes
All 4 models created in `app/Models/`:

#### UserBalance.php
- Properties: balance, total_recharged, total_spent, status, is_frozen, low_balance_threshold
- Methods: hasEnoughBalance($amount), isFrozen()
- Relations: user, recharges(), transactions()

#### UserRecharge.php
- Properties: amount, payment_method, status, paid_at, completed_at, expired_at, gateway_response
- Methods: isExpired(), markAsCompleted($gatewayResponse)
- Payment methods: bank_transfer, credit_card, paypal, zalo_pay, momo, etc.
- Statuses: pending, processing, completed, failed, cancelled

#### UserBalanceTransaction.php
- Properties: user_id, transaction_type, service_type, amount, balance_before, balance_after
- Methods: 
  - createTransaction() - Static method to create transactions
  - reverse() - Hoàn lại (refund) a transaction
  - getTotalSpentByService() - Get spending by service type
- Transaction types: recharge, service_fee, refund, adjustment, penalty
- Service types: vps, hosting, email, cdn, ssl, etc.

#### BalanceSuspensionLog.php
- Properties: user_id, reason, suspended_at, resumed_at, duration_minutes, balance_at_suspension
- Methods: isActive(), resume()

### 3. Service Class
File: `app/Services/BalanceService.php`

Key methods:
- `createRecharge($userId, $amount, $paymentMethod)` - Create recharge request
- `completeRecharge($rechargeId, $gatewayResponse)` - Complete recharge (atomically updates balance)
- `chargeService($userId, $amount, $serviceType, ...)` - Deduct service cost
- `checkAndSuspendIfNeeded($userId)` - Check balance and suspend if negative
- `suspendServices($userId, $reason)` - Suspend services
- `resumeServicesIfEligible($userId)` - Resume if balance positive
- `getBalanceInfo($userId)` - Get current balance info
- `getTransactionHistory($userId, $limit)` - Get transaction history
- `getRechargeHistory($userId, $limit)` - Get recharge history

### 4. Meta Classes (for Dynamic Admin Routing)
All 4 _Meta classes created in `app/Models/`:

- **UserBalance_Meta.php** - Display balance with 4-column format
- **UserRecharge_Meta.php** - Display recharge with status indicator
- **UserBalanceTransaction_Meta.php** - Display transaction with debit/credit color
- **BalanceSuspensionLog_Meta.php** - Display suspension status

Each Meta class provides:
- `getCoreFields()` - Field definitions for admin list
- `_name()` - Custom display format with HTML styling

### 5. Integration Documentation
File: `BALANCE_SYSTEM_INTEGRATION.md`

Includes:
- Complete usage examples
- VPS integration example (cron job)
- API endpoint specifications
- Database query reference
- Error codes and responses
- Future enhancement roadmap

## 📊 System Architecture

### Flow 1: Nạp Tiền (Recharge)
```
1. User initiates recharge
   → BalanceService::createRecharge()
   → Creates UserRecharge with status='pending'

2. Payment gateway processes payment
   → Callback confirmed

3. Complete recharge
   → BalanceService::completeRecharge()
   → Transaction: DB::transaction() ensures atomicity
   → Steps:
      a) Update UserRecharge status='completed'
      b) Create UserBalanceTransaction (recharge type)
      c) Update UserBalance.balance += amount
      d) Resume services if suspended
```

### Flow 2: Chi Phí Dịch Vụ (Service Charge)
```
1. Service usage recorded (e.g., VPS per-minute)
   → BalanceService::chargeService()
   → Check balance sufficient
   → Create transaction (service_fee type)
   → Update UserBalance.balance -= amount
   → Check and suspend if insufficient

2. If balance < 0
   → Create BalanceSuspensionLog
   → Suspend VPS instance (power_state='powered_off')
   → Send alert email (TODO)
```

### Flow 3: Gỡ Tạm Dừng (Service Resume)
```
1. User nạp tiền
   → completeRecharge() calls resumeServicesIfEligible()
   → Check balance > 0
   → Update BalanceSuspensionLog.resumed_at = now()
   → Turn on VPS instances (power_state='powered_on')
   → Send notification email (TODO)
```

## 🚀 Quick Start

### Step 1: Run Migration
```bash
php artisan migrate
```

### Step 2: Initialize User Balances (for existing users)
```php
$users = \App\Models\User::whereDoesntHave('userBalance')->get();
foreach ($users as $user) {
    \App\Models\UserBalance::create([
        'user_id' => $user->id,
        'balance' => 0,
        'status' => 1,
        'low_balance_threshold' => 10000,
    ]);
}
```

### Step 3: Use BalanceService
```php
// Recharge
$recharge = \App\Services\BalanceService::createRecharge(1, 500000, 'zalo_pay');

// After payment confirmed
\App\Services\BalanceService::completeRecharge($recharge->id, $gatewayResponse);

// Check balance
$info = \App\Services\BalanceService::getBalanceInfo(1);
// Returns: [balance, total_recharged, total_spent, is_frozen, is_suspended, ...]

// Charge service
try {
    \App\Services\BalanceService::chargeService(
        userId: 1,
        amount: 1500,
        serviceType: 'vps',
        description: 'VPS per-minute charge',
        referenceModel: 'VpsInstance',
        referenceId: 123
    );
} catch (\Exception $e) {
    // Insufficient balance
    \Log::error($e->getMessage());
}
```

## 🔗 Integration Points

### VPS Usage Tracking (Cron Job)
Location: `app/Console/Commands/RecordVpsUsage.php` (needs to be created)

```php
// For each active VPS instance
foreach ($vpsInstances as $instance) {
    // Check balance before recording
    $userBalance = UserBalance::where('user_id', $instance->user_id)->first();
    if (!$userBalance->hasEnoughBalance($instance->price_per_minute)) {
        BalanceService::suspendServices($instance->user_id);
        $instance->update(['power_state' => 'powered_off']);
        continue;
    }
    
    // Record usage
    VpsUsage::create([...]);
    
    // Charge the user
    BalanceService::chargeService(...);
}
```

### Admin Routes (Already implemented)
- `/_admin/user-balance` - List balances
- `/_admin/user-balance/{action}` - Actions (edit, view, etc.)
- `/_admin/user-recharge` - List recharges
- `/_admin/user-balance-transaction` - List transactions
- `/_admin/balance-suspension-log` - List suspensions

### API Routes (To be implemented)
- `POST /_api/user-balance/recharge` - Create recharge
- `POST /_api/user-balance/recharge-callback` - Payment gateway callback
- `GET /_api/user-balance/info` - Get balance info
- `GET /_api/user-balance/transactions` - Get transaction history

## 📝 Key Design Decisions

1. **Debit/Credit Ledger** - `user_balance_transaction` records ALL changes with before/after balances for audit trail

2. **Atomic Transactions** - `DB::transaction()` ensures balance updates are atomic with transaction creation

3. **Service-Type Tracking** - Each transaction records service_type (vps, hosting, etc.) for revenue tracking

4. **Suspension Log** - Tracks active and historical suspensions with duration, enabling reporting

5. **Reference Fields** - `reference_model` and `reference_id` link transactions to VPS instances, orders, etc.

6. **Reversal Support** - `is_reversed` flag on transactions enables easy refunds and testing

7. **Low Balance Alerts** - `low_balance_threshold` and `last_low_balance_alert` enable email notifications

8. **Account Freezing** - `is_frozen` flag allows manual admin action to freeze accounts

## 🔧 Future Enhancements

### Short-term (Week 1)
- [ ] Create Payment Gateway integration (Zalo Pay, Momo)
- [ ] Create API controllers for recharge and callbacks
- [ ] Create email notifications (low balance, suspension, resume)
- [ ] Create VPS usage cron job with balance checking

### Medium-term (Week 2-3)
- [ ] Admin dashboard for balance management
- [ ] User balance recharge history view
- [ ] Transaction history view with filters
- [ ] Suspension management interface

### Long-term (Month 2)
- [ ] Recurring charges for subscriptions
- [ ] Auto-retry failed charges
- [ ] Usage forecast and warnings
- [ ] Revenue and churn analytics
- [ ] Multi-currency support
- [ ] Promo code/voucher system

## 📊 Testing Checklist

Before going live:
- [ ] Run `php artisan migrate:fresh` on test database
- [ ] Test createRecharge() → completeRecharge() flow
- [ ] Test chargeService() with insufficient balance
- [ ] Test transaction reversal
- [ ] Test service suspension and resume
- [ ] Verify all transaction types are recorded
- [ ] Check transaction history accuracy
- [ ] Test atomic transaction (multi-step)
- [ ] Verify email notifications (TODO)
- [ ] Load test with concurrent charges

## 📞 Support Information

### Error Messages
- "User balance not found" - User doesn't have balance record yet
- "Số dư không đủ" - Insufficient balance for charge
- "Tài khoản đang bị khóa" - Account frozen by admin
- "Giao dịch đã được hoàn tất trước đó" - Recharge already completed

### Database Queries
See `BALANCE_SYSTEM_INTEGRATION.md` for common queries:
- Get current balance
- Get spending by service
- Check suspension status
- Get transaction history with references
