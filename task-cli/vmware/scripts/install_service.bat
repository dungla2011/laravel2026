@echo off
REM ============================================================================
REM install_service.bat
REM
REM Create Windows Service for init_network.exe using sc.exe
REM Service runs at startup with SYSTEM privileges (no UAC prompt)
REM
REM Usage: Right-click and Run as administrator
REM ============================================================================

setlocal enabledelayedexpansion

REM Check Administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    color 0C
    echo.
    echo [ERROR] This script must be run as Administrator!
    echo.
    echo Please right-click this file and select "Run as administrator"
    echo.
    pause
    exit /b 1
)

color 0A
cls
echo.
echo ============================================================================
echo  Windows Service Installation - init_network
echo ============================================================================
echo.

REM Configuration
set SERVICE_NAME=InitNetwork
set DISPLAY_NAME=VPS Network Initialization
set DESCRIPTION=Initializes VPS network configuration from metadata server on startup
set EXE_PATH=%~dp0dist\init_network.exe

REM Check if EXE exists
if not exist "%EXE_PATH%" (
    color 0C
    echo [ERROR] EXE not found: %EXE_PATH%
    echo.
    echo Please run build_exe.bat first to create the executable
    echo.
    pause
    exit /b 1
)

color 0A
echo [OK] EXE found: %EXE_PATH%
echo.

REM Check if service already exists
sc query "%SERVICE_NAME%" >nul 2>&1
if %errorLevel% equ 0 (
    echo [INFO] Service '%SERVICE_NAME%' already exists, removing...
    
    REM Stop service if running
    net stop "%SERVICE_NAME%" >nul 2>&1
    echo [OK] Service stopped
    
    REM Delete service
    sc delete "%SERVICE_NAME%" >nul 2>&1
    timeout /t 2 /nobreak >nul
    echo [OK] Service removed
)

echo.
echo [INFO] Creating service '%SERVICE_NAME%'...
echo.

REM Create service with sc.exe
REM  obj= "LocalSystem" = Run as SYSTEM account (no UAC)
REM  start= auto = Auto-start on boot
REM  type= own = Own process (not shared)
REM  error= normal = Normal error handling

sc create "%SERVICE_NAME%" ^
    binPath= "%EXE_PATH%" ^
    DisplayName= "%DISPLAY_NAME%" ^
    start= auto ^
    type= own ^
    error= normal

if %errorLevel% neq 0 (
    color 0C
    echo [ERROR] Failed to create service
    echo.
    pause
    exit /b 1
)

echo [OK] Service created successfully
echo.

REM Set description via registry
reg add "HKLM\SYSTEM\CurrentControlSet\Services\%SERVICE_NAME%" /v Description /d "%DESCRIPTION%" /f >nul 2>&1
echo [OK] Description set

REM Configure to run as SYSTEM (auto-elevate, no UAC prompt)
sc config "%SERVICE_NAME%" obj= "LocalSystem" password= "" >nul 2>&1
echo [OK] Service configured to run as SYSTEM (no UAC prompt)

REM Enable restart on failure
sc failure "%SERVICE_NAME%" reset= 300 actions= restart/10000 >nul 2>&1
echo [OK] Failure recovery configured (restart on failure)

echo.
echo [INFO] Starting service...
net start "%SERVICE_NAME%"

timeout /t 2 /nobreak >nul

REM Check service status
sc query "%SERVICE_NAME%" | findstr /I "STATE" >nul
if %errorLevel% equ 0 (
    echo [OK] Service is ready
) else (
    echo [WARNING] Service may start at next boot
)

echo.
echo ============================================================================
echo  Installation Complete
echo ============================================================================
echo.
echo Service Details:
echo   Name: %SERVICE_NAME%
echo   Display Name: %DISPLAY_NAME%
echo   Binary: %EXE_PATH%
echo   Start Type: Automatic
echo   Run As: SYSTEM (no UAC prompt)
echo.
echo Log file: %%TEMP%%\set_ip.log
echo.
echo Service will run automatically on system startup
echo.
echo To manage service:
echo   Start:   net start %SERVICE_NAME%
echo   Stop:    net stop %SERVICE_NAME%
echo   Status:  sc query %SERVICE_NAME%
echo   Remove:  sc delete %SERVICE_NAME%
echo.

pause
