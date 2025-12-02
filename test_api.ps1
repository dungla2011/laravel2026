# Test Service Manager API

$url = "https://test2023.mytree.vn/api/service-manager/plans"
$headers = @{
    "Content-Type" = "application/json"
}

$body = @{
    name = "VPS Basic"
    description = "Gói VPS cơ bản cho test"
    category = "vps"
    resources = @{
        cpu = 2
        ram = 4
        disk = 50
        network = 100
        ip = 1
    }
    pricing = @{
        cpu = @{
            hour = 5
            day = 100
            month = 2000
        }
        ram = @{
            hour = 2.5
            day = 50
            month = 1000
        }
        disk = @{
            hour = 0.5
            day = 10
            month = 200
        }
        network = @{
            hour = 1
            day = 20
            month = 400
        }
        ip = @{
            hour = 10
            day = 200
            month = 5000
        }
    }
} | ConvertTo-Json -Depth 10

Write-Host "Testing POST to: $url"
Write-Host "Body: $body"

try {
    $response = Invoke-RestMethod -Uri $url -Method POST -Headers $headers -Body $body
    Write-Host "Success!" -ForegroundColor Green
    Write-Host ($response | ConvertTo-Json -Depth 10)
} catch {
    Write-Host "Error:" -ForegroundColor Red
    Write-Host $_.Exception.Message
    if ($_.Exception.Response) {
        $reader = New-Object System.IO.StreamReader($_.Exception.Response.GetResponseStream())
        $responseBody = $reader.ReadToEnd()
        Write-Host "Response: $responseBody"
    }
} 