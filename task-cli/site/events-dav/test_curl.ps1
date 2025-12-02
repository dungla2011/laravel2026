# Test Face API với curl commands
# Đảm bảo face_api.py đã chạy trước khi test

Write-Host "🚀 FACE API CURL TEST" -ForegroundColor Green
Write-Host "=" * 50

$API_BASE = "http://localhost:5000"

# Test 1: Update Face Cache
Write-Host "`n🧪 TEST 1: UPDATE_FACE" -ForegroundColor Cyan
$updateData = @{
    face_array = @(
        @{
            id = "user1"
            name = "Test User 1"
            face = @(0.1) * 512
            url_confirm = "https://example.com/user1"
        },
        @{
            id = "user2"
            name = "Test User 2"
            face = @(0.2) * 512
            url_confirm = "https://example.com/user2"
        }
    )
} | ConvertTo-Json -Depth 3

try {
    $response = Invoke-RestMethod -Uri "$API_BASE/update_face" -Method POST -Body $updateData -ContentType "application/json"
    Write-Host "✅ Update Face: $($response.status)" -ForegroundColor Green
    Write-Host "Response: $($response | ConvertTo-Json)"
} catch {
    Write-Host "❌ Update Face Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: Get Face Vector
Write-Host "`n🧪 TEST 2: GET_FACE_VECTOR" -ForegroundColor Cyan
$vectorData = @{
    image_link = "https://upload.wikimedia.org/wikipedia/commons/thumb/5/50/Vd-Orig.png/256px-Vd-Orig.png"
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri "$API_BASE/get_face_vector" -Method POST -Body $vectorData -ContentType "application/json"
    Write-Host "✅ Get Vector: $($response.status)" -ForegroundColor Green
    if ($response.status -eq "success") {
        Write-Host "📊 Vector length: $($response.vector.Count)"
        Write-Host "📊 Sample: $($response.vector[0..2])"
    } else {
        Write-Host "❌ Error: $($response.error)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Get Vector Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 3: Detect Face (cần tạo file test trước)
Write-Host "`n🧪 TEST 3: DETECT_FACE" -ForegroundColor Cyan
Write-Host "📸 Creating test image..."

# Tạo ảnh test với PowerShell
Add-Type -AssemblyName System.Drawing
$bitmap = New-Object System.Drawing.Bitmap(200, 200)
$graphics = [System.Drawing.Graphics]::FromImage($bitmap)
$graphics.Clear([System.Drawing.Color]::LightBlue)
$graphics.Dispose()
$bitmap.Save("test_detect.jpg", [System.Drawing.Imaging.ImageFormat]::Jpeg)
$bitmap.Dispose()

Write-Host "📸 Test image created: test_detect.jpg"

try {
    $response = Invoke-RestMethod -Uri "$API_BASE/detect_face" -Method POST -InFile "test_detect.jpg" -ContentType "multipart/form-data"
    Write-Host "✅ Detect Face: $($response.status)" -ForegroundColor Green
    if ($response.status -eq "success") {
        Write-Host "🔍 Detected ID: $($response.data.id)"
        Write-Host "🔗 Confirm URL: $($response.data.url_confirm)"
    } else {
        Write-Host "❌ Error: $($response.error)" -ForegroundColor Red
    }
} catch {
    Write-Host "❌ Detect Face Error: $($_.Exception.Message)" -ForegroundColor Red
}

# Cleanup
Remove-Item "test_detect.jpg" -ErrorAction SilentlyContinue

Write-Host "`n🎉 Test hoàn tất!" -ForegroundColor Green
Write-Host "=" * 50 