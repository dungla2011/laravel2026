# Simple Face API Runner
# Thay đổi đường dẫn venv theo nhu cầu

param(
    [string]$VenvPath = "E:\Python_Environments\face_api_env",
    [switch]$UseLocal = $false
)

if ($UseLocal) {
    Write-Host "Using local venv..." -ForegroundColor Yellow
    & "venv\Scripts\Activate.ps1"
} else {
    Write-Host "Using venv from: $VenvPath" -ForegroundColor Yellow
    & "$VenvPath\Scripts\Activate.ps1"
}

Write-Host "Starting Face API..." -ForegroundColor Green
python face_api.py 