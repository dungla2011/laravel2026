# VPS Billing Report System - Documentation

## Tổng quan

Hệ thống báo cáo chi phí VPS đã được tổ chức thành các component có thể tái sử dụng:
- **Service Layer**: Logic xử lý dữ liệu
- **View Layer**: Template HTML/PDF
- **Controller**: API endpoints
- **Email**: Gửi báo cáo qua email

## Cấu trúc thư mục

```
app/
├── Services/
│   └── VpsBillingReportService.php    # Service xử lý logic
├── Http/Controllers/
│   └── VpsBillingController.php        # Controller API
└── Mail/
    └── VpsBillingReport.php            # Mailable class

resources/views/
├── vps/
│   ├── billing-report.blade.php        # HTML template (web)
│   └── billing-report-pdf.blade.php    # PDF template
└── emails/
    └── vps-billing-report.blade.php    # Email template
```

## Cài đặt thư viện PDF

```bash
composer require barryvdh/laravel-dompdf
```

Thêm vào `config/app.php`:
```php
'providers' => [
    // ...
    Barryvdh\DomPDF\ServiceProvider::class,
],

'aliases' => [
    // ...
    'PDF' => Barryvdh\DomPDF\Facade::class,
],
```

Publish config:
```bash
php artisan vendor:publish --provider="Barryvdh\DomPDF\ServiceProvider"
```

## Routes

Thêm vào `routes/web.php`:

```php
use App\Http\Controllers\VpsBillingController;

// VPS Billing Routes
Route::prefix('vps-billing')->group(function () {
    // Hiển thị báo cáo HTML
    Route::get('/report', [VpsBillingController::class, 'show'])
        ->name('vps.billing.report');
    
    // Download PDF
    Route::get('/report/pdf', [VpsBillingController::class, 'downloadPdf'])
        ->name('vps.billing.pdf');
    
    // Gửi email
    Route::post('/report/send-email', [VpsBillingController::class, 'sendEmail'])
        ->name('vps.billing.email');
});
```

## Cách sử dụng

### 1. Hiển thị báo cáo HTML trên web

```php
// URL: /vps-billing/report?email=khanhdh389@gmail.com
// Với date filter: /vps-billing/report?email=user@example.com&date_from=2026-01-01&date_to=2026-01-31
```

### 2. Sử dụng Service trong code

```php
use App\Services\VpsBillingReportService;
use App\Models\User;

// Inject service
$billingService = app(VpsBillingReportService::class);

// Get user
$user = User::where('email', 'khanhdh389@gmail.com')->first();

// Generate report data
$data = $billingService->generateReportData($user);

// Generate HTML
$html = $billingService->generateHtml($user);

// Generate PDF
$pdf = $billingService->generatePdf($user);

// Download PDF
return $pdf->download('billing-report.pdf');

// Stream PDF trong browser
return $pdf->stream('billing-report.pdf');

// Save PDF to file
$pdf->save('/path/to/billing-report.pdf');

// Send email
$billingService->sendEmail($user);
```

### 3. Với date filter

```php
$options = [
    'date_from' => '2026-01-01',
    'date_to' => '2026-01-31'
];

$data = $billingService->generateReportData($user, $options);
$pdf = $billingService->generatePdf($user, $options);
```

### 4. Tích hợp vào Admin Panel

```php
// Controller method
public function showUserBilling($userId)
{
    $user = User::findOrFail($userId);
    $billingService = app(VpsBillingReportService::class);
    
    $data = $billingService->generateReportData($user);
    
    return view('admin.user-billing', $data);
}

// Download button
public function downloadUserBillingPdf($userId)
{
    $user = User::findOrFail($userId);
    $billingService = app(VpsBillingReportService::class);
    
    $pdf = $billingService->generatePdf($user);
    
    return $pdf->download("billing-{$user->id}-" . now()->format('Y-m-d') . ".pdf");
}

// Send email button
public function emailUserBilling($userId)
{
    $user = User::findOrFail($userId);
    $billingService = app(VpsBillingReportService::class);
    
    $success = $billingService->sendEmail($user);
    
    return back()->with('success', 'Billing report sent to ' . $user->email);
}
```

### 5. Command line - Artisan Command

Tạo command để chạy từ terminal:

```php
// app/Console/Commands/SendMonthlyBillingCommand.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\VpsBillingReportService;
use App\Models\User;

class SendMonthlyBillingCommand extends Command
{
    protected $signature = 'billing:send-monthly {--user-id=} {--email=}';
    protected $description = 'Send monthly billing report to users';

    public function handle(VpsBillingReportService $billingService)
    {
        if ($userId = $this->option('user-id')) {
            $users = User::where('id', $userId)->get();
        } elseif ($email = $this->option('email')) {
            $users = User::where('email', $email)->get();
        } else {
            // Send to all active users
            $users = User::where('status', 1)->get();
        }

        foreach ($users as $user) {
            $this->info("Sending billing report to: {$user->email}");
            
            $success = $billingService->sendEmail($user, [
                'date_from' => now()->startOfMonth(),
                'date_to' => now()->endOfMonth()
            ]);
            
            if ($success) {
                $this->info("✓ Sent to {$user->email}");
            } else {
                $this->error("✗ Failed to send to {$user->email}");
            }
        }
    }
}
```

Chạy command:
```bash
# Send to specific user by email
php artisan billing:send-monthly --email=khanhdh389@gmail.com

# Send to specific user by ID
php artisan billing:send-monthly --user-id=123

# Send to all active users
php artisan billing:send-monthly
```

### 6. Schedule tự động gửi hàng tháng

Thêm vào `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    // Gửi billing report vào ngày 1 hàng tháng lúc 9h sáng
    $schedule->command('billing:send-monthly')
        ->monthlyOn(1, '9:00');
}
```

## Customize

### Thay đổi template HTML

Edit file: `resources/views/vps/billing-report.blade.php`

### Thay đổi template PDF

Edit file: `resources/views/vps/billing-report-pdf.blade.php`

### Thay đổi email template

Edit file: `resources/views/emails/vps-billing-report.blade.php`

### Thêm logic tính toán mới

Edit file: `app/Services/VpsBillingReportService.php`

### Thay đổi PDF settings

```php
// Trong VpsBillingReportService::generatePdf()
$pdf->setPaper('a4', 'landscape'); // Đổi orientation
$pdf->setPaper('a3', 'portrait');  // Đổi paper size

// Thêm options
$pdf->setOptions([
    'dpi' => 150,
    'defaultFont' => 'DejaVu Sans'
]);
```

## Testing

```bash
# Test service
php artisan tinker
>>> $service = app(\App\Services\VpsBillingReportService::class);
>>> $user = \App\Models\User::first();
>>> $data = $service->generateReportData($user);
>>> dump($data);

# Test PDF generation
>>> $pdf = $service->generatePdf($user);
>>> $pdf->save(storage_path('app/test-billing.pdf'));

# Test email
>>> $service->sendEmail($user);
```

## Ví dụ sử dụng trong View

```blade
{{-- Button download PDF --}}
<a href="{{ route('vps.billing.pdf', ['email' => $user->email]) }}" 
   class="btn btn-primary">
    Download PDF
</a>

{{-- Button gửi email --}}
<form action="{{ route('vps.billing.email') }}" method="POST">
    @csrf
    <input type="hidden" name="email" value="{{ $user->email }}">
    <button type="submit" class="btn btn-success">
        Send Email
    </button>
</form>

{{-- Nhúng report trực tiếp --}}
@php
    $billingService = app(\App\Services\VpsBillingReportService::class);
    $data = $billingService->generateReportData($user);
@endphp

@include('vps.billing-report', $data)
```

## API Response Examples

### GET /vps-billing/report?email=user@example.com
Returns HTML page with billing report

### GET /vps-billing/report/pdf?email=user@example.com
Downloads PDF file

### POST /vps-billing/report/send-email
Request body:
```json
{
    "email": "user@example.com",
    "date_from": "2026-01-01",
    "date_to": "2026-01-31"
}
```

Response:
```json
{
    "success": true,
    "message": "Billing report sent to user@example.com"
}
```

## Troubleshooting

### PDF không tạo được
- Kiểm tra thư viện dompdf đã cài: `composer show barryvdh/laravel-dompdf`
- Check permissions: `chmod -R 775 storage/`
- Check log: `tail -f storage/logs/laravel.log`

### Email không gửi được
- Check mail config trong `.env`
- Test mail: `php artisan tinker` → `Mail::raw('Test', fn($m) => $m->to('test@example.com')->subject('Test'));`
- Check queue: `php artisan queue:work`

### Font không hiển thị trong PDF
- Sử dụng DejaVu Sans font (hỗ trợ tiếng Việt)
- Config trong `config/dompdf.php`

## Next Steps

1. ✅ Service layer đã tạo
2. ✅ View templates đã tạo
3. ✅ Controller đã tạo
4. ✅ Email functionality đã tạo
5. ⏳ Cần cài đặt dompdf: `composer require barryvdh/laravel-dompdf`
6. ⏳ Cần thêm routes vào `routes/web.php`
7. ⏳ Cần tạo Artisan command nếu muốn automate
8. ⏳ Cần config mail settings trong `.env`
