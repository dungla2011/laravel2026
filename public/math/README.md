# MathBaby - Portable PHP App (API as Files)

Folder này chứa toàn bộ ứng dụng MathBaby và có thể copy đi bất cứ đâu để chạy.

**Thay đổi quan trọng**: Tất cả API giờ là file PHP riêng biệt trong folder `/api/`, không còn routing fancy nữa!

## Cấu trúc API (File-based)

```
api/
├── helpers.php              # JWT verification helper
├── login.php                # POST: Login
├── register.php             # POST: Register
├── exercises.php            # GET: List exercises
├── exercise_detail.php      # GET: Exercise detail (?id=1)
├── submission_start.php     # POST: Start submission
├── submission_answer.php    # POST: Save answer
├── submission_finish.php    # POST: Finish submission
├── submission_detail.php    # GET: View result (?id=1)
├── user_history.php         # GET: User history
└── exercise_stats.php       # GET: Exercise stats (?id=1)
```

## API Endpoints

### Login (No Auth)
```bash
POST /math/api/login.php
Content-Type: application/json

{
  "username": "user",
  "password": "pass"
}
```

### Register (No Auth)
```bash
POST /math/api/register.php
Content-Type: application/json

{
  "username": "newuser",
  "password": "pass"
}
```

### Get Exercises (Requires Auth)
```bash
GET /math/api/exercises.php
Authorization: Bearer YOUR_TOKEN
```

### Exercise Detail (Requires Auth)
```bash
GET /math/api/exercise_detail.php?id=1
Authorization: Bearer YOUR_TOKEN
```

### Start Submission (Requires Auth)
```bash
POST /math/api/submission_start.php
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "exerciseId": 1
}
```

### Save Answer (Requires Auth)
```bash
POST /math/api/submission_answer.php
Authorization: Bearer YOUR_TOKEN
Content-Type: application/json

{
  "submission_id": 1,
  "question_id": 5,
  "user_answer": 42
}
```

## Chạy Local (Dev)

```bash
# Trong folder này
php -S localhost:3000 router.php
```

Truy cập: http://localhost:3000

## Deploy lên Server

### Option 1: Deploy vào root domain (http://domain.com/)
```bash
# Copy toàn bộ folder này vào document root
cp -r math/* /var/www/html/
```

Đảm bảo BASE_PATH trong index.php = `/`

### Option 2: Deploy vào subfolder (http://domain.com/math/)
```bash
# Copy toàn bộ folder này vào subfolder
cp -r math /var/www/html/math/
```

Sau đó sửa trong `index.php`:
```php
define('BASE_PATH', '/math/');
```

**Quan trọng**: Đổi SECRET_KEY trong `api/helpers.php` và `api/login.php` để bảo mật!

## Cấu hình Server

### Apache (.htaccess đã có sẵn)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^ index.php [L]
```

### Nginx
```nginx
location /math {
    try_files $uri $uri/ /math/index.php?$query_string;
}
```

## Yêu cầu
- PHP 7.4+
- Extensions: pdo_sqlite, sqlite3, mbstring
- Composer (để cài firebase/php-jwt)

Chạy `composer install` sau khi copy nếu chưa có vendor/

## Lợi ích của File-based API

✅ Copy folder đi đâu cũng chạy được  
✅ Không cần config routing phức tạp  
✅ Dễ debug (mỗi API là 1 file riêng)  
✅ Độc lập, không phụ thuộc framework  
✅ .htaccess tự động handle routing
