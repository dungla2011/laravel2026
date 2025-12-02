# 🐍 Virtual Environment Management Guide

## 📋 Tổng quan

Hướng dẫn này sẽ giúp bạn quản lý virtual environment (venv) với đường dẫn tuyệt đối, giúp dễ dàng chia sẻ và tái sử dụng giữa các project.

## 🎯 Lợi ích của việc sử dụng venv với đường dẫn tuyệt đối

### ✅ **Ưu điểm:**
- **Tái sử dụng**: Một venv có thể dùng cho nhiều project
- **Quản lý tập trung**: Tất cả venv ở một vị trí
- **Tiết kiệm dung lượng**: Không tạo venv mới cho mỗi project
- **Dễ backup**: Backup một thư mục là xong

### ❌ **Nhược điểm:**
- **Dependency conflicts**: Nếu các project cần version khác nhau
- **Portable**: Không thể di chuyển project dễ dàng
- **Path dependency**: Phụ thuộc vào đường dẫn cụ thể

## 🚀 Cách tạo và sử dụng

### 1. Tạo venv ở đường dẫn tuyệt đối

```bash
# Tạo thư mục chứa các venv
mkdir "E:\Python_Environments"

# Tạo venv cho face_api
python -m venv "E:\Python_Environments\face_api_env"

# Tạo venv cho web_scraping
python -m venv "E:\Python_Environments\web_scraping_env"

# Tạo venv cho data_analysis
python -m venv "E:\Python_Environments\data_analysis_env"
```

### 2. Activate venv

```bash
# Activate venv từ bất kỳ đâu
"E:\Python_Environments\face_api_env\Scripts\Activate.ps1"

# Hoặc dùng & để chạy
& "E:\Python_Environments\face_api_env\Scripts\Activate.ps1"
```

### 3. Cài đặt packages

```bash
# Sau khi activate
pip install flask requests opencv-python-headless insightface onnxruntime

# Hoặc từ requirements.txt
pip install -r requirements.txt

# Tạo requirements.txt
pip freeze > requirements.txt
```

## 📁 Cấu trúc thư mục đề xuất

```
E:\Python_Environments\
├── face_api_env\              # Venv cho Face API
│   ├── Scripts\
│   ├── Lib\
│   └── pyvenv.cfg
├── web_scraping_env\          # Venv cho web scraping
├── data_analysis_env\         # Venv cho data analysis
├── django_project_env\        # Venv cho Django
└── requirements\              # Lưu requirements của từng env
    ├── face_api_requirements.txt
    ├── web_scraping_requirements.txt
    └── data_analysis_requirements.txt
```

## 🔧 Scripts tự động

### 1. Script PowerShell - `run_api.ps1`

```powershell
# Activate venv và chạy Face API
& "E:\Python_Environments\face_api_env\Scripts\Activate.ps1"
python face_api.py
```

### 2. Script Batch - `run_api.bat`

```batch
@echo off
call "E:\Python_Environments\face_api_env\Scripts\activate.bat"
python face_api.py
pause
```

### 3. Script với parameters

```powershell
# run_with_params.ps1
param(
    [string]$EnvName = "face_api_env",
    [string]$BasePath = "E:\Python_Environments",
    [string]$Script = "face_api.py"
)

$venvPath = "$BasePath\$EnvName"
Write-Host "Using venv: $venvPath"
& "$venvPath\Scripts\Activate.ps1"
python $Script
```

**Sử dụng:**
```bash
# Chạy với mặc định
.\run_with_params.ps1

# Chạy với env khác
.\run_with_params.ps1 -EnvName "web_scraping_env" -Script "scraper.py"
```

## 🛠️ Utilities Scripts

### 1. List tất cả venv

```powershell
# list_venvs.ps1
$basePath = "E:\Python_Environments"
$envs = Get-ChildItem $basePath -Directory

Write-Host "🐍 Available Virtual Environments:" -ForegroundColor Green
foreach ($env in $envs) {
    $configFile = "$($env.FullName)\pyvenv.cfg"
    if (Test-Path $configFile) {
        Write-Host "  - $($env.Name)" -ForegroundColor Yellow
    }
}
```

### 2. Backup venv requirements

```powershell
# backup_requirements.ps1
$basePath = "E:\Python_Environments"
$backupPath = "$basePath\requirements"

if (!(Test-Path $backupPath)) {
    New-Item -ItemType Directory -Path $backupPath
}

$envs = Get-ChildItem $basePath -Directory
foreach ($env in $envs) {
    $activateScript = "$($env.FullName)\Scripts\Activate.ps1"
    if (Test-Path $activateScript) {
        Write-Host "Backing up $($env.Name)..." -ForegroundColor Yellow
        & $activateScript
        pip freeze > "$backupPath\$($env.Name)_requirements.txt"
    }
}
```

### 3. Create new venv với template

```powershell
# create_venv.ps1
param(
    [string]$Name,
    [string]$Template = "basic"
)

$basePath = "E:\Python_Environments"
$venvPath = "$basePath\$Name"

if (Test-Path $venvPath) {
    Write-Host "❌ Environment $Name already exists!" -ForegroundColor Red
    exit 1
}

Write-Host "Creating $Name..." -ForegroundColor Green
python -m venv $venvPath

Write-Host "Activating $Name..." -ForegroundColor Yellow
& "$venvPath\Scripts\Activate.ps1"

# Install packages based on template
switch ($Template) {
    "web" {
        pip install flask requests beautifulsoup4 selenium
    }
    "data" {
        pip install pandas numpy matplotlib seaborn jupyter
    }
    "ai" {
        pip install tensorflow torch opencv-python scikit-learn
    }
    "face_api" {
        pip install flask requests opencv-python-headless insightface onnxruntime pillow numpy
    }
    default {
        pip install requests
    }
}

Write-Host "✅ Environment $Name created successfully!" -ForegroundColor Green
```

**Sử dụng:**
```bash
# Tạo venv cơ bản
.\create_venv.ps1 -Name "my_project"

# Tạo venv cho web development
.\create_venv.ps1 -Name "web_project" -Template "web"

# Tạo venv cho face API
.\create_venv.ps1 -Name "face_recognition" -Template "face_api"
```

## 📊 So sánh các phương pháp

| Phương pháp | Ưu điểm | Nhược điểm | Khi nào dùng |
|-------------|---------|------------|--------------|
| **Local venv** | Portable, Independent | Duplicate, Space consuming | Project độc lập |
| **Absolute path venv** | Reusable, Centralized | Path dependent | Nhiều project cùng stack |
| **Conda** | Cross-platform, Package management | Complex, Large size | Data science, Research |
| **Poetry** | Dependency resolution, Lock file | Learning curve | Professional development |

## 🔄 Migration từ local venv sang absolute path

### 1. Backup requirements

```bash
# Activate local venv
venv\Scripts\Activate.ps1

# Backup requirements
pip freeze > local_requirements.txt
```

### 2. Tạo venv mới

```bash
# Tạo venv ở đường dẫn tuyệt đối
python -m venv "E:\Python_Environments\face_api_env"

# Activate venv mới
"E:\Python_Environments\face_api_env\Scripts\Activate.ps1"

# Cài đặt từ backup
pip install -r local_requirements.txt
```

### 3. Test và cleanup

```bash
# Test chạy project
python face_api.py

# Nếu OK, xóa venv cũ
rmdir /s venv
```

## 🚨 Troubleshooting

### 1. Lỗi không thể activate

```bash
# Lỗi: execution policy
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Hoặc dùng batch file
call "E:\Python_Environments\face_api_env\Scripts\activate.bat"
```

### 2. Lỗi path quá dài

```bash
# Sử dụng đường dẫn ngắn hơn
python -m venv "E:\Envs\face_api"
```

### 3. Lỗi permission

```bash
# Chạy PowerShell as Administrator
# Hoặc thay đổi quyền folder
icacls "E:\Python_Environments" /grant Users:F /T
```

## 💡 Best Practices

### 1. Naming Convention

```bash
# Tên venv nên rõ ràng
face_api_env        # ✅ Good
web_scraper_v2      # ✅ Good
my_project          # ❌ Too generic
proj1               # ❌ Unclear
```

### 2. Documentation

```bash
# Tạo file README cho mỗi venv
E:\Python_Environments\face_api_env\README.md
```

### 3. Regular maintenance

```bash
# Định kỳ backup requirements
# Xóa venv không dùng
# Update packages
```

## 🎯 Kết luận

Việc sử dụng venv với đường dẫn tuyệt đối phù hợp khi:
- Bạn có nhiều project cùng stack công nghệ
- Cần quản lý tập trung
- Làm việc trên một máy cố định

Sử dụng local venv khi:
- Project cần di chuyển thường xuyên
- Các project có dependency conflicts
- Chia sẻ code với người khác

Chọn phương pháp phù hợp với nhu cầu cụ thể của bạn! 