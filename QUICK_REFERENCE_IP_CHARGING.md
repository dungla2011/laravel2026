# VPS IP Charging - Quick Reference

## Core Principle
```
FREE: All local IPs + First internet IP
CHARGE: Additional internet IPs at 50K/30 days
```

## Implementation Status

| Component | File | Status |
|-----------|------|--------|
| IP Detection | `VpsUsageFeeService.php` | ✅ Complete |
| Fee Calculation | `SyncVmwareInstancesCommand.php` | ✅ Complete |
| Display Table | `VpsUsage_Meta.php` | ✅ Complete |

## Method Reference

### Count Chargeable IPs
```php
use App\Services\VpsUsageFeeService;

// From comma-separated IP string
$chargeableCount = VpsUsageFeeService::countChargeableIPs("8.8.8.8, 1.1.1.1, 192.168.1.1");
// Returns: 1 (2 internet IPs - 1 free = 1 chargeable)
```

### Check if IP is Local
```php
$isLocal = VpsUsageFeeService::isLocalIP("192.168.1.1"); // true
$isLocal = VpsUsageFeeService::isLocalIP("8.8.8.8");     // false
```

### Calculate Fee with IPs
```php
$fee = VpsUsageFeeService::calculateFee(
    $priceConfig,      // JSON pricing config
    $cpuCount,         // CPU cores
    $ramGb,            // RAM in GB
    $diskGb,           // Disk in GB
    $durationMinutes,  // Usage duration
    $chargeableIpCount // From countChargeableIPs()
);
```

## Local IP Ranges

```
Free ranges:
├─ 192.168.0.0/16     (Private Class C)
├─ 10.0.0.0/8         (Private Class A)
├─ 172.16.0.0/12      (Private Class B)
├─ 127.0.0.0/8        (Localhost)
└─ 169.254.0.0/16     (Link-local)

Charged ranges:
└─ Everything else (Internet IPs)
   Except: 1st internet IP is FREE
```

## Calculation Example

**Input:**
- IPs: "8.8.8.8, 1.1.1.1, 192.168.1.1, 10.0.0.1"
- CPU: 4, RAM: 8GB, Disk: 200GB, Duration: 500min
- IP Price (30 days): 50,000K

**Processing:**
```
1. Internet IPs: 8.8.8.8, 1.1.1.1 = 2
2. Local IPs: 192.168.1.1, 10.0.0.1 = 2 (FREE)
3. Chargeable: 2 - 1 = 1
```

**Fee Calculation:**
```
Daily IP Fee = (50,000/30) × 1 = 1,667K
Period IP Fee = 1,667K × (500/1440) = 577K
```

## Display Format

```
IP (1 tính phí)       50000K      1      1.67K
```

Translation: "IP (1 charged) [price] [quantity] [daily_fee]"

## Command Integration

```bash
# Triggers fee calculation
php artisan vmware:sync-instances

# Logs output shows:
# VPS fee cal: ... Duration = 500 minutes => Fee = 123.45 K VND
```

## Testing

```bash
# Run test script
php test_ip_charging.php

# Expected output:
# ✓ PASS | Only local IPs (should be 0 chargeable)
# ✓ PASS | 1 Internet IP (should be 0 chargeable - 1 free)
# ✓ PASS | 2 Internet IPs (should be 1 chargeable)
# ... (9 tests total)
```

## Common Scenarios

### Scenario 1: Home Lab Setup
```
IPs: 192.168.1.1, 192.168.1.2, 192.168.1.3
Local: 3 | Internet: 0
Chargeable: 0 ✅ FREE
```

### Scenario 2: Single Internet IP
```
IPs: 8.8.8.8
Internet: 1
Chargeable: 0 ✅ FREE (1 free included)
```

### Scenario 3: Web Server Redundancy
```
IPs: 8.8.8.8, 1.1.1.1, 208.67.222.222
Internet: 3
Chargeable: 2 ✗ Charge 2 IPs at 1.67K/day each = 3.34K/day
```

### Scenario 4: Enterprise Setup
```
IPs: 8.8.8.8, 1.1.1.1, 208.67.222.222, 192.168.1.0/24 (many local)
Internet: 3 | Local: many
Chargeable: 2 ✗ Charge 2 IPs = 3.34K/day (all locals FREE)
```

## Debugging

### Check IPs in Database
```sql
SELECT vm_name, list_ip_address 
FROM vps_usages 
WHERE vm_name LIKE 'prod%' 
LIMIT 5;
```

### Check Calculated Fees
```sql
SELECT vm_name, list_ip_address, calculated_fee, created_at, lastest_time_the_same
FROM vps_usages 
WHERE vm_name = 'your-vm-name'
ORDER BY lastest_time_the_same DESC
LIMIT 5;
```

### Verify IP Counting Logic
```php
// Add to any PHP context with autoload
$ips = "8.8.8.8, 1.1.1.1, 192.168.1.1";
$count = \App\Services\VpsUsageFeeService::countChargeableIPs($ips);
echo "Chargeable: $count"; // Output: Chargeable: 1
```

## Support

**Documentation Files:**
- `IP_CHARGING_IMPLEMENTATION.md` - Full technical details
- `IP_CHARGING_VISUAL_GUIDE.md` - Diagrams and flow charts
- `IP_CHARGING_COMPLETION_REPORT.md` - Implementation summary

**Test File:**
- `test_ip_charging.php` - Automated test suite

---

**Quick Stats:**
- ✅ 2 new methods added
- ✅ 5 IP ranges detected
- ✅ 3 files modified
- ✅ 9 test cases included
- ✅ 0 breaking changes

**Ready to Deploy:** YES ✅
