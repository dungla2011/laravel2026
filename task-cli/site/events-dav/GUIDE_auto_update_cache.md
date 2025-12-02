# GUIDE: Auto-Update Cache Feature

## Tổng quan
Face API hiện đã có tính năng tự động cập nhật cache mỗi 10 giây để đảm bảo dữ liệu luôn được đồng bộ với server.

## Tính năng chính

### 1. Background Thread
- Tự động chạy nền khi server khởi động
- Cập nhật face cache mỗi 10 giây
- Daemon thread (tự động dừng khi server dừng)

### 2. API Endpoints mới

#### GET /cache_status
```json
{
  "status": "success",
  "data": {
    "total_entries": 150,
    "entries": [...],
    "auto_update_enabled": true,
    "auto_update_interval": 10
  }
}
```

#### POST /start_auto_update
Khởi động auto-update (nếu chưa chạy)
```json
{
  "status": "success",
  "message": "Auto update started"
}
```

#### POST /stop_auto_update
Dừng auto-update
```json
{
  "status": "success", 
  "message": "Auto update stopped"
}
```

### 3. Log Messages
- `🔄 Starting background cache updater (every 10 seconds)...`
- `⏰ Auto-updating face cache...`
- `✅ Face cache updated: 100 → 120 entries`
- `✅ Face cache refreshed: 120 entries`

## Cách sử dụng

### 1. Khởi động server
```bash
venv\Scripts\Activate.ps1 ; python face_api.py
```
Auto-update sẽ tự động bắt đầu.

### 2. Kiểm tra trạng thái
```php
$ch = curl_init("http://localhost:50000/cache_status");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
```

### 3. Tạm dừng auto-update
```php
$ch = curl_init("http://localhost:50000/stop_auto_update");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
```

### 4. Khởi động lại auto-update
```php
$ch = curl_init("http://localhost:50000/start_auto_update");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
```

## Lợi ích

1. **Tự động đồng bộ**: Không cần reload manual
2. **Hiệu suất cao**: Update trong background, không ảnh hưởng API
3. **Linh hoạt**: Có thể bật/tắt theo nhu cầu
4. **Monitoring**: Track được trạng thái qua API

## Lưu ý

- Interval mặc định: 10 giây
- Thread chạy daemon mode
- Tự động retry nếu có lỗi (chờ 5 giây)
- Graceful shutdown khi server dừng

## Test

Chạy file test:
```bash
php test_php_api.php
```

Test script sẽ kiểm tra:
- Cache status với auto-update info
- Start/stop auto-update
- Các API khác vẫn hoạt động bình thường 