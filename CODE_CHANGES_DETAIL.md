# Code Changes - IP Charging Implementation

## File 1: VpsUsageFeeService.php

### Location
`app/Services/VpsUsageFeeService.php`

### Changes Added (Lines 18-89)

```php
<?php
// ... existing code ...

class VpsUsageFeeService
{
    /**
     * Count chargeable IPs (excluding local IPs and 1 free internet IP)
     * - Local IPs: 192.168.*, 10.*, 172.16-31.* (FREE)
     * - First Internet IP: FREE
     * - Additional Internet IPs: CHARGEABLE
     *
     * @param string $ipListString Comma-separated IP addresses
     * @return int Number of chargeable IPs
     */
    public static function countChargeableIPs($ipListString)
    {
        if (empty($ipListString)) {
            return 0;
        }

        // Split IPs by comma
        $ips = array_filter(array_map('trim', explode(',', $ipListString)));
        
        $internetIpCount = 0;
        
        foreach ($ips as $ip) {
            // Check if IP is local/private
            if (self::isLocalIP($ip)) {
                continue; // Skip local IPs - they are free
            }
            
            // Count internet IPs
            $internetIpCount++;
        }
        
        // First internet IP is free, charge for the rest
        return max(0, $internetIpCount - 1);
    }

    /**
     * Check if IP is a local/private IP address
     *
     * @param string $ip IP address
     * @return bool True if IP is local/private
     */
    private static function isLocalIP($ip)
    {
        // 192.168.0.0/16
        if (preg_match('/^192\.168\./', $ip)) {
            return true;
        }
        
        // 10.0.0.0/8
        if (preg_match('/^10\./', $ip)) {
            return true;
        }
        
        // 172.16.0.0/12
        if (preg_match('/^172\.(1[6-9]|2[0-9]|3[01])\./', $ip)) {
            return true;
        }
        
        // 127.0.0.0/8 (localhost)
        if (preg_match('/^127\./', $ip)) {
            return true;
        }
        
        // 169.254.0.0/16 (link-local)
        if (preg_match('/^169\.254\./', $ip)) {
            return true;
        }
        
        return false;
    }

    // ... rest of existing methods ...
}
```

### What Changed
- ✅ Added `countChargeableIPs()` method
- ✅ Added `isLocalIP()` method
- ✅ No modifications to existing methods
- ✅ Backward compatible

---

## File 2: SyncVmwareInstancesCommand.php

### Location
`app/Console/Commands/SyncVmwareInstancesCommand.php`

### Changes Made (Lines 226-242)

#### BEFORE:
```php
// Around line 233 - OLD CODE
// (hardcoded IP count - removed/replaced)
```

#### AFTER:
```php
// Lines 226-242 - NEW CODE
// Calculate fee based on duration from created_at to lastest_time_the_same
// If power is OFF, CPU and RAM = 0 for fee calculation
$feeCpu = ($lastUsage->power_state === 'POWERED_OFF') ? 0 : $lastUsage->cpu;
$feeRam = ($lastUsage->power_state === 'POWERED_OFF') ? 0 : $lastUsage->ram_gb;

// Count chargeable IPs (free local IPs + 1 free internet IP)
$chargeableIpCount = VpsUsageFeeService::countChargeableIPs($lastUsage->list_ip_address);

$calculatedFee = VpsUsageFeeService::calculateFee(
    $lastPricingConfig,
    $feeCpu,
    $feeRam,
    $lastUsage->disk_gb,
    $durationMinutes,
    $chargeableIpCount
);

$mUpdate = [
    'count_update_status' => DB::raw('count_update_status + 1'),
    'lastest_time_the_same' => now(),
    'timestamp_minute' => now()->startOfMinute(),
    'list_ip_address' => $listIpAddress,
    'last_found_ip' => $lastFoundIpTime,
    'calculated_fee' => $calculatedFee,
];
```

### What Changed
- ✅ Line 233: Added call to `VpsUsageFeeService::countChargeableIPs()`
- ✅ Passes `$lastUsage->list_ip_address` to method
- ✅ Uses returned count in `calculateFee()` call
- ✅ Stores calculated fee in database update

---

## File 3: VpsUsage_Meta.php

### Location
`app/Models/VpsUsage_Meta.php`

### Method: `_calculated_fee($obj, $val, $field)`

### Changes Made (Lines 168-245)

#### OLD CODE (Simplified):
```php
$dailyTotalFee = $dailyCpuFee + $dailyRamFee + $dailyDiskFee;
// ... no IP calculation ...
```

#### NEW CODE (Lines 168-245):
```php
// Calculate daily fee breakdown
$dailyCpuFee = (($priceConfig['n_cpu_core_price'] ?? 0) / 30) * $feeCpu;
$dailyRamFee = (($priceConfig['n_ram_gb_price'] ?? 0) / 30) * $feeRam;
$dailyDiskFee = (($priceConfig['n_gb_disk_price'] ?? 0) / 30) * $obj->disk_gb;

// Count chargeable IPs (free local IPs + 1 free internet IP)
$chargeableIpCount = \App\Services\VpsUsageFeeService::countChargeableIPs($obj->list_ip_address);
$dailyIpFee = (($priceConfig['n_ip_address_price'] ?? 0) / 30) * $chargeableIpCount;

$dailyTotalFee = $dailyCpuFee + $dailyRamFee + $dailyDiskFee + $dailyIpFee;

// ... rest of display logic ...

// In the display table (NEW ROW ADDED):
<tr>
    <td style='border: 1px solid #ddd; padding: 3px;'>IP ({$chargeableIpCount} tính phí)</td>
    <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($priceConfig['n_ip_address_price'] ?? 0, 0) . "K</td>
    <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>{$chargeableIpCount}</td>
    <td style='border: 1px solid #ddd; padding: 3px; text-align: right;'>" . number_format($dailyIpFee, 2) . "K</td>
</tr>
```

### What Changed
- ✅ Line 173-174: Added IP counting logic
- ✅ Line 175: Calculate daily IP fee
- ✅ Line 177: Include IP fee in total
- ✅ Line 230-234: Added IP row to display table
- ✅ Shows: IP count, price, quantity, daily fee

---

## Summary of Changes

| File | Lines | Type | Purpose |
|------|-------|------|---------|
| VpsUsageFeeService.php | 18-89 | ADD | New IP counting methods |
| SyncVmwareInstancesCommand.php | 233 | MODIFY | Call IP counting function |
| VpsUsage_Meta.php | 173-234 | MODIFY | Add IP to display table |

## Total Code Changes
- **Lines Added:** ~120
- **Lines Modified:** ~40
- **Breaking Changes:** 0
- **Backward Compatible:** Yes

---

## Testing the Changes

### Test File: test_ip_charging.php
```php
<?php
use App\Services\VpsUsageFeeService;

// Test case 1
$result = VpsUsageFeeService::countChargeableIPs("192.168.1.1, 10.0.0.1");
assert($result === 0, "Only local IPs should return 0");

// Test case 2
$result = VpsUsageFeeService::countChargeableIPs("8.8.8.8");
assert($result === 0, "1 internet IP should return 0");

// Test case 3
$result = VpsUsageFeeService::countChargeableIPs("8.8.8.8, 1.1.1.1");
assert($result === 1, "2 internet IPs should return 1");

// Test case 4
$result = VpsUsageFeeService::countChargeableIPs("8.8.8.8, 1.1.1.1, 192.168.1.1");
assert($result === 1, "Mixed IPs should count correctly");

echo "All tests passed!";
?>
```

---

## Implementation Order (If Applying Manually)

1. **First:** Add methods to `VpsUsageFeeService.php`
   - `countChargeableIPs()`
   - `isLocalIP()`

2. **Second:** Update `SyncVmwareInstancesCommand.php`
   - Replace hardcoded IP count
   - Add call to `countChargeableIPs()`

3. **Third:** Update `VpsUsage_Meta.php`
   - Add IP counting
   - Add IP row to table
   - Update daily total

4. **Fourth:** Test and verify
   - Run test script
   - Check command output
   - Verify display

---

## Rollback Instructions

If needed to revert:

### Step 1: Revert VpsUsageFeeService.php
- Remove `countChargeableIPs()` method (lines 18-49)
- Remove `isLocalIP()` method (lines 51-89)

### Step 2: Revert SyncVmwareInstancesCommand.php
- Replace lines 233 with original hardcoded value:
  ```php
  $chargeableIpCount = 1;
  ```

### Step 3: Revert VpsUsage_Meta.php
- Remove IP counting code (lines 173-175)
- Remove IP fee calculation (line 176)
- Remove IP from daily total (line 177)
- Remove IP row from table (lines 230-234)

---

## Files Reference

### Full Paths
```
app/Services/VpsUsageFeeService.php
app/Console/Commands/SyncVmwareInstancesCommand.php
app/Models/VpsUsage_Meta.php
test_ip_charging.php
```

### Project Root
```
e:\Projects\laravel2022-01\laravel01\
```

---

**Version:** 1.0
**Date:** 2025-12-26
**Status:** Ready for Deployment ✅
