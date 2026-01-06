# Hệ Thống Quản Lý Số Dư và Nạp Tiền

## 📊 Các Bảng Liên Quan

### 1. **user_balances** (Số dư người dùng)
**Model:** `UserBalance.php`

**Cột chính:**
- `user_id` - ID người dùng
- `balance` - Số dư hiện tại (VND)
- `total_recharged` - Tổng tiền nạp
- `total_spent` - Tổng tiền chi tiêu
- `low_balance_threshold` - Ngưỡng cảnh báo số dư thấp
- `last_low_balance_alert` - Lần cảnh báo cuối cùng
- `last_transaction_at` - Giao dịch cuối cùng
- `is_frozen` - Tài khoản bị đóng băng hay không
- `status` - Trạng thái (1=active, 0=inactive)

**Quan hệ:**
```php
user_balances belongsTo users
user_balances hasMany user_recharges
user_balances hasMany user_balance_transactions
```

---

### 2. **user_recharges** (Nạp tiền)
**Model:** `UserRecharge.php`

**Cột chính:**
- `id` - ID nạp tiền
- `user_id` - ID người dùng
- `amount` - Số tiền nạp (VND)
- `status` - Trạng thái: pending, completed, failed, expired
- `method` - Phương thức: bank_transfer, momo, zalopay, credit_card...
- `paid_at` - Thời gian thanh toán
- `completed_at` - Thời gian hoàn tất
- `expired_at` - Thời gian hết hạn (nếu có)
- `gateway_response` - Response từ gateway thanh toán (JSON)
- `transaction_code` - Mã giao dịch từ gateway
- `order_code` - Mã order trong hệ thống

**Quan hệ:**
```php
user_recharges belongsTo users
user_recharges hasOne user_balance_transactions (qua related_recharge_id)
```

**Trạng thái nạp tiền:**
```
pending → completed (thành công) → cập nhật user_balances
       → failed (thất bại) → giữ nguyên balance
       → expired (hết hạn)
```

---

### 3. **user_balance_transactions** (Giao dịch chi tiêu/nạp tiền)
**Model:** `UserBalanceTransaction.php`

**Cột chính:**
- `user_id` - ID người dùng
- `transaction_type` - Loại: "recharge" (nạp), "charge" (trừ), "refund" (hoàn tiền)
- `service_type` - Dịch vụ: "vps_usage", "product_purchase", "order_shipping"...
- `amount` - Số tiền (+ nạp, - trừ)
- `balance_before` - Số dư trước giao dịch
- `balance_after` - Số dư sau giao dịch
- `reference_model` - Model liên quan: "UserRecharge", "VpsUsage", "OrderInfo"...
- `reference_id` - ID của model liên quan
- `description` - Mô tả giao dịch
- `transaction_date` - Ngày giờ giao dịch
- `related_recharge_id` - ID nạp tiền (nếu là loại nạp)
- `reversed_at` - Thời gian đảo ngược (nếu hoàn tiền)
- `is_reversed` - Đã hoàn tiền hay không

**Quan hệ:**
```php
user_balance_transactions belongsTo users
user_balance_transactions belongsTo user_recharges (qua related_recharge_id)
```

---

## 💰 Quy Trình Nạp Tiền

```
User click "Nạp tiền"
    ↓
Tạo bản ghi trong user_recharges (status = pending)
    ↓
Chuyển đến gateway thanh toán (Momo, Zalopay, Bank...)
    ↓
Gateway xử lý thanh toán
    ├─ Thành công → status = completed, paid_at = now()
    │   ↓
    │   Tạo record trong user_balance_transactions:
    │   ├─ transaction_type = "recharge"
    │   ├─ amount = + X,000 (số tiền nạp)
    │   ├─ balance_after = balance_before + X,000
    │   └─ related_recharge_id = [id nạp tiền]
    │   ↓
    │   Update user_balances:
    │   ├─ balance += X,000
    │   ├─ total_recharged += X,000
    │   └─ last_transaction_at = now()
    │
    └─ Thất bại → status = failed
        ↓
        Không cập nhật balance
        ↓
        Hiển thị lỗi cho user
```

---

## 🎯 Quy Trình Chi Tiêu (VPS Usage)

```
VPS hoạt động và tính phí
    ↓
SyncVmwareInstancesCommand tính calculated_fee
    ↓
Gọi BalanceService::chargeService()
    ├─ Kiểm tra balance đủ không
    ├─ Kiểm tra account bị freeze không
    │
    ├─ Nếu OK:
    │  ├─ Tạo record trong user_balance_transactions:
    │  │  ├─ transaction_type = "charge"
    │  │  ├─ service_type = "vps_usage"
    │  │  ├─ amount = - X,000 (số tiền trừ)
    │  │  ├─ reference_model = "VpsUsage"
    │  │  └─ reference_id = [id vps usage]
    │  │
    │  └─ Update user_balances:
    │     ├─ balance -= X,000
    │     ├─ total_spent += X,000
    │     └─ last_transaction_at = now()
    │
    └─ Nếu không đủ:
       ├─ Log lỗi
       ├─ Có thể dừng VPS (nếu cấu hình)
       └─ Gửi cảnh báo cho user
```

---

## 📈 Ví Dụ Cụ Thể

### Nạp tiền:
```
User balance ban đầu: 0K

1. User nạp 1,000,000 VND
   - user_recharges: INSERT (amount=1,000,000, status=pending)
   - Gateway: Thanh toán thành công
   - user_recharges: UPDATE status=completed, paid_at=now()

2. Tạo transaction:
   - user_balance_transactions: INSERT (
       transaction_type='recharge',
       amount=+1,000,000,
       balance_before=0,
       balance_after=1,000,000,
       related_recharge_id=123
     )

3. Cập nhật số dư:
   - user_balances: UPDATE balance=1,000,000, total_recharged=1,000,000

Kết quả:
   user_balances.balance = 1,000,000K
   user_recharges [status=completed, amount=1,000,000]
   user_balance_transactions [amount=+1,000,000]
```

### Chi tiêu VPS:
```
VPS chạy 1 tháng, phí = 500,000K

1. SyncVmwareInstancesCommand chạy
   - calculated_fee = 500,000K

2. Gọi BalanceService::chargeService():
   - Kiểm tra: user_balances.balance (1,000,000) >= 500,000 ✓

3. Tạo transaction:
   - user_balance_transactions: INSERT (
       transaction_type='charge',
       service_type='vps_usage',
       amount=-500,000,
       balance_before=1,000,000,
       balance_after=500,000,
       reference_model='VpsUsage',
       reference_id=456
     )

4. Cập nhật số dư:
   - user_balances: UPDATE balance=500,000, total_spent=500,000

Kết quả:
   user_balances.balance = 500,000K
   user_balance_transactions [amount=-500,000]
   Số tiền còn lại: 500,000K
```

---

## 🔍 Xem Chi Tiết Số Dư

### Bảng `user_balances`:
```sql
SELECT 
  user_id,
  balance,
  total_recharged,
  total_spent,
  is_frozen,
  status,
  last_transaction_at
FROM user_balances
WHERE user_id = 123;
```

### Lịch sử nạp tiền:
```sql
SELECT 
  id,
  amount,
  method,
  status,
  paid_at,
  completed_at
FROM user_recharges
WHERE user_id = 123
ORDER BY created_at DESC;
```

### Lịch sử giao dịch (chi tiêu):
```sql
SELECT 
  id,
  transaction_type,
  service_type,
  amount,
  balance_before,
  balance_after,
  reference_model,
  reference_id,
  transaction_date
FROM user_balance_transactions
WHERE user_id = 123
ORDER BY transaction_date DESC;
```

---

## 💾 Liên Kết Dữ Liệu

```
users
  ├─ (1:1) → user_balances (số dư hiện tại)
  │           ├─ (1:M) → user_recharges (lịch sử nạp tiền)
  │           │           └─ (1:1) → user_balance_transactions (ghi nhận nạp tiền)
  │           │
  │           └─ (1:M) → user_balance_transactions (mọi giao dịch)
  │                       ├─ reference_model='VpsUsage' 
  │                       │   → vps_usages (chi tiêu VPS)
  │                       │
  │                       ├─ reference_model='OrderInfo'
  │                       │   → order_infos (chi tiêu đơn hàng)
  │                       │
  │                       └─ reference_model='UserRecharge'
  │                           → user_recharges (nạp tiền)
  │
  └─ (1:M) → vps_usages (sử dụng VPS)
              └─ [calculated_fee] → trừ từ user_balances
```

---

## ⚙️ Cấu Hình Thêm

### Bảng `user_balance_configs` (nếu có):
- Ngưỡng cảnh báo số dư thấp
- Phí nạp tiền (nếu có)
- Hạn mức nạp tiền tối đa
- Số dư tối thiểu để sử dụng dịch vụ

### Bảng `balance_suspension_logs`:
```
user_id
suspended_at
suspended_by
reason
released_at
```

---

## 🚨 Các Trạng Thái Quan Trọng

### Trạng thái `user_recharges.status`:
- `pending` - Chờ thanh toán
- `processing` - Đang xử lý
- `completed` - Thành công
- `failed` - Thất bại
- `cancelled` - Bị hủy
- `expired` - Hết hạn

### Trạng thái `user_balances.status`:
- `1` - Hoạt động bình thường
- `0` - Vô hiệu hóa

### Flag `user_balances.is_frozen`:
- `true` - Tài khoản bị đóng băng (không thể dùng)
- `false` - Bình thường

---

**Tóm tắt:**
- 📥 **Nạp tiền** → `user_recharges` → cập nhật `user_balances.balance`
- 📤 **Chi tiêu** → `user_balance_transactions` → trừ `user_balances.balance`
- 📊 **Lịch sử** → `user_balance_transactions` (mọi giao dịch)
- ⚠️ **Cảnh báo** → `low_balance_threshold`, `is_frozen`
