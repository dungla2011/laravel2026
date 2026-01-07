@echo off
REM ============================================================================
REM build_exe_console.bat
REM 
REM Build init_network.py into EXE with console window (for debugging)
REM ============================================================================

echo.
echo ============================================================================
echo  Building init_network.py into EXE (with console)
echo ============================================================================
echo.

python --version >nul 2>&1
if %errorLevel% neq 0 (
    echo [ERROR] Python is not installed or not in PATH
    pause
    exit /b 1
)

echo [OK] Python found
python --version

python -m pip show pyinstaller >nul 2>&1
if %errorLevel% neq 0 (
    echo [INFO] PyInstaller not found, installing...
    python -m pip install pyinstaller
)

echo.
echo [INFO] Building executable with console window...
echo.

python -m PyInstaller ^
    --onefile ^
    --name init_network_console ^
    --clean ^
    init_network.py

echo.
echo ============================================================================
echo  Build Complete
echo ============================================================================
echo.

if exist "dist\init_network_console.exe" (
    echo [SUCCESS] Console EXE created: dist\init_network_console.exe
    echo.
    echo This version shows console output (useful for debugging)
    echo.
) else (
    echo [ERROR] Build failed
    exit /b 1
)

echo.
pause
