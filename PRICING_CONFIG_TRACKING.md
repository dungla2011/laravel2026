# 💰 Pricing Config Tracking in VPS Usage

## 📋 Overview

Added a new **`price_config`** column to `vps_usages` table to track the pricing configuration at the time each usage record is created. This enables:

1. ✅ Audit trail of pricing changes over time
2. ✅ Detect when pricing config changes and automatically create new records
3. ✅ Calculate accurate billing even when pricing changes mid-month

---

## 🔧 Implementation

### 1. Migration: Add price_config Column
**File:** `database/migrations/2025_12_25_add_price_config_to_vps_usages.php`

```php
Schema::table('vps_usages', function (Blueprint $table) {
    $table->json('price_config')
        ->nullable()
        ->after('price_per_minute')
        ->comment('Pricing config snapshot từ config/vps_config.php');
});
```

**Stores JSON containing:**
```json
{
  "n_cpu_core_price": 50,
  "n_ram_gb_price": 30,
  "n_gb_disk_price": 1,
  "n_ip_address_price": 50,
  "n_network_dedicated_mbit_price": 1000
}
```

---

### 2. Service: VpsPricingService
**File:** `app/Services/VpsPricingService.php`

**Main Methods:**

#### `getPricingConfig()`
```php
// Returns array of current pricing from config/vps_config.php
[
    'n_cpu_core_price' => 50,
    'n_ram_gb_price' => 30,
    'n_gb_disk_price' => 1,
    'n_ip_address_price' => 50,
    'n_network_dedicated_mbit_price' => 1000,
]
```

#### `getPricingConfigJson()`
```php
// Returns JSON string for storage in database
"{"n_cpu_core_price":50,"n_ram_gb_price":30,...}"
```

#### `hasPricingChanged($storedConfig)`
```php
// Compare current pricing with stored pricing
// Returns: true if any price changed, false otherwise

// Example:
$lastUsage = DB::table('vps_usages')->latest()->first();
$priceChanged = VpsPricingService::hasPricingChanged($lastUsage->price_config);
```

---

### 3. Updated: SyncVmwareInstancesCommand
**File:** `app/Console/Commands/SyncVmwareInstancesCommand.php`

#### Added Import
```php
use App\Services\VpsPricingService;
```

#### Enhanced Logic

**Before each vps_usages insert/update:**

```php
// 1. Get current pricing config
$currentPricingConfig = VpsPricingService::getPricingConfig();
$currentPricingHash = md5(json_encode($currentPricingConfig));

// 2. Get last record's pricing
$lastPricingConfig = $lastUsage->price_config 
    ? json_decode($lastUsage->price_config, true) 
    : VpsPricingService::getPricingConfig();
$lastPricingHash = md5(json_encode($lastPricingConfig));

// 3. Check if pricing changed
$pricingHasChanged = $currentPricingHash !== $lastPricingHash;

// 4. Determine if need new record
// Now checks: config hardware change OR time > 10min OR PRICING CHANGE
if (($currentConfigHash === $lastConfigHash && $timeSinceSame <= 10 && !$pricingHasChanged)
    || $instance->type == 'backup_glx' 
    || $instance->type == 'ignore_compare_config'
) {
    // Just UPDATE the last record
} else {
    // INSERT new record (pricing change detected!)
}
```

#### INSERT Statements Include price_config
```php
DB::table('vps_usages')->insert([
    'name' => $vm->name,
    'instance_id' => $instance->id,
    'price_per_minute' => $instance->price_per_minute,
    'price_config' => json_encode($currentPricingConfig),  // 🆕 NEW
    // ... other fields
]);
```

#### Logging for Pricing Changes
```php
if ($pricingHasChanged) {
    $this->line("  💰 Pricing config changed! Inserted new vps_usages snapshot");
} else {
    $this->line("  📊 Inserted vps_usages snapshot (config changed or 10+ min passed)");
}
```

---

## 📊 Usage Example

### Scenario: Price increases mid-month

```
2025-12-25 10:00 - VPS running
├─ Pricing: CPU=50, RAM=30, Disk=1
├─ Record 1: price_config = {...CPU:50...}
├─ price_per_minute = 0.29

2025-12-25 11:00 - Same VPS, same hardware
├─ Pricing: CPU=50, RAM=30, Disk=1 (no change)
├─ Update Record 1: count_update_status++
└─ NO NEW RECORD

2025-12-25 15:00 - Admin updates pricing
└─ config/vps_config.php: CPU price: 50 → 75 🔥

2025-12-25 16:00 - Next sync detects pricing change
├─ Hash mismatch detected!
├─ Pricing: CPU=75, RAM=30, Disk=1 (CHANGED!)
├─ Record 2: price_config = {...CPU:75...}
├─ price_per_minute = 0.44 (recalculated)
└─ NEW RECORD INSERTED ✅

Invoice for month:
├─ 2025-12-25 10:00 to 15:00: 50K per hour (old pricing)
├─ 2025-12-25 16:00 to 31st: 75K per hour (new pricing)
└─ Accurate split billing! ✅
```

### Query to Check Pricing Changes
```sql
-- Find all times pricing was changed
SELECT 
    id, 
    instance_id, 
    created_at, 
    price_config,
    LEAD(price_config) OVER (PARTITION BY instance_id ORDER BY created_at) as next_config
FROM vps_usages
WHERE price_config IS NOT NULL
ORDER BY instance_id, created_at;

-- Filter to changes only
SELECT 
    v1.*,
    v2.price_config as new_config,
    v2.created_at as changed_at
FROM vps_usages v1
JOIN vps_usages v2 ON v1.instance_id = v2.instance_id 
    AND v1.id + 1 = v2.id
WHERE v1.price_config != v2.price_config;
```

---

## ✅ Benefits

| Feature | Benefit |
|---------|---------|
| **Pricing Audit Trail** | See when prices changed historically |
| **Billing Accuracy** | Charge correct price based on when service was used |
| **Change Detection** | Auto-insert new record when config changes |
| **Transparency** | Each record shows exact prices used for that moment |
| **Compliance** | Full transparency for invoicing audits |

---

## 🔄 Database Schema Update

**Before:**
```sql
vps_usages: [..., price_per_minute, power_state, created_at, ...]
```

**After:**
```sql
vps_usages: [..., price_per_minute, price_config (JSON), power_state, created_at, ...]
```

**Example Record:**
```json
{
  "id": 12345,
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

## 🚀 How to Deploy

1. **Run Migration:**
   ```bash
   php artisan migrate
   ```

2. **Restart Sync Service:**
   ```bash
   php artisan vmware:sync-instances
   ```

3. **Monitor Logs:**
   ```bash
   tail -f storage/logs/laravel.log | grep -i pricing
   ```

---

## 📝 Notes

- **Backward Compatible:** Old records without `price_config` are handled gracefully
- **Performance:** JSON comparison uses MD5 hashing for efficiency
- **Flexibility:** Pricing service can be extended to include other configs
- **Audit Ready:** Every change is timestamped and stored

