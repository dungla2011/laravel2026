# IP Charging Logic - Visual Guide

## Flow Diagram

```
list_ip_address (from VpsUsage table)
    ↓
    ├─ Split by comma
    ↓
    ├─ For each IP:
    │   ├─ Check isLocalIP()
    │   │   ├─ 192.168.* → Skip (FREE)
    │   │   ├─ 10.* → Skip (FREE)
    │   │   ├─ 172.16-31.* → Skip (FREE)
    │   │   ├─ 127.* → Skip (FREE)
    │   │   ├─ 169.254.* → Skip (FREE)
    │   │   └─ Others → Count (INTERNET IP)
    │   └─
    ↓
    ├─ Count internet IPs
    ├─ Subtract 1 (free first IP)
    ├─ Return chargeable count
    ↓
    └─→ VpsUsageFeeService::calculateFee()
         ├─ Daily Fee = (IP_price/30) × chargeable_count
         ├─ Period Fee = Daily × (duration_min/1440)
         └─→ Store in calculated_fee column
```

## IP Classification Logic

```
Input: list_ip_address (comma-separated IPs)
       ↓
       ├─ 192.168.1.1 → LOCAL → FREE ✓
       ├─ 10.0.0.1 → LOCAL → FREE ✓
       ├─ 172.20.0.1 → LOCAL → FREE ✓
       ├─ 127.0.0.1 → LOCALHOST → FREE ✓
       ├─ 169.254.1.1 → LINK-LOCAL → FREE ✓
       ├─ 8.8.8.8 → INTERNET IP (1st) → FREE ✓
       ├─ 1.1.1.1 → INTERNET IP (2nd) → CHARGEABLE ✗
       └─ 208.67.222.222 → INTERNET IP (3rd) → CHARGEABLE ✗
       
Chargeable IPs: 2 (at 50K/30 days = 1.67K/day)
```

## Fee Breakdown Table

### Single VM Example
CPU: 4 cores | RAM: 8 GB | Disk: 200 GB | IPs: 3 (8.8.8.8, 1.1.1.1, 192.168.1.1)

```
┌────────────────────────────────────────────────────────────────┐
│                    VPS Usage Fee Breakdown                      │
├─────────────────┬──────────────┬────────┬────────────────────────┤
│   Component     │ Price/30 Day │ Qty    │  Daily Fee (÷30)       │
├─────────────────┼──────────────┼────────┼────────────────────────┤
│ CPU (4 core)    │ 1,500,000K   │  4    │ 200.00K               │
│ RAM (8 GB)      │ 1,200,000K   │  8    │ 320.00K               │
│ Disk (200 GB)   │    50,000K   │ 200   │ 333.33K               │
│ IP (1 charged)* │    50,000K   │  1    │   1.67K               │
├─────────────────┼──────────────┼────────┼────────────────────────┤
│ TOTAL/DAY       │              │        │ 855.00K               │
├────────────────────────────────────────────────────────────────┤
│ Duration: 500 minutes (≈8.33 hours)                             │
│ Period Fee = 855K × (500/1440) = 296.88K VND                   │
└────────────────────────────────────────────────────────────────┘
* IPs detail: 3 total - 1 local (192.168.1.1) = 2 internet
            - 1 free (8.8.8.8) = 1 chargeable (1.1.1.1)
```

## Local IP Ranges Reference

| CIDR Block       | Range               | Purpose          | Status  |
|------------------|---------------------|------------------|---------|
| 192.168.0.0/16   | 192.168.0.0 – 255   | Private (Class C) | FREE    |
| 10.0.0.0/8       | 10.0.0.0 – 255.255 | Private (Class A) | FREE    |
| 172.16.0.0/12    | 172.16.0.0 – 31.255| Private (Class B) | FREE    |
| 127.0.0.0/8      | 127.0.0.0 – 255     | Localhost        | FREE    |
| 169.254.0.0/16   | 169.254.0.0 – 255  | Link-local       | FREE    |

## Code Call Stack

```
SyncVmwareInstancesCommand.php (Line 233)
    ↓
    ├─ VpsUsageFeeService::countChargeableIPs($lastUsage->list_ip_address)
    │   ├─ Split IPs by comma
    │   ├─ For each IP:
    │   │   └─ isLocalIP($ip) → bool
    │   └─ Return: internet_count - 1
    ↓
    ├─ VpsUsageFeeService::calculateFee(..., $chargeableIpCount)
    │   ├─ calculateDailyFee(...)
    │   │   └─ (IP_price/30) × $ipCount
    │   ├─ Daily Fee × (duration_min/1440)
    │   └─ Return: period fee
    ↓
    └─→ Store in $mUpdate['calculated_fee']
        └─→ VpsUsage::query()->update($mUpdate)
```

## Data Flow

```
VpsUsage Record
    ├─ created_at: 2024-12-20 08:00:00
    ├─ lastest_time_the_same: 2024-12-20 16:48:00
    ├─ list_ip_address: "8.8.8.8, 1.1.1.1, 192.168.1.1"
    ├─ cpu: 4
    ├─ ram_gb: 8
    ├─ disk_gb: 200
    ├─ power_state: "POWERED_ON"
    └─ price_config: {...pricing...}
         ↓
    SyncVmwareInstancesCommand UPDATE
         ↓
    countChargeableIPs() → 1
         ↓
    calculateFee(config, 4, 8, 200, 12195.7 min, 1) → 194.79K
         ↓
    Store: calculated_fee = 194.79K
         ↓
    Display in VpsUsage_Meta._calculated_fee()
         ├─ Shows 3 internet - 1 local = 2 internet
         ├─ 2 internet - 1 free = 1 charged
         ├─ Daily IP Fee = 1.67K
         └─ Total Daily = 855K, Period = 194.79K
```

## Calculation Example

### Given:
- **IPs:** 8.8.8.8, 1.1.1.1, 208.67.222.222, 192.168.1.1, 10.0.0.1
- **Duration:** 500 minutes
- **Config Prices (per 30 days):**
  - CPU: 1,500,000K
  - RAM: 1,200,000K
  - Disk: 50,000K
  - IP: 50,000K

### Process:

1. **Count IPs:**
   - Total: 5 IPs
   - Internet: 3 (8.8.8.8, 1.1.1.1, 208.67.222.222)
   - Local: 2 (192.168.1.1, 10.0.0.1)
   - Chargeable: 3 - 1 = **2**

2. **Calculate Daily Fee:**
   - CPU: (1,500,000/30) × 4 = 200,000K
   - RAM: (1,200,000/30) × 8 = 320,000K
   - Disk: (50,000/30) × 200 = 333,333K
   - IP: (50,000/30) × 2 = 3,333K
   - **Daily Total: 856,666K**

3. **Calculate Period Fee:**
   - Period = 856,666 × (500/1440)
   - Period = 856,666 × 0.347222
   - **Period Fee: 297,440K VND**

---

## Integration Points

### 1. Command Execution
```bash
$ php artisan vmware:sync-instances
```
- Runs daily via Laravel schedule
- Updates existing VPS usage records
- Calculates fees on UPDATE
- Logs IP counts and fee amounts

### 2. Display
- VPS usage grid shows calculated_fee column
- Click to see breakdown table with IP details
- Drill-down shows which IPs are charged

### 3. Billing
- calculated_fee is source of truth for VM charges
- Daily fee is aggregated for user invoicing
- IP-specific charges are itemized

---

**Last Updated:** 2025-12-26
**Diagram Version:** 1.0
