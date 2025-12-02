# MongoDB CRUD Package

Một package Laravel đơn giản để thao tác CRUD với MongoDB. Package này hoàn toàn độc lập và có thể cài đặt vào vendor.

## Tính năng

- ✅ CRUD operations hoàn chỉnh (Create, Read, Update, Delete)
- ✅ Tìm kiếm và lọc dữ liệu
- ✅ Phân trang
- ✅ Bulk operations (xóa/cập nhật nhiều records)
- ✅ Thống kê dữ liệu
- ✅ Validation đầy đủ
- ✅ Error handling
- ✅ MongoDB native support
- ✅ Configurable và extensible

## Cài đặt

### 1. Thêm vào composer.json

```json
{
    "repositories": [
        {
            "type": "path",
            "url": "./packages/mongocrud"
        }
    ],
    "require": {
        "yourcompany/mongo-crud": "*"
    }
}
```

### 2. Cài đặt package

```bash
composer require yourcompany/mongo-crud
```

### 3. Publish config (tùy chọn)

```bash
php artisan vendor:publish --provider="YourCompany\MongoCrud\MongoCrudServiceProvider" --tag="config"
```

## Cấu hình

File config: `config/mongocrud.php`

```php
return [
    'connection' => env('MONGOCRUD_CONNECTION', 'mongodb'),
    'collection_prefix' => env('MONGOCRUD_PREFIX', ''),
    'route_prefix' => env('MONGOCRUD_ROUTE_PREFIX', 'api/mongo-crud'),
    'enable_routes' => env('MONGOCRUD_ENABLE_ROUTES', true),
    'pagination' => [
        'per_page' => env('MONGOCRUD_PER_PAGE', 20),
        'max_per_page' => env('MONGOCRUD_MAX_PER_PAGE', 100),
    ],
];
```

## API Endpoints

### Test Connection
```
GET /api/mongo-crud/test
```

### Demo01 CRUD Operations

#### 1. Lấy danh sách (GET)
```
GET /api/mongo-crud/demo01
```

**Query Parameters:**
- `search` - Tìm kiếm theo tên
- `status` - Lọc theo trạng thái (active/inactive)
- `min_age`, `max_age` - Lọc theo độ tuổi
- `tag` - Lọc theo tag
- `start_date`, `end_date` - Lọc theo ngày tạo
- `sort_by` - Sắp xếp theo field (mặc định: created_at)
- `sort_order` - Thứ tự sắp xếp (asc/desc, mặc định: desc)
- `per_page` - Số records mỗi trang

**Ví dụ:**
```
GET /api/mongo-crud/demo01?search=John&status=active&min_age=18&max_age=65&per_page=10
```

#### 2. Tạo mới (POST)
```
POST /api/mongo-crud/demo01
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "phone": "0123456789",
    "address": "123 Main St",
    "age": 30,
    "status": true,
    "description": "Sample description",
    "metadata": {
        "department": "IT",
        "position": "Developer"
    },
    "tags": ["developer", "fullstack"]
}
```

#### 3. Xem chi tiết (GET)
```
GET /api/mongo-crud/demo01/{id}
```

#### 4. Cập nhật (PUT)
```
PUT /api/mongo-crud/demo01/{id}
Content-Type: application/json

{
    "name": "John Smith",
    "age": 31,
    "status": false
}
```

#### 5. Xóa (DELETE)
```
DELETE /api/mongo-crud/demo01/{id}
```

#### 6. Thống kê (GET)
```
GET /api/mongo-crud/demo01/stats/overview
```

**Response:**
```json
{
    "success": true,
    "data": {
        "total": 100,
        "active": 85,
        "inactive": 15,
        "recent": 12,
        "avg_age": 32.5
    }
}
```

#### 7. Bulk Operations (POST)
```
POST /api/mongo-crud/demo01/bulk
Content-Type: application/json

{
    "action": "delete",
    "ids": ["id1", "id2", "id3"]
}
```

**Actions:**
- `delete` - Xóa nhiều records
- `activate` - Kích hoạt nhiều records
- `deactivate` - Vô hiệu hóa nhiều records

## Sử dụng trong Code

### 1. Sử dụng Model

```php
use YourCompany\MongoCrud\Models\Demo01;

// Tạo mới
$record = Demo01::create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 30,
    'status' => true
]);

// Tìm kiếm
$records = Demo01::where('status', true)
    ->ageRange(18, 65)
    ->search('name', 'John')
    ->paginate(20);

// Cập nhật
$record = Demo01::find($id);
$record->update(['age' => 31]);

// Xóa
Demo01::find($id)->delete();
```

### 2. Scopes có sẵn

```php
// Lọc theo trạng thái
Demo01::active()->get();
Demo01::inactive()->get();

// Lọc theo độ tuổi
Demo01::ageRange(18, 65)->get();

// Lọc theo tag
Demo01::withTag('developer')->get();

// Tìm kiếm
Demo01::search('name', 'John')->get();

// Lọc theo ngày
Demo01::dateRange('created_at', '2024-01-01', '2024-12-31')->get();
```

## Validation Rules

### Tạo mới (POST)
- `name` - required|string|max:255
- `email` - required|email|max:255
- `phone` - nullable|string|max:20
- `address` - nullable|string|max:500
- `age` - nullable|integer|min:0|max:150
- `status` - nullable|boolean
- `description` - nullable|string|max:1000
- `metadata` - nullable|array
- `tags` - nullable|array

### Cập nhật (PUT)
- Tất cả fields đều optional (sử dụng `sometimes` rule)

## Response Format

### Success Response
```json
{
    "success": true,
    "data": {...},
    "message": "Operation completed successfully"
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error message",
    "errors": {...}
}
```

## Mở rộng Package

### 1. Tạo Model mới

```php
<?php

namespace YourCompany\MongoCrud\Models;

class YourModel extends BaseModel
{
    protected $collection = 'your_collection';
    
    protected $fillable = [
        'field1', 'field2', 'field3'
    ];
    
    // Custom scopes và methods
}
```

### 2. Tạo Controller mới

```php
<?php

namespace YourCompany\MongoCrud\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use YourCompany\MongoCrud\Models\YourModel;

class YourController extends Controller
{
    // Implement CRUD methods
}
```

### 3. Thêm routes

Trong `src/Routes/api.php`:

```php
Route::prefix('your-model')->group(function () {
    Route::get('/', [YourController::class, 'index']);
    Route::post('/', [YourController::class, 'store']);
    // ... other routes
});
```

## Testing

### Test với curl

```bash
# Test connection
curl -X GET http://your-domain.com/api/mongo-crud/test

# Create record
curl -X POST http://your-domain.com/api/mongo-crud/demo01 \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "age": 25,
    "status": true
  }'

# Get records
curl -X GET "http://your-domain.com/api/mongo-crud/demo01?per_page=5"

# Update record
curl -X PUT http://your-domain.com/api/mongo-crud/demo01/{id} \
  -H "Content-Type: application/json" \
  -d '{"age": 26}'

# Delete record
curl -X DELETE http://your-domain.com/api/mongo-crud/demo01/{id}
```

## Requirements

- PHP 8.2+
- Laravel 11+
- MongoDB
- mongodb/laravel-mongodb ^5.0

## License

MIT License 