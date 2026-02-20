<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

require '/var/www/html/vendor/autoload.php';
require_once __DIR__ . '/lib_sign.php';

$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$ouser = getCurrentUserId(1);
if($ouser->hasRole(8) || $ouser->hasRole(1)){
}else{
    die("Bạn không có quyền duyệt thanh toán ($ouser->email)");
}


$evid = request('evid');
if(!$evid) {
    die("Event ID (evid) is required.");
}

$description = "HVNG TT chi phí sự kiện số $evid";
$payment_type = request('payment_type');

$payments = getPaymentsData($evid, $payment_type);
// AJAX endpoint for creating/regenerating archive files - MUST be before any output
if (request('action') == 'create_new_archive_file') {
    header('Content-Type: application/json');

    $result = generateArchiveFiles($evid, $payments, $payment_type);
    echo json_encode($result);
    exit;
}

try {

    // Auto-generate all 9 files (3 payment types × 3 file types) on page load, but only if they don't exist
    $paymentTypes = ['', 'trong_nuoc', 'nuoc_ngoai'];
    foreach ($paymentTypes as $ptype) {
        $paymentsData = getPaymentsData($evid, $ptype);
        if (!$paymentsData->isEmpty()) {
            $status = checkArchiveStatus($evid, $paymentsData, $ptype);
            // Only generate if files don't exist yet
            if (($status['status'] ?? '') == 'not_exist') {
                generateArchiveFiles($evid, $paymentsData, $ptype);
            }
        }
    }


    // HTML Header with Download Button

?>
<!DOCTYPE html>
<html lang='vi'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Danh sách thanh toán Event</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css' rel='stylesheet'>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css' rel='stylesheet'>
    <style>
        body { padding-top: 10px; background-color: #f5f5f5; }
        a {
            text-decoration: none;
        }
        .container { background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .table { margin-top: 20px; }
        .stats { background-color: #f9f9f9; padding: 15px; border-radius: 5px; margin: 20px 0; }
        .select-type { margin-bottom: 20px; }
        .status-bar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 8px 15px; border-radius: 8px; margin-bottom: 20px; font-weight: 500; display: flex; justify-content: space-between; align-items: center; }
        .status-icon { font-size: 1.2rem; margin-right: 10px; }
        .status-success { background: ; }
        .status-info { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .status-warning { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .file-info { font-size: 0.9rem; opacity: 0.9; }
        table td {
            font-size: 80%;
        }

        /* Payment card styles */
        .payment-card {
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 12px;
            background: white;
        }
        .payment-card.active {
            border: 2px solid #0d6efd;
        }
        .signature-badge {
            display: block;
            margin: 0 auto;
            max-width: fit-content;
            background: #28a745;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-top: 15px;
        }
        .signature-badge.empty {
            background: #6c757d;
        }
        .payment-card h5 {
            margin: 0 0 15px 0;
            color: #333;
            font-weight: bold;
            font-size: 1.2rem;
            text-align: center;
        }
        .payment-card p {
            color: #999;
            font-size: 0.85rem;
            margin: 0;
        }
        .payment-card-actions {
            display: flex;
            gap: 6px;
            margin-bottom: 8px;
        }
        .payment-card-actions .btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .payment-card-files {
            display: flex;
            gap: 6px;
        }
        .payment-card-files .btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .payment-card-warning {
            background: #fff3cd;
            border: 1px solid #ffc107;
            border-radius: 4px;
            padding: 8px;
            margin-top: 10px;
            color: #856404;
            font-size: 0.85rem;
        }
        .payment-card-error {
            color: #dc3545;
            font-size: 0.85rem;
            margin: 0;
        }

        @media (max-width: 1200px) {
            .payment-sections { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 768px) {
            .payment-sections { grid-template-columns: 1fr; }
        }
    </style>
    <script>
        function exportFiles() {
            location.reload();
        }
        function createNewArchive(btn, evid, paymentType) {
            //Confirm yes no:
            if (!confirm('Bạn có chắc chắn muốn tạo lại và ký lại PDF không?')) {
                return;
            }

            const originalText = btn.textContent;
            btn.disabled = true;
            btn.textContent = 'Đang tạo...';
            fetch('?evid=' + evid + '&payment_type=' + paymentType + '&action=create_new_archive_file', {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    alert('✅ Tạo mới mới file thành công');
                    setTimeout(() => location.reload(), 500);
                } else {
                    alert('❌ Lỗi: ' + data.message);
                }
            })
            .catch(e => alert('❌ Lỗi: ' + e.message))
            .finally(() => { btn.disabled = false; btn.textContent = originalText; });
        }

        // Highlight changed cells in payment table
        function highlightChangedCells(logChanges) {
            if (!logChanges || logChanges.length === 0) {
                return;
            }

            const table = document.querySelector('.payment_table');
            if (!table) return;

            // Map field names to column indices (0-based)
            // Columns: STT, ID, Họ Tên, Thu nhập, Khấu trừ, Thực nhận, Mã số thuế, Số tài khoản, Ngân hàng, Ghi chú
            const fieldColumnMap = {
                'payed': 3,        // Thu nhập
                'khau_tru': 4,     // Khấu trừ TNCN
                'thuc_nhan': 5,    // Thực nhận
                'tax_number': 6,   // Mã số thuế
                'id_number': 7,    // CCCD/Passport
                'bank_acc_number': 8,  // Số tài khoản
                'bank_name_text': 9     // Ngân hàng
            };

            logChanges.forEach(change => {
                const row = change.row;  // Row number (1-indexed in log)
                const field = change.field;
                const colIndex = fieldColumnMap[field];

                if (colIndex !== undefined) {
                    // Get the body rows (skip header)
                    const tbody = table.querySelector('tbody');
                    if (tbody) {
                        const rows = tbody.querySelectorAll('tr');
                        // row is 1-indexed in log, but we need array index (0-indexed)
                        if (row - 1 < rows.length) {
                            const targetRow = rows[row - 1];
                            const cells = targetRow.querySelectorAll('td');
                            if (cells[colIndex]) {
                                cells[colIndex].style.backgroundColor = 'lavender';
                                cells[colIndex].style.fontWeight = 'bold';
                                cells[colIndex].title = `Thay đổi: ${change.old} → ${change.new}`;
                            }
                        }
                    }
                }
            });
        }
    </script>
</head>
<body>
    <div class='container-fluid'>
    <?php

    echo "<div style=' border-radius: 10px 10px 0px 0px; margin-bottom: 0px; padding: 10px 15px; background-color: #0d6efd; color: white'> Duyệt thanh toán cho  <a href='/admin/event-info/edit/$evid'> <strong style='color: white'> Sự kiện số $evid

        <i class='bi bi-box-arrow-up-right'></i>

    </strong> </a>


    <a href='/admin/event-info/edit/$evid'  style='color: white; float: right; cursor: pointer; display: flex; '>
    &nbsp | &nbsp;
    Quay lại
     </a>
    <a href='#' data-bs-toggle='modal' data-bs-target='#guideModal' style='color: white; float: right; cursor: pointer; display: flex; align-items: center; gap: 5px;'><i class='bi bi-question-circle'></i> Hướng dẫn </a>


    </div>";

    // Payment Type Selector Links


    // Lấy dữ liệu payments theo payment_type được chọn

    // Check if files exist and data changed (compare JSON)
    // $archiveStatus = checkArchiveStatus($evid, $payments, $payment_type);


    ?>

    <div style="border: 1px solid #ccc; padding: 20px">

        <!-- 3 Section Cards for Payment Types -->
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; " class="payment-sections">

            <!-- Section 1: Tất cả khách hàng -->
            <div class="payment-card <?php echo (empty($payment_type) ? 'active' : ''); ?>">
                <h5><i class="bi bi-people"></i>
                <a href="?evid=<?php echo $evid; ?>&payment_type=">
                Tất cả khách
                </a>
            </h5>

                <?php


                    $allPayments = getPaymentsData($evid, '');
                    $allStatus = checkArchiveStatus($evid, $allPayments, '');
                    // echo "<pre>";
                    // print_r($allStatus);
                    // echo "</pre>";

                    if ($allStatus['success'] ?? false):
                ?>
                    <div class="payment-card-actions">

                        <button class="btn btn-sm btn-outline-warning flex-grow-1" onclick="createNewArchive(this, '<?php echo $evid; ?>', '')">Tạo lại PDF, Excel</button>
                        <a href="/tool1/_site/event_mng/payment/kyso.php?evid=<?php echo $evid; ?>&payment_type=" class="btn btn-sm btn-primary flex-grow-1">Ký số</a>
                    </div>
                    <div class="payment-card-files">
                        <a href="/tool1/_site/event_mng/payment/download_payment_file.php?evid=<?php echo $evid; ?>&payment_type=&file=excel" class="btn btn-sm btn-outline-success flex-grow-1" download>Excel</a>
                        <a href="/tool1/_site/event_mng/payment/download_payment_file.php?evid=<?php echo $evid; ?>&payment_type=&file=pdf" class="btn btn-sm btn-outline-danger flex-grow-1" target="_blank">PDF</a>
                    </div>
                    <?php
                        $sigCount = countSignatures($evid, '');
                        if ($sigCount > 0) {
                            echo '<div class="signature-badge">✅ ' . $sigCount . ' chữ ký</div>';
                        } else {
                            echo '<div class="signature-badge empty">⚪ Chưa ký</div>';
                        }
                    ?>
                    <?php
                        if (($allStatus['status'] ?? '') == 'changed'):
                            echo '<div class="payment-card-warning">⚠️ Dữ liệu đã thay đổi. Có thể Tạo lại và Ký lại PDF.</div>';
                        endif;
                    ?>
                <?php
                    else:
                        echo '<p class="payment-card-error">Lỗi: ' . htmlspecialchars($allStatus['message']) . '</p>';
                    endif;



                ?>
            </div>

            <!-- Section 2: Khách trong nước -->
            <div class="payment-card <?php echo ($payment_type === 'trong_nuoc' ? 'active' : ''); ?>">

                <h5><i class="bi bi-geo-alt"></i>
                <a href="?evid=<?php echo $evid; ?>&payment_type=trong_nuoc">
                Khách Trong nước
                </a>
            </h5>


                <?php
                    $domesticPayments = getPaymentsData($evid, 'trong_nuoc');
                    $domesticStatus = checkArchiveStatus($evid, $domesticPayments, 'trong_nuoc');
                    if ($domesticPayments->isEmpty()):
                ?>
                    <p>Không có dữ liệu</p>
                <?php
                    else:
                        if ($domesticStatus['success'] ?? false):
                ?>
                    <div class="payment-card-actions">

                        <button class="btn btn-sm btn-outline-warning flex-grow-1" onclick="createNewArchive(this, '<?php echo $evid; ?>', 'trong_nuoc')">Tạo lại PDF, Excel</button>
                        <a href="/tool1/_site/event_mng/payment/kyso.php?evid=<?php echo $evid; ?>&payment_type=trong_nuoc" class="btn btn-sm btn-primary flex-grow-1">Ký số</a>
                    </div>
                    <div class="payment-card-files">
                        <a href="/tool1/_site/event_mng/payment/download_payment_file.php?evid=<?php echo $evid; ?>&payment_type=trong_nuoc&file=excel" class="btn btn-sm btn-outline-success flex-grow-1" download>Excel</a>
                        <a href="/tool1/_site/event_mng/payment/download_payment_file.php?evid=<?php echo $evid; ?>&payment_type=trong_nuoc&file=pdf" class="btn btn-sm btn-outline-danger flex-grow-1" target="_blank">PDF</a>
                    </div>
                    <?php
                        $sigCount = countSignatures($evid, 'trong_nuoc');
                        if ($sigCount > 0) {
                            echo '<div class="signature-badge">✅ ' . $sigCount . ' chữ ký</div>';
                        } else {
                            echo '<div class="signature-badge empty">⚪ Chưa ký</div>';
                        }
                    ?>
                    <?php
                        if (($domesticStatus['status'] ?? '') == 'changed'):
                            echo '<div class="payment-card-warning">⚠️ Dữ liệu đã thay đổi. Có thể Tạo lại và Ký lại PDF.</div>';
                        endif;
                    ?>
                <?php
                        else:
                            echo '<p class="payment-card-error">Lỗi: ' . htmlspecialchars($domesticStatus['message']) . '</p>';
                        endif;
                    endif;
                ?>
            </div>

            <!-- Section 3: Khách nước ngoài -->
            <div class="payment-card <?php echo ($payment_type === 'nuoc_ngoai' ? 'active' : ''); ?>">
                <h5><i class="bi bi-globe"></i>
                <a href="?evid=<?php echo $evid; ?>&payment_type=nuoc_ngoai">
                Khách Nước ngoài
                </a>
            </h5>
                <?php
                    $intlPayments = getPaymentsData($evid, 'nuoc_ngoai');
                    $intlStatus = checkArchiveStatus($evid, $intlPayments, 'nuoc_ngoai');
                    if ($intlPayments->isEmpty()):
                ?>
                    <p>Không có dữ liệu</p>
                <?php
                    else:
                        if ($intlStatus['success'] ?? false):
                ?>
                    <div class="payment-card-actions">

                        <button class="btn btn-sm btn-outline-warning flex-grow-1" onclick="createNewArchive(this, '<?php echo $evid; ?>', 'nuoc_ngoai')">Tạo lại PDF, Excel</button>
                        <a href="/tool1/_site/event_mng/payment/kyso.php?evid=<?php echo $evid; ?>&payment_type=nuoc_ngoai" class="btn btn-sm btn-primary flex-grow-1" style="flex: 1;">Ký số</a>

                    </div>
                    <div class="payment-card-files">
                        <a href="/tool1/_site/event_mng/payment/download_payment_file.php?evid=<?php echo $evid; ?>&payment_type=nuoc_ngoai&file=excel" class="btn btn-sm btn-outline-success flex-grow-1" download>Excel</a>
                        <a href="/tool1/_site/event_mng/payment/download_payment_file.php?evid=<?php echo $evid; ?>&payment_type=nuoc_ngoai&file=pdf" class="btn btn-sm btn-outline-danger flex-grow-1" target="_blank">PDF</a>
                    </div>
                    <?php
                        $sigCount = countSignatures($evid, 'nuoc_ngoai');
                        if ($sigCount > 0) {
                            echo '<div class="signature-badge">✅ ' . $sigCount . ' chữ ký</div>';
                        } else {
                            echo '<div class="signature-badge empty">⚪ Chưa ký</div>';
                        }
                    ?>
                    <?php
                        if (($intlStatus['status'] ?? '') == 'changed'):
                            echo '<div class="payment-card-warning">⚠️ Dữ liệu đã thay đổi. Có thể Tạo lại và Ký lại PDF.</div>';
                        endif;
                    ?>
                <?php
                        else:
                            echo '<p class="payment-card-error">Lỗi: ' . htmlspecialchars($intlStatus['message']) . '</p>';
                        endif;
                    endif;
                ?>
            </div>

        </div>

    </div>

    <div style="border: 1px solid #ccc; padding: 20px; margin-top: 20px;">

        <?php
    if ($payments->isEmpty()) {
        echo '<p style="text-align: center; color: red; padding: 20px;">Không có dữ liệu thanh toán nào. Chỉ các thành viên đã điền Số tiền mới hiện lên danh sách này</p>';
        exit;
    }

    // Show current payment type filter
    $typeLabel = '';
    if ($payment_type == 'trong_nuoc') {
        $typeLabel = ' - Khách trong nước (VNĐ)';
    } else if ($payment_type == 'nuoc_ngoai') {
        $typeLabel = ' - Khách nước ngoài (USD)';
    } else {
        $typeLabel = ' - Tất cả khách hàng';
    }
    echo '<h5 style="margin-bottom: 15px; color: #0d6efd;">📋 Danh sách thanh toán' . htmlspecialchars($typeLabel) . '</h5>';
?>
    <table class="table table-striped table-hover border payment_table">
    <thead class="table-light">
    <tr>
    <th>STT</th>
    <th>ID</th>
    <th>Họ Tên</th>
    <th>Thu nhập</th>
    <th>Khấu trừ TNCN</th>
    <th>Thực nhận</th>
    <th>Mã số thuế</th>
    <th>CCCD/Passport</th>
    <th>Số tài khoản</th>
    <th>Ngân hàng</th>
    <th>Ghi chú</th>
    </tr>
    </thead>
    <tbody>

<?php
        $totalAmount = 0;
        $totalVND = 0;
        $totalUSD = 0;
        $cc = 0;
        foreach ($payments as $payment) {
        $formattedAmount = number_format($payment->payed, 0, ',', '.');
        $totalAmount += $payment->payed;

        $payedAfterTax = $payment->payed;
//        $taxRate = 0.1; // Mặc định 10%
        // Tách tổng theo loại
        if ($payment->payment_type == 'trong_nuoc') {
            $totalVND += $payment->payed;
            $currencyUnit = 'VNĐ';
        } else if ($payment->payment_type == 'nuoc_ngoai') {
//            $taxRate = 0.2; // 20% cho khách nước ngoài
            $totalUSD += $payment->payed;
            $currencyUnit = 'USD';
        } else {
            // Nếu không có payment_type, mặc định là VNĐ
            $totalVND += $payment->payed;
            $currencyUnit = 'VNĐ';
        }

        $bankName = config("banks")[$payment->bank_name_text]['bidv_name'] ?? '';
//        $bankName = str_replace(["&", '-' ], " ", $bankName);
//        $bankName = str_replace(["  "], " ", $bankName);


        $userEvent = \App\Models\EventUserInfo::find($payment->user_event_id) ?? '';
        $cccd = $userEvent->id_number ?? null;

        $cc++;
        echo "<tr>";
        echo "<td>$cc</td>";
        echo "<td>" . $payment->id . "</td>";
        echo "<td>" . \LadLib\Common\cstring2::convert_codau_khong_dau(($payment->_last_name ?? '') ." ". ($payment->_first_name ?? '')) . "</td>";

        echo "<td style='text-align: right;'>" . $formattedAmount . " " . $currencyUnit . "</td>";
        $taxAmount = $payment->khau_tru;
        echo "<td style='text-align: right;'>" . number_format($taxAmount,
    0, ',', '.') . " " . $currencyUnit . "</td>";
        $payedAfterTax = $payment->payed - $taxAmount;
        echo "<td style='text-align: right;'>" . number_format($payedAfterTax, 0, ',', '.') . " " . $currencyUnit . "</td>";

        echo "<td>" . ($payment->tax_number ?? '') . "</td>";

        echo "<td>" . ($cccd ?? '<span style="color: red"> Cần cập nhật </span>') . "</td>";

        echo "<td>" . ($payment->bank_acc_number ?? '') . "</td>";
        echo "<td>" . \LadLib\Common\cstring2::convert_codau_khong_dau($bankName ?? '') . "</td>";
//        echo "<td>" . ($payment->transaction_id ?? '') . "</td>";
        echo "<td>" .\LadLib\Common\cstring2::convert_codau_khong_dau($description ?? '') . "</td>";
        echo "</tr>";
    }

    ?>

    </tbody>
    </table>

    <?php
    // Thống kê
    echo "<div class='stats'>";
    echo "<h4>Thống kê tổng quan:</h4>";
    echo "<p><strong>Tổng số giao dịch:</strong> " . $payments->count() . "</p>";
    echo "<p><strong>Tổng số tiền:</strong></p>";
    echo "<ul>";
    echo "<li><strong>Khách trong nước (VNĐ):</strong> " . number_format($totalVND, 0, ',', '.') . " VNĐ</li>";
    echo "<li><strong>Khách nước ngoài (USD):</strong> " . number_format($totalUSD, 0, ',', '.') . " USD</li>";
    echo "</ul>";

    // Thống kê theo trạng thái
    $statusStats = [];
    foreach ($payments as $payment) {
        $status = $payment->payment_status ?? 'unknown';
        if (!isset($statusStats[$status])) {
            $statusStats[$status] = ['count' => 0, 'amount' => 0];
        }
        $statusStats[$status]['count']++;
        $statusStats[$status]['amount'] += $payment->amount;
    }

?>
    </div>
        <?php
    echo "</div>";
    echo "<script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js'></script>";

    // Highlight changed cells if data has changed
    if (($allStatus['status'] ?? '') == 'changed' && !empty($allStatus['log_change'])) {
        echo "<script>";
        echo "document.addEventListener('DOMContentLoaded', function() {";
        echo "  highlightChangedCells(" . json_encode($allStatus['log_change']) . ");";
        echo "});";
        echo "</script>";
    }

    // Modal Dialog HTML
    ?>

    <!-- Hướng Dẫn Modal -->
    <div class='modal fade' id='guideModal' tabindex='-1' aria-labelledby='guideModalLabel' aria-hidden='true'>
        <div class='modal-dialog modal-lg'>
            <div class='modal-content'>
                <div class='modal-header' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;'>
                    <h5 class='modal-title' id='guideModalLabel'>
                        <i class='bi bi-info-circle'></i> Hướng dẫn sử dụng
                    </h5>
                    <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body'>
                    <h6 style='color: #667eea; font-weight: bold; margin-top: 15px;'>📋 Bước 1: Chọn loại khách hàng</h6>
                    <p>Chọn một trong ba tùy chọn:</p>
                    <ul>
                        <li><strong>Tất cả khách hàng:</strong> Hiển thị danh sách thanh toán của tất cả khách hàng</li>
                        <li><strong>Khách trong nước:</strong> Chỉ hiển thị khách hàng trong nước (tính bằng VNĐ)</li>
                        <li><strong>Khách nước ngoài:</strong> Chỉ hiển thị khách hàng nước ngoài (tính bằng USD)</li>
                    </ul>

                    <h6 style='color: #667eea; font-weight: bold; margin-top: 15px;'>📄 Bước 2: Tải file Excel & PDF</h6>
                    <p>Sau khi chọn loại khách hàng, hệ thống sẽ tự động tạo các file:</p>
                    <ul>
                        <li><strong>File Excel:</strong> Dùng <i class='bi bi-file-earmark-spreadsheet'></i> để tải file bảng tính</li>
                        <li><strong>File PDF:</strong> Dùng <i class='bi bi-file-pdf' style='color: #dc3545;'></i> để xem hoặc tải file PDF</li>
                    </ul>

                    <h6 style='color: #667eea; font-weight: bold; margin-top: 15px;'>✍️ Bước 3: Ký số PDF</h6>
                    <p>Bấm nút <strong><i class='bi bi-pen'></i> Ký số</strong> để:</p>
                    <ul>
                        <li>Mở file PDF trong cửa sổ ký số</li>
                        <li>Sử dụng chữ ký số để ký phê duyệt</li>
                        <li>Hệ thống sẽ tự động lưu file PDF đã ký (_signed.pdf)</li>
                    </ul>

                    <h6 style='color: #667eea; font-weight: bold; margin-top: 15px;'>🔄 Bước 3b: Tạo lại PDF</h6>
                    <p>Bấm nút <strong><i class='bi bi-arrow-clockwise'></i> Tạo lại</strong> để:</p>
                    <ul>
                        <li>Tạo lại file PDF để ký lại khi dữ liệu thanh toán thay đổi</li>
                        <li>Cập nhật PDF khi chữ ký chưa chuẩn hoặc lỗi</li>
                        <li>Hệ thống sẽ tự động sinh ra file PDF mới nhất</li>
                    </ul>

                    <h6 style='color: #667eea; font-weight: bold; margin-top: 15px;'>📊 Bước 4: Xem danh sách thanh toán</h6>
                    <p>Bảng dưới hiển thị chi tiết các giao dịch thanh toán:</p>
                    <ul>
                        <li><strong>STT:</strong> Số thứ tự</li>
                        <li><strong>ID:</strong> Mã định danh giao dịch</li>
                        <li><strong>Họ Tên:</strong> Tên người thanh toán</li>
                        <li><strong>Thu nhập:</strong> Số tiền gốc</li>
                        <li><strong>Khấu trừ TNCN:</strong> Thuế thu nhập cá nhân</li>
                        <li><strong>Thực nhận:</strong> Số tiền sau thuế</li>
                        <li><strong>Mã số thuế:</strong> Mã số thuế cá nhân</li>
                        <li><strong>Số tài khoản:</strong> Tài khoản ngân hàng nhận tiền</li>
                        <li><strong>Ngân hàng:</strong> Tên ngân hàng</li>
                    </ul>

                    <div class='alert alert-info mt-3' style='border-left: 4px solid #0d6efd;'>
                        <strong><i class='bi bi-lightbulb'></i> Mẹo:</strong> Nếu dữ liệu thanh toán thay đổi sau khi tạo file, hệ thống sẽ yêu cầu tạo lại PDF.
                        Bấm nút \"Tạo lại PDF\" để cập nhật file mới nhất.
                    </div>
                </div>
                <div class='modal-footer'>
                    <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>Đóng</button>
                </div>
            </div>
        </div>
    </div>


    </body>
    </html>

<?php
} catch (\Exception $e) {
    echo "<div style='color: red; background-color: #ffe6e6; padding: 10px; border-radius: 5px;'>";
    echo "<h3>Lỗi:</h3>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<p><strong>File:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Line:</strong> " . $e->getLine() . "</p>";
    echo "</div>";
}

/**
 * Đếm số chữ ký số từ file PDF
 */
function countSignatures($evid, $payment_type = '') {
    $pdfPath = getFileNamePdf($evid, $payment_type);
    $signedPdfPath = str_replace('.pdf', '_signed.pdf', $pdfPath);

    // Use signed version if exists
    if (file_exists($signedPdfPath)) {
        $pdfPath = $signedPdfPath;
    }

    if (!file_exists($pdfPath)) {
        return 0;
    }

    try {
        $extractor = new PDFSignatureExtractor($pdfPath);
        $extractedSigs = $extractor->extract();
        return count($extractedSigs);
    } catch (Exception $e) {
        error_log("Error counting signatures: " . $e->getMessage());
        return 0;
    }
}

/**
 * Lấy dữ liệu thanh toán từ database
 */
function getPaymentsData($evid, $payment_type = '') {
    $query = \App\Models\EventUserPayment::leftJoin('event_user_infos', 'event_user_payments.user_event_id', '=', 'event_user_infos.id')
        ->where('event_user_payments.event_id', $evid)
        ->where('event_user_payments.payed', '>', 0)
        ->select([
            'event_user_payments.*',
            'event_user_infos.first_name as _first_name',
            'event_user_infos.last_name as _last_name',
            'event_user_infos.tax_number',
            'event_user_infos.payment_type',
            'event_user_infos.bank_acc_number',
            'event_user_infos.bank_name_text'
        ])
        ->orderBy('event_user_infos.first_name', 'asc')
        ->orderBy('event_user_infos.last_name', 'asc');

    if (!$payment_type) {
        return $query->get();
    } else {
        return $query->where('event_user_infos.payment_type', $payment_type)->get();
    }
}

/**
 * Hàm xuất Excel từ file mẫu
 * Tạo file mới với tên ThanhToan_Event_<evid>.xlsx
 * Dữ liệu ghi đè từ hàng 3 với các cột:
 * 1: STT (tăng từ 1)
 * 2: Tên (first_name + last_name)
 * 3: Số tài khoản (bank_acc_number)
 * 4: Số tiền (thực nhận sau khi tính thuế)
 * 5: Chi nhánh ngân hàng (bank_name_text)
 * 6: Nội dung (Thanh toán Sự kiện số <evid>)
 */
function exportToExcel($evid, $payments = [], $payment_type = '') {
    try {
        // Lấy data từ database nếu chưa có
        if (empty($payments)) {
            $payments = getPaymentsData($evid, $payment_type);
        }

        // Đường dẫn file mẫu
        $templatePath = __DIR__ . '/event-dav-salary.xlsx';

        if (!file_exists($templatePath)) {
            throw new \Exception("File mẫu không tồn tại: $templatePath");
        }

        // Load file mẫu
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Set style cho cột C (Số tài khoản): căn phải, format text
        // $sheet->getColumnDimension('C')->setAlignment(['horizontal' => Alignment::HORIZONTAL_RIGHT]);
        $sheet->getStyle('C')->getNumberFormat()->setFormatCode('@'); // Text format

        // Bắt đầu ghi dữ liệu từ hàng 3 (index 3)
        $row = 3;
        $stt = 1;

        foreach ($payments as $payment) {
            $firstName = $payment->_first_name ?? '';
            $lastName = $payment->_last_name ?? '';
            $fullName = trim("$lastName $firstName");
            $fullName = \LadLib\Common\cstring2::convert_codau_khong_dau($fullName);
            $bankAccNumber = $payment->bank_acc_number ?? '';
            $amount = $payment->payed ?? 0;
//            $taxRate = 0.1; // Mặc định 10%

            // Xác định taxRate dựa trên payment_type (tương tự ở trên)
//            if ($payment->payment_type == 'trong_nuoc') {
//                $taxRate = 0.1; // 10% cho khách trong nước
//            } else if ($payment->payment_type == 'nuoc_ngoai') {
//                $taxRate = 0.2; // 20% cho khách nước ngoài
//            } else {
//                $taxRate = 0.1; // Mặc định 10%
//            }

            // Tính toán thuế và lấy thực nhận
//            $taxAmount = round($amount * $taxRate);
            $payedAfterTax = $amount - ($payment->khau_tru ?? 0);

            // $payment->bank_name_text lấy ra tên ngân hàng từ config.banks.php
            $bankName = config("banks")[$payment->bank_name_text]['bidv_name'] ?? '';
            $bankName = \LadLib\Common\cstring2::convert_codau_khong_dau($bankName);
//            $bankName = str_replace(["&", '-' ], " ", $bankName);
//            $bankName = str_replace(["  "], " ", $bankName);

            $description = "HVNG TT Chi phí Sự kiện số $evid";
            $description = \LadLib\Common\cstring2::convert_codau_khong_dau($description);

            // Cột 1: STT
            $sheet->setCellValue('A' . $row, $stt);

            // Cột 2: Tên (first_name + last_name)
            $sheet->setCellValue('B' . $row, $fullName);

            // Cột 3: Số tài khoản (giữ nguyên format text, căn phải)
            $sheet->setCellValue('C' . $row, $bankAccNumber);
            $sheet->getStyle('C' . $row)->getNumberFormat()->setFormatCode('@'); // Text format
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Cột 4: Số tiền (thực nhận sau khi tính thuế)
            $sheet->setCellValue('D' . $row, $payedAfterTax);
            $sheet->getStyle('D' . $row)->getNumberFormat()->setFormatCode('#,##0');

            // Cột 5: CCCD/Passport
//            $sheet->setCellValue('E' . $row, $payment->id_number ?? '');

            // Cột 6: Chi nhánh ngân hàng
            $sheet->setCellValue('E' . $row, $bankName);

            // Cột 7: Nội dung
            $sheet->setCellValue('F' . $row, $description);

            // Áp dụng border và alignment cho các ô
            for ($col = 'A'; $col <= 'F'; $col++) {
                $cell = $sheet->getCell($col . $row);
                $cell->getStyle()
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

                $cell->getStyle()
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
            }

            // Align số tiền sang phải
            $sheet->getStyle('D' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            // Align số tài khoản sang phải (đặt lại vì loop trên có thể reset)
            $sheet->getStyle('C' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

            $row++;
            $stt++;
        }

        // Auto adjust column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(30);
        $sheet->getColumnDimension('F')->setWidth(80);
//        $sheet->getColumnDimension('G')->setWidth(80);

        // Tạo tên file
        $filename = "ThanhToanEvent_$evid.xlsx";

        // Lưu file Excel vào folder tạm
        $tempExcelPath = sys_get_temp_dir() . '/' . $filename;
        $writer = new Xlsx($spreadsheet);
        $writer->save($tempExcelPath);

        // Copy sang archive folder
        $archiveExcelPath = getFileNameExcel($evid, $payment_type);
        $archiveFolder = getFolderPdf($evid);

        // Đảm bảo folder tồn tại
        if (!is_dir($archiveFolder)) {
            @mkdir($archiveFolder, 0755, true);
        }

        // Copy file Excel sang archive
        if (!copy($tempExcelPath, $archiveExcelPath)) {
            error_log("Failed to copy Excel to archive: $tempExcelPath -> $archiveExcelPath");
        }

        // Clean up temp file
        @unlink($tempExcelPath);

        // Return temp path để convertExcelToPdf() dùng (nó sẽ load từ archive folder)
        return $archiveExcelPath;

    } catch (\Exception $e) {
        die("Lỗi xuất Excel: " . $e->getMessage());
    }
}

function comparePaymentData($existing, $current) {
    // Normalize arrays for comparison
    if (!is_array($existing) || !is_array($current)) {
        return true; // Data changed if format is different
    }

    if (count($existing) !== count($current)) {
        return true; // Different number of records
    }

    // Compare key payment fields
    foreach ($current as $idx => $payment) {
        if (!isset($existing[$idx])) {
            return true; // Missing record in existing data
        }

        $existingPay = $existing[$idx];

        // Compare critical fields (handle both array and object)
        $compareFields = ['id', 'payed', 'khau_tru', 'payment_type'];
        foreach ($compareFields as $field) {
            // Get current value (handle object or array)
            if (is_object($payment)) {
                $currentVal = $payment->$field ?? null;
            } else {
                $currentVal = $payment[$field] ?? null;
            }

            // Get existing value (always array)
            $existingVal = $existingPay[$field] ?? null;

            // Strict comparison with type coercion
            if ((string)$currentVal !== (string)$existingVal) {
                error_log("Data mismatch at [$idx][$field]: '$currentVal' vs '$existingVal'");
                return true; // Data has changed
            }
        }
    }

    return false; // Data is the same
}

/**
 * Check archive status WITHOUT creating files
 * Returns:
 * - 'not_exist': Files not created yet
 * - 'unchanged': Files exist and data is same
 * - 'changed': Files exist but data changed
 */
function checkArchiveStatus($evid, $payments, $payment_type = '') {
    try {
        $folderPath = getFolderPdf($evid, $payment_type);
        $excelPath = getFileNameExcel($evid, $payment_type);
        $pdfPath = getFileNamePdf($evid, $payment_type);
        $jsonPath = getFileNameJson($evid, $payment_type);

        // Check if all files exist
        $allFilesExist = file_exists($excelPath) && file_exists($pdfPath) && file_exists($jsonPath);

        if (!$allFilesExist) {
            // Files don't exist yet
            return [
                'success' => true,
                'status' => 'not_exist',
                'message' => 'Chưa có file. Hãy tạo để ký PDF',
                'excelPath' => $excelPath,
                'pdfPath' => $pdfPath,
                'jsonPath' => $jsonPath,
                'log_change' => [],
                'timestamp' => null,
            ];
        }

        // Files exist - check if data changed by comparing JSON strings
        if (file_exists($jsonPath)) {
            // Build current payment metadata (same as generateArchiveFiles does)
            $paymentData = [];
            foreach ($payments as $payment) {
                $paymentData[] = [
                    'id' => $payment->id ?? null,
                    'user_event_id' => $payment->user_event_id ?? null,
                    'first_name' => \LadLib\Common\cstring2::convert_codau_khong_dau($payment->_first_name ?? null),
                    'last_name' => \LadLib\Common\cstring2::convert_codau_khong_dau($payment->_last_name ?? null),
                    'payed' => $payment->payed ?? 0,
                    'khau_tru' => $payment->khau_tru ?? 0,
                    'thuc_nhan' => ($payment->payed ?? 0) - ($payment->khau_tru ?? 0),
                    'tax_number' => $payment->tax_number ?? null,
                    'bank_acc_number' => $payment->bank_acc_number ?? null,
                    'bank_name_text' => \LadLib\Common\cstring2::convert_codau_khong_dau($payment->bank_name_text ?? null),
                    'payment_type' => $payment->payment_type ?? null,
                ];
            }

            $metadata = [
                'evid' => $evid,
                'total_payments' => count($paymentData),
                'payments' => $paymentData,
            ];

            // Generate current JSON string (same as in convertExcelToPdf)
            $currentJsonString = json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // Read existing JSON file
            $existingJsonString = file_get_contents($jsonPath);

            // Debug: Log JSON comparison
            error_log("=== JSON Comparison for event $evid ===");
            error_log("Payments count: " . count($paymentData));
            error_log("Current JSON length: " . strlen($currentJsonString));
            error_log("Existing JSON length: " . strlen($existingJsonString));
            error_log("JSON Match: " . ($currentJsonString === $existingJsonString ? 'YES' : 'NO'));

            // Show first payment to debug
            if (!empty($paymentData)) {
                error_log("First payment current: " . json_encode($paymentData[0]));
            }

            // Parse existing to compare
            $existingData = json_decode($existingJsonString, true);
            if (!empty($existingData['payments'])) {
                error_log("First payment existing: " . json_encode($existingData['payments'][0]));
            }

            // Compare JSON strings directly
            if ($currentJsonString === $existingJsonString) {
                // Data unchanged
                error_log("Archive status: data unchanged for event $evid");
                return [
                    'success' => true,
                    'status' => 'unchanged',
                    'message' => 'Dữ liệu không thay đổi so với DB',
                    'excelPath' => $excelPath,
                    'pdfPath' => $pdfPath,
                    'jsonPath' => $jsonPath,
                    'log_change' => [],
                    'timestamp' => filemtime($jsonPath),
                ];
            } else {
                // Data changed
                error_log("Archive status: data changed for event $evid");
                error_log("Existing JSON (first 300 chars): " . substr($existingJsonString, 0, 300));
                error_log("Current JSON (first 300 chars): " . substr($currentJsonString, 0, 300));

                // Tìm field nào đã thay đổi
                $logChanges = [];
                $existingData = json_decode($existingJsonString, true);

                if (!empty($existingData['payments']) && !empty($paymentData)) {
                    for ($i = 0; $i < count($paymentData); $i++) {
                        if (isset($existingData['payments'][$i])) {
                            $oldPayment = $existingData['payments'][$i];
                            $newPayment = $paymentData[$i];

                            foreach ($newPayment as $field => $newValue) {
                                $oldValue = $oldPayment[$field] ?? null;
                                if ((string)$oldValue !== (string)$newValue) {
                                    $logChanges[] = [
                                        'row' => $i + 1,
                                        'field' => $field,
                                        'old' => $oldValue,
                                        'new' => $newValue
                                    ];
                                }
                            }
                        }
                    }
                }

                return [
                    'success' => true,
                    'status' => 'changed',
                    'message' => 'Dữ liệu đã có thay đổi từ file được tạo trước đây',
                    'excelPath' => $excelPath,
                    'pdfPath' => $pdfPath,
                    'jsonPath' => $jsonPath,
                    'log_change' => $logChanges,
                    'timestamp' => filemtime($jsonPath),
                ];
            }
        }

        return [
            'success' => true,
            'status' => 'not_exist',
            'message' => 'Chưa có file JSON',
            'excelPath' => $excelPath,
            'pdfPath' => $pdfPath,
            'jsonPath' => $jsonPath,
            'timestamp' => null,
        ];

    } catch (Exception $e) {
        error_log("Error in checkArchiveStatus: " . $e->getMessage());
        return [
            'success' => false,
            'status' => 'error',
            'message' => 'Lỗi: ' . $e->getMessage(),
            'excelPath' => getFileNameExcel($evid, $payment_type),
            'pdfPath' => getFileNamePdf($evid, $payment_type),
            'jsonPath' => getFileNameJson($evid, $payment_type),
            'timestamp' => null,
        ];
    }
}

function generateArchiveFiles($evid, $payments, $payment_type = '') {
    try {
        $folderPath = getFolderPdf($evid, $payment_type);
        $excelPath = getFileNameExcel($evid, $payment_type);
        $pdfPath = getFileNamePdf($evid, $payment_type);
        $pdfPathSigned = getFileNamePdf($evid, $payment_type, true);
        $jsonPath = getFileNameJson($evid, $payment_type);

        // Create folder if it doesn't exist
        if (!is_dir($folderPath)) {
            @mkdir($folderPath, 0755, true);
        }

        if(file_exists($pdfPathSigned))
            unlink($pdfPathSigned);

        if(file_exists($excelPath))
            unlink($excelPath);
        if(file_exists($pdfPath))
            unlink($pdfPath);
        if(file_exists($jsonPath))
            unlink($jsonPath);


        // die("Deleted signed PDF: $pdfPathSigned");

        $needsRegen = true;
        $existingData = null;

        // Check if JSON exists and compare data
        if (file_exists($jsonPath)) {
            // Build current payment metadata (same structure as in convertExcelToPdf)
            $paymentData = [];
            foreach ($payments as $payment) {
                $paymentData[] = [
                    'id' => $payment->id ?? null,
                    'user_event_id' => $payment->user_event_id ?? null,
                    'first_name' => \LadLib\Common\cstring2::convert_codau_khong_dau($payment->_first_name ?? null),
                    'last_name' => \LadLib\Common\cstring2::convert_codau_khong_dau($payment->_last_name ?? null),
                    'payed' => $payment->payed ?? 0,
                    'khau_tru' => $payment->khau_tru ?? 0,
                    'thuc_nhan' => ($payment->payed ?? 0) - ($payment->khau_tru ?? 0),
                    'tax_number' => $payment->tax_number ?? null,
                    'bank_acc_number' => $payment->bank_acc_number ?? null,
                    'bank_name_text' => \LadLib\Common\cstring2::convert_codau_khong_dau($payment->bank_name_text ?? null),
                    'payment_type' => $payment->payment_type ?? null,
                ];
            }

            $metadata = [
                'evid' => $evid,
                'total_payments' => count($paymentData),
                'payments' => $paymentData,
            ];

            // Generate current JSON string
            $currentJsonString = json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

            // Read existing JSON file
            $existingJsonString = file_get_contents($jsonPath);

            // Compare JSON strings directly
            if ($currentJsonString === $existingJsonString) {
                // Data unchanged - no need to regenerate
                $needsRegen = false;
                error_log("Files exist and data unchanged for event $evid - skipping regeneration");

                return [
                    'success' => true,
                    'regenerated' => false,
                    'message' => 'Dữ liệu trong DB không thay đổi so với các file đã tạo',
                    'excelPath' => $excelPath,
                    'pdfPath' => $pdfPath,
                    'jsonPath' => $jsonPath,
                    'timestamp' => filemtime($jsonPath),
                ];
            }
        }

        if ($needsRegen) {
            $startTime = microtime(true);

            // Generate Excel - returns path to created Excel file
            try {
                $createdExcelPath = exportToExcel($evid, $payments, $payment_type);
                if (!$createdExcelPath) {
                    throw new Exception('exportToExcel returned no path');
                }
            } catch (Exception $e) {
                error_log("Error generating Excel for event $evid: " . $e->getMessage());
                return [
                    'success' => false,
                    'regenerated' => true,
                    'message' => 'Lỗi Excel: ' . $e->getMessage(),
                    'excelPath' => $excelPath,
                    'pdfPath' => $pdfPath,
                    'jsonPath' => $jsonPath,
                    'timestamp' => time(),
                ];
            }

            // Check if Excel was created
            if (!file_exists($createdExcelPath)) {
                error_log("Excel file not found after export: $createdExcelPath");
                return [
                    'success' => false,
                    'regenerated' => true,
                    'message' => 'Lỗi: File Excel không được tạo',
                    'excelPath' => $excelPath,
                    'pdfPath' => $pdfPath,
                    'jsonPath' => $jsonPath,
                    'timestamp' => time(),
                ];
            }

            // Convert Excel to PDF (also saves JSON) - pass created Excel path
            try {
                convertExcelToPdf($createdExcelPath, $evid, $payments, $payment_type);
            } catch (Exception $e) {
                error_log("Error converting Excel to PDF for event $evid: " . $e->getMessage());
                return [
                    'success' => false,
                    'regenerated' => true,
                    'message' => 'Lỗi PDF: ' . $e->getMessage(),
                    'excelPath' => $excelPath,
                    'pdfPath' => $pdfPath,
                    'jsonPath' => $jsonPath,
                    'timestamp' => time(),
                ];
            }

            $endTime = microtime(true);
            $duration = round($endTime - $startTime, 2);

            // Verify all 3 files exist
            $allFilesExist = file_exists($excelPath) && file_exists($pdfPath) && file_exists($jsonPath);

            if ($allFilesExist) {
                error_log("Files regenerated for event $evid in {$duration}s (data changed or files missing)");

                return [
                    'success' => true,
                    'regenerated' => true,
                    'message' => "✅ Tạo mới file thành công ({$duration}s)",
                    'excelPath' => $excelPath,
                    'pdfPath' => $pdfPath,
                    'jsonPath' => $jsonPath,
                    'timestamp' => time(),
                ];
            } else {
                $missing = [];
                !file_exists($excelPath) && $missing[] = 'Excel';
                !file_exists($pdfPath) && $missing[] = 'PDF';
                !file_exists($jsonPath) && $missing[] = 'JSON';

                error_log("File generation incomplete for event $evid - missing: " . implode(', ', $missing));

                return [
                    'success' => false,
                    'regenerated' => true,
                    'message' => '⚠️ Tạo file không hoàn tất - thiếu: ' . implode(', ', $missing),
                    'excelPath' => $excelPath,
                    'pdfPath' => $pdfPath,
                    'jsonPath' => $jsonPath,
                    'timestamp' => time(),
                ];
            }
        }

        return [
            'success' => true,
            'regenerated' => false,
            'message' => 'OK',
            'excelPath' => $excelPath,
            'pdfPath' => $pdfPath,
            'jsonPath' => $jsonPath,
            'timestamp' => time(),
        ];

    } catch (Exception $e) {
        error_log("Error in generateArchiveFiles: " . $e->getMessage());
        return [
            'success' => false,
            'regenerated' => false,
            'message' => 'Lỗi: ' . $e->getMessage(),
            'excelPath' => '',
            'pdfPath' => '',
            'jsonPath' => '',
            'timestamp' => time(),
        ];
    }
}

/**
 * Convert Excel file directly to PDF (like Google Sheets "Download as PDF")
 * Sử dụng PhpSpreadsheet PDF Writer hoặc mPDF
 */
function convertExcelToPdf($excelFilePath, $evid, $payments = null, $payment_type = '') {
    try {
        // Tạo folder nếu chưa tồn tại
        $pdfDir = getFolderPdf($evid);
        if (!is_dir($pdfDir)) {
            @mkdir($pdfDir, 0755, true);
        }

        // Lưu bản Excel vào folder
        $excelArchivePath = getFileNameExcel($evid, $payment_type);
        copy($excelFilePath, $excelArchivePath);
        error_log("Excel archived: $excelArchivePath");

        // Tên file PDF
        $pdfPath = getFileNamePdf($evid, $payment_type);
        $jsonFilePath = getFileNameJson($evid, $payment_type);

        // Prepare JSON metadata từ payment data
        $paymentData = [];
        if ($payments) {
            foreach ($payments as $payment) {
                $paymentData[] = [
                    'id' => $payment->id ?? null,
                    'user_event_id' => $payment->user_event_id ?? null,
                    'first_name' => \LadLib\Common\cstring2::convert_codau_khong_dau($payment->_first_name ?? null),
                    'last_name' => \LadLib\Common\cstring2::convert_codau_khong_dau($payment->_last_name ?? null),
                    'payed' => $payment->payed ?? 0,
                    'khau_tru' => $payment->khau_tru ?? 0,
                    'thuc_nhan' => ($payment->payed ?? 0) - ($payment->khau_tru ?? 0),
                    'tax_number' => $payment->tax_number ?? null,
                    'bank_acc_number' => $payment->bank_acc_number ?? null,
                    'bank_name_text' => \LadLib\Common\cstring2::convert_codau_khong_dau($payment->bank_name_text ?? null),
                    'payment_type' => $payment->payment_type ?? null,
                ];
            }
        }

        $metadata = [
            'evid' => $evid,
            'total_payments' => count($paymentData),
            'payments' => $paymentData,
        ];

        $metadataJson = json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Load Excel file
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($excelFilePath);
        $sheet = $spreadsheet->getActiveSheet();

        // Try PhpSpreadsheet TCPDF/mPDF Writer first
        try {
            // Check if PhpSpreadsheet PDF Writer is available
            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Pdf');

            // Configure PDF output
            $writer->setPreCalculateFormulas(false);

            // Save PDF using PhpSpreadsheet built-in PDF support
            $writer->save($pdfPath);

            error_log("PDF created using PhpSpreadsheet PDF Writer: $pdfPath");
        } catch (\Exception $e1) {
            error_log("PhpSpreadsheet PDF Writer failed: " . $e1->getMessage());

            // Fallback: Use mPDF with HTML conversion
            if (!class_exists('\Mpdf\Mpdf')) {
                if (!file_exists('/var/www/html/vendor/mpdf/mpdf/src/Mpdf.php')) {
                    error_log("Warning: mPDF not available. Saving Excel only.");
                    file_put_contents($jsonFilePath, $metadataJson);
                    return $excelArchivePath;
                }
                require '/var/www/html/vendor/mpdf/mpdf/src/Mpdf.php';
            }

            // Generate HTML from Excel sheet
            $html = generatePdfHtml($sheet, $evid);

            // Create PDF with mPDF
            $mpdf = new \Mpdf\Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'orientation' => 'L',
                'margin_top' => 10,
                'margin_bottom' => 10,
                'margin_left' => 10,
                'margin_right' => 10,
            ]);

            $mpdf->SetTitle("Thanh toán Event $evid");
            $mpdf->SetAuthor("DAV");
            $mpdf->SetSubject("Danh sách thanh toán sự kiện");
            $mpdf->SetKeywords("event, payment, bill");

            $mpdf->WriteHTML($html);
            $mpdf->Output($pdfPath, \Mpdf\Output\Destination::FILE);

            error_log("PDF created using mPDF (HTML fallback): $pdfPath");
        }

        // Lưu JSON metadata riêng
        file_put_contents($jsonFilePath, $metadataJson);

        error_log("Metadata saved: $jsonFilePath");
        error_log("Excel saved: $excelArchivePath");

        return $pdfPath;

    } catch (\Exception $e) {
        error_log("Convert PDF error: " . $e->getMessage());
        return null;
    }
}

/**
 * Tạo HTML từ sheet Excel để convert sang PDF
 * Export Tab đầu tiên (sheet 0) từ Excel file
 */
function generatePdfHtml($sheet, $evid) {
    $html = '<!DOCTYPE html><html><head><meta charset="UTF-8" />';
    $html .= '<style>body { font-family: DejaVu Sans; font-size: 10pt; margin: 10px; }';
    $html .= 'table { width: 100%; border-collapse: collapse; margin-top: 10px; }';
    $html .= 'th, td { border: 1px solid #999; padding: 6px; text-align: left; }';
    $html .= 'th { background-color: #e8e8e8; font-weight: bold; }';
    $html .= 'td.number { text-align: right; }';
    $html .= 'h2 { margin: 5px 0; }';
    $html .= 'p { margin: 2px 0; font-size: 9pt; }';
    $html .= '.signature-table { width: 100%; margin-bottom: 15px; }';
    $html .= '.signature-cell {width: 20%; border: 1px solid gray; padding: 8px 8px 8px 8px; text-align: center; font-size: 6pt; height: 130px; vertical-align: top; }';
    $html .= '.signature-title { font-size: 8pt; color: gray }';
    $html .= '</style></head><body>';

    $time =  nowyh_vn(time(), "/");
    $html .= "<h2>Danh sách thanh toán Sự kiện số " . htmlspecialchars($evid) . " </h2>";
    $html .= "<div style='display: block'>DAV - Học Viện Ngoại Giao, <span style='float: right'> Ngày tạo: $time </span> </div>";

    // Signature section - 5 columns
    $html .= '<table class="signature-table" cellpadding="0" cellspacing="0">';
    $html .= '<tr>';
    $html .= '<td class="signature-cell"><div class="signature-title">Chữ ký Chuyên viên</div></td>';
    $html .= '<td class="signature-cell"><div class="signature-title">Chữ ký Phụ trách đơn vị</div></td>';
    $html .= '<td class="signature-cell"><div class="signature-title">Chữ ký Kế toán viên</div></td>';
    $html .= '<td class="signature-cell"><div class="signature-title">Chữ ký Kế toán trưởng</div></td>';
    $html .= '<td class="signature-cell"><div class="signature-title">Chữ ký Lãnh đạo Học viện</div></td>';
    $html .= '</tr>';
    $html .= '</table>';

    $html .= '<table>';

    // Read all rows from Excel sheet
    $rowNum = 0;
    $isFirstRow = true;
    $maxCol = 'F'; // Giới hạn cột dữ liệu (A-F)

    foreach ($sheet->getRowIterator() as $row) {
        $rowNum++;
        $html .= '<tr>';
        $colNum = 0;
        $cellValues = [];

        // Chỉ lặp qua các cột A-F (6 cột dữ liệu)
        foreach ($row->getCellIterator('A', $maxCol) as $cell) {
            $colNum++;
            $value = $cell->getValue();
            $cellCoord = $cell->getCoordinate(); // e.g., "A1", "B1", "C1"
            $cellCol = preg_replace('/[0-9]/', '', $cellCoord); // Get column letter

            // Format số, nhưng KHÔNG format cột C (Số tài khoản / bank_acc_number)
            // để giữ nguyên như Excel (0012345678 chứ không phải 12.345.678)
            if (is_numeric($value) && !is_null($value) && $cellCol !== 'C') {
                $value = number_format($value, 0, ',', '.');
                $cellValues[] = ['value' => $value, 'isNumber' => true];
            } else {
                $cellValues[] = ['value' => (string)$value ?? '', 'isNumber' => false];
            }
        }

        // Header row (row 1)
        if ($isFirstRow) {
            foreach ($cellValues as $cellData) {
                $html .= '<td>' . htmlspecialchars($cellData['value']) . '</td>';
            }
            $isFirstRow = false;
        } else if ($rowNum == 2) {
            // Row 2 có thể là sub-header, skip
            continue;
        } else {
            $cc = 0;
            // Data rows
            foreach ($cellValues as $cellData) {
                $cc++;
                $padStyle = "";
                if($cc == 1){
                    $padStyle = "text-align: center;";
                }
                if($cc == 3){
                    $padStyle = "text-align: right;";
                }
                $class = $cellData['isNumber'] ? ' class="number"' : '';
                $html .= '<td style="' . $padStyle . '" ' . $class . '>' . htmlspecialchars($cellData['value']) . '</td>';
            }
        }

        $html .= '</tr>';
    }

    $html .= '</table>';
    $html .= '</body></html>';

    return $html;
}
