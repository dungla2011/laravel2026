# 📋 Implementation Summary: Pricing Config Tracking

## 🎯 What Was Implemented

Added automatic **Pricing Change Detection** to VPS usage tracking system. When configuration pricing changes (CPU price, RAM price, etc.), the system now:

1. ✅ Detects the price change automatically
2. ✅ Creates a new record in `vps_usages` table (doesn't just update)
3. ✅ Stores pricing snapshot as JSON for audit trail
4. ✅ Ensures billing accuracy when prices change mid-period

---

## 📁 Files Created/Modified

### Created Files:

#### 1. **Migration: 2025_12_25_add_price_config_to_vps_usages.php**
**Location:** `database/migrations/2025_12_25_add_price_config_to_vps_usages.php`

Adds new column `price_config` (JSON) to `vps_usages` table:
- Stores pricing configuration snapshot
- Immutable after creation
- Enables pricing change detection and audit

**Run:** `php artisan migrate`

#### 2. **Service: VpsPricingService.php**
**Location:** `app/Services/VpsPricingService.php`

New service class with 3 static methods:

- `getPricingConfig()` - Returns array of current prices from config
- `getPricingConfigJson()` - Returns JSON string for DB storage
- `hasPricingChanged($storedConfig)` - Compares stored vs current pricing

**Usage:**
```php
use App\Services\VpsPricingService;

$current = VpsPricingService::getPricingConfig();
$changed = VpsPricingService::hasPricingChanged($lastStoredConfig);
```

#### 3. **Documentation Files:**

- `PRICING_CONFIG_TRACKING.md` - Complete implementation guide
- `PRICING_CHANGE_DETECTION_FLOW.md` - Visual flow & decision tree

---

### Modified Files:

#### 1. **SyncVmwareInstancesCommand.php**
**Location:** `app/Console/Commands/SyncVmwareInstancesCommand.php`

**Changes:**
- Added import: `use App\Services\VpsPricingService;`
- Added pricing extraction before checking for config changes
- Added pricing change detection logic (MD5 hash comparison)
- Modified INSERT conditions to include `$pricingHasChanged`
- Added `price_config` JSON to both INSERT statements
- Enhanced logging: shows "💰 Pricing config changed!" when detected

**Key Logic:**
```php
// Get current pricing
$currentPricingConfig = VpsPricingService::getPricingConfig();

// Check if pricing changed from last record
$pricingHasChanged = !$pricingHasChanged = 
    md5(json_encode($currentPricingConfig)) 
    !== md5(json_encode($lastPricingConfig));

// Include in decision tree
if ((...config same... && ...time <= 10 min... && !$pricingHasChanged) 
    || ...instance types...) {
    // UPDATE old record
} else {
    // INSERT new record
}
```

---

## 🔄 How It Works

### Before (Old System)
```
VPS Running
├─ Check: Hardware changed? (CPU/RAM/Disk)
├─ Check: 10+ minutes passed?
├─ Decision: UPDATE or INSERT
└─ Issue: Pricing changes NOT detected
```

### After (New System)
```
VPS Running
├─ Check: Hardware changed? (CPU/RAM/Disk)
├─ Check: 10+ minutes passed?
├─ Check: Pricing changed? (MD5 hash) ← NEW!
│         (Compare config/vps_config.php prices)
├─ Decision: UPDATE or INSERT
└─ Benefit: Pricing changes automatically trigger new record ✅
```

---

## 📊 Database Schema Change

**New Column Added to `vps_usages`:**

```sql
ALTER TABLE vps_usages ADD COLUMN `price_config` JSON NULL 
AFTER `price_per_minute`;
```

**Sample Data:**
```json
{
  "id": 12345,
  "name": "WebServer-01",
  "instance_id": 101,
  "price_per_minute": 0.29166667,
  "price_config": {
    "n_cpu_core_price": 50,
    "n_ram_gb_price": 30,
    "n_gb_disk_price": 1,
    "n_ip_address_price": 50,
    "n_network_dedicated_mbit_price": 1000
  },
  "timestamp_minute": "2025-12-25 10:00:00",
  "created_at": "2025-12-25 10:01:23"
}
```

---

## 🎬 Typical Workflow

```
1. Admin edits config/vps_config.php
   └─ Changes: n_cpu_core.price from 50 → 75

2. SyncVmwareInstancesCommand runs (next minute)
   ├─ Get current pricing: {cpu:75, ram:30, ...}
   ├─ Get last record's pricing: {cpu:50, ram:30, ...}
   ├─ Compare MD5 hashes: abc123 ≠ def456
   ├─ pricingHasChanged = TRUE
   └─ INSERT new record ✅

3. New record created
   ├─ price_per_minute = recalculated with new price
   ├─ price_config = {cpu:75, ram:30, ...}
   ├─ timestamp_minute = now()
   └─ count_update_status = 0

4. Next minutes continue using new pricing
   └─ Record UPDATEd, no new INSERTs

5. Billing runs
   ├─ Find all records for this instance
   ├─ Old records use old pricing
   ├─ New records use new pricing
   └─ Split billing works correctly ✅
```

---

## 🔍 Verification Queries

### Check If Price_config Is Stored
```sql
SELECT id, created_at, price_per_minute, price_config
FROM vps_usages 
WHERE instance_id = 123 
ORDER BY created_at DESC
LIMIT 5;
```

### Find Pricing Changes
```sql
SELECT 
    v1.id, v1.created_at,
    JSON_EXTRACT(v1.price_config, '$.n_cpu_core_price') as old_cpu_price,
    JSON_EXTRACT(v2.price_config, '$.n_cpu_core_price') as new_cpu_price,
    v2.created_at
FROM vps_usages v1
JOIN vps_usages v2 ON v1.instance_id = v2.instance_id AND v1.id + 1 = v2.id
WHERE v1.price_config != v2.price_config
ORDER BY v1.created_at;
```

### Extract CPU Price from All Records
```sql
SELECT 
    id, created_at,
    JSON_EXTRACT(price_config, '$.n_cpu_core_price') as cpu_price
FROM vps_usages 
WHERE instance_id = 123
ORDER BY created_at;
```

---

## 🚀 Deployment Steps

```bash
# 1. Pull latest code
git pull origin main

# 2. Run migration
php artisan migrate

# 3. Restart sync command
php artisan vmware:sync-instances

# 4. Monitor logs
tail -f storage/logs/laravel.log

# 5. Verify in database
php artisan tinker
>>> DB::table('vps_usages')->latest()->first(['price_config']);
```

---

## ✅ Testing Checklist

- [ ] Migration runs without errors
- [ ] `price_config` column added to `vps_usages`
- [ ] VpsPricingService extracts prices from config correctly
- [ ] Sync command runs and creates records with `price_config`
- [ ] When config prices change, new records are created
- [ ] Logging shows "💰 Pricing config changed!" message
- [ ] Old records have old prices, new records have new prices
- [ ] Billing calculations use correct prices

---

## 📈 Benefits

| Feature | Before | After |
|---------|--------|-------|
| **Pricing Audit** | ❌ Lost when changed | ✅ Stored in each record |
| **Billing Accuracy** | ⚠️ Manual adjustment needed | ✅ Automatic split billing |
| **Change Detection** | ❌ Must monitor manually | ✅ Automatic via hash comparison |
| **Compliance** | ⚠️ Hard to prove charges | ✅ Full audit trail |
| **Transparency** | ❌ Pricing hidden | ✅ Visible in DB JSON |

---

## 🔧 Configuration Source

**File:** `config/vps_config.php`

**Extracted Prices:**
```php
[
    'n_cpu_core_price' => 50,              // K (50,000 VND)
    'n_ram_gb_price' => 30,                // K (30,000 VND)
    'n_gb_disk_price' => 1,                // K (1,000 VND)
    'n_ip_address_price' => 50,            // K (50,000 VND)
    'n_network_dedicated_mbit_price' => 1000, // K (1M VND per 100Mbps)
]
```

These values are loaded every sync run and compared with last stored values.

---

## 🎓 How to Use in Code

```php
// Get current config
use App\Services\VpsPricingService;

$pricingConfig = VpsPricingService::getPricingConfig();
// Returns:
// [
//     'n_cpu_core_price' => 50,
//     'n_ram_gb_price' => 30,
//     ...
// ]

// Check if changed
$lastUsageConfig = json_decode($lastUsage->price_config, true);
$hasChanged = VpsPricingService::hasPricingChanged($lastUsageConfig);

// Store in DB
$jsonConfig = json_encode($pricingConfig);
DB::table('vps_usages')->insert([
    'price_config' => $jsonConfig,
    // ... other fields
]);
```

---

## 📞 Support

For questions about:
- **Pricing logic:** See `config/vps_config.php`
- **Service methods:** See `app/Services/VpsPricingService.php`
- **Sync logic:** See `app/Console/Commands/SyncVmwareInstancesCommand.php`
- **Billing flow:** See `PRICING_CHANGE_DETECTION_FLOW.md`

