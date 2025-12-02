# MongoDB Indexes cho Service Manager

Để tối ưu hiệu suất, bạn nên tạo các indexes sau cho MongoDB:

## 1. Service Plans Collection

```javascript
// Tạo indexes cho service_plans collection
db.service_plans.createIndex({ "status": 1 })
db.service_plans.createIndex({ "category": 1 })
db.service_plans.createIndex({ "created_at": -1 })
```

## 2. Services Collection

```javascript
// Tạo indexes cho services collection
db.services.createIndex({ "user_id": 1 })
db.services.createIndex({ "plan_id": 1 })
db.services.createIndex({ "status": 1 })
db.services.createIndex({ "next_billing_date": 1 })
db.services.createIndex({ "user_id": 1, "status": 1 })
db.services.createIndex({ "created_at": -1 })
```

## 3. Billing Records Collection

```javascript
// Tạo indexes cho billing_records collection
db.billing_records.createIndex({ "user_id": 1 })
db.billing_records.createIndex({ "service_id": 1 })
db.billing_records.createIndex({ "status": 1 })
db.billing_records.createIndex({ "due_date": 1 })
db.billing_records.createIndex({ "user_id": 1, "status": 1 })
db.billing_records.createIndex({ "billing_start_date": 1, "billing_end_date": 1 })
db.billing_records.createIndex({ "created_at": -1 })
```

## 4. Resource Usage Collection

```javascript
// Tạo indexes cho resource_usage collection
db.resource_usage.createIndex({ "service_id": 1 })
db.resource_usage.createIndex({ "change_date": -1 })
db.resource_usage.createIndex({ "service_id": 1, "change_date": -1 })
```

## 5. User Balances Collection

```javascript
// Tạo indexes cho user_balances collection
db.user_balances.createIndex({ "user_id": 1 }, { unique: true })
```

## 6. Balance Transactions Collection

```javascript
// Tạo indexes cho balance_transactions collection
db.balance_transactions.createIndex({ "user_id": 1 })
db.balance_transactions.createIndex({ "type": 1 })
db.balance_transactions.createIndex({ "user_id": 1, "created_at": -1 })
db.balance_transactions.createIndex({ "created_at": -1 })
```

## Chạy tất cả indexes

Bạn có thể chạy script sau trong MongoDB shell:

```javascript
// Kết nối đến database
use service_manager

// Service Plans
db.service_plans.createIndex({ "status": 1 })
db.service_plans.createIndex({ "category": 1 })
db.service_plans.createIndex({ "created_at": -1 })

// Services
db.services.createIndex({ "user_id": 1 })
db.services.createIndex({ "plan_id": 1 })
db.services.createIndex({ "status": 1 })
db.services.createIndex({ "next_billing_date": 1 })
db.services.createIndex({ "user_id": 1, "status": 1 })
db.services.createIndex({ "created_at": -1 })

// Billing Records
db.billing_records.createIndex({ "user_id": 1 })
db.billing_records.createIndex({ "service_id": 1 })
db.billing_records.createIndex({ "status": 1 })
db.billing_records.createIndex({ "due_date": 1 })
db.billing_records.createIndex({ "user_id": 1, "status": 1 })
db.billing_records.createIndex({ "billing_start_date": 1, "billing_end_date": 1 })
db.billing_records.createIndex({ "created_at": -1 })

// Resource Usage
db.resource_usage.createIndex({ "service_id": 1 })
db.resource_usage.createIndex({ "change_date": -1 })
db.resource_usage.createIndex({ "service_id": 1, "change_date": -1 })

// User Balances
db.user_balances.createIndex({ "user_id": 1 }, { unique: true })

// Balance Transactions
db.balance_transactions.createIndex({ "user_id": 1 })
db.balance_transactions.createIndex({ "type": 1 })
db.balance_transactions.createIndex({ "user_id": 1, "created_at": -1 })
db.balance_transactions.createIndex({ "created_at": -1 })

print("All indexes created successfully!")
```

## Kiểm tra indexes

Để kiểm tra indexes đã được tạo:

```javascript
// Kiểm tra indexes của từng collection
db.service_plans.getIndexes()
db.services.getIndexes()
db.billing_records.getIndexes()
db.resource_usage.getIndexes()
db.user_balances.getIndexes()
db.balance_transactions.getIndexes()
``` 