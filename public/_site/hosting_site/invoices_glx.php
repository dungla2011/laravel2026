<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
//$domain = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'ncbd.mytree.vn';
$domain = $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'glx.lad.vn';
//require_once '/var/www/html/public/index.php';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


use App\Helpers\BkavEHoaDonAPI;

$invoice_dir = '/var/glx/upload_file_glx/user_files/siteid_63/invoice_files/';

// Danh sách trạng thái hoá đơn theo Bkav
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
    11 => "Trống - Chờ ký",
    14 => "Chờ điều chỉnh chiết khấu",
    15 => "Điều chỉnh chiết khấu"
];
$invoices = [];
$errors = [];

// Lấy tham số filter từ GET
$filter_tax_code = $_GET['tax_code'] ?? '';
$filter_amount = $_GET['amount'] ?? '';
$total_invoices_before_filter = 0;
$filtered_invoices_count = 0;
$user_id = getCurrentUserId();

// Xử lý action stream PDF
if (isset($_GET['action']) && $_GET['action'] === 'view_pdf' && isset($_GET['file'])) {
    $pdf_filename = basename($_GET['file']) . '.pdf';
    $pdf_filepath = $invoice_dir . $pdf_filename;

    $invoice_number = substr(trim(basename($_GET['file'])), 0, 60);
    //Chỉ giữ lại số, chữ và _ để tránh lỗi khi lưu trữ hoặc truy cập file
    $invoice_number = preg_replace('/[^a-zA-Z0-9_]/', '', $invoice_number);
    //Kiểm tra xem có invoice_number này có trong user_recharges không
    $recharge = \App\Models\UserRecharge::where('user_id', $user_id)->where('invoice_number', $invoice_number)->first();
    if(!$recharge){
        if(!isAdminLrv_())
           die("Truy cập không hợp lệ!");
    }

     // Kiểm tra file PDF tồn tại
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


if(!isAdminLrv_()){
    die("You do not have permission to access this page.");
}



// Kiểm tra thư mục tồn tại
if (!is_dir($invoice_dir)) {
    $errors[] = "❌ Thư mục không tồn tại: $invoice_dir";
} else {
    // Lấy danh sách tất cả file XML
    $xml_files = glob($invoice_dir . '*.xml');

    if (count($xml_files) === 0) {
        $errors[] = "⚠️ Không tìm thấy file XML nào trong thư mục: $invoice_dir";
    } else {
        // Tạo instance của BkavEHoaDonAPI để sử dụng hàm parseInvoiceXml
        $api = new BkavEHoaDonAPI();

        // Phân tích từng file XML
        foreach ($xml_files as $xml_file) {
            $parsed = $api->parseInvoiceXml($xml_file);
            if ($parsed['success']) {
                $total_invoices_before_filter++;

                // Áp dụng filter
                $should_include = true;

                // Filter theo tax_code (MST người mua)
                if (!empty($filter_tax_code)) {
                    $nmua_mst = $parsed['nguoi_mua']['mst'] ?? '';
                    if (stripos($nmua_mst, trim($filter_tax_code)) === false) {
                        $should_include = false;
                    }
                }

                // Filter theo amount (tổng tiền)
                if ($should_include && !empty($filter_amount)) {
                    $tong_cong = floatval($parsed['tong_tien']['tong_cong_thanh_toan'] ?? 0);
                    $filter_amount_value = floatval($filter_amount);

                    // So sánh: nếu tổng tiền không bằng với amount được filter thì loại bỏ
                    if (abs($tong_cong - $filter_amount_value) > 0.01) {
                        $should_include = false;
                    }
                }

                if ($should_include) {
                    $invoices[] = $parsed;
                    $filtered_invoices_count++;
                }
            } else {
                $errors[] = "❌ " . basename($xml_file) . ": " . $parsed['error'];
            }
        }

        // Sắp xếp theo ngày lập từ mới nhất đến cũ nhất
        usort($invoices, function($a, $b) {
            $date_a = strtotime($a['thong_tin_chung']['ngay_lap'] ?? '1970-01-01');
            $date_b = strtotime($b['thong_tin_chung']['ngay_lap'] ?? '1970-01-01');
            return $date_b - $date_a; // Mới nhất trên cùng
        });
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách hoá đơn XML</title>
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
            max-width: 1400px;
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

        .header p {
            opacity: 0.9;
            font-size: 14px;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 20px;
            padding: 20px;
            background: #f8f9fa;
            border-bottom: 1px solid #e0e0e0;
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

        .error-box {
            background: #fff3cd;
            border: 1px solid #ffc107;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .error-box ul {
            list-style: none;
            padding-left: 0;
        }

        .error-box li {
            padding: 5px 0;
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

        table tbody tr:nth-child(even) {
            background: #fafafa;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-muted {
            color: #999;
        }

        .label-success {
            background: #d4edda;
            color: #155724;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
        }

        .no-data {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }

        .no-data svg {
            width: 80px;
            height: 80px;
            opacity: 0.3;
            margin-bottom: 20px;
        }

        .footer {
            background: #f8f9fa;
            padding: 15px 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
        }

        .expandable-row {
            cursor: pointer;
        }

        .expandable-row:hover {
            background: #e8f4f8 !important;
        }

        .expanded-content {
            display: none;
            background: #f0f8ff;
            padding: 15px;
            border-top: 1px solid #dee2e6;
        }

        .expanded-content.show {
            display: table-row;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 10px;
            background: white;
        }

        .items-table th {
            background: #f0f8ff;
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #bee5eb;
            font-weight: 600;
            font-size: 11px;
        }

        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #e0e0e0;
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

        .badge-info {
            background: #d1ecf1;
            color: #0c5460;
        }

        a[href*="action=view_pdf"]:hover {
            background: #5568d3 !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
    <script>
        function toggleExpand(row) {
            const expandedRow = row.nextElementSibling;
            if (expandedRow && expandedRow.classList.contains('expanded-content')) {
                expandedRow.classList.toggle('show');
                row.classList.toggle('expanded');
            }
        }

        function copyInvoiceCode(row) {
            // Tìm hàng cha (expandable-row)
            const expandableRow = row.closest('.expandable-row');
            if (!expandableRow) return;

            // Lấy tên file XML từ cột thứ 2
            const fileCell = expandableRow.querySelector('td:nth-child(2) small');
            if (fileCell) {
                // Lấy tên file và bỏ .xml
                let filename = fileCell.textContent.trim();
                filename = filename.replace(/\.xml$/i, '');

                // Copy vào clipboard
                navigator.clipboard.writeText(filename).then(() => {
                    // Hiện thị thông báo
                    const tooltip = document.createElement('div');
                    tooltip.textContent = '✓ Copied: ' + filename;
                    tooltip.style.cssText = 'position: fixed; top: 20px; right: 20px; background: #4caf50; color: white; padding: 10px 15px; border-radius: 5px; z-index: 9999; font-size: 13px; font-weight: 600;';
                    document.body.appendChild(tooltip);

                    setTimeout(() => tooltip.remove(), 2000);
                    console.log('✓ Copied: ' + filename);
                }).catch((err) => {
                    console.error('Copy failed:', err);
                });
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Danh sách Hoá đơn XML </h1>
            <p>Folder: <?php echo $invoice_dir; ?>   <a href="/_site/hosting_site/list-invoices-from-api.php" target="_blank"
            style="color: white; text-decoration: underline;"
            > From API </a>  </p>

            <div style="margin-top: 15px; display: flex; gap: 15px; justify-content: center; flex-wrap: wrap;">
                <form method="GET" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <input type="text" name="tax_code" placeholder="Lọc theo MST..." value="<?php echo htmlspecialchars($filter_tax_code); ?>" style="padding: 8px 12px; border: 1px solid rgba(255,255,255,0.3); border-radius: 5px; background: rgba(255,255,255,0.1); color: white; font-size: 13px;">
                    <input type="number" name="amount" placeholder="Lọc theo số tiền..." value="<?php echo htmlspecialchars($filter_amount); ?>" step="0.01" style="padding: 8px 12px; border: 1px solid rgba(255,255,255,0.3); border-radius: 5px; background: rgba(255,255,255,0.1); color: white; font-size: 13px; width: 180px;">
                    <button type="submit" style="padding: 8px 16px; background: rgba(255,255,255,0.2); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 5px; cursor: pointer; font-weight: 600; transition: all 0.3s;">🔍 Lọc</button>
                    <?php if (!empty($filter_tax_code) || !empty($filter_amount)): ?>
                    <a href="?" style="padding: 8px 16px; background: rgba(255,255,255,0.15); border: 1px solid rgba(255,255,255,0.4); color: white; border-radius: 5px; text-decoration: none; font-weight: 600; transition: all 0.3s;">✕ Xóa lọc</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <div class="stats">
            <div class="stat-box">
                <div class="number"><?php echo count($xml_files ?? []); ?></div>
                <div class="label">File XML</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo $total_invoices_before_filter; ?></div>
                <div class="label">Hoá đơn phân tích</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo count($invoices); ?></div>
                <div class="label">Hoá đơn hiển thị <?php if (!empty($filter_tax_code) || !empty($filter_amount)): ?><br><small>(sau lọc)</small><?php endif; ?></div>
            </div>
            <div class="stat-box">
                <div class="number"><?php echo count($errors); ?></div>
                <div class="label">Lỗi</div>
            </div>
            <div class="stat-box">
                <div class="number"><?php
                    $total_items = 0;
                    foreach ($invoices as $inv) {
                        $total_items += count($inv['hang_hoa_dich_vu'] ?? []);
                    }
                    echo $total_items;
                ?></div>
                <div class="label">Tổng HHDV</div>
            </div>
        </div>

        <div class="content">
            <?php if (!empty($errors)): ?>
            <div class="error-box">
                <strong>⚠️ Các lỗi phát hiện:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                    <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if (empty($invoices)): ?>
            <div class="no-data">
                <p style="font-size: 18px; margin-bottom: 10px;">📭</p>
                <p>Không có hoá đơn nào để hiển thị</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 3%">STT</th>
                            <th style="width: 12%">File</th>
                            <th style="width: 10%">Ký hiệu / Số</th>
                            <th style="width: 12%">Ngày lập</th>
                            <th style="width: 20%">Người mua</th>
                            <th style="width: 18%">MST Người mua</th>
                            <th style="width: 13%">Tổng tiền</th>
                            <th style="width: 10%">Trạng thái ký</th>
                            <th style="width: 8%">HHDV</th>
                            <th style="width: 10%" class="text-center">Xem PDF</th>
                            <th style="width: 4%" class="text-center">Chi tiết</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($invoices as $idx => $invoice):
                            $ttc = $invoice['thong_tin_chung'] ?? [];
                            $nmua = $invoice['nguoi_mua'] ?? [];
                            $ttien = $invoice['tong_tien'] ?? [];
                            $items_count = count($invoice['hang_hoa_dich_vu'] ?? []);
                            $tong_cong = floatval($ttien['tong_cong_thanh_toan'] ?? 0);
                            $xml_basename = pathinfo($invoice['filename'], PATHINFO_FILENAME);
                            $pdf_file = $invoice_dir . $xml_basename . '.pdf';
                            $pdf_exists = file_exists($pdf_file);
                        ?>
                        <tr class="expandable-row" onclick="copyInvoiceCode(this)">
                            <td class="text-center"><?php echo $idx + 1; ?></td>
                            <td><small><?php echo basename($invoice['filename']); ?></small></td>
                            <td><span class="badge badge-primary"><?php echo $ttc['serial'] ?? 'N/A'; ?>-<?php echo str_pad($ttc['so_hdon'] ?? '', 4, '0', STR_PAD_LEFT); ?></span></td>
                            <td><?php echo substr($ttc['ngay_lap'] ?? '', 0, 10); ?></td>
                            <td><?php echo htmlspecialchars(substr($nmua['ten'] ?? '', 0, 30)); ?></td>
                            <td><span class="badge badge-info"><?php echo $nmua['mst'] ?? 'N/A'; ?></span></td>
                            <td class="text-right"><strong><?php echo number_format($tong_cong, 0, ',', '.'); ?></strong></td>
                            <td class="text-center">
                                <!-- Tất cả file XML trong folder này là hoá đơn đã ký
                                     (vì chỉ hoá đơn đã phát hành mới được download từ API) -->
                                <span style="display: inline-block; padding: 4px 8px; background: #d4edda; color: #155724; border-radius: 3px; font-size: 11px; font-weight: 600;">✓ Đã ký</span>
                            </td>
                            <td class="text-center"><?php echo $items_count; ?></td>
                            <td class="text-center" onclick="event.stopPropagation();">
                                <?php if ($pdf_exists): ?>
                                    <a href="?action=view_pdf&file=<?php echo urlencode($xml_basename); ?>" target="_blank" style="display: inline-block; padding: 6px 12px; background: #667eea; color: white; text-decoration: none; border-radius: 4px; font-weight: 600; transition: all 0.3s; cursor: pointer; font-size: 12px;">
                                        📄 Xem
                                    </a>
                                <?php else: ?>
                                    <span style="display: inline-block; padding: 6px 12px; background: #ddd; color: #999; border-radius: 4px; font-weight: 600; cursor: not-allowed; font-size: 12px;">
                                        ❌
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center" onclick="event.stopPropagation(); toggleExpand(this);" style="cursor: pointer;">👇</td>
                        </tr>
                        <tr class="expanded-content">
                            <td colspan="9">
                                <div style="padding: 15px 0;">
                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 15px;">
                                        <div>
                                            <h4 style="margin-bottom: 10px; color: #333;">Thông tin Chung</h4>
                                            <table style="width: 100%; font-size: 12px; border: none;">
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Loại HĐKD:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo $ttc['loai_hdon'] ?? 'N/A'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Hình thức TT:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo $ttc['hthuc_thanh_toan'] ?? 'N/A'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Tiền tệ:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo $ttc['tien_te'] ?? 'VND'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Tỷ giá:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo $ttc['ty_gia'] ?? '1'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>MCCQT:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo $ttc['mccqt'] ?? 'N/A'; ?></td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div>
                                            <h4 style="margin-bottom: 10px; color: #333;">Thông tin Tài chính</h4>
                                            <table style="width: 100%; font-size: 12px; border: none;">
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Tổng tiền chịu thuế:</strong></td>
                                                    <td style="border: none; padding: 3px; text-align: right;"><?php echo number_format(floatval($ttien['tong_tien_chiu_thue'] ?? 0), 0, ',', '.'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Tổng tiền thuế:</strong></td>
                                                    <td style="border: none; padding: 3px; text-align: right;"><?php echo number_format(floatval($ttien['tong_tien_thue'] ?? 0), 0, ',', '.'); ?></td>
                                                </tr>
                                                <tr style="background: #f0f8ff; border-top: 2px solid #667eea;">
                                                    <td style="border: none; padding: 5px; color: #333; font-weight: bold;"><strong>Tổng cộng TT:</strong></td>
                                                    <td style="border: none; padding: 5px; text-align: right; font-weight: bold; color: #667eea;"><?php echo number_format($tong_cong, 0, ',', '.'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>TTCK tạm mai:</strong></td>
                                                    <td style="border: none; padding: 3px; text-align: right;"><?php echo number_format(floatval($ttien['ttck_t_mai'] ?? 0), 0, ',', '.'); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                                        <div>
                                            <h4 style="margin-bottom: 10px; color: #333;">Thông tin Người Bán</h4>
                                            <table style="width: 100%; font-size: 12px; border: none;">
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Tên:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo htmlspecialchars($invoice['nguoi_ban']['ten'] ?? 'N/A'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>MST:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo $invoice['nguoi_ban']['mst'] ?? 'N/A'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Địa chỉ:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo htmlspecialchars(substr($invoice['nguoi_ban']['dia_chi'] ?? '', 0, 50)); ?></td>
                                                </tr>
                                            </table>
                                        </div>

                                        <div>
                                            <h4 style="margin-bottom: 10px; color: #333;">Thông tin Người Mua</h4>
                                            <table style="width: 100%; font-size: 12px; border: none;">
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Tên:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo htmlspecialchars($nmua['ten'] ?? 'N/A'); ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>MST:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo $nmua['mst'] ?? 'N/A'; ?></td>
                                                </tr>
                                                <tr>
                                                    <td style="border: none; padding: 3px; color: #666;"><strong>Địa chỉ:</strong></td>
                                                    <td style="border: none; padding: 3px;"><?php echo htmlspecialchars(substr($nmua['dia_chi'] ?? '', 0, 50)); ?></td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>

                                    <?php if (!empty($invoice['hang_hoa_dich_vu'])): ?>
                                    <div style="margin-top: 15px;">
                                        <h4 style="margin-bottom: 10px; color: #333;">Danh sách Hàng hóa / Dịch vụ (<?php echo count($invoice['hang_hoa_dich_vu']); ?> item)</h4>
                                        <table class="items-table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%">STT</th>
                                                    <th style="width: 35%">Tên hàng hóa / DV</th>
                                                    <th style="width: 12%">Số lượng</th>
                                                    <th style="width: 15%">Đơn giá</th>
                                                    <th style="width: 15%">Thành tiền</th>
                                                    <th style="width: 10%">Thuế suất</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($invoice['hang_hoa_dich_vu'] as $item): ?>
                                                <tr>
                                                    <td><?php echo $item['stt'] ?? ''; ?></td>
                                                    <td><?php echo htmlspecialchars(substr($item['ten'] ?? '', 0, 60)); ?></td>
                                                    <td class="text-right"><?php echo number_format(floatval($item['sluong'] ?? 0), 2, ',', '.'); ?></td>
                                                    <td class="text-right"><?php echo number_format(floatval($item['don_gia'] ?? 0), 0, ',', '.'); ?></td>
                                                    <td class="text-right"><strong><?php echo number_format(floatval($item['thanh_tien'] ?? 0), 0, ',', '.'); ?></strong></td>
                                                    <td class="text-center"><?php echo $item['thue_suat'] ?? 'N/A'; ?>%</td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <div class="footer">
            <strong>Tổng cộng:</strong> <?php echo count($invoices); ?> hoá đơn
            <?php if (!empty($filter_tax_code) || !empty($filter_amount)): ?>
            | <strong>Filter:</strong>
            <?php if (!empty($filter_tax_code)): ?>
                MST: <code><?php echo htmlspecialchars($filter_tax_code); ?></code>
            <?php endif; ?>
            <?php if (!empty($filter_amount)): ?>
                <?php if (!empty($filter_tax_code)): ?> + <?php endif; ?>
                Số tiền: <code><?php echo number_format(floatval($filter_amount), 0, ',', '.'); ?></code>
            <?php endif; ?>
            | <strong>Kết quả:</strong> <?php echo count($invoices); ?>/<?php echo $total_invoices_before_filter; ?>
            <?php else: ?>
            | <strong>File XML:</strong> <?php echo count($xml_files ?? []); ?>
            <?php endif; ?>
            | <strong>Lỗi:</strong> <?php echo count($errors); ?> |
            <strong>Cập nhật:</strong> <?php echo date('H:i:s d/m/Y'); ?>
        </div>
    </div>
</body>
</html>


