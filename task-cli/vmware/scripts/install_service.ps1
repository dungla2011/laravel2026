# ============================================================================
# install_service.ps1
#
# Create Windows Service for init_network.exe
# Service runs automatically at startup with SYSTEM privileges (no UAC prompt)
#
# Usage: 
#   Run as Administrator in PowerShell
#   powershell -ExecutionPolicy Bypass -File install_service.ps1
# ============================================================================

# Require administrator privileges
if (-NOT ([Security.Principal.WindowsPrincipal] [Security.Principal.WindowsIdentity]::GetCurrent()).IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)) {
    Write-Host "[ERROR] This script must be run as Administrator!" -ForegroundColor Red
    Write-Host "Right-click PowerShell and select 'Run as administrator'" -ForegroundColor Yellow
    pause
    exit 1
}

Write-Host ""
Write-Host "============================================================================"
Write-Host " Windows Service Installation - init_network"
Write-Host "============================================================================"
Write-Host ""

# Configuration
$ServiceName = "InitNetwork"
$DisplayName = "VPS Network Initialization"
$Description = "Initializes VPS network configuration from metadata server on startup"
$ExePath = "$PSScriptRoot\dist\init_network.exe"

# Check if EXE exists
if (-not (Test-Path $ExePath)) {
    Write-Host "[ERROR] EXE not found: $ExePath" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please run build_exe.bat first to create the executable" -ForegroundColor Yellow
    pause
    exit 1
}

Write-Host "[OK] EXE found: $ExePath" -ForegroundColor Green

# Check if service already exists
$existingService = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue

if ($existingService) {
    Write-Host "[INFO] Service '$ServiceName' already exists, removing..." -ForegroundColor Yellow
    
    # Stop service if running
    if ($existingService.Status -eq 'Running') {
        Stop-Service -Name $ServiceName -Force
        Write-Host "[OK] Service stopped" -ForegroundColor Green
    }
    
    # Remove service
    sc.exe delete $ServiceName | Out-Null
    Start-Sleep -Seconds 2
    Write-Host "[OK] Service removed" -ForegroundColor Green
}

# Create new service
Write-Host ""
Write-Host "[INFO] Creating service '$ServiceName'..." -ForegroundColor Cyan

# Method 1: Using sc.exe (native Windows)
$scResult = sc.exe create $ServiceName `
    binPath= "`"$ExePath`"" `
    DisplayName= "$DisplayName" `
    start= auto `
    type= own `
    error= normal

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Service created successfully" -ForegroundColor Green
} else {
    Write-Host "[ERROR] Failed to create service" -ForegroundColor Red
    Write-Host $scResult
    pause
    exit 1
}

# Set service description
$regPath = "HKLM:\SYSTEM\CurrentControlSet\Services\$ServiceName"
Set-ItemProperty -Path $regPath -Name "Description" -Value $Description
Write-Host "[OK] Description set" -ForegroundColor Green

# Set service to run as SYSTEM account (auto-elevate, no UAC prompt)
# This is the default, but let's be explicit
sc.exe config $ServiceName obj= "LocalSystem" password= "" | Out-Null
Write-Host "[OK] Service configured to run as SYSTEM (no UAC prompt)" -ForegroundColor Green

# Enable service to restart on failure
sc.exe failure $ServiceName reset= 300 actions= restart/10000 | Out-Null
Write-Host "[OK] Failure recovery configured (restart on failure)" -ForegroundColor Green

# Start service
Write-Host ""
Write-Host "[INFO] Starting service..." -ForegroundColor Cyan
Start-Service -Name $ServiceName -ErrorAction SilentlyContinue
Start-Sleep -Seconds 2

# Verify service status
$service = Get-Service -Name $ServiceName
if ($service.Status -eq 'Running') {
    Write-Host "[OK] Service is running" -ForegroundColor Green
} else {
    Write-Host "[WARNING] Service is not running, may start at next boot" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "============================================================================"
Write-Host " Installation Complete"
Write-Host "============================================================================"
Write-Host ""
Write-Host "Service Details:" -ForegroundColor Cyan
Write-Host "  Name: $ServiceName"
Write-Host "  Display Name: $DisplayName"
Write-Host "  Binary: $ExePath"
Write-Host "  Start Type: Automatic"
Write-Host "  Run As: SYSTEM (no UAC prompt)"
Write-Host ""
Write-Host "Log file: %TEMP%\set_ip.log" -ForegroundColor Green
Write-Host ""
Write-Host "Service will run automatically on system startup" -ForegroundColor Green
Write-Host ""
Write-Host "To manage service:" -ForegroundColor Cyan
Write-Host "  Start:   net start $ServiceName"
Write-Host "  Stop:    net stop $ServiceName"
Write-Host "  Status:  sc query $ServiceName"
Write-Host "  Remove:  sc delete $ServiceName (then restart)"
Write-Host ""

pause
