# Face API Management Script

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("start", "stop", "restart", "status", "test")]
    [string]$Action,
    
    [int]$Port = 50000
)

function Start-API {
    param([int]$Port)
    
    Write-Host "🚀 Starting Face API on port $Port..." -ForegroundColor Green
    
    # Activate virtual environment and start API
    $env:FLASK_PORT = $Port
    & "venv\Scripts\Activate.ps1"
    
    # Start in background
    Start-Process powershell -ArgumentList "-NoExit", "-Command", "venv\Scripts\Activate.ps1; python face_api.py" -WindowStyle Minimized
    
    Write-Host "✅ Face API started on port $Port" -ForegroundColor Green
    Write-Host "   Access at: http://localhost:$Port" -ForegroundColor Yellow
}

function Stop-API {
    Write-Host "🛑 Stopping Face API..." -ForegroundColor Red
    
    # Kill Python processes running face_api.py
    Get-Process | Where-Object { $_.ProcessName -eq "python" -and $_.CommandLine -like "*face_api.py*" } | Stop-Process -Force
    
    Write-Host "✅ Face API stopped" -ForegroundColor Green
}

function Get-APIStatus {
    param([int]$Port)
    
    Write-Host "📊 Checking API status..." -ForegroundColor Cyan
    
    try {
        # Check if port is listening
        $connection = Test-NetConnection -ComputerName localhost -Port $Port -InformationLevel Quiet
        
        if ($connection) {
            Write-Host "✅ API is running on port $Port" -ForegroundColor Green
            
            # Try to get cache status
            try {
                $response = Invoke-RestMethod -Uri "http://localhost:$Port/cache_status" -Method Get -TimeoutSec 5
                Write-Host "   Cache entries: $($response.data.total_entries)" -ForegroundColor Yellow
            } catch {
                Write-Host "   Warning: Could not get cache status" -ForegroundColor Yellow
            }
        } else {
            Write-Host "❌ API is not running on port $Port" -ForegroundColor Red
        }
    } catch {
        Write-Host "❌ Error checking API status: $($_.Exception.Message)" -ForegroundColor Red
    }
}

function Test-API {
    param([int]$Port)
    
    Write-Host "🧪 Testing API..." -ForegroundColor Magenta
    
    # Test cache status
    try {
        $response = Invoke-RestMethod -Uri "http://localhost:$Port/cache_status" -Method Get
        Write-Host "✅ Cache status: $($response.data.total_entries) entries" -ForegroundColor Green
    } catch {
        Write-Host "❌ Cache status test failed: $($_.Exception.Message)" -ForegroundColor Red
        return
    }
    
    # Test get_face_vector
    try {
        $body = @{
            image_link = "https://events.dav.edu.vn/test_cloud_file?fid=4866"
        }
        $response = Invoke-RestMethod -Uri "http://localhost:$Port/get_face_vector" -Method Post -Body $body -TimeoutSec 30
        
        if ($response.status -eq "success") {
            Write-Host "✅ Face vector extraction: Success (vector length: $($response.vector.Count))" -ForegroundColor Green
        } else {
            Write-Host "⚠️  Face vector extraction: $($response.error)" -ForegroundColor Yellow
        }
    } catch {
        Write-Host "❌ Face vector test failed: $($_.Exception.Message)" -ForegroundColor Red
    }
}

# Main execution
switch ($Action) {
    "start" {
        Start-API -Port $Port
    }
    "stop" {
        Stop-API
    }
    "restart" {
        Stop-API
        Start-Sleep -Seconds 2
        Start-API -Port $Port
    }
    "status" {
        Get-APIStatus -Port $Port
    }
    "test" {
        Test-API -Port $Port
    }
}

Write-Host ""
Write-Host "Available commands:" -ForegroundColor Cyan
Write-Host "  .\manage_api.ps1 start [-Port 50000]" -ForegroundColor White
Write-Host "  .\manage_api.ps1 stop" -ForegroundColor White
Write-Host "  .\manage_api.ps1 restart [-Port 50000]" -ForegroundColor White
Write-Host "  .\manage_api.ps1 status [-Port 50000]" -ForegroundColor White
Write-Host "  .\manage_api.ps1 test [-Port 50000]" -ForegroundColor White 