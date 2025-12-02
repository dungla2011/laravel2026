# Balance Sync Documentation

## Overview

Hệ thống balance sử dụng **denormalized schema** với 2 bảng:

- `user_balance_transactions`: Chi tiết từng giao dịch (audit trail)
- `user_balances`: Tóm tắt số dư hiện tại (denormalized cache)

**Tại sao?** Performance - query `user_balances.balance` là O(1), thay vì SUM 10,000+ transactions.

## Flow: Khi charge VPS

```
User uses VPS 1 minute ($50,000)
         ↓
BalanceService::chargeService(userId, 50000, 'vps')
         ↓
DB::transaction() {  ← ATOMIC: All-or-nothing
    1. Lock user_balance (pessimistic lock)
    2. Validate balance >= 50000
    3. INSERT user_balance_transactions (-50000)
    4. UPDATE user_balance (-50000)
    5. Check & suspend if needed
}
         ↓
✅ Both tables updated atomically
❌ Any error → ROLLBACK both
```

## Sync Guarantee Methods

### 1. DB::transaction() (Real-time Protection)

**Code:**
```php
DB::transaction(function () use ($userId, $amount) {
    // Lock to prevent race conditions
    $userBalance = UserBalance::lockForUpdate()->find($userId);
    
    // Check balance
    $balanceBefore = $userBalance->balance;
    if ($balanceBefore < $amount) {
        throw new Exception("Insufficient balance");
    }
    
    // Both inserts/updates are atomic
    UserBalanceTransaction::create([...]);
    $userBalance->update(['balance' => $balanceBefore - $amount]);
    
    // Commit or rollback together
});
```

**Benefit:** Prevents race condition where 2 concurrent charges both pass validation

**Example Desync Without Lock:**
```
Balance: 100,000

Charge 1:         Charge 2:
Check: 100k >= 80k ✓
                  Check: 100k >= 80k ✓
INSERT trans -80k
                  INSERT trans -80k
UPDATE 20k        
                  UPDATE 20k
                  
Result: Balance = 20k (should be -60k!)
```

### 2. Pessimistic Lock (lockForUpdate())

**Mechanism:**
```php
$userBalance = UserBalance::lockForUpdate()->find($userId);
// Acquires FOR UPDATE lock in MySQL
// Other transactions WAIT until this one commits
```

**Lock Timeline:**
```
Transaction 1:
  acquire lock on row 5 ────────────────────┐
  check balance ✓                          │
  insert transaction                       │
  update balance                           │
  commit & release lock ────────────────────┤
                                           │
Transaction 2:                             │
  acquire lock on row 5 ═════ WAIT HERE ═══┘
  check balance ✓
  insert transaction
  update balance
  commit
```

### 3. Nightly Verification Job

**Schedule:**
```php
// app/Console/Kernel.php
$schedule->command('balance:verify-sync --fix')
    ->dailyAt('02:00')  // 2 AM every night
```

**What it does:**
```
For each user:
  actual_balance = SUM(user_balance_transactions.amount)
  stored_balance = user_balances.balance
  
  if actual_balance != stored_balance:
    Log discrepancy
    Update stored_balance to actual_balance
```

**Run manually:**
```bash
# Verify all users (report only)
php artisan balance:verify-sync

# Verify + auto-fix
php artisan balance:verify-sync --fix

# Verify specific user
php artisan balance:verify-sync --user-id=5 --fix

# Full rebuild (1st of month at 03:00)
php artisan balance:verify-sync --rebuild
```

## Desync Risk Analysis

### Scenario 1: Transaction Failure

```
INSERT transaction ✅
UPDATE balance ❌ (connection lost, constraint violation, etc)

Without DB::transaction():
  ⚠️ DESYNC: Transaction recorded but balance not updated

With DB::transaction():
  ✅ Auto-ROLLBACK: Both operations undo
```

### Scenario 2: Concurrent Charges

```
Without pessimistic lock:
  Charge 1: Read balance = 100k → Update to 50k
  Charge 2: Read balance = 100k → Update to 50k
  ⚠️ Both succeeded, but balance wrong!

With lockForUpdate():
  Charge 1: Lock row → Read 100k → Debit 50k
  Charge 2: WAIT for lock...
           Once released, read 50k → Debit 20k
  ✅ Correct!
```

### Scenario 3: Manual Corruption

```
MySQL: UPDATE user_balances SET balance = 0 WHERE user_id = 5

Nightly job runs:
  1. Detect: balance=0 but transactions sum to -500k
  2. Log discrepancy
  3. Auto-fix: Update balance to -500k
  ✅ Corrected
```

## Testing

Run tests to verify sync behavior:

```bash
# Run all balance sync tests
php artisan test tests/Feature/BalanceSyncTest.php

# Run specific test
php artisan test tests/Feature/BalanceSyncTest.php --filter=test_concurrent_charges_dont_overdraw

# Test with coverage
php artisan test tests/Feature/BalanceSyncTest.php --coverage
```

**Test Cases Included:**
- ✅ Single charge updates both tables atomically
- ✅ Insufficient balance throws exception (no partial data)
- ✅ Concurrent charges handled correctly with locking
- ✅ Verify sync detects mismatches
- ✅ Balance before/after values correct
- ✅ Recharge updates both tables
- ✅ Rebuild from transactions works
- ✅ Transaction rollback on error
- ✅ Get discrepancy report

## Monitoring & Alerts

### Daily Report

```bash
# Get discrepancy report
php artisan balance:verify-sync
```

**Output:**
```
Checked: 150 users
✅ Synced: 150 users
⚠️  Fixed: 0 users

[No issues detected]
```

### If Desync Detected

```
Checked: 150 users
✅ Synced: 148 users
⚠️  Fixed: 2 users

Discrepancies found:
│ User ID │ Stored Balance │ Actual Balance │ Difference │
├─────────┼────────────────┼────────────────┼────────────┤
│ 5       │ 500,000        │ 450,000        │ 50,000     │
│ 42      │ 0              │ -100,000       │ 100,000    │
```

**Action:** Investigate why sync failed, check logs for errors during charges

## Best Practices

### ✅ DO:

1. **Always use DB::transaction()** for balance operations
   ```php
   DB::transaction(function () {
       // All DB operations here
   });
   ```

2. **Always lock user_balance** when reading for modification
   ```php
   $userBalance = UserBalance::lockForUpdate()->find($userId);
   ```

3. **Store balance_before/after** in every transaction
   ```php
   'balance_before' => $balanceBefore,
   'balance_after' => $balanceAfter,
   ```

4. **Run nightly verification**
   ```
   Scheduled in Kernel.php at 02:00 daily
   ```

### ❌ DON'T:

1. **Don't update balances without transactions**
   ```php
   // ❌ BAD
   $userBalance->update(['balance' => $newBalance]);
   UserBalanceTransaction::create([...]);
   ```

2. **Don't trust concurrent reads without locking**
   ```php
   // ❌ BAD
   if ($userBalance->balance >= $amount) { ... }
   
   // ✅ GOOD
   $userBalance = UserBalance::lockForUpdate()->find($userId);
   if ($userBalance->balance >= $amount) { ... }
   ```

3. **Don't modify balance directly in migrations or scripts**
   ```php
   // ❌ BAD
   DB::table('user_balances')->update(['balance' => 0]);
   
   // ✅ GOOD
   BalanceService::rebuildAllBalances();
   ```

## Implementation Checklist

- ✅ `BalanceService::chargeService()` with lockForUpdate + transaction
- ✅ `BalanceService::verifyAndFixBalance()` for nightly audit
- ✅ `BalanceService::rebuildAllBalances()` for monthly full rebuild
- ✅ `VerifyBalanceSyncCommand` for CLI execution
- ✅ Scheduled jobs in Kernel.php (02:00 daily, 03:00 monthly)
- ✅ Test suite in `tests/Feature/BalanceSyncTest.php`
- ✅ Logging & error handling in commands

## Monitoring Dashboard (Recommended)

Track these metrics:

1. **Daily Discrepancies:** Should be 0 (if all working)
2. **Fix Count:** Should be 0 (indicates no sync issues)
3. **Transaction Count:** Trending up (normal)
4. **Average Balance:** Monitor for suspicious changes
5. **Charge Failures:** Should be low (insufficient balance reasons)

## Emergency Recovery

If you suspect widespread desync:

```bash
# 1. Check discrepancies
php artisan balance:verify-sync

# 2. If many issues, do full rebuild
php artisan balance:verify-sync --rebuild

# 3. Verify fix
php artisan balance:verify-sync

# 4. Check logs for errors
tail -f storage/logs/laravel.log | grep -i balance
```

**Note:** Rebuild is safe because it only uses `user_balance_transactions` as source of truth.
