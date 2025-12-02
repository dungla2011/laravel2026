# Test Face API với PowerShell

Write-Host "=== Test 1: get_face_vector với form-data ===" -ForegroundColor Green

$uri = "http://localhost:50000/get_face_vector"
$body = @{
    image_link = "https://events.dav.edu.vn/test_cloud_file?fid=4866"
}

try {
    $response = Invoke-RestMethod -Uri $uri -Method Post -Body $body
    Write-Host "Response: $($response | ConvertTo-Json)" -ForegroundColor Yellow
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

Write-Host "=== Test 2: get_face_vector với JSON ===" -ForegroundColor Green

$uri = "http://localhost:50000/get_face_vector"
$body = @{
    image_link = "https://events.dav.edu.vn/test_cloud_file?fid=4866"
} | ConvertTo-Json

try {
    $response = Invoke-RestMethod -Uri $uri -Method Post -Body $body -ContentType "application/json"
    Write-Host "Response: $($response | ConvertTo-Json)" -ForegroundColor Yellow
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

Write-Host "=== Test 3: Cache status ===" -ForegroundColor Green

$uri = "http://localhost:50000/cache_status"

try {
    $response = Invoke-RestMethod -Uri $uri -Method Get
    Write-Host "Response: $($response | ConvertTo-Json)" -ForegroundColor Yellow
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host ""

Write-Host "=== Test 4: Reload cache ===" -ForegroundColor Green

$uri = "http://localhost:50000/reload_face_cache"

try {
    $response = Invoke-RestMethod -Uri $uri -Method Post
    Write-Host "Response: $($response | ConvertTo-Json)" -ForegroundColor Yellow
} catch {
    Write-Host "Error: $($_.Exception.Message)" -ForegroundColor Red
}

Write-Host "" 