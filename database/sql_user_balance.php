<?php

/**
 * User Balance Management System
 * Quản lý credit/wallet, nạp tiền, và ghi chép chi tiêu cho các dịch vụ (VPS, Hosting, etc)
 */

// ============================================================================
// TABLE 1: user_balance - Số dư tài khoản
// ============================================================================
?>

CREATE TABLE `user_balance` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT NOT NULL UNIQUE,
    
    -- Số tiền
    `balance` DECIMAL(18,2) NOT NULL DEFAULT 0,  -- Số dư hiện tại (VND)
    `total_recharged` DECIMAL(18,2) NOT NULL DEFAULT 0,  -- Tổng tiền đã nạp (VND)
    `total_spent` DECIMAL(18,2) NOT NULL DEFAULT 0,  -- Tổng tiền đã chi tiêu (VND)
    
    -- Trạng thái
    `status` SMALLINT DEFAULT 1,  -- 1=active, 0=inactive, -1=frozen
    `is_frozen` BOOLEAN DEFAULT 0,  -- Bị khóa tạm thời (nợ tiền, vi phạm, etc)
    `frozen_reason` VARCHAR(255) NULL,
    
    -- Cảnh báo
    `low_balance_threshold` DECIMAL(18,2) DEFAULT 100000,  -- Ngưỡng cảnh báo thấp (100k default)
    `last_low_balance_alert` DATETIME NULL,  -- Lần cuối cảnh báo
    
    -- Audit
    `last_transaction_at` DATETIME NULL,  -- Giao dịch cuối cùng
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_status` (`status`),
    KEY `idx_is_frozen` (`is_frozen`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


// ============================================================================
// TABLE 2: user_recharge - Lịch sử nạp tiền
// ============================================================================
?>

CREATE TABLE `user_recharge` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT NOT NULL,
    
    -- Thông tin nạp tiền
    `amount` DECIMAL(18,2) NOT NULL,  -- Số tiền nạp (VND)
    `payment_method` VARCHAR(64) NOT NULL,  -- 'credit_card', 'bank_transfer', 'paypal', 'zalo_pay', 'momo', etc
    `transaction_code` VARCHAR(128) UNIQUE NULL,  -- Mã giao dịch từ gateway
    `reference_code` VARCHAR(128) NULL,  -- Mã tham chiếu (vd: invoice number)
    
    -- Trạng thái
    `status` VARCHAR(32) NOT NULL DEFAULT 'pending',  -- pending, processing, completed, failed, cancelled
    `notes` TEXT NULL,  -- Ghi chú
    
    -- Chi tiết thanh toán
    `paid_at` DATETIME NULL,  -- Thời gian thanh toán thành công
    `completed_at` DATETIME NULL,  -- Thời gian hoàn tất (nạp vào balance)
    `expired_at` DATETIME NULL,  -- Hạn cuối để hoàn tất giao dịch (nếu dùng bank transfer)
    
    -- Gateway response
    `gateway_response` LONGTEXT NULL,  -- JSON response từ payment gateway
    
    -- Audit
    `ip_address` VARCHAR(45) NULL,
    `user_agent` TEXT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_status` (`status`),
    KEY `idx_paid_at` (`paid_at`),
    KEY `idx_transaction_code` (`transaction_code`),
    KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


// ============================================================================
// TABLE 3: user_balance_transaction - Ghi chép chi tiêu (Debit/Credit)
// ============================================================================
?>

CREATE TABLE `user_balance_transaction` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT NOT NULL,
    
    -- Loại giao dịch
    `transaction_type` VARCHAR(64) NOT NULL,  -- 'recharge', 'service_fee', 'refund', 'adjustment', 'penalty'
    `service_type` VARCHAR(64) NULL,  -- 'vps', 'hosting', 'email', 'cdn', 'ssl', etc (NULL nếu không phải service fee)
    
    -- Liên kết đến các resource
    `reference_model` VARCHAR(128) NULL,  -- 'VpsUsage', 'HostingUsage', etc
    `reference_id` BIGINT NULL,  -- ID của resource (vps_usage.id, hosting_usage.id, etc)
    `related_recharge_id` BIGINT NULL,  -- reference đến user_recharge.id (nếu là recharge)
    
    -- Số tiền
    `amount` DECIMAL(18,2) NOT NULL,  -- Số tiền giao dịch (VND)
    `balance_before` DECIMAL(18,2) NOT NULL,  -- Số dư trước giao dịch
    `balance_after` DECIMAL(18,2) NOT NULL,  -- Số dư sau giao dịch
    
    -- Mô tả
    `description` TEXT NULL,  -- Mô tả giao dịch (vd: "VPS usage: 60 phút @ 50đ/phút = 3000đ")
    `notes` TEXT NULL,
    
    -- Trạng thái
    `status` VARCHAR(32) DEFAULT 'completed',  -- 'pending', 'completed', 'failed', 'reversed'
    `is_reversed` BOOLEAN DEFAULT 0,  -- Giao dịch đã bị hủy/hoàn lại?
    `reversed_at` DATETIME NULL,
    `reversed_reason` VARCHAR(255) NULL,
    
    -- Thời gian
    `transaction_date` DATETIME NOT NULL,  -- Ngày giờ giao dịch xảy ra (có thể khác với created_at nếu là batch processing)
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    `deleted_at` DATETIME DEFAULT NULL,
    
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_transaction_type` (`transaction_type`),
    KEY `idx_service_type` (`service_type`),
    KEY `idx_reference` (`reference_model`, `reference_id`),
    KEY `idx_transaction_date` (`transaction_date`),
    KEY `idx_created_at` (`created_at`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


// ============================================================================
// TABLE 4: balance_suspension_log - Lịch sử tạm dừng dịch vụ (optional)
// ============================================================================
?>

CREATE TABLE `balance_suspension_log` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` BIGINT NOT NULL,
    
    -- Thông tin tạm dừng
    `reason` VARCHAR(255) NOT NULL,  -- 'insufficient_balance', 'account_frozen', 'payment_overdue', etc
    `suspended_at` DATETIME NOT NULL,
    `resumed_at` DATETIME NULL,  -- NULL nếu vẫn bị tạm dừng
    `duration_minutes` INT NULL,  -- Số phút tạm dừng (NULL nếu vẫn đang tạm dừng)
    
    -- Chi tiết
    `balance_at_suspension` DECIMAL(18,2) NOT NULL,
    `notes` TEXT NULL,
    
    -- Audit
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_suspended_at` (`suspended_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


// ============================================================================
// SAMPLE DATA & QUERIES
// ============================================================================
?>

-- Khởi tạo balance cho user mới
INSERT INTO user_balance (user_id, balance, total_recharged, total_spent, status)
VALUES (1, 0, 0, 0, 1);

-- Nạp tiền (pending)
INSERT INTO user_recharge (user_id, amount, payment_method, transaction_code, status)
VALUES (1, 500000, 'bank_transfer', 'BT20241124001', 'pending');

-- Nạp tiền thành công → tạo transaction và update balance
-- Step 1: Tạo transaction (recharge)
INSERT INTO user_balance_transaction 
(user_id, transaction_type, amount, balance_before, balance_after, description, status, transaction_date)
VALUES 
(1, 'recharge', 500000, 0, 500000, 'Nạp tiền qua bank transfer', 'completed', NOW());

-- Step 2: Update user_balance
UPDATE user_balance SET 
  balance = 500000, 
  total_recharged = total_recharged + 500000,
  last_transaction_at = NOW()
WHERE user_id = 1;

-- Sử dụng VPS (60 phút @ 50đ/phút = 3000đ)
-- Step 1: Tạo transaction (service_fee)
INSERT INTO user_balance_transaction 
(user_id, transaction_type, service_type, reference_model, reference_id, amount, balance_before, balance_after, description, status, transaction_date)
VALUES 
(1, 'service_fee', 'vps', 'VpsUsage', 1, -3000, 500000, 497000, 'VPS usage: 60 phút @ 50đ/phút', 'completed', NOW());

-- Step 2: Update user_balance
UPDATE user_balance SET 
  balance = balance - 3000,
  total_spent = total_spent + 3000,
  last_transaction_at = NOW()
WHERE user_id = 1;

-- Kiểm tra dư tiền trước khi tạo instance
SELECT balance FROM user_balance WHERE user_id = 1;  -- >= price_per_minute * expected_minutes?

-- Lấy lịch sử giao dịch của user
SELECT 
  t.id, t.transaction_type, t.service_type, t.amount, 
  t.balance_before, t.balance_after, t.description, t.transaction_date
FROM user_balance_transaction t
WHERE t.user_id = 1
ORDER BY t.transaction_date DESC
LIMIT 50;

-- Tổng chi tiêu theo dịch vụ
SELECT 
  service_type, 
  COUNT(*) as transactions,
  SUM(ABS(amount)) as total_spent
FROM user_balance_transaction
WHERE user_id = 1 AND transaction_type = 'service_fee'
GROUP BY service_type;

-- Tìm user cần cảnh báo balance thấp
SELECT ub.user_id, ub.balance, ub.low_balance_threshold
FROM user_balance ub
WHERE ub.balance <= ub.low_balance_threshold 
  AND ub.status = 1
  AND ub.deleted_at IS NULL;

-- Tìm user bị tạm dừng và lý do
SELECT DISTINCT u.id, u.email, bsl.reason, bsl.suspended_at
FROM users u
JOIN balance_suspension_log bsl ON u.id = bsl.user_id
WHERE bsl.resumed_at IS NULL
ORDER BY bsl.suspended_at DESC;
