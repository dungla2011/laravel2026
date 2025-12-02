# Smart File Generation Implementation - Complete

## Overview
Implemented smart change detection system for payment export files (Excel, PDF, JSON). Files are now only regenerated when payment data actually changes, avoiding unnecessary disk I/O.

## Functions Implemented

### 1. `comparePaymentData($existing, $current)`
**Purpose:** Compare two payment datasets to detect changes

**Logic:**
- Returns `true` if data has changed, `false` if identical
- Compares array counts first (quick fail)
- Iterates through all records comparing key fields:
  - `id` - Payment record ID
  - `payed` - Payment amount
  - `khau_tru` - Tax deduction
  - `payment_type` - Payment method type
  - `ten_san_pham` - Product name
  - `so_luong` - Quantity

**Returns:** `bool`

### 2. `generateArchiveFiles($evid, $payments)`
**Purpose:** Main orchestrator for file generation with smart change detection

**Workflow:**
1. Gets folder path, Excel, PDF, and JSON file paths
2. Creates folder if it doesn't exist
3. **SMART CHECK:** If JSON file exists:
   - Loads existing JSON metadata
   - Extracts payment array
   - Builds current payment array with same structure
   - Compares using `comparePaymentData()`
   - Sets `$needsRegen = false` if data unchanged
4. **REGENERATION (only if needed):**
   - Calls `exportToExcel()` to create Excel file
   - Validates Excel was created
   - Calls `convertExcelToPdf()` to create PDF and JSON
5. **ERROR HANDLING:**
   - Returns `false` if any step fails
   - Logs all errors to PHP error log
   - Returns `true` on success

**File Paths Created:**
- Excel: `/var/glx/web_log/pdf_event_bill/ev_id_<EVID>/ThanhToan_Event_<EVID>.xlsx`
- PDF: `/var/glx/web_log/pdf_event_bill/ev_id_<EVID>/ThanhToan_Event_<EVID>.pdf`
- JSON: `/var/glx/web_log/pdf_event_bill/ev_id_<EVID>/ThanhToan_Event_<EVID>.json`

### 3. Helper Functions
```php
getFileNameExcel($evid)  // Returns full path to Excel file
getFileNamePdf($evid)    // Returns full path to PDF file
getFileNameJson($evid)   // Returns full path to JSON file
getFolderPdf($evid)      // Returns folder path for event
```

## Integration Points

### Main Entry (Line 36)
```php
generateArchiveFiles($evid, $payments);
```
Called automatically when page loads after fetching payment data.

### Data Flow
```
User requests event page
    ↓
Fetch payments from DB: getPaymentsData($evid, $payment_type)
    ↓
SMART CHECK: generateArchiveFiles($evid, $payments)
    ├─ Check if JSON exists
    ├─ Compare payment data
    └─ Regenerate ONLY if changed
    ↓
Display HTML table with payments
```

## Benefits

✅ **Performance:** Avoids unnecessary file regeneration
✅ **Reliability:** Detects payment data changes accurately
✅ **Logging:** All operations logged for debugging
✅ **Error Handling:** Graceful failures with detailed error messages
✅ **Scalability:** Works with large payment datasets

## JSON Metadata Structure

```json
{
  "evid": "123",
  "total_payments": 50,
  "payments": [
    {
      "id": 1,
      "user_event_id": "ue_123",
      "first_name": "Nguyễn",
      "last_name": "Văn A",
      "payed": 5000000,
      "khau_tru": 500000,
      "thuc_nhan": 4500000,
      "tax_number": "0123456789",
      "bank_acc_number": "1234567890",
      "bank_name_text": "Vietcombank",
      "payment_type": "bank_transfer"
    }
  ]
}
```

## Testing Checklist

- [ ] Visit event page - files created on first load
- [ ] Refresh page - files NOT recreated (check error log)
- [ ] Update payment amount in DB
- [ ] Refresh page - files RECREATED (check error log for "data changed")
- [ ] Add new payment in DB
- [ ] Refresh page - files RECREATED
- [ ] Remove payment from DB
- [ ] Refresh page - files RECREATED
- [ ] Check file timestamps to confirm regeneration

## Error Logging Examples

**Files created:**
```
Files regenerated for event 123 (data changed or files missing)
Excel archived: /var/glx/web_log/pdf_event_bill/ev_id_123/ThanhToan_Event_123.xlsx
PDF saved: /var/glx/web_log/pdf_event_bill/ev_id_123/ThanhToan_Event_123.pdf
Metadata saved: /var/glx/web_log/pdf_event_bill/ev_id_123/ThanhToan_Event_123.json
```

**Files NOT recreated (unchanged data):**
```
Files exist and data unchanged for event 123 - skipping regeneration
```

**Error example:**
```
Failed to generate Excel for event 123
```

## File Locations

**Main implementation file:**
- `/public/tool1/_site/event_mng/download_payment_event.php`
  - Lines 354-468: Helper functions (getFileName*, comparePaymentData, generateArchiveFiles)
  - Line 36: Function call on page load

**Related files:**
- `/public/testing/kyso.php` - File manager UI (download/view PDFs)
- `/public/testing/api/file-manager.php` - File API backend

## Notes

1. **Folder structure** is organized by event ID for easy file management
2. **JSON metadata** enables future features like payment history tracking
3. **Error handling** is non-critical for PDF generation (system continues if PDF fails)
4. **Change detection** works with object-to-array conversion (handles both stdClass and arrays)
