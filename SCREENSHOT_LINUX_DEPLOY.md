# Screenshot Service - Linux Server Deployment Guide

## 🚀 Triển khai trên Linux Server (Production)

### Prerequisites

```bash
# Ubuntu/Debian
sudo apt update
sudo apt install -y nodejs npm
sudo npm install -g n
sudo n stable  # Install latest stable Node.js

# CentOS/RHEL
sudo yum install -y nodejs npm
```

### 1. Cài đặt Screenshot Service

```bash
# Di chuyển đến thư mục Laravel
cd /path/to/laravel01

# Copy package.json
cp package-screenshot.json package.json

# Install dependencies
npm install

# Test service locally
npm start
```

### 2. Chạy Service với PM2 (Production)

PM2 giúp service chạy persistent, auto-restart khi crash, và start on boot.

```bash
# Install PM2 globally
sudo npm install -g pm2

# Start service
pm2 start task-cli/screenshot-service.js --name screenshot-service

# Save PM2 configuration
pm2 save

# Setup auto-start on boot
pm2 startup
# Copy và chạy lệnh mà PM2 suggest

# Check status
pm2 status
pm2 logs screenshot-service
```

### 3. Cấu hình Nginx Reverse Proxy

Để Laravel API có thể gọi screenshot service qua localhost:

```nginx
# /etc/nginx/sites-available/mytree.vn

server {
    listen 80;
    server_name mytree.vn www.mytree.vn;
    root /path/to/laravel01/public;

    index index.php index.html;

    # Laravel routes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Static files
    location ~ /\.(?!well-known).* {
        deny all;
    }
}

# Reload Nginx
sudo nginx -t
sudo systemctl reload nginx
```

### 4. Cấu hình Laravel Environment

```bash
# Edit .env
nano /path/to/laravel01/.env
```

Thêm dòng này:

```ini
# Screenshot Service
SCREENSHOT_SERVICE_URL=http://localhost:3000
```

### 5. Kiểm tra Service

```bash
# Test health endpoint
curl http://localhost:3000/health

# Kết quả mong đợi:
# {"status":"ok","browser":"connected","uptime":123,...}

# Test từ Laravel
curl https://mytree.vn/api/screenshot/health
```

### 6. Monitoring và Logs

```bash
# Xem logs real-time
pm2 logs screenshot-service

# Xem status
pm2 status

# Restart nếu cần
pm2 restart screenshot-service

# Stop service
pm2 stop screenshot-service

# Remove service
pm2 delete screenshot-service
```

### 7. Firewall Configuration

Screenshot service chỉ cần lắng nghe localhost (port 3000), không cần mở ra ngoài:

```bash
# UFW (Ubuntu)
sudo ufw status
# Port 3000 KHÔNG nên được mở ra internet
# Chỉ Laravel (localhost) gọi được

# Nếu cần kiểm tra port
sudo netstat -tulpn | grep 3000
```

### 8. Performance Tuning

#### Tăng Memory Limit cho Node.js

```bash
# Edit PM2 ecosystem file
pm2 ecosystem
```

Thêm vào:

```javascript
module.exports = {
  apps: [{
    name: 'screenshot-service',
    script: './task-cli/screenshot-service.js',
    instances: 1,
    exec_mode: 'fork',
    node_args: '--max-old-space-size=4096', // 4GB RAM
    env: {
      NODE_ENV: 'production'
    }
  }]
}
```

```bash
# Restart với config mới
pm2 delete screenshot-service
pm2 start ecosystem.config.js
pm2 save
```

#### Tăng PHP Memory Limit

```bash
# Edit php.ini
sudo nano /etc/php/8.1/fpm/php.ini
```

```ini
memory_limit = 512M
max_execution_time = 120
upload_max_filesize = 50M
post_max_size = 50M
```

```bash
sudo systemctl restart php8.1-fpm
```

### 9. Backup và Restore

#### Backup

```bash
# Backup PM2 configuration
pm2 save

# Backup node_modules (optional)
tar -czf node_modules-backup.tar.gz node_modules
```

#### Restore

```bash
# Restore PM2
pm2 resurrect

# Restore dependencies
npm install
```

### 10. Troubleshooting

#### Service không start

```bash
# Check logs
pm2 logs screenshot-service --lines 100

# Check Node.js version
node --version  # Should be >= 14

# Reinstall dependencies
rm -rf node_modules package-lock.json
npm install
```

#### Laravel không gọi được service

```bash
# Test từ server
curl http://localhost:3000/health

# Check Laravel logs
tail -f storage/logs/laravel.log

# Check .env
cat .env | grep SCREENSHOT
```

#### Memory issues

```bash
# Check memory usage
free -h
pm2 monit

# Restart service
pm2 restart screenshot-service

# Increase memory limit (see Performance Tuning above)
```

#### Browser crashes

```bash
# Install dependencies
sudo apt install -y \
    gconf-service \
    libasound2 \
    libatk1.0-0 \
    libc6 \
    libcairo2 \
    libcups2 \
    libdbus-1-3 \
    libexpat1 \
    libfontconfig1 \
    libgcc1 \
    libgconf-2-4 \
    libgdk-pixbuf2.0-0 \
    libglib2.0-0 \
    libgtk-3-0 \
    libnspr4 \
    libpango-1.0-0 \
    libpangocairo-1.0-0 \
    libstdc++6 \
    libx11-6 \
    libx11-xcb1 \
    libxcb1 \
    libxcomposite1 \
    libxcursor1 \
    libxdamage1 \
    libxext6 \
    libxfixes3 \
    libxi6 \
    libxrandr2 \
    libxrender1 \
    libxss1 \
    libxtst6 \
    ca-certificates \
    fonts-liberation \
    libappindicator1 \
    libnss3 \
    lsb-release \
    xdg-utils \
    wget
```

### 11. Security Best Practices

1. **Service chỉ listen localhost**
   ```javascript
   // task-cli/screenshot-service.js
   app.listen(PORT, '127.0.0.1', () => {
       console.log(`Service running on http://127.0.0.1:${PORT}`);
   });
   ```

2. **Rate limiting trong Laravel**
   ```php
   // app/Http/Controllers/ScreenshotController.php
   public function __construct()
   {
       $this->middleware('throttle:10,1'); // 10 requests per minute
   }
   ```

3. **Validate input size**
   ```php
   'width' => 'required|integer|min:100|max:10000',
   'height' => 'required|integer|min:100|max:10000',
   ```

4. **Monitoring**
   ```bash
   # Setup monitoring alerts
   pm2 install pm2-logrotate
   pm2 set pm2-logrotate:max_size 10M
   ```

### 12. Testing Production Setup

```bash
# 1. Test service health
curl http://localhost:3000/health

# 2. Test Laravel API
curl https://mytree.vn/api/screenshot/health

# 3. Test screenshot (from browser console)
fetch('/api/screenshot/svg', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        svg_html: '<svg><circle cx="50" cy="50" r="40"/></svg>',
        bbox: { x: 0, y: 0, width: 100, height: 100 },
        scale: 2,
        format: 'png',
        filename: 'test'
    })
})
.then(r => r.blob())
.then(b => console.log('Success:', b.size))
.catch(e => console.error('Error:', e));
```

### 13. Cron Jobs (Optional)

Restart service mỗi ngày để clear memory:

```bash
# Edit crontab
crontab -e

# Add line
0 3 * * * pm2 restart screenshot-service
```

## 📊 Monitoring Dashboard

```bash
# PM2 web dashboard (optional)
pm2 install pm2-server-monit
```

Visit: `http://your-server-ip:9615`

## ✅ Quick Checklist

- [ ] Node.js >= 14 installed
- [ ] npm dependencies installed
- [ ] PM2 service running
- [ ] PM2 auto-start configured
- [ ] .env updated with SCREENSHOT_SERVICE_URL
- [ ] Nginx configured (if needed)
- [ ] Firewall: Port 3000 NOT exposed
- [ ] Health check passes: `/api/screenshot/health`
- [ ] Memory limits configured
- [ ] Logs rotation setup
- [ ] Test screenshot works from browser

## 🆘 Support

Logs locations:
- **PM2 logs**: `~/.pm2/logs/`
- **Laravel logs**: `storage/logs/laravel.log`
- **Nginx logs**: `/var/log/nginx/error.log`

Check everything:
```bash
pm2 logs screenshot-service --lines 50
tail -f storage/logs/laravel.log
sudo tail -f /var/log/nginx/error.log
```
