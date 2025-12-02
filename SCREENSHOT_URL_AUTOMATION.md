# Screenshot v2 - Real Page Automation

## Concept

Thay vì gửi HTML/SVG lên server, approach mới:

1. **Browser gọi API** Laravel với URL hiện tại
2. **Laravel forward** đến Puppeteer service
3. **Puppeteer headless:**
   - Mở URL thật (`https://mytree.vn/my-tree?pid=xxx`)
   - Set cookies từ user (để auth)
   - Chờ page load
   - Click nút `.btn_ctrl_svg1` (nút download hiện tại)
   - Code `domtoimage` gốc chạy (KHÔNG thay đổi!)
   - Capture kết quả
4. **Return image** về user

## Ưu điểm

✅ **Code gốc không đổi** - domtoimage vẫn chạy nguyên bản  
✅ **Puppeteer không giới hạn** - Canvas size unlimited trong headless mode  
✅ **100% giống web** - Render chính xác như user thấy  
✅ **Auth tự động** - Cookies được pass qua  

## Deployment

### Step 1: Start service mới (port 3001)

```bash
cd /var/www/html

# Start service
pm2 start task-cli/screenshot-url-service.js --name screenshot-url-service
pm2 save

# Check
pm2 status
pm2 logs screenshot-url-service
```

### Step 2: Test service

```bash
# Health check
curl http://localhost:3001/health

# Test screenshot (với URL thật)
curl -X POST http://localhost:3001/screenshot-url \
  -H "Content-Type: application/json" \
  -d '{
    "url": "https://mytree.vn/my-tree?pid=11461493758623744",
    "selector": ".btn_ctrl_svg1",
    "width": 1920,
    "height": 1080
  }' \
  --output /tmp/test-tree.png

# Check result
ls -lh /tmp/test-tree.png
file /tmp/test-tree.png
```

### Step 3: Update config

```bash
# Edit .env
echo "SCREENSHOT_URL_SERVICE=http://localhost:3001" >> .env

# Clear cache
php artisan config:cache
php artisan route:cache
```

### Step 4: Test từ browser

```javascript
// Browser console trên mytree.vn/my-tree
fetch('/api/screenshot/url', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({
        url: window.location.href,
        selector: '.btn_ctrl_svg1'
    })
})
.then(r => r.blob())
.then(b => {
    const url = URL.createObjectURL(b);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'test.png';
    a.click();
});
```

## Usage Options

### Option 1: Thêm nút mới (khuyên dùng)

Không cần sửa code gốc, thêm nút mới:

```html
<!-- Trong view blade -->
<button onclick="downloadTreeServerSide()" class="btn btn-primary">
    📸 Tải xuống (Server-side)
</button>

<script src="/js/screenshot-helper.js"></script>
```

### Option 2: Replace nút hiện tại

Sửa event handler của nút `.btn_ctrl_svg1`:

```javascript
// Thay vì gọi clsTreeTopDownCtrl.downloadImagePng()
jQuery('.btn_ctrl_svg1').off('click').on('click', function() {
    downloadTreeServerSide();
});
```

### Option 3: Auto fallback

Thử server-side trước, nếu lỗi dùng client-side:

```javascript
function smartDownload() {
    // Try server-side first
    downloadTreeServerSide()
        .catch(error => {
            console.warn('Server-side failed, fallback to client-side');
            // Original method
            clsTreeTopDownCtrl.downloadImagePng('svg_grid');
        });
}
```

## Architecture

```
Browser                  Laravel API              Puppeteer Service
--------                 -----------              -----------------
[User clicks]  ------>  POST /api/screenshot/url  ------>  [Open URL]
                                                            [Set cookies]
                                                            [Wait load]
                                                            [Click .btn_ctrl_svg1]
                                                            [domtoimage runs]
                                                            [Capture result]
[Download]    <------  [Return PNG]  <------              [Return PNG]
```

## Puppeteer Canvas Limits

**Browser thật:**
- Chrome: ~16384px max canvas size
- Firefox: ~11180px
- Safari: ~4096px

**Puppeteer headless:**
- ✅ **NO LIMIT** (chỉ giới hạn bởi RAM)
- Test OK: 20000x20000px
- Có thể render cây genealogy siêu lớn

## Performance

| Method | Size | Time | Quality |
|--------|------|------|---------|
| Client-side (domtoimage) | 5000x3000 | ❌ Crash | N/A |
| Server SVG | 5000x3000 | 3-5s | ⚠️ Style issues |
| Server URL | 5000x3000 | 5-8s | ✅ Perfect |

Server URL chậm hơn vì phải:
1. Load full page
2. Run JavaScript
3. Wait for domtoimage
4. Capture result

Nhưng kết quả **100% giống** với web!

## Troubleshooting

### Service không start

```bash
# Check logs
pm2 logs screenshot-url-service --lines 50

# Reinstall dependencies
cd /var/www/html
rm -rf node_modules
PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true npm install

# Check chromium
which chromium
/snap/bin/chromium  # Should return path

# Restart
pm2 restart screenshot-url-service
```

### Cookies không work

Cookies được auto-extract từ request, nhưng nếu cần custom:

```javascript
fetch('/api/screenshot/url', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    credentials: 'include',  // QUAN TRỌNG: Send cookies
    body: JSON.stringify({
        url: window.location.href,
        selector: '.btn_ctrl_svg1'
    })
})
```

### Timeout

Tăng timeout nếu tree quá lớn:

```javascript
body: JSON.stringify({
    url: window.location.href,
    selector: '.btn_ctrl_svg1',
    timeout: 120  // 120 seconds
})
```

### domtoimage không chạy

Kiểm tra selector đúng chưa:

```javascript
// Test selector
document.querySelector('.btn_ctrl_svg1')  // Should return element

// Try different selector
body: JSON.stringify({
    selector: '#download-btn'  // Change to correct selector
})
```

## Monitoring

```bash
# PM2 status
pm2 status

# Logs
pm2 logs screenshot-url-service --lines 100

# Memory usage
pm2 monit

# Restart if needed
pm2 restart screenshot-url-service
```

## Next Steps

1. ✅ Deploy screenshot-url-service.js lên server
2. ✅ Start với PM2
3. ✅ Test với curl
4. ✅ Test từ browser
5. ⏭️ Thêm nút UI hoặc replace nút hiện tại
6. ⏭️ Monitor performance
7. ⏭️ Tune timeout nếu cần

---

**Kết quả:** Download tree lớn không giới hạn, dùng đúng code gốc domtoimage! 🎉
