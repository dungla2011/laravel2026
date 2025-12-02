@echo off
echo.
echo ======================================
echo Screenshot Service Installation
echo ======================================
echo.

REM Check if Node.js is installed
where node >nul 2>nul
if %ERRORLEVEL% NEQ 0 (
    echo [ERROR] Node.js is not installed!
    echo Please install Node.js first: https://nodejs.org/
    pause
    exit /b 1
)

echo [OK] Node.js version:
node --version
echo [OK] npm version:
npm --version
echo.

REM Copy package.json
echo Setting up package.json...
if exist package-screenshot.json (
    copy /Y package-screenshot.json package.json >nul
    echo [OK] package.json created
) else (
    echo [ERROR] package-screenshot.json not found!
    pause
    exit /b 1
)

REM Install dependencies
echo.
echo Installing dependencies (this may take a few minutes)...
echo.
call npm install

if %ERRORLEVEL% EQU 0 (
    echo.
    echo ======================================
    echo Installation completed!
    echo ======================================
    echo.
    echo Next steps:
    echo   1. Start the service:
    echo      npm start
    echo.
    echo   2. Test the service:
    echo      npm test
    echo.
    echo   3. Open demo page:
    echo      http://localhost:8000/demo-screenshot.html
    echo.
    echo   4. Read documentation:
    echo      type SCREENSHOT_SERVICE.md
    echo.
) else (
    echo.
    echo [ERROR] Installation failed!
    echo Please check the errors above
    pause
    exit /b 1
)

pause
