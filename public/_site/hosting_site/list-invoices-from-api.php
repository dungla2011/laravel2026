<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$domain = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'glx.lad.vn';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Helpers\BkavEHoaDonAPI;

$invoice_dir = '/var/glx/upload_file_glx/user_files/siteid_63/invoice_files/';

$invoices = [];
$errors = [];
$loading = false;
$total_count = 0;

// Lấy tham số từ GET
$from_date = $_GET['from_date'] ?? date('Y-m-d', strtotime('-3 months'));
$to_date = $_GET['to_date'] ?? date('Y-m-d');
$filter_status = $_GET['status'] ?? '';
$filter_serial = $_GET['serial'] ?? '';
$filter_buyer = $_GET['buyer'] ?? '';

if (!isAdminLrv_()) {
    die("You do not have permission to access this page.");
}

// Xử lý action stream PDF
if (isset($_GET['action']) && $_GET['action'] === 'view_pdf' && isset($_GET['file'])) {
    $pdf_filename = basename($_GET['file']) . '.pdf';
    $pdf_filepath = $invoice_dir . $pdf_filename;

    if (file_exists($pdf_filepath)) {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $pdf_filename . '"');
        header('Content-Length: ' . filesize($pdf_filepath));
        header('Cache-Control: public, must-revalidate, max-age=0');
        header('Pragma: public');
        header('Expires: 0');
        readfile($pdf_filepath);
        exit;
    } else {
        die("❌ File PDF không tồn tại: $pdf_filename");
    }
}

// Auto-load nếu truy cập lần đầu (không có param gì)
$is_first_visit = !isset($_GET['from_date']) && !isset($_GET['to_date']) && 
                  !isset($_GET['serial']) && !isset($_GET['buyer']) && 
                  !isset($_GET['status']) && !isset($_GET['action']);

// Nếu có request lấy danh sách hoặc truy cập lần đầu
if ((isset($_GET['action']) && $_GET['action'] === 'load') || $is_first_visit) {
    $loading = true;
    
    try {
        $api = new BkavEHoaDonAPI();
        $page = 1;
        
        while (true) {
            $result = $api->getInvoicesByDate($from_date, $to_date, $page);
            
            if (!$result['isOk'] || $result['Status'] != 0) {
                $errors[] = "Lỗi trang $page: " . ($result['Object'] ?? 'Unknown');
                break;
            }
            
            $page_invoices = $result['Object'];
            if (is_string($page_invoices)) {
                $page_invoices = json_decode($page_invoices, true);
            }
            
            if (!is_array($page_invoices) || count($page_invoices) === 0) {
                break;
            }
            
            $invoices = array_merge($invoices, $page_invoices);
            
            if (count($page_invoices) < 30) {
                break;
            }
            
            $page++;
            
            if ($page > 100) {
                $errors[] = "Đã lấy 100 trang, dừng để an toàn";
                break;
            }
        }
        
        $total_count = count($invoices);
        
        // Áp dụng filter
        if (!empty($filter_status) || !empty($filter_serial) || !empty($filter_buyer)) {
            $invoices = array_filter($invoices, function($inv_data) use ($filter_status, $filter_serial, $filter_buyer) {
                $invoice = $inv_data['Invoice'] ?? [];
                
                if (!empty($filter_status)) {
                    $status = $invoice['InvoiceStatusID'] ?? 0;
                    if ($status != $filter_status) return false;
                }
                
                if (!empty($filter_serial)) {
                    $serial = $invoice['InvoiceSerial'] ?? '';
                    if (stripos($serial, $filter_serial) === false) return false;
                }
                
                if (!empty($filter_buyer)) {
                    $buyer = $invoice['BuyerUnitName'] ?? '';
                    if (stripos($buyer, $filter_buyer) === false) return false;
                }
                
                return true;
            });
        }
        
        // Sắp xếp từ mới nhất
        usort($invoices, function($a, $b) {
            $date_a = strtotime($a['Invoice']['InvoiceDate'] ?? '1970-01-01');
            $date_b = strtotime($b['Invoice']['InvoiceDate'] ?? '1970-01-01');
            return $date_b - $date_a;
        });
        
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}

$status_names = [
    1 => "Mới tạo",
    2 => "Đã phát hành",
    3 => "Đã hủy",
    5 => "Chờ thay thế",
    6 => "Thay thế",
    7 => "Chờ điều chỉnh",
    8 => "Điều chỉnh",
    9 => "Bị thay thế",
    10 => "Bị điều chỉnh",
    11 => "Trống - Chờ ký"
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách tất cả hoá đơn</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1600px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 28px;
            margin-bottom: 10px;
        }
        
        .filters {
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 15px;
        }
        
        .filter-row input, .filter-row select {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 13px;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
        }
        
        button {
            padding: 10px 20px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        button:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 15px;
            padding: 20px;
            background: #f8f9fa;
        }
        
        .stat-box {
            text-align: center;
            padding: 15px;
            background: white;
            border-radius: 5px;
            border-left: 4px solid #667eea;
        }
        
        .stat-box .number {
            font-size: 24px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-box .label {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
        
        .content {
            padding: 20px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }
        
        table thead {
            background: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        
        table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            white-space: nowrap;
        }
        
        table td {
            padding: 10px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        
        table tbody tr:hover {
            background: #f8f9fa;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 3px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .badge-primary {
            background: #cce5ff;
            color: #004085;
        }
        
        .badge-success {
            background: #d4edda;
            color: #155724;
        }
        
        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }
        
        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }
        
        .badge-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .no-data {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .loading {
            text-align: center;
            padding: 40px;
            color: #667eea;
            font-weight: 600;
        }
        
        .expandable-row {
            cursor: pointer;
        }
        
        .expandable-row:hover {
            background: #e8f4f8 !important;
        }
        
        a[href*="action=view_pdf"]:hover {
            background: #5568d3 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
    <script>
        function copyInvoiceCode(row) {
            // Tìm cột Ký hiệu/Số
            const serialCell = row.querySelector('td:nth-child(2)');
            const mtcCell = row.querySelector('td:nth-child(3)');
            
            if (serialCell && mtcCell) {
                // Lấy nội dung từ badge
                const serialBadge = serialCell.querySelector('.badge-primary');
                const mtcBadge = mtcCell.querySelector('.badge-primary');
                
                let textToCopy = '';
                if (serialBadge) textToCopy = serialBadge.textContent.trim();
                if (mtcBadge) textToCopy += ' | ' + mtcBadge.textContent.trim();
                
                if (textToCopy) {
                    navigator.clipboard.writeText(textToCopy).then(() => {
                        // Hiển thị thông báo
                        const tooltip = document.createElement('div');
                        tooltip.textContent = '✓ Copied: ' + textToCopy;
                        tooltip.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #4caf50; color: white; padding: 10px 15px; border-radius: 5px; z-index: 9999; font-size: 13px; font-weight: 600;';
                        document.body.appendChild(tooltip);
                        setTimeout(() => tooltip.remove(), 2000);
                    }).catch((err) => {
                        console.error('Copy failed:', err);
                    });
                }
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📋 Danh sách tất cả hoá đơn</h1>
            <p>Lấy trực tiếp từ Bkav API (CmdType 853)</p>
        </div>
        
        <div class="filters">
            <form method="GET">
                <div class="filter-row">
                    <input type="date" name="from_date" value="<?php echo htmlspecialchars($from_date); ?>" placeholder="Từ ngày">
                    <input type="date" name="to_date" value="<?php echo htmlspecialchars($to_date); ?>" placeholder="Đến ngày">
                    <input type="text" name="serial" value="<?php echo htmlspecialchars($filter_serial); ?>" placeholder="Lọc theo ký hiệu...">
                    <input type="text" name="buyer" value="<?php echo htmlspecialchars($filter_buyer); ?>" placeholder="Lọc theo người mua...">
                    <select name="status">
                        <option value="">-- Tất cả trạng thái --</option>
                        <?php foreach ($status_names as $id => $name): ?>
                            <option value="<?php echo $id; ?>" <?php echo ($filter_status == $id) ? 'selected' : ''; ?>>
                                <?php echo "Status $id: $name"; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="submit" name="action" value="load">🔍 Tải danh sách</button>
                    <a href="?">
                        <button type="button" > Xóa lọc</button>
                    </a>
                </div>
            </form>
        </div>
        
        <?php if (!empty($errors)): ?>
        <div class="error-box">
            <?php foreach ($errors as $error): ?>
                <div>⚠️ <?php echo htmlspecialchars($error); ?></div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if ($loading && !empty($invoices)): ?>
        <div class="stats">
            <div class="stat-box">
                <div class="number"><?php echo $total_count; ?></div>
                <div class="label">Tổng tìm thấy</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo count($invoices); ?></div>
                <div class="label">Kết quả hiển thị</div>
            </div>
            <?php
            $status_count = [];
            foreach ($invoices as $inv_data) {
                $status = $inv_data['Invoice']['InvoiceStatusID'] ?? 0;
                $status_count[$status] = ($status_count[$status] ?? 0) + 1;
            }
            ?>
            <div class="stat-box">
                <div class="number"><?php echo $status_count[2] ?? 0; ?></div>
                <div class="label">Đã phát hành</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo ($status_count[1] ?? 0) + ($status_count[11] ?? 0); ?></div>
                <div class="label">Chưa ký</div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="content">
            <?php if (!$loading): ?>
                <div class="no-data">
                    <p style="font-size: 18px; margin-bottom: 10px;">📭</p>
                    <p>Chưa tải danh sách. Chọn ngày rồi ấn "Tải danh sách"</p>
                </div>
            <?php elseif (empty($invoices)): ?>
                <div class="no-data">
                    <p style="font-size: 18px; margin-bottom: 10px;">📭</p>
                    <p>Không tìm thấy hoá đơn nào</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 3%">STT</th>
                                <th style="width: 12%">Ký hiệu / Số</th>
                                <th style="width: 12%">MTC</th>
                                <th style="width: 10%">Ngày lập</th>
                                <th style="width: 23%">Người mua</th>
                                <th style="width: 10%">MST</th>
                                <th style="width: 12%">Tổng tiền</th>
                                <th style="width: 13%">Trạng thái</th>
                                <th style="width: 10%" class="text-center">Xem PDF</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($invoices as $idx => $inv_data):
                                $invoice = $inv_data['Invoice'] ?? [];
                                $serial = $invoice['InvoiceSerial'] ?? 'N/A';
                                $no = $invoice['InvoiceNo'] ?? 'N/A';
                                $date = substr($invoice['InvoiceDate'] ?? '', 0, 10);
                                $buyer = $invoice['BuyerUnitName'] ?? 'N/A';
                                $tax_code = $invoice['BuyerTaxCode'] ?? 'N/A';
                                $amount = $invoice['SumPaymentAmount'] ?? 0;
                                $status = $invoice['InvoiceStatusID'] ?? 0;
                                $mtc = $invoice['InvoiceCode'] ?? '';
                                $invoice_guid = $invoice['InvoiceGUID'] ?? '';
                                
                                // Chọn màu badge theo status
                                if ($status == 2) $badge_class = 'badge-success';
                                elseif ($status == 1 || $status == 11) $badge_class = 'badge-warning';
                                elseif ($status == 3) $badge_class = 'badge-danger';
                                else $badge_class = 'badge-info';
                                
                                $status_name = $status_names[$status] ?? "Status $status";
                                
                                // Kiểm tra file PDF - thử nhiều tên file khác nhau
                                $pdf_exists = false;
                                $pdf_filename = '';
                                
                                // Cách 1: Serial_No_MTC
                                if (!empty($serial) && !empty($no) && !empty($mtc)) {
                                    $try_filename = $serial . '_' . str_pad($no, 4, '0', STR_PAD_LEFT) . '_' . $mtc;
                                    $try_filepath = $invoice_dir . $try_filename . '.pdf';
                                    if (file_exists($try_filepath)) {
                                        $pdf_exists = true;
                                        $pdf_filename = $try_filename;
                                    }
                                }
                                
                                // Cách 2: Serial-No
                                if (!$pdf_exists && !empty($serial) && !empty($no)) {
                                    $try_filename = $serial . '-' . str_pad($no, 4, '0', STR_PAD_LEFT);
                                    $try_filepath = $invoice_dir . $try_filename . '.pdf';
                                    if (file_exists($try_filepath)) {
                                        $pdf_exists = true;
                                        $pdf_filename = $try_filename;
                                    }
                                }
                                
                                // Cách 3: Serial_No
                                if (!$pdf_exists && !empty($serial) && !empty($no)) {
                                    $try_filename = $serial . '_' . str_pad($no, 4, '0', STR_PAD_LEFT);
                                    $try_filepath = $invoice_dir . $try_filename . '.pdf';
                                    if (file_exists($try_filepath)) {
                                        $pdf_exists = true;
                                        $pdf_filename = $try_filename;
                                    }
                                }
                                
                                // Cách 4: Chỉ dùng MTC
                                if (!$pdf_exists && !empty($mtc)) {
                                    $try_filename = $mtc;
                                    $try_filepath = $invoice_dir . $try_filename . '.pdf';
                                    if (file_exists($try_filepath)) {
                                        $pdf_exists = true;
                                        $pdf_filename = $try_filename;
                                    }
                                }
                                
                                // Cách 5: Dùng InvoiceGUID
                                if (!$pdf_exists && !empty($invoice_guid)) {
                                    $try_filename = $invoice_guid;
                                    $try_filepath = $invoice_dir . $try_filename . '.pdf';
                                    if (file_exists($try_filepath)) {
                                        $pdf_exists = true;
                                        $pdf_filename = $try_filename;
                                    }
                                }
                            ?>
                            <tr class="expandable-row" onclick="copyInvoiceCode(this)">
                                <td><?php echo $idx + 1; ?></td>
                                <td><span class="badge badge-primary"><?php echo htmlspecialchars($serial); ?>-<?php echo str_pad($no, 4, '0', STR_PAD_LEFT); ?></span></td>
                                <td>
                                    <?php if (!empty($mtc)): ?>
                                        <span class="badge badge-primary"><?php echo htmlspecialchars($mtc); ?></span>
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 11px;">—</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $date; ?></td>
                                <td><?php echo htmlspecialchars(substr($buyer, 0, 30)); ?></td>
                                <td><?php echo htmlspecialchars($tax_code); ?></td>
                                <td class="text-right"><strong><?php echo number_format($amount, 0, ',', '.'); ?></strong></td>
                                <td><span class="badge <?php echo $badge_class; ?>"><?php echo $status_name; ?></span></td>
                                <td class="text-center" onclick="event.stopPropagation();">
                                    <?php if ($pdf_exists): ?>
                                        <a href="?action=view_pdf&file=<?php echo urlencode($pdf_filename); ?>" target="_blank" style="display: inline-block; padding: 6px 12px; background: #667eea; color: white; text-decoration: none; border-radius: 4px; font-weight: 600; transition: all 0.3s; cursor: pointer; font-size: 12px;">
                                            📄 Xem
                                        </a>
                                    <?php else: ?>
                                        <span style="display: inline-block; padding: 6px 12px; background: #ddd; color: #999; border-radius: 4px; font-weight: 600; cursor: not-allowed; font-size: 12px;">
                                            ❌
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
