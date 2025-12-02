# Hướng dẫn cài đặt nhanh Service Manager

## Bước 1: Thêm package vào project

Thêm vào `composer.json` của project chính:

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/servicemanager"
        }
    ],
    "require": {
        "yourcompany/service-manager": "*"
    }
}
```

## Bước 2: Cài đặt dependencies

```bash
composer require yourcompany/service-manager
composer require mongodb/laravel-mongodb
```

## Bước 3: Cấu hình MongoDB

Thêm vào `config/database.php`:

```php
'connections' => [
    // ... existing connections
    'mongodb' => [
        'driver' => 'mongodb',
        'dsn' => env('DB_MONGO_DSN'),
        'host' => env('DB_MONGO_HOST', '127.0.0.1'),
        'port' => env('DB_MONGO_PORT', 27017),
        'database' => env('DB_MONGO_DATABASE', 'service_manager'),
        'username' => env('DB_MONGO_USERNAME'),
        'password' => env('DB_MONGO_PASSWORD'),
        'options' => [
            'appName' => env('DB_MONGO_APP_NAME', 'Laravel'),
        ],
    ],
],
```

## Bước 4: Publish config

```bash
php artisan vendor:publish --provider="YourCompany\ServiceManager\ServiceManagerServiceProvider" --tag="config"
```

## Bước 5: Cấu hình .env

```env
# MongoDB
DB_MONGO_HOST=127.0.0.1
DB_MONGO_PORT=27017
DB_MONGO_DATABASE=service_manager
DB_MONGO_USERNAME=
DB_MONGO_PASSWORD=
# Hoặc sử dụng DSN
# DB_MONGO_DSN=mongodb://username:password@host:port/database

# Service Manager
SERVICEMANAGER_MONGODB_CONNECTION=mongodb
SERVICEMANAGER_MONGODB_DATABASE=service_manager
SERVICEMANAGER_CURRENCY=VND
SERVICEMANAGER_AUTO_SUSPEND=true
```

## Bước 6: Tạo MongoDB Indexes

Để tối ưu hiệu suất, chạy script sau trong MongoDB shell:

```javascript
// Kết nối đến database
use service_manager

// Tạo indexes cho hiệu suất tốt hơn
db.service_plans.createIndex({ "status": 1 })
db.service_plans.createIndex({ "category": 1 })
db.services.createIndex({ "user_id": 1 })
db.services.createIndex({ "status": 1 })
db.services.createIndex({ "next_billing_date": 1 })
db.billing_records.createIndex({ "user_id": 1 })
db.billing_records.createIndex({ "status": 1 })
db.billing_records.createIndex({ "due_date": 1 })
db.user_balances.createIndex({ "user_id": 1 }, { unique: true })
db.balance_transactions.createIndex({ "user_id": 1 })

print("Indexes created successfully!")
```

Xem file `database_indexes.md` để biết chi tiết về tất cả indexes.

## Bước 7: Test API

Tạo một service plan đầu tiên:

```bash
curl -X POST /api/service-manager/plans \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "VPS Basic",
    "description": "Gói VPS cơ bản",
    "category": "vps",
    "resources": {
      "cpu": 2,
      "ram": 4,
      "disk": 50
    },
    "pricing": {
      "cpu": {"month": 2000},
      "ram": {"month": 1000},
      "disk": {"month": 200}
    }
  }'
```

## Bước 8: Cron job (tùy chọn)

Thêm vào `app/Console/Kernel.php` để tự động xử lý billing:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        app(\YourCompany\ServiceManager\Services\BillingService::class)->processBillingCycle();
    })->everyMinute();
}
```

## Hoàn thành!

Package đã sẵn sàng sử dụng với Laravel 11+ và MongoDB driver native. Xem file `README.md` để biết thêm chi tiết về cách sử dụng. 