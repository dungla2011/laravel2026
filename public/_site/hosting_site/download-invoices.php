<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Helpers\BkavEHoaDonAPI;

//
require "/var/www/html/vendor/autoload.php";
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

if(!isCli()){
    die("Not cli!");
}

/**
 * Script PHP để lấy tất cả hoá đơn trong khoảng thời gian
 * Sử dụng CmdType 853 với pagination
 */

/**
 * Lấy tất cả hoá đơn trong khoảng thời gian với pagination
 */
function getAllInvoicesByDateRange($from_date, $to_date, $output_file = "invoices.json")
{
    echo str_repeat("=", 80) . "\n";
    echo "LẤY TẤT CẢ HOÁ ĐƠN TRONG KHOẢNG THỜI GIAN\n";
    echo str_repeat("=", 80) . "\n";
    echo "\nKhoảng thời gian: $from_date đến $to_date\n";

    try {
        $api = new BkavEHoaDonAPI();
        $all_invoices = [];
        $page_number = 1;
        $total_records = 0;

        while (true) {
            echo "\n→ Đang lấy trang $page_number...\n";

            $result = $api->getInvoicesByDate($from_date, $to_date, $page_number);

            if ($result['isOk'] && $result['Status'] == 0) {
                $invoices = $result['Object'];

                // Nếu Object là string, decode nó
                if (is_string($invoices)) {
                    $invoices = json_decode($invoices, true);
                }

                if (!is_array($invoices) || count($invoices) === 0) {
                    echo "✓ Không có dữ liệu trên trang $page_number, kết thúc.\n";
                    break;
                }

                echo "✓ Lấy được " . count($invoices) . " hoá đơn từ trang $page_number\n";
                $all_invoices = array_merge($all_invoices, $invoices);
                $total_records += count($invoices);

                // Kiểm tra xem có trang tiếp theo không
                if (count($invoices) < 30) {
                    echo "✓ Dường như đã đến trang cuối\n";
                    break;
                }

                $page_number++;

                // Giới hạn an toàn để tránh lặp quá nhiều
                if ($page_number > 100) {
                    echo "⚠ Đã lấy 100 trang, dừng lại để an toàn\n";
                    break;
                }
            } else {
                $error = $result['Object'] ?? 'Unknown error';
                echo "✗ Lỗi: $error\n";
                break;
            }
        }

        echo "\n" . str_repeat("=", 80) . "\n";
        echo "TỔNG CỘNG: $total_records hoá đơn được lấy\n";
        echo str_repeat("=", 80) . "\n";

        // Lưu kết quả ra file JSON
        $json_content = json_encode($all_invoices, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($output_file, $json_content);

        echo "\n✓ Đã lưu vào file: $output_file\n";

        // In thống kê
        echo "\nThống kê:\n";

        $invoices_by_status = [];
        $invoices_by_date = [];

        foreach ($all_invoices as $inv_data) {
            $invoice = $inv_data['Invoice'] ?? [];

            // Thống kê theo trạng thái
            $status_id = $invoice['InvoiceStatusID'] ?? 0;
            $invoices_by_status[$status_id] = ($invoices_by_status[$status_id] ?? 0) + 1;

            // Thống kê theo ngày
            $inv_date = substr($invoice['InvoiceDate'] ?? '', 0, 10);
            $invoices_by_date[$inv_date] = ($invoices_by_date[$inv_date] ?? 0) + 1;
        }

        $status_names = [
            1 => "Mới tạo",
            2 => "Đã phát hành",
            3 => "Đã hủy",
            4 => "Đã xóa",
            5 => "Chờ thay thế",
            6 => "Thay thế",
            7 => "Chờ điều chỉnh",
            8 => "Điều chỉnh"
        ];

        echo "\n→ Hoá đơn theo trạng thái:\n";
        ksort($invoices_by_status);
        foreach ($invoices_by_status as $status_id => $count) {
            $status_name = $status_names[$status_id] ?? "Trạng thái $status_id";
            echo "  $status_name: $count\n";
        }

        echo "\n→ Hoá đơn theo ngày (Top 10):\n";
        arsort($invoices_by_date);
        $top_10 = array_slice($invoices_by_date, 0, 10);
        foreach ($top_10 as $date => $count) {
            echo "  $date: $count\n";
        }

        return $all_invoices;

    } catch (Exception $e) {
        echo "\n✗ Lỗi khi lấy dữ liệu: " . $e->getMessage() . "\n";
        echo $e->getTraceAsString() . "\n";
        return [];
    }
}

/**
 * Lấy hoá đơn 2 năm gần đây
 */
function getInvoicesLast2Years()
{
    $to_date = date('Y-m-d');
    $from_date = date('Y-m-d', strtotime('-2 years'));

    return getAllInvoicesByDateRange($from_date, $to_date, "invoices_2years.json");
}

/**
 * Lấy hoá đơn của 1 năm cụ thể
 */
function getInvoicesByYear($year)
{
    $from_date = "$year-01-01";
    $to_date = "$year-12-31";

    return getAllInvoicesByDateRange($from_date, $to_date, "invoices_$year.json");
}

/**
 * Lấy hoá đơn trong khoảng thời gian tùy chỉnh
 */
function getInvoicesByRange($from_date, $to_date)
{
    return getAllInvoicesByDateRange($from_date, $to_date, "invoices_custom.json");
}

// Main function
function mainGetAllInvoices($argv)
{
    echo "LẤY DỮ LIỆU HOÁ ĐƠN\n\n";

    if (count($argv) > 1) {
        if ($argv[1] === "2years") {
            // Lấy 2 năm gần đây
            getInvoicesLast2Years();
        } elseif ($argv[1] === "year") {
            // Lấy năm cụ thể
            if (count($argv) > 2) {
                $year = intval($argv[2]);
                getInvoicesByYear($year);
            } else {
                echo "Cách dùng: php GetAllInvoices.php year <YYYY>\n";
            }
        } elseif ($argv[1] === "range") {
            // Lấy khoảng thời gian tùy chọn
            if (count($argv) > 3) {
                $from_date = $argv[2];
                $to_date = $argv[3];
                getInvoicesByRange($from_date, $to_date);
            } else {
                echo "Cách dùng: php GetAllInvoices.php range <YYYY-MM-DD> <YYYY-MM-DD>\n";
            }
        } else {
            echo "Cách dùng:\n";
            echo "  php GetAllInvoices.php 2years           # Lấy 2 năm gần đây\n";
            echo "  php GetAllInvoices.php year 2025       # Lấy năm 2025\n";
            echo "  php GetAllInvoices.php range 2025-01-01 2025-12-31  # Lấy khoảng thời gian\n";
        }
    } else {
        echo "Cách dùng:\n";
        echo "  php GetAllInvoices.php 2years           # Lấy 2 năm gần đây\n";
        echo "  php GetAllInvoices.php year 2025       # Lấy năm 2025\n";
        echo "  php GetAllInvoices.php range 2025-01-01 2025-12-31  # Lấy khoảng thời gian\n";
    }
}

// Chỉ chạy main nếu file được gọi trực tiếp
if (php_sapi_name() === 'cli' && basename($argv[0] ?? '') === basename(__FILE__)) {
    // mainGetAllInvoices($argv);


}

$api = new BkavEHoaDonAPI();

// // Lấy hoá đơn theo MST trong khoảng thời gian
// $invoices = $api->getInvoicesInPeriod('0107003437', '2025-12-01', '2026-02-01');

// // Hoặc rút gọn: 6 tháng gần nhất
// $invoices = $api->getInvoicesInPeriod('0107003437',
//     date('Y-m-d', strtotime('-6 months')),
//     date('Y-m-d')
// );

// // Sau đó tải PDF từ danh sách
// $api->downloadInvoicePdfs($invoices, 'invoices_pdf', '{serial}_{number}.pdf');


// Tải 5 XML gần nhất
//$api->downloadInvoiceXmls(5, 'invoices_xml');

// Hoặc từ danh sách đã lọc
//$invoices = $api->getInvoicesInPeriod('0107003437', '2025-12-01', '2025-12-31');

$api->downloadInvoiceXmls(20, '/var/glx/upload_file_glx/user_files/siteid_63/invoice_files');
$api->downloadInvoicePdfs(20, '/var/glx/upload_file_glx/user_files/siteid_63/invoice_files');
// Custom tên file
//$api->downloadInvoiceXmls(10, '/var/glx/upload_file_glx/user_files/siteid_63/invoice_files', '{date}_{serial}_{number}.xml');
