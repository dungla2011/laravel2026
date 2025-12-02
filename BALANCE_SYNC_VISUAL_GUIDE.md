# Balance Sync - Visual Guide

## 🔄 VPS Usage → Balance Charge Flow

```
┌─────────────────────────────────────────────────────────────────┐
│ REAL-TIME: User runs VPS instance                              │
└─────────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────────┐
│ CRON JOB (every minute):                                        │
│ → Query powered-on VPS instances                                │
│ → Create vps_usage record for each minute of usage              │
└─────────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────────┐
│ BATCH PROCESSING (hourly):                                      │
│ → Get all vps_usage records for past hour                       │
│ → Group by user                                                 │
│ → Calculate total charge per user                               │
└─────────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────────┐
│ FOR EACH USER: BalanceService::chargeService()                  │
│                                                                  │
│ DB::transaction() {                                             │
│   1. Lock user_balance row                                      │
│   2. Check: balance >= charge_amount                            │
│      ├─ YES → Continue                                          │
│      └─ NO → Throw exception, rollback, suspend                │
│   3. INSERT user_balance_transactions (detail log)              │
│      └─ Amount: -charge_amount                                  │
│      └─ balance_before: current balance                         │
│      └─ balance_after: current - charge                         │
│   4. UPDATE user_balances (denormalized cache)                  │
│      └─ balance: balance - charge_amount                        │
│      └─ total_spent: total_spent + charge_amount                │
│      └─ last_transaction_at: now()                              │
│   5. Check if balance < 0                                       │
│      └─ YES → Suspend VPS + log                                 │
│      └─ NO → Continue                                           │
│ } ← ALL-OR-NOTHING COMMIT/ROLLBACK                              │
└─────────────────────────────────────────────────────────────────┘
    ↓
┌─────────────────────────────────────────────────────────────────┐
│ RESULT:                                                          │
│ ✅ Both tables updated atomically                               │
│ ❌ If any error: ROLLBACK both (no partial data)                │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔒 Sync Guarantee - 3 Layers

```
┌─────────────────────────────────────────────────────────────────┐
│ LAYER 1: DB::transaction() (Real-time)                          │
├─────────────────────────────────────────────────────────────────┤
│ Prevents: Partial updates (one operation fails)                 │
│ Mechanism: Begin transaction → Do all operations → Commit/Rollback│
│ Timeline:                                                        │
│   INSERT user_balance_transactions ──┐                          │
│                                      ├─ ATOMIC                  │
│   UPDATE user_balances             ──┘                          │
│ If any fails: ROLLBACK ALL                                      │
│ Benefit: No orphaned records                                    │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ LAYER 2: Pessimistic Lock (Concurrency Safety)                  │
├─────────────────────────────────────────────────────────────────┤
│ Prevents: Race condition (2 charges both pass validation)        │
│ Mechanism: SELECT ... FOR UPDATE                                │
│ Timeline (2 concurrent charges):                                │
│                                                                  │
│   Charge 1: Lock row 5      Charge 2: Try lock row 5            │
│     ↓                                    ↓ WAIT                 │
│   Check balance ✓                                                │
│   Debit                      ← Charge 1 releases lock           │
│   Unlock ──────────────→                                         │
│                         Check balance (new balance!)            │
│                         If fail → Reject                        │
│                                                                  │
│ Benefit: Serialized access, no overdraft                        │
└─────────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────────┐
│ LAYER 3: Nightly Verification (Detection & Recovery)            │
├─────────────────────────────────────────────────────────────────┤
│ Prevents: Undetected desync from crashes                        │
│ Mechanism: Verify stored balance = SUM(transactions)            │
│ Schedule: 02:00 AM DAILY                                        │
│ Timeline:                                                        │
│                                                                  │
│ Normal day:                                                     │
│   Charge 1: INSERT + UPDATE ✅                                  │
│   Charge 2: INSERT + UPDATE ✅                                  │
│   02:00 AM: Verify → All synced ✅                              │
│                                                                  │
│ Crash scenario:                                                 │
│   Charge 1: INSERT ✅ + UPDATE ❌ (connection lost)            │
│   02:00 AM: Detect mismatch                                     │
│            → Auto-fix: Update balance from transactions         │
│            → Log issue for audit                                │
│                                                                  │
│ Benefit: Auto-recovery, no manual intervention                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📊 Race Condition Example

### Without Pessimistic Lock ❌
```
Balance: 100,000 VND

PROCESS 1:            PROCESS 2:
Check: 100k >= 80k ✓  
                      Check: 100k >= 80k ✓
  Debit -80k        
                        Debit -80k
  → 20k              
                        → 20k
  
RESULT: Both succeed! ❌
Final balance: 20k (should be -60k!)
OVERDRAFT!
```

### With Pessimistic Lock ✅
```
Balance: 100,000 VND

PROCESS 1:                PROCESS 2:
Lock row 5 ✓            Try lock row 5
  ↓                        ↓ WAIT...
Check: 100k >= 80k ✓
  Debit -80k
  → 20k
Release lock ─────────→ Lock acquired
                       Check: 20k >= 80k ✗
                       FAIL → Throw exception
                       Release lock

RESULT: One succeeds, one fails ✅
Final balance: 20k (CORRECT!)
NO OVERDRAFT!
```

---

## 📈 Desync Scenario & Recovery

### Scenario: Server Crash During Balance Update
```
INSERT user_balance_transactions:
  CREATE {
    user_id: 5,
    amount: -50000,
    balance_before: 100000,
    balance_after: 50000
  } ✅ SUCCESS

UPDATE user_balances:
  SET balance = 50000 ❌ CONNECTION LOST (crash!)

Database state after crash:
  user_balance_transactions: Has -50k record ✅
  user_balances: Still shows 100k ❌
  
  DESYNC!
  Actual balance (from ledger): 100k - 50k = 50k
  Stored balance: 100k
```

### Recovery: Nightly Job (02:00 AM)
```
VerifyBalanceSyncCommand runs:

For user 5:
  actual = SUM(user_balance_transactions.amount)
         = -50000
         = balance of 50k

  stored = user_balances.balance
         = 100k

  MISMATCH DETECTED! ⚠️
  
  Action taken:
  UPDATE user_balances
    SET balance = 50k
    WHERE user_id = 5
  
  Logged:
  {
    user_id: 5,
    status: 'FIXED',
    stored_balance: 100k,
    actual_balance: 50k,
    discrepancy: 50k,
    fixed_at: 2024-11-24 02:05:00
  }
  
Result: ✅ RECOVERED
```

---

## 🔄 Complete Transaction Lifecycle

```
MINUTE 0:00
└─ VPS instance runs (power_state = 'running')

MINUTE 0:01 - 1:00 (every minute)
└─ Cron job creates vps_usage records
   └─ 60 records × $50,000/min = $3,000,000 total

HOUR 01:00 (batch processing)
└─ BalanceService::chargeService() runs for user
   │
   ├─ DB::transaction() starts
   │  ├─ Lock user_balance row
   │  ├─ Check: balance ($10M) >= charge ($3M) ✓
   │  ├─ INSERT user_balance_transactions
   │  │  └─ amount: -3000000
   │  │     balance_before: 10000000
   │  │     balance_after: 7000000
   │  ├─ UPDATE user_balances
   │  │  └─ balance: 7000000
   │  │     total_spent: 3000000
   │  │     last_transaction_at: 2024-11-24 01:00:00
   │  └─ Check balance >= 0: YES, no suspend
   │
   └─ DB::transaction() commits
      └─ Lock released, transaction finalized

NEXT CHARGE
└─ Same process repeats

DAILY 02:00 AM
└─ Nightly verification job runs
   └─ For each user:
      ├─ Calculate actual: SUM(transactions)
      ├─ Compare with stored balance
      ├─ If mismatch:
      │  ├─ Log discrepancy
      │  ├─ Auto-fix
      │  └─ Record in audit log
      └─ Report results

MONTHLY (1st day, 03:00 AM)
└─ Full rebuild job runs
   └─ For each user:
      ├─ Rebuild balance from transactions
      ├─ Recalculate total_recharged
      ├─ Recalculate total_spent
      └─ Update all fields
```

---

## 📋 Command Reference

```bash
# VERIFY ALL USERS (report only)
php artisan balance:verify-sync

Output:
  Checked: 1250 users
  ✅ Synced: 1250 users
  ⚠️  Fixed: 0 users
  [All good!]

---

# VERIFY + AUTO-FIX
php artisan balance:verify-sync --fix

Output:
  Checked: 1250 users
  ✅ Synced: 1248 users
  ⚠️  Fixed: 2 users
  
  Discrepancies found:
  │ User ID │ Stored │ Actual │ Difference │
  ├─────────┼────────┼────────┼────────────┤
  │ 5       │ 500k   │ 450k   │ 50k        │
  │ 42      │ 0      │ -100k  │ 100k       │

---

# VERIFY SPECIFIC USER
php artisan balance:verify-sync --user-id=5 --fix

Output:
  Checked: 1 user
  ✅ Synced: 0 users
  ⚠️  Fixed: 1 user
  
  User 5 fixed: 500k → 450k

---

# FULL REBUILD (EMERGENCY)
php artisan balance:verify-sync --rebuild

Output:
  Rebuilding all balances from transactions...
  ✅ Rebuilt: 1250 users
  
  [All balances recalculated from source of truth]
```

---

## 🎯 State Transitions

```
User Creates VPS Instance
    ↓
[RUNNING]
├─ Balance check: Have money?
│  ├─ YES → Power on instance
│  └─ NO → Reject, show payment screen
│
├─ Every minute: Create vps_usage record
│
├─ Every hour: Charge user
│  ├─ Balance >= charge?
│  │  ├─ YES → Debit balance
│  │  └─ NO → [SUSPENDED]
│  │           Power off instance
│  │           Log suspension
│  │           Send alert
│  │
│  └─ Nightly: Verify sync
│     └─ Auto-fix any desync
│
└─ User Recharges Money
   └─ [ACTIVE]
      Power on instance
      Clear suspension log
      Resume operation
```

---

## ✅ Guarantees Summary

| Guarantee | Mechanism | Timing | Recovery |
|-----------|-----------|--------|----------|
| **Atomicity** | DB::transaction | Real-time | Auto-rollback |
| **Concurrency** | Pessimistic lock | Real-time | Serialized access |
| **Consistency** | Nightly verify | 02:00 AM | Auto-fix |
| **Durability** | MySQL ACID | Real-time | INNODB |

---

## 🚀 Key Takeaways

1. **Every charge is atomic** - Both tables updated together or not at all
2. **Concurrent charges are safe** - Lock prevents overdraft
3. **Desync is auto-detected** - Nightly job catches any issues
4. **Recovery is automatic** - No manual intervention needed
5. **Audit trail is immutable** - Transactions are source of truth
6. **Cache can be rebuilt** - Balance is just a denormalized copy
7. **Monitoring is built-in** - Daily verification, monthly full rebuild

---

**Result:** A bulletproof billing system that guarantees consistency even in the face of crashes, concurrent requests, and hardware failures.
