# ✅ Pricing Config Tracking - Deployment Checklist

## 📋 Pre-Deployment

- [ ] Review all changes in this document
- [ ] Backup production database
- [ ] Plan maintenance window
- [ ] Notify users of system updates

## 🔧 Files to Review

- [ ] [Migration: 2025_12_25_add_price_config_to_vps_usages.php](database/migrations/2025_12_25_add_price_config_to_vps_usages.php)
- [ ] [Service: VpsPricingService.php](app/Services/VpsPricingService.php)
- [ ] [Command: SyncVmwareInstancesCommand.php](app/Console/Commands/SyncVmwareInstancesCommand.php)
- [ ] [Config: vps_config.php](config/vps_config.php) (no changes, reference only)

## 🚀 Deployment Steps

### Step 1: Code Deployment
```bash
# 1.1 Pull latest code
cd /path/to/laravel01
git pull origin main

# 1.2 Verify files exist
ls -la database/migrations/2025_12_25_add_price_config_to_vps_usages.php
ls -la app/Services/VpsPricingService.php
```

**Checklist:**
- [ ] Files pulled successfully
- [ ] No merge conflicts
- [ ] Code review completed

### Step 2: Database Migration
```bash
# 2.1 Run migration
php artisan migrate

# 2.2 Verify migration
php artisan migrate:status | grep 2025_12_25
```

**Expected Output:**
```
2025_12_25_add_price_config_to_vps_usages  Illuminate\Database\Migrations  2025-12-25 xx:xx:xx  Batch N
```

**Checklist:**
- [ ] Migration ran without errors
- [ ] price_config column exists in vps_usages
- [ ] Column type is JSON

### Step 3: Verify Database Schema

```bash
# 3.1 Connect to database
mysql -u root -p laravel_db

# 3.2 Check column
DESCRIBE vps_usages;

# 3.3 Check column exists
SELECT COLUMN_NAME, COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'vps_usages' 
AND COLUMN_NAME = 'price_config';
```

**Expected Result:**
```
COLUMN_NAME: price_config
COLUMN_TYPE: json
```

**Checklist:**
- [ ] price_config column visible
- [ ] Column type is JSON
- [ ] Column is NULLABLE
- [ ] Column positioned after price_per_minute

### Step 4: Test VpsPricingService

```bash
# 4.1 Open Tinker
php artisan tinker

# 4.2 Test getPricingConfig()
>>> use App\Services\VpsPricingService;
>>> $config = VpsPricingService::getPricingConfig();
>>> var_dump($config);

# 4.3 Should return array with:
# [
#   "n_cpu_core_price" => 50,
#   "n_ram_gb_price" => 30,
#   "n_gb_disk_price" => 1,
#   "n_ip_address_price" => 50,
#   "n_network_dedicated_mbit_price" => 1000
# ]

# 4.4 Test getPricingConfigJson()
>>> $json = VpsPricingService::getPricingConfigJson();
>>> echo $json;

# 4.5 Test hasPricingChanged()
>>> $changed = VpsPricingService::hasPricingChanged($config);
>>> echo $changed ? 'true' : 'false';  # Should be: false

# 4.6 Exit Tinker
>>> exit()
```

**Checklist:**
- [ ] getPricingConfig() returns correct array
- [ ] getPricingConfigJson() returns valid JSON string
- [ ] hasPricingChanged() returns boolean

### Step 5: Run Sync Command

```bash
# 5.1 Stop existing sync process
pkill -f "vmware:sync"

# 5.2 Run sync manually
php artisan vmware:sync-instances \
  --domain=vcenter.local \
  --uid=admin@vsphere.local \
  --pw=your_password

# 5.3 Watch output for:
# - "📊 Inserted vps_usages snapshot"
# - "💰 Pricing config changed!" (if prices differ)

# 5.4 Check for errors
```

**Checklist:**
- [ ] Sync runs without errors
- [ ] New records created in vps_usages
- [ ] price_config column populated with JSON

### Step 6: Verify Data

```bash
# 6.1 Check records have price_config
php artisan tinker

>>> DB::table('vps_usages')
    ->latest('id')
    ->limit(5)
    ->select(['id', 'instance_id', 'price_config'])
    ->get();

# 6.2 Verify JSON format
>>> $record = DB::table('vps_usages')->latest('id')->first();
>>> $config = json_decode($record->price_config, true);
>>> print_r($config);

# 6.3 Exit
>>> exit()
```

**Expected Result:**
```
price_config should contain:
{
  "n_cpu_core_price": 50,
  "n_ram_gb_price": 30,
  "n_gb_disk_price": 1,
  "n_ip_address_price": 50,
  "n_network_dedicated_mbit_price": 1000
}
```

**Checklist:**
- [ ] Records have price_config data
- [ ] JSON format is valid
- [ ] All required pricing fields present

### Step 7: Test Pricing Change Detection

```bash
# 7.1 Edit vps_config.php to change a price
nano config/vps_config.php

# Change n_cpu_core price from 50 to 75

# 7.2 Run sync again
php artisan vmware:sync-instances \
  --domain=vcenter.local \
  --uid=admin@vsphere.local \
  --pw=your_password

# 7.3 Look for message:
# "💰 Pricing config changed! Inserted new vps_usages snapshot"

# 7.4 Verify database
php artisan tinker
>>> DB::table('vps_usages')
    ->where('instance_id', 123)  # Use actual instance ID
    ->latest('id')
    ->limit(2)
    ->select(['id', 'created_at', 'price_config'])
    ->get();

# 7.5 Compare price_config between last 2 records
# Should show different CPU prices

# 7.6 Exit
>>> exit()

# 7.7 Revert config change
nano config/vps_config.php
# Change n_cpu_core price from 75 back to 50
```

**Checklist:**
- [ ] Pricing change detected correctly
- [ ] New record created when price changed
- [ ] price_config shows different values
- [ ] Config reverted to original

### Step 8: Enable Cron Job

```bash
# 8.1 Check crontab
crontab -l

# 8.2 Ensure sync command is running
# Should see: * * * * * php artisan vmware:sync-instances

# 8.3 If not, add to crontab
crontab -e

# Add this line:
# * * * * * cd /path/to/laravel01 && php artisan vmware:sync-instances >> storage/logs/sync.log 2>&1

# 8.4 Verify
crontab -l | grep vmware:sync-instances
```

**Checklist:**
- [ ] Cron job scheduled
- [ ] Command runs every minute
- [ ] Logs are being written

### Step 9: Monitor Logs

```bash
# 9.1 Check logs in real-time
tail -f storage/logs/laravel.log | grep -i pricing

# 9.2 Should see entries like:
# [2025-12-25 10:01:23] laravel.INFO: 📊 Inserted vps_usages snapshot
# [2025-12-25 10:05:45] laravel.INFO: 💰 Pricing config changed!

# 9.3 Check sync logs
tail -f storage/logs/sync.log

# 9.4 Monitor for 5-10 minutes
```

**Checklist:**
- [ ] Logs show successful sync runs
- [ ] No error messages
- [ ] Pricing detection working

### Step 10: Test Billing Impact

```bash
# 10.1 Verify billing calculations still work
php artisan tinker

>>> use DB;
>>> $records = DB::table('vps_usages')
      ->where('instance_id', 123)
      ->get();
>>> echo count($records) . " records";
>>> $total = $records->sum('price_per_minute');
>>> echo number_format($total, 2) . " total charge";

# 10.2 Verify price_config doesn't break aggregation
>>> use App\Services\VpsUsageAggregationService;
>>> $service = new VpsUsageAggregationService();
>>> $count = $service->aggregateHourly();
>>> echo "Aggregated $count records";

# 10.3 Exit
>>> exit()
```

**Checklist:**
- [ ] Billing calculations accurate
- [ ] Aggregation still works
- [ ] No calculation errors

## ✅ Post-Deployment

### Immediate Checks (First Hour)
- [ ] No errors in logs
- [ ] Sync commands running successfully
- [ ] New records have price_config
- [ ] Database queries fast and responsive
- [ ] Billing calculations correct

### Daily Checks (First Week)
- [ ] Cron jobs running on schedule
- [ ] Records accumulating normally
- [ ] price_config consistent in records
- [ ] No billing discrepancies reported
- [ ] User reports no issues

### Weekly Checks (First Month)
- [ ] Month-end billing accurate
- [ ] Price change detection working
- [ ] Audit logs accessible
- [ ] Performance metrics stable
- [ ] Backup procedures working

## 🚨 Rollback Plan (If Issues Occur)

```bash
# 1. Revert code
git revert <commit-hash>

# 2. Rollback migration
php artisan migrate:rollback

# 3. Clear cache
php artisan cache:clear

# 4. Restart services
systemctl restart laravel-queue
systemctl restart nginx

# 5. Monitor logs
tail -f storage/logs/laravel.log

# 6. Notify users
```

## 📊 Verification Queries

### Check Migration Status
```sql
SELECT * FROM migrations WHERE migration LIKE '%pricing%';
```

### Count Records with price_config
```sql
SELECT COUNT(*) as total_records,
       COUNT(price_config) as with_pricing
FROM vps_usages;
```

### Find Pricing Changes
```sql
SELECT v1.id, v1.created_at, 
       v1.price_config as old_config,
       v2.price_config as new_config
FROM vps_usages v1
JOIN vps_usages v2 ON v1.instance_id = v2.instance_id 
  AND v1.id + 1 = v2.id
WHERE v1.price_config != v2.price_config;
```

## 📞 Support Contacts

- **Database Issues:** DBA Team
- **Sync Issues:** DevOps Team
- **Billing Issues:** Finance Team
- **Questions:** Senior Dev

## 📝 Sign-Off

- [ ] Tech Lead Review: __________ Date: __________
- [ ] QA Sign-Off: __________ Date: __________
- [ ] Ops Approval: __________ Date: __________
- [ ] Deployment Complete: __________ Date: __________

---

**Deployment Date:** ___________
**Deployed By:** ___________
**Version:** 1.0
**Status:** 🟡 In Progress / 🟢 Complete

