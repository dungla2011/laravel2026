@echo off
REM ============================================================================
REM build_exe.bat
REM 
REM Build init_network.py into standalone EXE using PyInstaller
REM ============================================================================

echo.
echo ============================================================================
echo  Building init_network.py into EXE
echo ============================================================================
echo.

REM Check if Python is available
python --version >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Python is not installed or not in PATH
    pause
    exit /b 1
)

echo [OK] Python found
python --version

REM Check if PyInstaller is installed
python -m pip show pyinstaller >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] PyInstaller not found, installing...
    python -m pip install pyinstaller
)

echo.
echo [INFO] Building executable...
echo.

REM Build EXE
REM --onefile: Single executable file
REM --windowed: No console window (remove for console output)
REM --name: Output filename
REM --icon: (optional) Icon file
REM --uac-admin: Request admin privileges on Windows

python -m PyInstaller ^
    --onefile ^
    --name init_network ^
    --clean ^
    init_network.py

echo.
echo ============================================================================
echo  Build Complete
echo ============================================================================
echo.

if exist "dist\init_network.exe" (
    echo [SUCCESS] EXE created: dist\init_network.exe
    echo.
    echo To use:
    echo   1. Copy dist\init_network.exe to your VPS
    echo   2. Right-click and Run as administrator
    echo   3. Check log file in %%TEMP%%\set_ip.log
    echo.
    
    REM Show file size
    for %%A in ("dist\init_network.exe") do echo Size: %%~zA bytes
) else (
    echo [ERROR] Build failed
    exit /b 1
)

echo.
pause
