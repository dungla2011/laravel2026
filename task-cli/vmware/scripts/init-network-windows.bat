@echo off
REM ============================================================================
REM init-network-windows.bat
REM 
REM Pure Batch script for Windows network configuration
REM - Get MAC address of first NIC
REM - Query metadata server
REM - Apply IP/Gateway/DNS using netsh
REM - Set computer name
REM
REM Metadata Response Format (comma-separated):
REM ip,subnet_mask,gateway,dns1,dns2,hostname
REM 10.0.1.10,255.255.255.0,10.0.1.1,8.8.8.8,8.8.4.4,vps-prod-10
REM
REM Usage: Right-click → Run as administrator
REM ============================================================================

setlocal enabledelayedexpansion

REM ============================================================================
REM Configuration
REM ============================================================================
set METADATA_SERVER=10.1.1.1
set METADATA_URL=http://%METADATA_SERVER%/tool/get_ip_glx.php
set LOG_FILE=%SystemRoot%\Logs\init-network.log
set MAX_RETRIES=5
set RETRY_INTERVAL=5

REM Colors
color 0A
title Windows Network Initialization

REM ============================================================================
REM Check Administrator
REM ============================================================================
net session >nul 2>&1
if %errorLevel% neq 0 (
    cls
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
echo  Windows Network Initialization
echo ============================================================================
echo.
echo [OK] Running as Administrator
echo.

REM Create log directory if not exists
if not exist "%SystemRoot%\Logs" mkdir "%SystemRoot%\Logs"

REM ============================================================================
REM Function: Log message
REM ============================================================================
setlocal enabledelayedexpansion
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c-%%a-%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a:%%b)

call :log "========== Network Configuration Started =========="
call :log "System: %COMPUTERNAME%"
call :log "OS: %OS%"

REM ============================================================================
REM Get MAC Address of first NIC
REM ============================================================================
call :log "Getting network adapter information..."

REM Simple approach: use ipconfig and parse Physical Address
setlocal enabledelayedexpansion
set "MAC_FOUND=0"

for /f "tokens=*" %%a in ('ipconfig /all') do (
    set "line=%%a"
    
    REM Check if line contains "Physical Address"
    echo !line! | findstr /I "Physical Address" >nul
    if !errorLevel! equ 0 (
        REM Extract MAC from line - split by colon only, take second token
        REM Format: "Physical Address. . . . . . . . . . . . : 00-0C-29-1F-DD-BC"
        for /f "tokens=2 delims=:" %%b in ("!line!") do (
            REM Trim leading/trailing spaces
            set "MAC=%%b"
            REM Remove leading spaces
            for /f "tokens=* " %%c in ("!MAC!") do set "MAC=%%c"
            if not "!MAC!"=="" (
                set "MAC_FOUND=1"
                goto found_mac
            )
        )
    )
)

:found_mac
if "!MAC_FOUND!"=="0" (
    call :log "ERROR: Could not get MAC address"
    call :log_error "FAILED"
)

REM Trim spaces from MAC
for /f "tokens=* " %%a in ("!MAC!") do set "MAC=%%a"

call :log "MAC Address: !MAC!"

REM Show network config for debugging
call :log "Available network adapters:"
netsh interface ipv4 show interfaces >> "%LOG_FILE%"

REM ============================================================================
REM Query Metadata Server
REM ============================================================================
call :log "Querying metadata server..."
set RETRY_COUNT=0

:retry_metadata
set /a RETRY_COUNT+=1
if %RETRY_COUNT% gtr %MAX_RETRIES% (
    call :log "ERROR: Failed to get metadata after %MAX_RETRIES% attempts"
    call :log_error "FAILED"
    exit /b 1
)

call :log "Attempt %RETRY_COUNT%/%MAX_RETRIES%..."

REM Build and display the URL being called
set FULL_URL=%METADATA_URL%?mac=%MAC%
call :log "URL: %FULL_URL%"

set "METADATA_RESPONSE="
for /f "delims=" %%a in ('curl -s --connect-timeout 10 "%METADATA_URL%?mac=%MAC%" 2^>nul') do (
    set "METADATA_RESPONSE=%%a"
)

if not defined METADATA_RESPONSE (
    call :log "No response from server, retrying..."
    timeout /t %RETRY_INTERVAL% /nobreak >nul
    goto retry_metadata
)

call :log "Metadata received: %METADATA_RESPONSE%"

REM ============================================================================
REM Parse Configuration (comma-separated format)
REM ip,subnet_mask,gateway,dns1,dns2,hostname
REM ============================================================================
call :parse_config "%METADATA_RESPONSE%"

REM ============================================================================
REM Validate IP Address
REM ============================================================================
if not defined IP_ADDRESS (
    call :log "ERROR: No IP address in metadata"
    call :log_error "FAILED"
    exit /b 1
)

call :log "Configuration:"
call :log "   IP: %IP_ADDRESS%"
call :log "   Subnet: %SUBNET_MASK%"
call :log "   Gateway: %GATEWAY%"
call :log "   DNS1: %DNS1%"
call :log "   DNS2: %DNS2%"
call :log "   Hostname: %HOSTNAME%"

REM ============================================================================
REM Configure Network using netsh
REM ============================================================================
call :log "Configuring network interface..."

REM Get first network adapter index
for /f "tokens=1" %%a in ('netsh interface ipv4 show interfaces ^| findstr /R "[0-9]"') do (
    set INTERFACE_INDEX=%%a
    goto found_interface
)

:found_interface
if not defined INTERFACE_INDEX (
    call :log "ERROR: Could not find network interface"
    call :log_error "FAILED"
    exit /b 1
)

call :log "Interface Index: %INTERFACE_INDEX%"

REM Remove existing static IP
call :log "Removing existing static IP configuration..."
netsh interface ipv4 set address name="%INTERFACE_INDEX%" dhcp >nul 2>&1

timeout /t 2 /nobreak >nul

REM Set static IP with netsh
call :log "Setting IP: %IP_ADDRESS% / %SUBNET_MASK% / %GATEWAY%"
netsh interface ipv4 set address name="%INTERFACE_INDEX%" static %IP_ADDRESS% %SUBNET_MASK% %GATEWAY% >nul

if %ERRORLEVEL% neq 0 (
    call :log "WARNING: netsh command returned error %ERRORLEVEL%"
) else (
    call :log "IP address set successfully"
)

REM Set Primary DNS
if defined DNS1 (
    call :log "Setting DNS1: %DNS1%"
    netsh interface ipv4 set dnsservers name="%INTERFACE_INDEX%" static %DNS1% primary >nul
)

REM Set Secondary DNS
if defined DNS2 (
    call :log "Setting DNS2: %DNS2%"
    netsh interface ipv4 add dnsservers name="%INTERFACE_INDEX%" %DNS2% index=2 >nul
)

REM ============================================================================
REM Set Computer Name (Hostname)
REM ============================================================================
if not "%HOSTNAME%"=="" if not "%HOSTNAME%"=="null" (
    call :log "Setting computer name: %HOSTNAME%"
    
    if not "%HOSTNAME%"=="%COMPUTERNAME%" (
        REM Computer name change requires reboot
        wmic computersystem where name="%COMPUTERNAME%" rename name="%HOSTNAME%" >nul 2>&1
        call :log "Computer name set (reboot required to take effect)"
    ) else (
        call :log "Computer name already correct"
    )
)

REM ============================================================================
REM Verify Configuration
REM ============================================================================
call :log "Verifying network configuration..."
timeout /t 3 /nobreak >nul

REM Show ipconfig
echo.
call :log "Current Network Configuration:"
echo.
ipconfig /all >> "%LOG_FILE%"
ipconfig /all

REM ============================================================================
REM Verify IP was set correctly
REM ============================================================================
call :log "Verifying IP configuration..."

REM Check if our IP appears in ipconfig
findstr /I "%IP_ADDRESS%" >nul < <(ipconfig /all)
if %errorLevel% equ 0 (
    call :log "IP address verified: %IP_ADDRESS%"
) else (
    call :log "WARNING: IP address %IP_ADDRESS% not found in ipconfig"
)

REM ============================================================================
REM Test Internet connectivity
REM ============================================================================
call :log "Testing internet connectivity..."

REM Ping 8.8.8.8 (up to 4 pings, timeout 5 seconds)
ping -n 4 -w 5000 8.8.8.8 >nul 2>&1
if %errorLevel% equ 0 (
    call :log "Internet connectivity verified (8.8.8.8 ping successful)"
) else (
    call :log "WARNING: Cannot ping 8.8.8.8, but continuing..."
)

REM ============================================================================
REM Callback to metadata server
REM ============================================================================
call :log "Notifying metadata server (set_ip_done=1)..."

set CALLBACK_URL=%METADATA_URL%?mac=%MAC%^&set_ip_done=1
call :log "Callback URL: %CALLBACK_URL%"

set "CALLBACK_RESPONSE="
for /f "delims=" %%a in ('curl -s --connect-timeout 10 "%METADATA_URL%?mac=%MAC%^&set_ip_done=1" 2^>nul') do (
    set "CALLBACK_RESPONSE=%%a"
)

if defined CALLBACK_RESPONSE (
    call :log "Callback response: %CALLBACK_RESPONSE%"
) else (
    call :log "No response from callback (may be normal)"
)

REM ============================================================================
REM Success
REM ============================================================================
echo.
color 0B
call :log "========== Network Configuration Completed Successfully =========="
echo.
echo [SUCCESS] Network configuration completed!
echo.
echo IP Address: %IP_ADDRESS%
echo Hostname: %HOSTNAME%
echo Log file: %LOG_FILE%
echo.

pause

exit /b 0

REM ============================================================================
REM Subroutines
REM ============================================================================

REM Log function
:log
set LOG_MSG=%~1
for /f "tokens=2-4 delims=/ " %%a in ('date /t') do (set mydate=%%c-%%a-%%b)
for /f "tokens=1-2 delims=/:" %%a in ('time /t') do (set mytime=%%a:%%b)
echo [!mydate! !mytime!] !LOG_MSG!
echo [!mydate! !mytime!] !LOG_MSG! >> "%LOG_FILE%"
goto :eof

REM Log error function
:log_error
color 0C
echo.
echo ============================================================================
echo  %~1
echo ============================================================================
echo.
pause
exit /b 1

REM Parse comma-separated configuration
:parse_config
setlocal enabledelayedexpansion
set "config=%~1"

REM Split by comma
for /f "tokens=1 delims=," %%a in ("!config!") do set "IP_ADDRESS=%%a"
for /f "tokens=2 delims=," %%a in ("!config!") do set "SUBNET_MASK=%%a"
for /f "tokens=3 delims=," %%a in ("!config!") do set "GATEWAY=%%a"
for /f "tokens=4 delims=," %%a in ("!config!") do set "DNS1=%%a"
for /f "tokens=5 delims=," %%a in ("!config!") do set "DNS2=%%a"
for /f "tokens=6 delims=," %%a in ("!config!") do set "HOSTNAME=%%a"

REM Return values
endlocal & set "IP_ADDRESS=%IP_ADDRESS%" & set "SUBNET_MASK=%SUBNET_MASK%" & set "GATEWAY=%GATEWAY%" & set "DNS1=%DNS1%" & set "DNS2=%DNS2%" & set "HOSTNAME=%HOSTNAME%"

goto :eof

