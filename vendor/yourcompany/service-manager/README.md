# Service Manager Package

Gói Laravel để quản lý dịch vụ online với MongoDB, hỗ trợ tính phí theo thời gian thực và quản lý tài nguyên động.

## Tính năng

- ✅ Quản lý gói dịch vụ (Service Plans) với tài nguyên tùy biến
- ✅ Tính phí theo phút/giờ/ngày/tháng
- ✅ Tăng giảm tài nguyên theo thời gian thực với tính phí prorated
- ✅ Quản lý số dư tài khoản và giao dịch
- ✅ Lịch sử thanh toán và sử dụng tài nguyên
- ✅ Tự động suspend dịch vụ khi hết tiền
- ✅ API RESTful đầy đủ
- ✅ Sử dụng MongoDB với driver native Laravel 11+

## Yêu cầu

- Laravel 11+
- PHP 8.1+
- MongoDB 4.4+

## Cài đặt

### 1. Thêm package vào composer.json

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

### 2. Cài đặt package

```bash
composer require yourcompany/service-manager
composer require mongodb/laravel-mongodb
```

### 3. Cấu hình MongoDB trong config/database.php

```php
'connections' => [
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

### 4. Publish config

```bash
php artisan vendor:publish --provider="YourCompany\ServiceManager\ServiceManagerServiceProvider" --tag="config"
```

### 5. Cấu hình .env

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

## Sử dụng

### 1. Tạo Service Plan

```php
use YourCompany\ServiceManager\Models\ServicePlan;

$plan = ServicePlan::create([
    'name' => 'VPS Basic',
    'description' => 'Gói VPS cơ bản',
    'category' => 'vps',
    'status' => true,
    'resources' => [
        'cpu' => 2,
        'ram' => 4,
        'disk' => 50,
        'network' => 100,
        'ip' => 1
    ],
    'pricing' => [
        'cpu' => [
            'minute' => 0.1,
            'hour' => 5,
            'day' => 100,
            'month' => 2000
        ],
        'ram' => [
            'minute' => 0.05,
            'hour' => 2.5,
            'day' => 50,
            'month' => 1000
        ],
        'disk' => [
            'minute' => 0.01,
            'hour' => 0.5,
            'day' => 10,
            'month' => 200
        ],
        'network' => [
            'minute' => 0.02,
            'hour' => 1,
            'day' => 20,
            'month' => 400
        ],
        'ip' => [
            'hour' => 10,
            'day' => 200,
            'month' => 5000
        ]
    ],
    'created_by' => 1
]);
```

### 2. Tạo dịch vụ cho khách hàng

```php
use YourCompany\ServiceManager\Services\ServiceProvisioningService;

$provisioningService = app(ServiceProvisioningService::class);

$service = $provisioningService->createService(
    $userId = 1,
    $planId = $plan->_id,
    $customResources = [
        'cpu' => 4,
        'ram' => 8,
        'disk' => 100,
        'network' => 200,
        'ip' => 2
    ],
    $billingPeriod = 'month',
    $metadata = [
        'name' => 'My VPS Server',
        'description' => 'Production server'
    ]
);
```

### 3. Thay đổi tài nguyên

```php
use YourCompany\ServiceManager\Services\BillingService;

$billingService = app(BillingService::class);

// Tính toán chi phí trước khi thay đổi
$billing = $billingService->calculateProratedBilling($service, [
    'cpu' => 6,
    'ram' => 12,
    'disk' => 150,
    'network' => 300,
    'ip' => 3
]);

// Áp dụng thay đổi
$result = $billingService->processResourceChangeBilling($service, [
    'cpu' => 6,
    'ram' => 12,
    'disk' => 150,
    'network' => 300,
    'ip' => 3
]);
```

### 4. Quản lý số dư

```php
use YourCompany\ServiceManager\Models\UserBalance;

$balance = UserBalance::getOrCreateForUser($userId);

// Nạp tiền
$transaction = $balance->addFunds(1000000, 'Nạp tiền vào tài khoản');

// Kiểm tra số dư
$availableBalance = $balance->getAvailableBalance();
```

## API Endpoints

### Service Plans
- `GET /api/service-manager/plans` - Danh sách gói dịch vụ
- `GET /api/service-manager/plans/{id}` - Chi tiết gói dịch vụ
- `POST /api/service-manager/plans/{id}/calculate-price` - Tính giá

### Services
- `GET /api/service-manager/services` - Danh sách dịch vụ của user
- `POST /api/service-manager/services` - Tạo dịch vụ mới
- `GET /api/service-manager/services/{id}` - Chi tiết dịch vụ
- `PUT /api/service-manager/services/{id}/resources` - Thay đổi tài nguyên
- `POST /api/service-manager/services/{id}/calculate-resource-change` - Tính chi phí thay đổi
- `POST /api/service-manager/services/{id}/suspend` - Tạm dừng dịch vụ
- `POST /api/service-manager/services/{id}/reactivate` - Kích hoạt lại
- `POST /api/service-manager/services/{id}/terminate` - Hủy dịch vụ

### Billing
- `GET /api/service-manager/billing/balance` - Số dư tài khoản
- `POST /api/service-manager/billing/add-funds` - Nạp tiền
- `GET /api/service-manager/billing/transactions` - Lịch sử giao dịch
- `GET /api/service-manager/billing/records` - Hóa đơn
- `GET /api/service-manager/billing/summary` - Tổng quan thanh toán

## Ví dụ sử dụng API

### Tạo dịch vụ mới

```bash
curl -X POST /api/service-manager/services \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "plan_id": "60f1b2c3d4e5f6789abcdef0",
    "billing_period": "month",
    "custom_resources": {
      "cpu": 4,
      "ram": 8,
      "disk": 100,
      "network": 200,
      "ip": 2
    },
    "name": "My VPS Server"
  }'
```

### Thay đổi tài nguyên

```bash
curl -X PUT /api/service-manager/services/{id}/resources \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "resources": {
      "cpu": 6,
      "ram": 12,
      "disk": 150,
      "network": 300,
      "ip": 3
    }
  }'
```

## Cron Jobs

Để tự động xử lý billing cycle, thêm vào crontab:

```bash
# Chạy mỗi phút để kiểm tra billing
* * * * * php /path/to/artisan schedule:run
```

Và trong `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->call(function () {
        app(\YourCompany\ServiceManager\Services\BillingService::class)->processBillingCycle();
    })->everyMinute();
}
```

## Tùy chỉnh

Package này được thiết kế để dễ dàng tùy chỉnh. Bạn có thể:

1. Extend các Model để thêm tính năng
2. Override các Service để thay đổi logic
3. Thêm middleware cho authorization
4. Tùy chỉnh validation rules
5. Thêm event listeners cho các hành động

## License

MIT License 