# 💰 Pricing Change Detection Flow

## 🔄 Process Flow Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│ SyncVmwareInstancesCommand runs (mỗi phút)                    │
└────────────────┬────────────────────────────────────────────────┘
                 ↓
         Get Current VMware Status
         Get VPS Instance Config
                 ↓
    ┌─────────────────────────────────────┐
    │ Get Last vps_usages Record          │
    │ Extract its price_config (JSON)     │
    └────────────────┬────────────────────┘
                     ↓
    ┌──────────────────────────────────────────────────────────┐
    │ Step 1: Get Current Pricing Config from config file      │
    │ VpsPricingService::getPricingConfig()                    │
    │                                                           │
    │ Returns:                                                 │
    │ ┌──────────────────────────────────────────────────┐    │
    │ │ n_cpu_core_price: 50                             │    │
    │ │ n_ram_gb_price: 30                               │    │
    │ │ n_gb_disk_price: 1                               │    │
    │ │ n_ip_address_price: 50                           │    │
    │ │ n_network_dedicated_mbit_price: 1000             │    │
    │ └──────────────────────────────────────────────────┘    │
    └────────────────┬──────────────────────────────────────────┘
                     ↓
    ┌────────────────────────────────────────────────────────────┐
    │ Step 2: Get Last Record's Pricing Config                   │
    │                                                             │
    │ IF $lastUsage->price_config EXISTS:                        │
    │   $lastPricingConfig = json_decode($lastUsage->price_config)
    │ ELSE:                                                       │
    │   $lastPricingConfig = VpsPricingService::getPricingConfig()
    │                                                             │
    │ Example:                                                    │
    │ ┌──────────────────────────────────────────────────┐       │
    │ │ n_cpu_core_price: 50 ← from last record          │       │
    │ │ n_ram_gb_price: 30                               │       │
    │ │ n_gb_disk_price: 1                               │       │
    │ │ n_ip_address_price: 50                           │       │
    │ │ n_network_dedicated_mbit_price: 1000             │       │
    │ └──────────────────────────────────────────────────┘       │
    └────────────────┬─────────────────────────────────────────┘
                     ↓
    ┌────────────────────────────────────────────────────────────┐
    │ Step 3: Compare Pricing (MD5 Hash)                         │
    │                                                             │
    │ $currentPricingHash = md5(json($currentPricingConfig))    │
    │ $lastPricingHash = md5(json($lastPricingConfig))          │
    │                                                             │
    │ $pricingHasChanged = ($currentPricingHash !== $lastPricingHash)
    │                                                             │
    │ Example:                                                    │
    │ Before: abc123...  (CPU=50, RAM=30)                        │
    │ After:  def456...  (CPU=75, RAM=30)  ← DIFFERENT!          │
    │ Result: $pricingHasChanged = TRUE ✅                        │
    └────────────────┬─────────────────────────────────────────┘
                     ↓
    ┌────────────────────────────────────────────────────────────┐
    │ Step 4: Check All Change Conditions                        │
    │                                                             │
    │ if (($hardwareConfigHash === $lastConfigHash              │
    │     && $timeSinceSame <= 10 minutes                         │
    │     && !$pricingHasChanged)                                 │
    │     || $instance->type == 'backup_glx'                     │
    │     || $instance->type == 'ignore_compare_config')         │
    │ {                                                           │
    │     // No change detected                                   │
    │     // UPDATE existing record (count_update_status++)       │
    │ } else {                                                    │
    │     // Change detected! (hardware OR time OR PRICING)       │
    │     // INSERT NEW RECORD                                    │
    │ }                                                           │
    │                                                             │
    │ CHANGE DETECTED IF ANY OF:                                 │
    │ ✓ Hardware changed (CPU/RAM/Disk/PowerState)               │
    │ ✓ Time > 10 minutes                                         │
    │ ✓ Pricing changed  ← NEW!                                  │
    └────────────────┬─────────────────────────────────────────┘
                     ↓
    ┌────────────────────────────────────────────────────────────┐
    │ Step 5: INSERT or UPDATE                                   │
    │                                                             │
    │ INSERT INTO vps_usages {                                   │
    │     name, instance_id, vmware_vm_id,                       │
    │     cpu, ram_gb, disk_gb,                                  │
    │     price_per_minute,                                      │
    │     price_config: json_encode($currentPricingConfig),  🆕 │
    │     timestamp_minute, power_state,                         │
    │     ... other fields                                        │
    │ }                                                           │
    │                                                             │
    │ IF $pricingHasChanged:                                      │
    │     Log: "💰 Pricing config changed! New record created"   │
    │ ELSE:                                                       │
    │     Log: "📊 Config changed or 10+ min passed"              │
    └────────────────┬─────────────────────────────────────────┘
                     ↓
          ✅ Record inserted with pricing snapshot
          ✅ Billing will use correct prices
          ✅ Audit trail preserved
```

---

## 🎯 Decision Tree

```
Does $lastUsage exist?
├─ NO → Insert new record with current pricing ✅
│
└─ YES → Compare configs
    │
    ├─ Hardware changed? (CPU/RAM/Disk/Power)
    │  ├─ YES → Insert new record ✅
    │  │
    │  └─ NO → Next check...
    │
    ├─ Time > 10 minutes?
    │  ├─ YES → Insert new record ✅
    │  │
    │  └─ NO → Next check...
    │
    └─ Pricing changed? (MD5 hash comparison) ← NEW!
       ├─ YES → Insert new record ✅ 💰
       │         (price_config stored as JSON)
       │
       └─ NO → Just UPDATE last record
               (count_update_status++)
```

---

## 📊 Example Timeline

```
Timeline of VPS Usage with Pricing Change:

10:00 AM - VPS starts running
├─ Config: CPU=2, RAM=4GB, Disk=100GB
├─ Pricing: CPU=$50, RAM=$30, Disk=$1
├─ price_per_minute = 0.29166667
├─ Record #1 created
│  └─ price_config = {cpu:50, ram:30, disk:1, ...}
│
10:01-10:59 (59 times) - Same config
├─ Pricing: No change
├─ Record #1 UPDATED (count_update_status = 1,2,3...59)
└─ No new records created

11:00 AM - Admin updates pricing 🔥
└─ config/vps_config.php modified:
   └─ n_cpu_core.price: 50 → 75 (increased!)

11:01 AM - Next sync run
├─ Compare pricing hashes:
│  ├─ Last: {cpu:50, ram:30, disk:1} → MD5: abc123
│  ├─ Current: {cpu:75, ram:30, disk:1} → MD5: def456
│  └─ MISMATCH! 🔔
├─ price_per_minute recalculated = 0.4375 (new)
├─ Record #2 created ✅
│  └─ price_config = {cpu:75, ram:30, disk:1, ...}
├─ Log: "💰 Pricing config changed! Inserted new record"
│
11:02-11:59 (59 times) - Updated pricing
├─ Record #2 UPDATED (count_update_status++)
└─ All charges use new price ($0.4375/min)

Billing Summary:
├─ 10:00-11:00 (60 min) × $0.29166667 = $17.50
├─ 11:01-11:59 (59 min) × $0.4375 = $25.81
└─ Total = $43.31

✅ Accurate billing based on when price was active!
```

---

## 🔍 Inspection Queries

### Find All Pricing Changes
```sql
-- Show when pricing was different between records
SELECT 
    v1.id as 'Record #1',
    v1.created_at,
    v1.price_config,
    v2.id as 'Record #2',
    v2.created_at,
    v2.price_config,
    'PRICING CHANGED' as status
FROM vps_usages v1
JOIN vps_usages v2 ON 
    v1.instance_id = v2.instance_id 
    AND v1.id < v2.id
WHERE v1.price_config != v2.price_config
    AND v2.id - v1.id = 1  -- consecutive records
ORDER BY v1.created_at;
```

### Get Current Pricing Applied to VPS
```sql
-- Latest pricing config for specific VPS
SELECT 
    instance_id,
    created_at,
    price_config,
    price_per_minute
FROM vps_usages
WHERE instance_id = 123
ORDER BY created_at DESC
LIMIT 1;
```

### Extract Specific Price from JSON
```sql
-- Show CPU price from all records
SELECT 
    instance_id,
    created_at,
    JSON_EXTRACT(price_config, '$.n_cpu_core_price') as cpu_price,
    JSON_EXTRACT(price_config, '$.n_ram_gb_price') as ram_price
FROM vps_usages
WHERE instance_id = 123
ORDER BY created_at;
```

---

## 💾 Database Fields

```
vps_usages table:

price_per_minute: DECIMAL(18,8)
  └─ Calculated price per minute
     Formula: (cpu×price_cpu + ram×price_ram + ...) / 1440
     Updated when pricing config changes
     Used to charge user

price_config: JSON (🆕 NEW)
  └─ Snapshot of pricing config at this moment
     Contains: {
       "n_cpu_core_price": 50,
       "n_ram_gb_price": 30,
       "n_gb_disk_price": 1,
       "n_ip_address_price": 50,
       "n_network_dedicated_mbit_price": 1000
     }
     Used for audit & verification
     Immutable after created
```

---

## 🚀 How VpsPricingService Works

```php
// 1. Get current pricing from config
$current = VpsPricingService::getPricingConfig();
// Returns: ['n_cpu_core_price' => 50, 'n_ram_gb_price' => 30, ...]

// 2. Store as JSON in DB
$json = VpsPricingService::getPricingConfigJson();
// Returns: '{"n_cpu_core_price":50,"n_ram_gb_price":30,...}'

// 3. Check if pricing changed
$hasChanged = VpsPricingService::hasPricingChanged($lastStoredConfig);
// Returns: true or false

// 4. Use for comparison
if ($hasChanged) {
    // Insert new record, don't update old one
}
```

---

## ✅ Testing

```bash
# Test 1: Check pricing extraction
php artisan tinker
>>> use App\Services\VpsPricingService;
>>> VpsPricingService::getPricingConfig();
=> ["n_cpu_core_price" => 50, "n_ram_gb_price" => 30, ...]

# Test 2: Run sync and check logs
php artisan vmware:sync-instances --domain=vcenter.local --uid=admin --pw=pass

# Look for:
# ✅ "📊 Inserted vps_usages snapshot" = no pricing change
# ✅ "💰 Pricing config changed!" = pricing change detected

# Test 3: Verify price_config in DB
php artisan tinker
>>> DB::table('vps_usages')->latest()->first(['price_per_minute', 'price_config']);
```

