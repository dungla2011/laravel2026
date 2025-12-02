# PostgreSQL Sequence Fix Helper

## 📋 Mô tả

Sau khi sync database từ MySQL sang PostgreSQL bằng `pgloader`, các sequence (auto-increment) sẽ bị out-of-sync với dữ liệu thực tế, gây lỗi **duplicate key constraint** khi insert record mới.

Helper này tự động fix tất cả sequences trong database.

---

## 🚨 Vấn đề

```
ERROR:  duplicate key value violates unique constraint "model_meta_infos_pkey"
DETAIL:  Key (id)=(12) already exists.
```

**Nguyên nhân:** 
- MySQL AUTO_INCREMENT: metadata, PostgreSQL không biết
- pgloader insert với explicit IDs → sequence không được update
- Sequence value = 12, nhưng MAX(id) = 3368

---

## ✅ Giải pháp

### 1. Sử dụng Function (Anywhere in PHP)

```php
// Fix tất cả sequences
$stats = fixAllPostgresSequences();

// Dry run (check trước, không fix)
$stats = fixAllPostgresSequences($verbose = true, $dryRun = true);

// Silent mode (không in ra, chỉ return stats)
$stats = fixAllPostgresSequences($verbose = false, $dryRun = false);

// Fix 1 bảng cụ thể
fixSequenceForTable('model_meta_infos', 'id');
```

### 2. Sử dụng Artisan Command

```bash
# Fix tất cả sequences
php artisan db:fix-sequences

# Dry run (check trước)
php artisan db:fix-sequences --dry-run

# Fix 1 bảng cụ thể
php artisan db:fix-sequences --table=model_meta_infos

# Silent mode
php artisan db:fix-sequences --silent
```

### 3. Chạy qua Web Browser

```
http://yourdomain.com/tool1/fix_sequences.php
```

---

## 📊 Output Example

```
══════════════════════════════════════════════════════════
FIX ALL POSTGRESQL SEQUENCES
══════════════════════════════════════════════════════════
Found 25 tables with sequences

──────────────────────────────────────────────────────────
📋 Table: model_meta_infos
   Column: id
   Sequence: public.model_meta_infos_id_seq
   Current: 12 | MAX: 3368 | Records: 39
   ⚠️  MISMATCH! (diff: 3356)
   ✅ FIXED! New value: 3369

──────────────────────────────────────────────────────────
📋 Table: users
   Column: id
   Sequence: public.users_id_seq
   Current: 150 | MAX: 150 | Records: 150
   ✓ OK - Sequence is correct

══════════════════════════════════════════════════════════
SUMMARY:
══════════════════════════════════════════════════════════
Total tables:    25
✅ Fixed:        8
✓  OK/Skipped:   17
❌ Errors:       0
```

---

## 🔧 Tích hợp vào Workflow

### Deployment Script

```bash
#!/bin/bash

# 1. Sync from MySQL
pgloader --with "quote identifiers" mysql://user:pass@host/db postgresql://user:pass@host/db

# 2. Fix sequences (CRITICAL!)
php artisan db:fix-sequences

# 3. Run migrations
php artisan migrate

# 4. Clear cache
php artisan cache:clear
```

### Cron Job (Optional)

```bash
# Check và fix sequences hàng ngày
0 2 * * * cd /path/to/project && php artisan db:fix-sequences --silent
```

---

## 📁 Files Created

```
app/Helpers/SequenceHelper.php          # Helper functions
app/Console/Commands/FixPostgresSequences.php  # Artisan command
public/tool1/fix_sequences.php          # Web interface
```

---

## 🎯 Use Cases

| Scenario | Command |
|----------|---------|
| Sau khi chạy pgloader | `php artisan db:fix-sequences` |
| Check trước khi fix | `php artisan db:fix-sequences --dry-run` |
| Fix 1 bảng cụ thể | `php artisan db:fix-sequences --table=users` |
| Trong code PHP | `fixAllPostgresSequences()` |
| Deployment script | `php artisan db:fix-sequences --silent` |
| Browser testing | `http://domain/tool1/fix_sequences.php` |

---

## 🧪 Testing

```bash
# 1. Check current state (dry run)
php artisan db:fix-sequences --dry-run

# 2. Fix issues
php artisan db:fix-sequences

# 3. Verify by inserting new record
php artisan tinker
>>> User::create(['name' => 'Test', 'email' => 'test@test.com']);
```

---

## ⚠️ Important Notes

1. **Run after EVERY pgloader sync** - sequences will be out of sync again
2. **Safe to run multiple times** - only fixes if needed
3. **No downtime required** - can run on live database
4. **Backup recommended** - always backup before database operations
5. **Check connection** - uses Laravel's default DB connection

---

## 🆘 Troubleshooting

### Error: "relation does not exist"
→ Check database connection in `.env`

### Error: "permission denied for sequence"
→ Database user needs USAGE privilege on sequences

### Sequence not found for table
→ Table might not have auto-increment column (normal, skip)

### Still getting duplicate key error after fix
→ Check if you're using `DB::table()->insert()` (not `DB::statement()`)

---

## 📝 Technical Details

**What the helper does:**

1. Query `information_schema` for all tables with sequences
2. For each table:
   - Get current sequence value: `SELECT last_value FROM sequence`
   - Get max ID from table: `SELECT MAX(id) FROM table`
   - If `max_id > last_value`: run `SELECT setval('sequence', max_id + 1, false)`
3. Return statistics and detailed report

**Why `setval(..., false)`?**
- Second parameter `false` = next `nextval()` will return the value we set
- If `true`, next `nextval()` returns value + 1 (skip one number)

---

## 💡 Best Practices

✅ **DO:**
- Run after every pgloader import
- Use dry-run first to preview changes
- Add to deployment checklist
- Monitor for "duplicate key" errors

❌ **DON'T:**
- Run during heavy write operations
- Modify sequences manually without understanding
- Forget to run after database sync
- Use `DB::statement()` for inserts (use Query Builder instead)

---

## 📚 References

- [PostgreSQL Sequences Documentation](https://www.postgresql.org/docs/current/sql-createsequence.html)
- [pgloader Documentation](https://pgloader.readthedocs.io/)
- [Laravel Query Builder](https://laravel.com/docs/queries)

---

## 🤝 Support

If issues persist:
1. Check PostgreSQL logs: `/var/log/postgresql/`
2. Verify database connection
3. Run with `--dry-run` to see what would be fixed
4. Check if user has sequence privileges

---

**Created:** 2024  
**Last Updated:** 2024  
**Compatibility:** Laravel 8+, PostgreSQL 12+
