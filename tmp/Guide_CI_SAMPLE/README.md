# Laravel CI/CD Setup Guide

Hướng dẫn cấu hình CI/CD cho Laravel với GitHub Actions, MySQL, và Test API.

## 📋 Nội dung

1. [Database Setup](#database-setup)
2. [Environment Configuration](#environment-configuration)
3. [API Routes](#api-routes)
4. [Test Structure](#test-structure)
5. [GitHub Actions Workflow](#github-actions-workflow)
6. [Cách áp dụng vào project khác](#cách-áp-dụng-vào-project-khác)

---

## Database Setup

### 1. Tạo file `db.sql`

File này chứa schema database và sample data. Đặt ở root project.

```sql
CREATE DATABASE IF NOT EXISTS testCI;
USE testCI;

CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE sessions (
  id VARCHAR(255) NOT NULL PRIMARY KEY,
  user_id BIGINT UNSIGNED NULL,
  ip_address VARCHAR(45) NULL,
  user_agent LONGTEXT NULL,
  payload LONGTEXT NOT NULL,
  last_activity INT NOT NULL,
  created_at TIMESTAMP NULL,
  updated_at TIMESTAMP NULL,
  KEY sessions_user_id_index (user_id),
  KEY sessions_last_activity_index (last_activity)
);

INSERT INTO users (name, email, password) VALUES
('John Doe', 'john@example.com', 'password123'),
('Jane Smith', 'jane@example.com', 'password123');
```

**Lưu ý:** 
- Luôn thêm bảng `sessions` (Laravel cần cho session management)
- Tạo sample data để test

---

## Environment Configuration

### 1. Cập nhật `.env.example`

```dotenv
APP_NAME=Laravel
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=testCI
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
```

**Thay đổi chính:**
- `DB_CONNECTION=mysql` (không dùng sqlite)
- `DB_DATABASE=testCI` (khớp với tên DB trong db.sql)
- `DB_PASSWORD=` (empty, root không có password)

### 2. Cập nhật local `.env`

Copy từ `.env.example`:
```bash
cp .env.example .env
```

---

## API Routes

### 1. Tạo file `routes/api.php`

```php
<?php

use Illuminate\Support\Facades\Route;
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

### 2. Cập nhật `bootstrap/app.php`

Đảm bảo API routes được load:

```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',  // ← Thêm dòng này
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
```

---

## Test Structure

### 1. Feature Test: `tests/Feature/HttpClientTest.php`

Test homepage trả về 200:

```php
<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

class HttpClientTest extends \PHPUnit\Framework\TestCase
{
    private $client;
    private $baseUrl = 'http://127.0.0.1:8001';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false,
            'timeout' => 5,
        ]);
    }

    public function test_home_page_returns_200(): void
    {
        try {
            $response = $this->client->get('/');
            
            $this->assertEquals(200, $response->getStatusCode());
            $this->assertStringContainsString('Laravel', (string) $response->getBody());
        } catch (ConnectException $e) {
            $this->markTestSkipped('Server không chạy tại ' . $this->baseUrl);
        }
    }
}
```

### 2. API Test: `tests/Feature/ApiUserListTest.php`

Test API endpoint trả về JSON:

```php
<?php

namespace Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;

class ApiUserListTest extends \PHPUnit\Framework\TestCase
{
    private $client;
    private $baseUrl = 'http://127.0.0.1:8001';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'http_errors' => false,
            'timeout' => 5,
        ]);
    }

    public function test_api_user_list_returns_json(): void
    {
        try {
            $response = $this->client->get('/api/user/list');

            $this->assertEquals(200, $response->getStatusCode());
            
            $body = json_decode((string) $response->getBody(), true);
            
            $this->assertIsArray($body);
            $this->assertEquals('success', $body['status']);
            $this->assertArrayHasKey('data', $body);
            $this->assertArrayHasKey('count', $body);
            $this->assertIsArray($body['data']);
            $this->assertGreaterThan(0, $body['count']);
            
        } catch (ConnectException $e) {
            $this->markTestSkipped('Server không chạy tại ' . $this->baseUrl);
        }
    }
}
```

---

## GitHub Actions Workflow

### Tạo file `.github/workflows/tests.yml`

```yaml
name: Laravel Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  test:
    runs-on: ubuntu-latest

    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: ""
          MYSQL_ALLOW_EMPTY_PASSWORD: "yes"
        options: >-
          --health-cmd="mysqladmin ping"
          --health-interval=10s
          --health-timeout=5s
          --health-retries=3
        ports:
          - 3306:3306

    steps:
    - uses: actions/checkout@v3

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.2'
        extensions: mbstring, pdo, pdo_mysql

    - name: Install Dependencies
      run: composer install --prefer-dist --no-progress --no-interaction

    - name: Setup .env file
      run: cp .env.example .env

    - name: Wait for MySQL
      run: |
        for i in {1..30}; do
          if mysqladmin ping -h 127.0.0.1 -u root 2>/dev/null; then
            echo "MySQL is ready"
            exit 0
          fi
          echo "Waiting for MySQL... ($i/30)"
          sleep 1
        done
        echo "MySQL failed to start"
        exit 1

    - name: Generate APP_KEY
      run: php artisan key:generate

    - name: Import Database
      run: mysql -h 127.0.0.1 -u root < db.sql

    - name: Start Server
      run: |
        nohup php artisan serve --host=0.0.0.0 --port=8001 > /tmp/server.log 2>&1 &
        sleep 5
      
    - name: Verify Server
      run: |
        sleep 2
        if curl -v http://127.0.0.1:8001 2>&1 | grep -q "HTTP"; then
          echo "Server is running"
        else
          echo "Server check failed - showing logs:"
          cat /tmp/server.log
          ps aux | grep -E "php|artisan"
          exit 1
        fi

    - name: Run Tests
      run: php artisan test --no-coverage
```

**Giải thích các bước:**
1. **MySQL Service** - Khởi động MySQL 8.0 với root không có password
2. **Setup PHP** - Cài PHP 8.2 + extensions cần thiết
3. **Install Dependencies** - `composer install`
4. **Setup .env** - Copy từ `.env.example`
5. **Wait MySQL** - Chờ MySQL sẵn sàng (loop 30 lần)
6. **Generate Key** - `php artisan key:generate`
7. **Import DB** - Chạy `db.sql` (tạo DB + tables + sample data)
8. **Start Server** - Khởi động `php artisan serve` trên port 8001
9. **Verify Server** - Kiểm tra server đã up hay chưa
10. **Run Tests** - Chạy `php artisan test`

---

## Cách áp dụng vào project khác

### Bước 1: Copy files

```bash
# Copy từ project gốc
cp -r laravel2026-test-ci/GUIDE/files/* project-moi/

# Hoặc copy từng file:
cp laravel2026-test-ci/.github/workflows/tests.yml project-moi/.github/workflows/
cp laravel2026-test-ci/db.sql project-moi/
cp laravel2026-test-ci/.env.example project-moi/
cp laravel2026-test-ci/routes/api.php project-moi/routes/
cp -r laravel2026-test-ci/tests/Feature/HttpClientTest.php project-moi/tests/Feature/
cp -r laravel2026-test-ci/tests/Feature/ApiUserListTest.php project-moi/tests/Feature/
```

### Bước 2: Cập nhật `.env.example` (nếu cần)

Thay đổi:
- `DB_DATABASE` - Tên database của project
- `DB_USERNAME` - Username nếu khác
- `DB_PASSWORD` - Password nếu có

### Bước 3: Cập nhật `db.sql`

Thêm tables cần thiết cho project (không delete `sessions` table!)

### Bước 4: Cập nhật routes

Thêm API endpoints mới vào `routes/api.php`

### Bước 5: Tạo tests

Tạo test files mới trong `tests/Feature/` nếu cần

### Bước 6: Push to GitHub

```bash
git add .
git commit -m "Setup CI/CD with GitHub Actions"
git push origin main
```

GitHub Actions sẽ tự động chạy test! ✅

---

## Troubleshooting

### MySQL Connection Failed
- Kiểm tra `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD` trong `.env`
- Đảm bảo MySQL service đang chạy

### Table Not Found
- Kiểm tra `db.sql` đã được import
- Đảm bảo tên database khớp với `DB_DATABASE` trong `.env`

### Server Failed to Start
- Check `/tmp/server.log` để xem error
- Đảm bảo port 8001 không bị chiếm dụng
- Check Laravel logs: `storage/logs/laravel.log`

### Test Skipped
- Server không chạy trên port 8001
- Kiểm tra `Start Server` step
- Kiểm tra `Verify Server` output

---

## Quick Checklist

- [ ] `db.sql` - Có bảng users + sessions
- [ ] `.env.example` - DB config đúng (mysql, testCI, root, no password)
- [ ] `routes/api.php` - Có endpoint `/api/user/list`
- [ ] `bootstrap/app.php` - Có load API routes
- [ ] `tests/Feature/HttpClientTest.php` - Test homepage
- [ ] `tests/Feature/ApiUserListTest.php` - Test API
- [ ] `.github/workflows/tests.yml` - Workflow đúng
- [ ] Test local: `php artisan test` ✅
- [ ] Push to GitHub ✅
- [ ] GitHub Actions pass ✅

---

**Tác giả:** Setup by GitHub Copilot  
**Ngày:** December 6, 2025  
**Version:** 1.0
