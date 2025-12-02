@echo off
echo ========================================
echo          FACE API RUNNER
echo ========================================

REM Cấu hình đường dẫn venv (thay đổi theo nhu cầu)
set VENV_PATH=E:\Python_Environments\face_api_env
set USE_ABSOLUTE_PATH=false

if "%USE_ABSOLUTE_PATH%"=="true" (
    echo Using venv from: %VENV_PATH%
    call "%VENV_PATH%\Scripts\activate.bat"
) else (
    echo Using local venv...
    call "venv\Scripts\activate.bat"
)

echo Starting Face API...
python face_api.py

pause 