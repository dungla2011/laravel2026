# VPS Tables Usage Documentation

## Tổng Quan

Sau khi migrate products sang vps_plans, hệ thống VPS sử dụng 4 bảng chính:

| Bảng | Mục Đích | Dữ Liệu | Cập Nhật |
|------|---------|--------|---------|
| `vps_plans` | Kế hoạch/gói VPS | Cấu hình + giá | Tĩnh (admin định nghĩa) |
| `vps_instances` | Instances khách hàng | Cấu hình + trạng thái | Khi khách đặt/nâng cấp |
| `vps_instance_config_history` | Lịch sử thay đổi cấu hình | Snapshot cấu hình cũ | Mỗi lần nâng/hạ cấp |
| `vps_usage` | Sử dụng theo phút | Giá tính theo từng phút | Mỗi phút (định kỳ cron) |

---

## 1. vps_plans - VPS Plans (Gói VPS định nghĩa sẵn)

**Mục đích**: Định nghĩa các gói VPS chuẩn mà khách hàng có thể chọn.

**Trường dữ liệu**:
```
- id: BIGINT UNSIGNED PRIMARY KEY
- name: VARCHAR(64) - Tên gói (vd: "Basic VPS", "Professional VPS")
- status: SMALLINT - 1=active, 0=inactive
- user_id: BIGINT - Người tạo gói (có thể NULL)

- cpu: INT - Số core (2, 4, 8, 16...)
- ram_gb: INT - RAM GB (2, 4, 8, 16...)
- disk_gb: INT - Disk GB (20, 40, 80...)
- network_mbit: INT - Băng thông dedicated Mbps (0, 100, 200...)
- number_ip_address: INT DEFAULT 1 - Số IP địa chỉ

- price_per_minute: DECIMAL(18,8) - Giá/phút từ migrate
- price_per_hour: GENERATED - Giá/giờ (tự tính)

- created_at, updated_at, deleted_at, log
```

**Dữ liệu ví dụ**:
```sql
INSERT INTO vps_plans (name, cpu, ram_gb, disk_gb, network_mbit, number_ip_address, price_per_minute, status)
VALUES 
  ('Starter', 2, 2, 20, 100, 1, 50.00, 1),      -- 50đ/phút = 1.5M/tháng
  ('Professional', 4, 8, 80, 500, 2, 250.00, 1), -- 250đ/phút = 7.5M/tháng
  ('Enterprise', 8, 32, 200, 1000, 4, 900.00, 1);
```

**Ngữ cảnh sử dụng**:
- Hiển thị trên trang chọn gói VPS (`vps.blade.php` - query từ vps_plans)
- Tạo admin interface quản lý gói
- Reference khi khách đặt hàng

---

## 2. vps_instances - VPS Instances (Instances khách hàng)

**Mục đích**: Lưu thông tin instance VPS của từng khách hàng đã mua.

**Trường dữ liệu**:
```
- id: BIGINT UNSIGNED PRIMARY KEY
- name: VARCHAR(64) - Tên instance (vd: "customer1-vps-001")
- status: SMALLINT - 1=running, 0=stopped, -1=deleted
- user_id: BIGINT - Khách hàng
- plan_id: BIGINT - Reference đến vps_plans (gói ban đầu)

- cpu, ram_gb, disk_gb, network_mbit, number_ip_address: INT
  → Cấu hình hiện tại (có thể khác plan_id nếu đã upgrade)
  
- price_per_minute: DECIMAL(18,8) - Giá/phút hiện tại
- vmware_vm_id: VARCHAR(128) - ID trên VMware/KVM
- power_state: VARCHAR(32) - powered_on/powered_off

- created_at, updated_at, deleted_at, log
```

**Dữ liệu ví dụ**:
```sql
INSERT INTO vps_instances (name, user_id, plan_id, cpu, ram_gb, disk_gb, network_mbit, number_ip_address, price_per_minute, power_state)
VALUES
  ('cust123-vps1', 100, 1, 2, 2, 20, 100, 1, 50.00, 'powered_on'),
  ('cust456-vps1', 101, 2, 4, 8, 80, 500, 2, 250.00, 'powered_on');
```

**Ngữ cảnh sử dụng**:
- Khách đặt hàng → tạo instance mới
- Khách upgrade/downgrade → update CPU/RAM/Disk/Network
- Dashboard customer: hiển thị instances và giá
- Billing: tính tiền theo giá trong instance

---

## 3. vps_instance_config_history - Lịch Sử Cấu Hình

**Mục đích**: Ghi lại mỗi lần thay đổi cấu hình (upgrade/downgrade) để audit và tính tiền theo đúng thời điểm.

**Trường dữ liệu**:
```
- id: BIGINT UNSIGNED PRIMARY KEY
- name: VARCHAR(64) - Tên ghi chú
- status: SMALLINT
- user_id: BIGINT
- instance_id: BIGINT - Reference vps_instances

- cpu, ram_gb, disk_gb, network_mbit, number_ip_address: INT
  → Cấu hình tại thời điểm thay đổi
  
- price_per_minute: DECIMAL(18,8) - Giá/phút tại thời điểm đó
- change_type: VARCHAR(64) - 'upgrade', 'downgrade', 'create'
- changed_at: DATETIME - Khi thay đổi

- created_at, updated_at, deleted_at, log
```

**Dữ liệu ví dụ**:
```sql
-- Tạo instance lần đầu
INSERT INTO vps_instance_config_history 
(instance_id, cpu, ram_gb, disk_gb, network_mbit, number_ip_address, price_per_minute, change_type)
VALUES
(1, 2, 2, 20, 100, 1, 50.00, 'create');

-- Upgrade từ 2 core 2GB RAM sang 4 core 8GB RAM
INSERT INTO vps_instance_config_history 
(instance_id, cpu, ram_gb, disk_gb, network_mbit, number_ip_address, price_per_minute, change_type)
VALUES
(1, 4, 8, 80, 500, 2, 250.00, 'upgrade');
```

**Ngữ cảnh sử dụng**:
- Khi khách upgrade/downgrade → thêm record này
- Tính tiền: chia khoảng thời gian, dùng price_per_minute tương ứng
- Audit log: khách nâng cấp bao nhiêu, khi nào
- Report: tổng quát upgrade/downgrade

---

## 4. vps_usage - Sử Dụng Theo Phút

**Mục đích**: Ghi lại sử dụng từng phút (cron job định kỳ) để tính tiền chính xác.

**Trường dữ liệu**:
```
- id: BIGINT UNSIGNED PRIMARY KEY
- name: VARCHAR(64) - Tên ghi chú
- status: SMALLINT
- user_id: BIGINT
- instance_id: BIGINT - Reference vps_instances

- timestamp_minute: DATETIME - Thời điểm phút này (vd: 2024-11-21 10:30:00)
- number_ip_address: INT - Số IP tại thời điểm này
- price_per_minute: DECIMAL(18,8) - Giá/phút tại thời điểm
- power_state: VARCHAR(32) - 'running', 'stopped'

- created_at, updated_at, deleted_at, log
```

**Dữ liệu ví dụ**:
```sql
-- Mỗi phút instance 1 chạy, cron job insert:
INSERT INTO vps_usage (instance_id, timestamp_minute, number_ip_address, price_per_minute, power_state)
VALUES
(1, '2024-11-21 10:30:00', 1, 50.00, 'running'),
(1, '2024-11-21 10:31:00', 1, 50.00, 'running'),
(1, '2024-11-21 10:32:00', 1, 50.00, 'running'),
...

-- Sau khi upgrade, price thay đổi:
(1, '2024-11-21 11:00:00', 2, 250.00, 'running'),  -- upgraded
(1, '2024-11-21 11:01:00', 2, 250.00, 'running'),
```

**Ngữ cảnh sử dụng**:
- Cron job (mỗi phút): query vps_instances, kiểm tra trạng thái, insert vào vps_usage
- Billing tính tiền: sum(price_per_minute) từ vps_usage trong khoảng thời gian
- Report: instance nào chạy bao nhiêu phút, tốn bao nhiêu tiền
- Xuất hóa đơn: chi tiết từng khoảng thời gian upgrade

---

## Luồng Quy Trình Thực Tế

### 1. Khách chọn gói (vps.blade.php)
```
vps.blade.php: Query vps_plans → Hiển thị gói → Khách chọn plan_id
```

### 2. Tạo đơn hàng
```
Order created → OrderItem có reference plan_id
→ Create vps_instances record với CPU/RAM/Disk/Network từ plan
```

### 3. Instance được khởi động
```
vps_instances.power_state = 'powered_on'
vps_instances.vmware_vm_id = (ID từ VMware API)

Cron job mỗi phút:
  SELECT vps_instances WHERE power_state='powered_on'
  INSERT INTO vps_usage (timestamp_minute, price_per_minute, ...)
```

### 4. Khách upgrade gói
```
Update vps_instances: cpu=8, ram_gb=32, price_per_minute=900
Insert vps_instance_config_history: change_type='upgrade'

Cron job phút sau sẽ dùng price_per_minute mới (900)
```

### 5. Tính tiền cuối tháng
```
SELECT SUM(price_per_minute) FROM vps_usage 
WHERE instance_id=? AND timestamp_minute BETWEEN 'start' AND 'end'
= Tổng tiền tháng

Chi tiết từ vps_instance_config_history:
  - Phút 1-1000: price=50 → 50,000đ
  - Phút 1001-2000: price=250 → 250,000đ
  - TOTAL: 300,000đ
```

---

## Migration từ Product → VpsPlan

**Script**: `migrate_products_to_vps_plans.php`

Chuyển đổi:
```
Product (type='vps_glx') + ProductAttribute
    ↓ (migrate_products_to_vps_plans.php)
VpsPlan table

Tính giá:
  Product.price (per tháng, VND)
  ÷ (30 * 24 * 60) = price_per_minute
```

**Ưu điểm**:
- ✅ Dữ liệu VPS tập trung vào bảng riêng
- ✅ Dễ quản lý cấu hình (không phải join ProductAttribute)
- ✅ Dễ tracking usage (vps_usage + vps_instance_config_history)
- ✅ Dễ billing tính tiền chính xác

---

## Các Trường Dùng Chung

Tất cả 4 bảng có trường:
```
- id: BIGINT UNSIGNED PRIMARY KEY
- status: SMALLINT (1=active, 0=inactive, -1=deleted)
- user_id: BIGINT (nullable, ai tạo record)
- created_at, updated_at, deleted_at, log
```

**Lợi ích**:
- Soft delete (deleted_at)
- Audit trail (log field)
- Tracking người tạo (user_id)
- Timestamp (created_at, updated_at)

---

## Query Ví Dụ

### Hiển thị các plan active
```sql
SELECT * FROM vps_plans WHERE status=1;
```

### Instances của khách hàng
```sql
SELECT * FROM vps_instances WHERE user_id=100 AND deleted_at IS NULL;
```

### Lịch sử upgrade của instance
```sql
SELECT * FROM vps_instance_config_history 
WHERE instance_id=1 
ORDER BY changed_at DESC;
```

### Tính tiền tháng này
```sql
SELECT instance_id, SUM(price_per_minute) as total_price
FROM vps_usage
WHERE timestamp_minute >= '2024-11-01' 
  AND timestamp_minute < '2024-12-01'
GROUP BY instance_id;
```

### Tính tiền chi tiết theo cấu hình
```sql
SELECT 
  h.cpu, h.ram_gb, h.disk_gb, h.number_ip_address,
  h.price_per_minute,
  COUNT(u.id) as minutes_used,
  h.price_per_minute * COUNT(u.id) as total_price
FROM vps_instance_config_history h
LEFT JOIN vps_usage u ON u.instance_id = h.instance_id 
  AND u.timestamp_minute >= h.changed_at
  AND u.timestamp_minute < COALESCE(
      (SELECT MIN(changed_at) FROM vps_instance_config_history 
       WHERE instance_id=h.instance_id AND changed_at > h.changed_at),
      NOW()
    )
WHERE h.instance_id = 1
GROUP BY h.id, h.cpu, h.ram_gb, h.disk_gb, h.price_per_minute;
```

---

## Summary

| Bảng | Khi Tạo | Khi Cập Nhật | Khi Xóa |
|------|---------|------------|---------|
| **vps_plans** | Admin define gói | Edit gói (ít) | Soft delete |
| **vps_instances** | Khách đặt hàng | Upgrade/Downgrade | Khách cancel |
| **vps_instance_config_history** | Mỗi thay đổi config | - | Không xóa (audit) |
| **vps_usage** | Cron job mỗi phút | - | Không xóa (billing) |

**Dòng tiền**:
```
vps_plans.price_per_minute (gốc)
  → vps_instances.price_per_minute (hiện tại)
    → vps_instance_config_history.price_per_minute (snapshot)
      → vps_usage.price_per_minute (ghi lại mỗi phút)
        → Billing: SUM(vps_usage.price_per_minute) = tiền khách trả
```
