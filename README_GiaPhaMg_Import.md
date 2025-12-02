# GiaPhaMg Import Guide

Hướng dẫn import dữ liệu từ MySQL `GiaPha` sang MongoDB `GiaPhaMg` với trường `idsql` để giữ ID gốc.

## 📋 Tổng quan

- **Nguồn**: MySQL table `GiaPha` 
- **Đích**: MongoDB collection `GiaPhaMg`
- **Trường đặc biệt**: `idsql` - lưu ID gốc từ MySQL
- **Tổng số bản ghi**: ~378,000 records

## 🚀 Các lệnh import

### 1. Import thường (có kiểm tra duplicate)
```bash
php artisan import:giaphamg --batch=1000
```

**Tùy chọn:**
- `--batch=1000`: Số bản ghi mỗi batch (mặc định: 1000)
- `--truncate`: Xóa collection trước khi import
- `--no-check`: Bỏ qua kiểm tra duplicate (nhanh hơn)

**Ví dụ:**
```bash
# Import với batch 2000, xóa dữ liệu cũ, không kiểm tra duplicate
php artisan import:giaphamg --batch=2000 --truncate --no-check
```

### 2. Fast Import (không kiểm tra duplicate)
```bash
php artisan fast-import:giaphamg --batch=5000
```

**Đặc điểm:**
- Tốc độ: 1000-5000 records/giây
- Không kiểm tra duplicate
- Phù hợp cho import lần đầu

## 🔍 Kiểm tra dữ liệu

### Kiểm tra sau import
```bash
php artisan check:giaphamg --sample=10
```

**Hiển thị:**
- Số lượng records MySQL vs MongoDB
- Tỷ lệ import hoàn thành
- Sample records với trường `idsql`
- Kiểm tra duplicate
- Range của `idsql`

### Xóa dữ liệu (nếu cần)
```bash
php artisan clear:giaphamg --force
```

## 📊 Cấu trúc dữ liệu

### Model GiaPhaMg
```php
class GiaPhaMg extends Mongo1
{
    protected $connection = 'mongodb';
    protected $collection = 'giaphamg';
    
    protected $casts = [
        'idsql' => 'integer', // ID gốc từ MySQL
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
```

### Trường đặc biệt
- `_id`: MongoDB ObjectId (tự động tạo)
- `idsql`: ID gốc từ MySQL (integer)
- Các trường khác: giữ nguyên từ MySQL

## 💡 Sử dụng sau import

### Tìm record theo ID MySQL cũ
```php
// Cách 1: Query trực tiếp
$record = GiaPhaMg::where('idsql', 123)->first();

// Cách 2: Sử dụng scope
$record = GiaPhaMg::bySqlId(123)->first();

// Cách 3: Alias (tương thích)
$record = GiaPhaMg::byMysqlId(123)->first();
```

### Thống kê
```php
// Đếm tổng records
$total = GiaPhaMg::count();

// Đếm records có idsql
$withIdsql = GiaPhaMg::whereNotNull('idsql')->count();

// Range idsql
$min = GiaPhaMg::whereNotNull('idsql')->min('idsql');
$max = GiaPhaMg::whereNotNull('idsql')->max('idsql');
```

## ⚡ Hiệu suất

### Benchmark
- **Import thường**: ~20-50 records/giây (có kiểm tra duplicate)
- **Fast import**: ~1000-5000 records/giây (không kiểm tra)
- **Thời gian ước tính**: 1-19 phút (tùy batch size)

### Tối ưu hóa
```bash
# Cho dataset lớn (378k records)
php artisan fast-import:giaphamg --batch=5000

# Cho import an toàn (có kiểm tra)
php artisan import:giaphamg --batch=2000 --no-check
```

## 🛠️ Troubleshooting

### Lỗi thường gặp

1. **Connection timeout**
   ```bash
   # Giảm batch size
   php artisan fast-import:giaphamg --batch=1000
   ```

2. **Memory limit**
   ```bash
   # Tăng memory limit
   php -d memory_limit=512M artisan fast-import:giaphamg
   ```

3. **Duplicate records**
   ```bash
   # Xóa và import lại
   php artisan clear:giaphamg --force
   php artisan fast-import:giaphamg
   ```

### Kiểm tra kết nối
```bash
# Kiểm tra MySQL
php artisan tinker
>>> App\Models\GiaPha::count()

# Kiểm tra MongoDB  
>>> App\Models\GiaPhaMg::count()
```

## 📈 Monitoring

### Theo dõi tiến trình
```bash
# Terminal 1: Chạy import
php artisan fast-import:giaphamg

# Terminal 2: Theo dõi
watch -n 5 'php artisan check:giaphamg'
```

### Log files
- Laravel log: `storage/logs/laravel.log`
- MongoDB log: Kiểm tra MongoDB server logs

## 🎯 Next Steps

Sau khi import thành công:

1. **Tạo CRUD interface** tương tự TestMongo1
2. **Tạo indexes** cho trường `idsql`
3. **Backup** dữ liệu MongoDB
4. **Performance testing** với dataset lớn

```bash
# Tạo index cho idsql
php artisan tinker
>>> DB::connection('mongodb')->collection('giaphamg')->createIndex(['idsql' => 1])
``` 