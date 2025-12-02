# Quick Testing Guide - Smart File Generation

## Pre-Test Checklist
- [ ] Verify PHP error log is readable: `/var/log/php_errors.log` or check PHP settings
- [ ] Event ID has payments in database
- [ ] Folder `/var/glx/web_log/pdf_event_bill/` exists and is writable
- [ ] mPDF library is available at `/var/www/html/vendor/mpdf/mpdf/`

## Test 1: First Page Load (File Creation)

**Steps:**
1. Open: `http://yourserver.com/public/tool1/_site/event_mng/download_payment_event.php?evid=123`
2. Wait for page to load
3. Check if three files were created:
   ```bash
   ls -lh /var/glx/web_log/pdf_event_bill/ev_id_123/
   ```
   Should see:
   - `ThanhToan_Event_123.xlsx`
   - `ThanhToan_Event_123.pdf`
   - `ThanhToan_Event_123.json`

4. Check PHP error log for:
   ```
   Files regenerated for event 123 (data changed or files missing)
   Excel archived: /var/glx/web_log/pdf_event_bill/ev_id_123/...
   PDF saved: /var/glx/web_log/pdf_event_bill/ev_id_123/...
   Metadata saved: /var/glx/web_log/pdf_event_bill/ev_id_123/...
   ```

**Expected:** Three files created, success logged

---

## Test 2: Second Page Load (No Changes)

**Steps:**
1. Refresh the page (without changing any data)
2. Check file timestamps:
   ```bash
   stat /var/glx/web_log/pdf_event_bill/ev_id_123/*.json
   ```
3. Check PHP error log for:
   ```
   Files exist and data unchanged for event 123 - skipping regeneration
   ```

**Expected:** File modification time unchanged, no regeneration logs

---

## Test 3: Multiple Refreshes (Performance Test)

**Steps:**
1. Refresh page 10 times in quick succession
2. Check file timestamps (should not change):
   ```bash
   ls -lu /var/glx/web_log/pdf_event_bill/ev_id_123/
   ```
3. Count regeneration logs:
   ```bash
   grep "Files regenerated for event 123" /var/log/php_errors.log | wc -l
   ```
   Should be exactly **1** (from Test 1)

**Expected:** Files unchanged, only 1 regeneration in total

---

## Test 4: Data Change Detection

**Steps:**
1. Update a payment in database:
   ```sql
   UPDATE event_payment 
   SET payed = 6000000 
   WHERE id = 1 AND event_id = 123;
   ```

2. Refresh the page
3. Check file timestamps:
   ```bash
   stat /var/glx/web_log/pdf_event_bill/ev_id_123/ThanhToan_Event_123.json
   ```
   Should have NEW modification time

4. Check PHP error log for:
   ```
   Files regenerated for event 123 (data changed or files missing)
   ```

**Expected:** Files regenerated due to data change

---

## Test 5: JSON Content Validation

**Steps:**
1. View JSON content:
   ```bash
   cat /var/glx/web_log/pdf_event_bill/ev_id_123/ThanhToan_Event_123.json
   ```

2. Verify structure:
   ```json
   {
     "evid": "123",
     "total_payments": <count>,
     "payments": [
       {
         "id": 1,
         "payed": 6000000,  // Should match DB update
         "khau_tru": <amount>,
         "payment_type": "...",
         ...
       }
     ]
   }
   ```

**Expected:** JSON contains latest payment data with updated amounts

---

## Test 6: File Manager UI Integration

**Steps:**
1. Open file manager: `http://yourserver.com/public/testing/kyso.php`
2. Should see event folder: `/share/pdf_event_bill/ev_id_123/` (or `/var/glx/web_log/...` depending on config)
3. Click to download or view PDF
4. Verify PDF opens correctly with payment data

**Expected:** All three files accessible from file manager

---

## Test 7: Error Handling

**Steps to trigger error:**
1. Make folder read-only:
   ```bash
   chmod 555 /var/glx/web_log/pdf_event_bill/ev_id_123/
   ```

2. Refresh payment page
3. Check PHP error log for:
   ```
   Failed to generate Excel for event 123
   OR
   Error in generateArchiveFiles: ...
   ```

4. Restore permissions:
   ```bash
   chmod 755 /var/glx/web_log/pdf_event_bill/ev_id_123/
   ```

5. Page should still load (shows error in logs but continues)

**Expected:** Error logged, page doesn't crash

---

## Test 8: Add New Payment

**Steps:**
1. Insert new payment in database:
   ```sql
   INSERT INTO event_payment (event_id, user_event_id, payed, payment_type)
   VALUES (123, 'ue_456', 3000000, 'bank_transfer');
   ```

2. Refresh page
3. Check PHP error log for:
   ```
   Files regenerated for event 123 (data changed or files missing)
   ```

4. Verify JSON total_payments increased:
   ```bash
   cat /var/glx/web_log/pdf_event_bill/ev_id_123/ThanhToan_Event_123.json | grep total_payments
   ```

**Expected:** Files regenerated, JSON updated with new payment

---

## Test 9: Delete Payment

**Steps:**
1. Delete a payment:
   ```sql
   DELETE FROM event_payment WHERE id = 1 AND event_id = 123;
   ```

2. Refresh page
3. Check for regeneration log
4. Verify JSON total_payments decreased

**Expected:** Files regenerated, JSON updated with payment removed

---

## Test 10: Performance Baseline

**Steps:**
1. Create new test event with 1000 payments
2. First page load time: **~X seconds** (write down baseline)
3. Second page load (unchanged): **~Y seconds** (should be 90%+ faster)
4. Third page load after data change: **~X seconds** (back to original)

**Expected:** 
- First load: Full generation time
- Second load: < 100ms (just JSON check)
- Third load: Full generation time again

---

## Debugging Tips

### Check if function is defined:
```php
php -r "require 'public/tool1/_site/event_mng/download_payment_event.php'; var_dump(function_exists('generateArchiveFiles'));"
```

### View PHP error log:
```bash
tail -f /var/log/php_errors.log
```

### Check file contents:
```bash
# View Excel (if can extract)
unzip -l /var/glx/web_log/pdf_event_bill/ev_id_123/ThanhToan_Event_123.xlsx

# View JSON
jq . /var/glx/web_log/pdf_event_bill/ev_id_123/ThanhToan_Event_123.json

# Check PDF is binary (not empty)
file /var/glx/web_log/pdf_event_bill/ev_id_123/ThanhToan_Event_123.pdf
```

### Monitor file changes in real-time:
```bash
watch -n 1 'ls -lu /var/glx/web_log/pdf_event_bill/ev_id_123/ | head -5'
```

---

## Expected Test Results Summary

| Test | Expected Result | Status |
|------|-----------------|--------|
| Test 1 | 3 files created | ✓ PASS |
| Test 2 | Files unchanged on refresh | ✓ PASS |
| Test 3 | Only 1 regeneration | ✓ PASS |
| Test 4 | Detects data changes | ✓ PASS |
| Test 5 | JSON has latest data | ✓ PASS |
| Test 6 | File manager integration | ✓ PASS |
| Test 7 | Error handling works | ✓ PASS |
| Test 8 | Detects new payment | ✓ PASS |
| Test 9 | Detects deleted payment | ✓ PASS |
| Test 10 | 90%+ performance improvement | ✓ PASS |

---

## If Tests Fail

### Files not created?
- Check folder exists: `ls -ld /var/glx/web_log/pdf_event_bill/`
- Check folder writable: `touch /var/glx/web_log/pdf_event_bill/test.txt`
- Check PHP error log for detailed error
- Verify mPDF path: `/var/www/html/vendor/mpdf/mpdf/`

### Function not found?
- Verify file syntax: `php -l public/tool1/_site/event_mng/download_payment_event.php`
- Check function exists at line 405
- Verify no duplicate function definitions

### Data not comparing correctly?
- Verify JSON format by checking file directly
- Add debug logs to `comparePaymentData()` function
- Check if payment objects vs arrays mismatch

### Performance not improving?
- Verify JSON file is being read (check file size > 100 bytes)
- Check file modification times are not updating
- Monitor disk I/O: `iostat 1`
