# PowerShell script to set DHCP and download setip.zip
# Run with: powershell -ExecutionPolicy Bypass -File setip.ps1
# Invoke-Expression (Invoke-WebRequest -Uri http://10.1.1.1/setup/setip.ps1 -UseBasicParsing).Content

Write-Host "====================================" -ForegroundColor Cyan
Write-Host "GlxService Setup Script" -ForegroundColor Cyan
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""

# Step 1: Set DHCP for first network adapter
Write-Host "[Step 1] Setting DHCP for network adapter..." -ForegroundColor Yellow

try {
    # Get first connected network adapter (exclude loopback, tunnel, etc)
    $adapter = Get-NetAdapter | Where-Object { 
        $_.Status -eq "Up" -and 
        $_.InterfaceDescription -notlike "*Loopback*" -and
        $_.InterfaceDescription -notlike "*Tunnel*" -and
        $_.InterfaceDescription -notlike "*Virtual*"
    } | Select-Object -First 1
    
    if ($adapter) {
        $adapterName = $adapter.Name
        Write-Host "  Found adapter: $adapterName" -ForegroundColor Green
        
        # Set DHCP for IPv4
        netsh interface ipv4 set address name="$adapterName" dhcp | Out-Null
        netsh interface ipv4 set dnsservers name="$adapterName" dhcp | Out-Null
        
        Write-Host "  DHCP enabled successfully" -ForegroundColor Green
    } else {
        Write-Host "  WARNING: No active network adapter found" -ForegroundColor Red
    }
} catch {
    Write-Host "  ERROR: Failed to set DHCP - $($_.Exception.Message)" -ForegroundColor Red
}

# Step 2: Wait 3 seconds
Write-Host ""
Write-Host "[Step 2] Waiting 3 seconds..." -ForegroundColor Yellow
Start-Sleep -Seconds 3

# Step 3: Download setip.zip
Write-Host ""
Write-Host "[Step 3] Downloading setip.zip..." -ForegroundColor Yellow

$downloadUrl = "http://10.1.1.1/setup/setip.zip"
$downloadsFolder = [Environment]::GetFolderPath("UserProfile") + "\Downloads"
$zipPath = Join-Path $downloadsFolder "setip.zip"
$exePath = Join-Path $downloadsFolder "GlxService.exe"

try {
    Write-Host "  URL: $downloadUrl" -ForegroundColor Gray
    Write-Host "  Destination: $zipPath" -ForegroundColor Gray
    
    # Download file using WebClient
    $webClient = New-Object System.Net.WebClient
    $webClient.DownloadFile($downloadUrl, $zipPath)
    $webClient.Dispose()
    
    Write-Host "  Download completed" -ForegroundColor Green
} catch {
    Write-Host "  ERROR: Failed to download - $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "Press any key to exit..." -ForegroundColor Yellow
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
    exit 1
}

# Step 4: Backup existing GlxService.exe if exists
Write-Host ""
Write-Host "[Step 4] Checking for existing GlxService.exe..." -ForegroundColor Yellow

if (Test-Path $exePath) {
    $unixTime = [int][double]::Parse((Get-Date -UFormat %s))
    $backupPath = "$exePath.$unixTime"
    
    Write-Host "  Found existing file, renaming to: GlxService.exe.$unixTime" -ForegroundColor Yellow
    
    try {
        Move-Item -Path $exePath -Destination $backupPath -Force
        Write-Host "  Backup completed" -ForegroundColor Green
    } catch {
        Write-Host "  ERROR: Failed to rename - $($_.Exception.Message)" -ForegroundColor Red
    }
} else {
    Write-Host "  No existing file found" -ForegroundColor Gray
}

# Step 5: Extract setip.zip
Write-Host ""
Write-Host "[Step 5] Extracting setip.zip..." -ForegroundColor Yellow

try {
    # Extract zip file to Downloads folder
    Expand-Archive -Path $zipPath -DestinationPath $downloadsFolder -Force
    
    Write-Host "  Extraction completed" -ForegroundColor Green
    
    # Verify GlxService.exe exists
    if (Test-Path $exePath) {
        $fileSize = (Get-Item $exePath).Length
        Write-Host "  GlxService.exe extracted successfully ($fileSize bytes)" -ForegroundColor Green
    } else {
        Write-Host "  WARNING: GlxService.exe not found in zip file" -ForegroundColor Red
    }
    
    # Clean up zip file
    Remove-Item $zipPath -Force -ErrorAction SilentlyContinue
    Write-Host "  Cleaned up zip file" -ForegroundColor Gray
    
} catch {
    Write-Host "  ERROR: Failed to extract - $($_.Exception.Message)" -ForegroundColor Red
}

# Step 6: Summary
Write-Host ""
Write-Host "====================================" -ForegroundColor Cyan
Write-Host "Setup Completed!" -ForegroundColor Green
Write-Host "====================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "GlxService.exe location: $exePath" -ForegroundColor White
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Install service: cd Downloads && .\GlxService.exe install" -ForegroundColor Gray
Write-Host "  2. Start service:   .\GlxService.exe start" -ForegroundColor Gray
Write-Host ""
Write-Host "Press any key to exit..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
