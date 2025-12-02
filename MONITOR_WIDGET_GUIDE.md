# 🎯 Monitor Graph Widget - Complete Guide

## 📦 Tổng quan

Hệ thống monitoring graph widgets hoàn chỉnh với:
- ✅ API Controller với 4 endpoints
- ✅ Chart.js widget có thể nhúng vào bất kỳ HTML nào
- ✅ CSS responsive đẹp mắt
- ✅ Auto-refresh tự động
- ✅ Demo page đầy đủ

---

## 🚀 Cài đặt

### 1. Đăng ký Routes

Thêm vào `routes/api.php`:

```php
require __DIR__ . '/api_monitor_graph.php';
```

### 2. Test API Endpoints

```bash
# Test uptime endpoint
curl "http://your-domain/api/monitor-graph/uptime?monitor_id=1&period=24h"

# Test response time endpoint
curl "http://your-domain/api/monitor-graph/response-time?monitor_id=1&period=7d"

# Test system metrics endpoint
curl "http://your-domain/api/monitor-graph/system-metrics?metric_type=cpu_usage&period=24h"

# Get available metric types
curl "http://your-domain/api/monitor-graph/metric-types"
```

### 3. View Demo Page

Mở browser: `http://your-domain/monitor-demo.html`

---

## 📊 Cách sử dụng `monitor_checks` (Uptime/Response Time)

### Bảng `monitor_checks`:

```sql
CREATE TABLE `monitor_checks` (
  `id` bigint(20) NOT NULL,
  `time` datetime NOT NULL,
  `monitor_id` int(11) NOT NULL,
  `status` int(11) DEFAULT NULL COMMENT '1 = success, -1 = error',
  `response_time` decimal(10,2) DEFAULT NULL COMMENT 'milliseconds',
  `message` text DEFAULT NULL
);
```

### Insert data mẫu:

```php
// Website check - UP
DB::table('monitor_checks')->insert([
    'time' => now(),
    'monitor_id' => 1,
    'status' => 1,           // 1 = SUCCESS
    'response_time' => 120.50, // milliseconds
    'message' => 'Website is up'
]);

// API check - DOWN
DB::table('monitor_checks')->insert([
    'time' => now(),
    'monitor_id' => 2,
    'status' => -1,          // -1 = FAILED
    'response_time' => null,
    'message' => 'Connection timeout'
]);
```

### Hiển thị graph:

```html
<!-- Uptime Status -->
<div id="uptime-widget" 
     data-auto-init="monitor-widget"
     data-type="uptime"
     data-monitor-id="1"
     data-period="24h">
</div>

<!-- Response Time -->
<div id="response-widget"
     data-auto-init="monitor-widget"
     data-type="response-time"
     data-monitor-id="1"
     data-period="7d">
</div>
```

---

## 📈 Cách sử dụng `monitor_system_metrics`

### Bảng `monitor_system_metrics`:

```sql
CREATE TABLE `monitor_system_metrics` (
  `id` bigint(20) NOT NULL,
  `time` datetime NOT NULL,
  `metric_type` varchar(100) NOT NULL,
  `value` decimal(15,6) DEFAULT NULL,
  `tags` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags`))
);
```

### Use Cases:

#### 1. **CPU Usage** (%)
```php
DB::table('monitor_system_metrics')->insert([
    'time' => now(),
    'metric_type' => 'cpu_usage',
    'value' => 65.5,
    'tags' => json_encode(['server' => 'web-01', 'core' => 'all'])
]);
```

```html
<div id="cpu-widget"
     data-auto-init="monitor-widget"
     data-type="system-metrics"
     data-metric-type="cpu_usage"
     data-period="24h">
</div>
```

#### 2. **Memory Usage** (GB hoặc %)
```php
DB::table('monitor_system_metrics')->insert([
    'time' => now(),
    'metric_type' => 'memory_usage',
    'value' => 8.2,  // GB used
    'tags' => json_encode(['server' => 'web-01', 'total' => 16])
]);
```

#### 3. **Disk I/O** (MB/s)
```php
DB::table('monitor_system_metrics')->insert([
    'time' => now(),
    'metric_type' => 'disk_io',
    'value' => 45.3,  // MB/s
    'tags' => json_encode(['operation' => 'read', 'disk' => 'sda1'])
]);
```

#### 4. **Network Bandwidth** (Mbps)
```php
DB::table('monitor_system_metrics')->insert([
    'time' => now(),
    'metric_type' => 'network_bandwidth',
    'value' => 125.8,  // Mbps
    'tags' => json_encode(['interface' => 'eth0', 'direction' => 'upload'])
]);
```

#### 5. **Database Connections**
```php
DB::table('monitor_system_metrics')->insert([
    'time' => now(),
    'metric_type' => 'db_connections',
    'value' => 45,
    'tags' => json_encode(['pool_size' => 100, 'database' => 'main'])
]);
```

#### 6. **Application Metrics** (Custom)
```php
// Active users
DB::table('monitor_system_metrics')->insert([
    'time' => now(),
    'metric_type' => 'active_users',
    'value' => 1250,
    'tags' => json_encode(['platform' => 'web'])
]);

// Request rate (requests/minute)
DB::table('monitor_system_metrics')->insert([
    'time' => now(),
    'metric_type' => 'request_rate',
    'value' => 850.5,
    'tags' => json_encode(['endpoint' => '/api/*'])
]);

// Error rate (%)
DB::table('monitor_system_metrics')->insert([
    'time' => now(),
    'metric_type' => 'error_rate',
    'value' => 2.3,
    'tags' => json_encode(['http_code' => '5xx'])
]);
```

---

## 🎨 Nhúng Widget vào HTML

### Cách 1: Auto-init với data attributes

```html
<!DOCTYPE html>
<html>
<head>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="/css/monitor-widget.css">
</head>
<body>
    <div id="my-monitor" 
         data-auto-init="monitor-widget"
         data-type="uptime"
         data-monitor-id="1"
         data-period="24h"
         data-auto-refresh="60000">
    </div>
    
    <script src="/js/monitor-widget.js"></script>
</body>
</html>
```

### Cách 2: Manual JavaScript init

```html
<div id="my-monitor"></div>

<script>
new MonitorWidget('my-monitor', {
    type: 'response-time',
    monitorId: 1,
    period: '7d',
    autoRefresh: 30000,  // 30 seconds
    showLegend: true,
    showStats: true,
    height: '400px'
});
</script>
```

### Cách 3: Trong Blade template

```blade
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Server Monitoring Dashboard</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div id="website-uptime"
                 data-auto-init="monitor-widget"
                 data-type="uptime"
                 data-monitor-id="{{ $monitor->id }}"
                 data-period="24h">
            </div>
        </div>
        
        <div class="col-md-6">
            <div id="cpu-usage"
                 data-auto-init="monitor-widget"
                 data-type="system-metrics"
                 data-metric-type="cpu_usage"
                 data-period="24h">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="/js/monitor-widget.js"></script>
@endpush

@push('styles')
<link rel="stylesheet" href="/css/monitor-widget.css">
@endpush
```

---

## ⚙️ Configuration Options

```javascript
new MonitorWidget('element-id', {
    // Required
    type: 'uptime|response-time|system-metrics',
    
    // For uptime & response-time
    monitorId: 1,
    
    // For system-metrics
    metricType: 'cpu_usage|memory_usage|disk_io|...',
    
    // Optional
    period: '1h|24h|7d|30d|90d',     // Default: 24h
    height: '300px',                  // Chart height
    autoRefresh: 60000,               // Auto-refresh ms (0 = off)
    showLegend: true,                 // Show chart legend
    showStats: true,                  // Show statistics
    apiBase: '/api/monitor-graph',    // API base URL
});
```

---

## 🔧 Widget Methods

```javascript
const widget = new MonitorWidget('my-widget', options);

// Manual refresh
widget.refresh();

// Change time period
widget.changePeriod('7d');

// Start auto-refresh
widget.startAutoRefresh();

// Stop auto-refresh
widget.stopAutoRefresh();

// Destroy widget
widget.destroy();
```

---

## 📱 Responsive Design

Widget tự động responsive cho:
- 🖥️ Desktop (1200px+)
- 💻 Tablet (768px - 1199px)
- 📱 Mobile (< 768px)

CSS classes có sẵn:
```html
<!-- Small widget -->
<div class="monitor-widget widget-sm">...</div>

<!-- Large widget -->
<div class="monitor-widget widget-lg">...</div>

<!-- Dark theme -->
<div class="monitor-widget dark-theme">...</div>
```

---

## 🎯 Example: Complete Dashboard

```html
<!DOCTYPE html>
<html>
<head>
    <title>Monitoring Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="/css/monitor-widget.css">
    <style>
        .dashboard { display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 20px; }
    </style>
</head>
<body>
    <h1>Server Monitoring Dashboard</h1>
    
    <div class="dashboard">
        <!-- Website Uptime -->
        <div id="website-uptime"
             data-auto-init="monitor-widget"
             data-type="uptime"
             data-monitor-id="1"
             data-period="24h"
             data-auto-refresh="60000">
        </div>
        
        <!-- API Response Time -->
        <div id="api-response"
             data-auto-init="monitor-widget"
             data-type="response-time"
             data-monitor-id="2"
             data-period="24h"
             data-auto-refresh="60000">
        </div>
        
        <!-- CPU Usage -->
        <div id="cpu-usage"
             data-auto-init="monitor-widget"
             data-type="system-metrics"
             data-metric-type="cpu_usage"
             data-period="24h"
             data-auto-refresh="30000">
        </div>
        
        <!-- Memory Usage -->
        <div id="memory-usage"
             data-auto-init="monitor-widget"
             data-type="system-metrics"
             data-metric-type="memory_usage"
             data-period="24h"
             data-auto-refresh="30000">
        </div>
    </div>
    
    <script src="/js/monitor-widget.js"></script>
</body>
</html>
```

---

## 🔍 Troubleshooting

### API returns 404
```bash
# Make sure routes are registered
php artisan route:list | grep monitor-graph
```

### Widget không hiển thị
```javascript
// Check browser console for errors
// Make sure Chart.js is loaded before monitor-widget.js
```

### Data không load
```javascript
// Check API response in Network tab
fetch('/api/monitor-graph/uptime?monitor_id=1&period=24h')
  .then(res => res.json())
  .then(data => console.log(data));
```

---

## 🎉 Done!

Files đã tạo:
- ✅ `app/Http/Controllers/Api/MonitorGraphController.php` - API Controller
- ✅ `public/js/monitor-widget.js` - Widget JavaScript
- ✅ `public/css/monitor-widget.css` - Widget CSS
- ✅ `public/monitor-demo.html` - Demo page
- ✅ `routes/api_monitor_graph.php` - API routes
- ✅ `MONITOR_WIDGET_GUIDE.md` - This guide

**Next steps:**
1. Thêm `require __DIR__ . '/api_monitor_graph.php';` vào `routes/api.php`
2. Test API: `curl http://your-domain/api/monitor-graph/uptime?monitor_id=1&period=24h`
3. Mở demo: `http://your-domain/monitor-demo.html`
4. Nhúng widget vào trang của bạn!

Enjoy! 🚀
