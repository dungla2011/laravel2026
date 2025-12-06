# Setup Laravel CI/CD - Step by Step

## Files trong folder này

```
.
├── README.md                     # Hướng dẫn chi tiết
├── files/
│   ├── db.sql                   # Database schema
│   ├── .env.example             # Environment config
│   ├── api.php                  # API routes (copy vào routes/)
│   ├── .github/workflows/tests.yml  # GitHub Actions workflow
│   └── tests/Feature/           # Test files
│       ├── HttpClientTest.php   # Test homepage
│       └── ApiUserListTest.php  # Test API
```

## Quick Start (5 phút)

### 1. Copy files vào project mới

```bash
# Copy tất cả
cp -r GUIDE/files/* /path/to/your-project/

# Hoặc copy từng file:
mkdir -p project/.github/workflows
cp GUIDE/files/.github/workflows/tests.yml project/.github/workflows/
cp GUIDE/files/db.sql project/
cp GUIDE/files/.env.example project/
cp GUIDE/files/api.php project/routes/
cp GUIDE/files/tests/Feature/*.php project/tests/Feature/
```

### 2. Cập nhật tên database (nếu cần)

**File:** `db.sql`
```sql
CREATE DATABASE IF NOT EXISTS YOUR_DB_NAME;  ← Thay tên tại đây
```

**File:** `.env.example`
```
DB_DATABASE=YOUR_DB_NAME  ← Thay tên tại đây
```

### 3. Thêm API routes vào `routes/api.php`

```php
use App\Models\User;

Route::get('/user/list', function () {
    $users = User::select('id', 'name', 'email', 'created_at')->get();
    return response()->json([
        'status' => 'success',
        'data' => $users,
        'count' => $users->count()
    ]);
});
```

### 4. Update `bootstrap/app.php`

```php
->withRouting(
    web: __DIR__.'/../routes/web.php',
    api: __DIR__.'/../routes/api.php',  // ← Thêm dòng này
    commands: __DIR__.'/../routes/console.php',
    health: '/up',
)
```

### 5. Test local

```bash
# Setup database
cp .env.example .env
php artisan key:generate
mysql -u root < db.sql

# Test
php artisan test
```

### 6. Push to GitHub

```bash
git add .
git commit -m "Setup CI/CD"
git push origin main
```

GitHub Actions sẽ tự động chạy! ✅

---

## Chi tiết từng file

### db.sql
- Tạo database `testCI`
- Tạo bảng `users` (required)
- Tạo bảng `sessions` (required!)
- Insert sample data

### .env.example
- Config MySQL (host, port, database, user, password)
- Session driver = database

### api.php
- Endpoint GET `/api/user/list`
- Trả về JSON list users

### .github/workflows/tests.yml
- MySQL service (Docker)
- PHP 8.2 + pdo_mysql
- Wait MySQL → Generate key → Import DB → Start server → Test

### Tests
- HttpClientTest - Test homepage 200
- ApiUserListTest - Test API response

---

## Customization

### Thêm table mới
Edit `db.sql`:
```sql
CREATE TABLE products (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  ...
);
```

### Thêm API endpoint mới
Edit `routes/api.php`:
```php
Route::get('/products/list', function () {
    return Product::all();
});
```

### Thêm test mới
Tạo file mới trong `tests/Feature/`:
```php
class ProductTest extends \PHPUnit\Framework\TestCase {
    // test code
}
```

### Thay đổi PHP version
Edit `.github/workflows/tests.yml`:
```yaml
php-version: '8.3'  # Thay từ 8.2 → 8.3
```

---

## Troubleshooting

| Problem | Solution |
|---------|----------|
| "Table not found" | Check `db.sql` imported correctly |
| "Connection refused" | Check MySQL is running |
| "No such file or directory" | Check file paths in .env |
| "Server failed to start" | Check port 8001 not in use |

Xem `README.md` để chi tiết hơn!

---

**Tạo bởi:** GitHub Copilot  
**Ngày:** December 6, 2025
