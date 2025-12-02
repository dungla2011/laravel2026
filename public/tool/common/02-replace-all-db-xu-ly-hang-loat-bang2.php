<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kết nối đến MySQL server
$host = 'localhost';
$username = 'admin';
$password = 'Cloud!@)((';


// Kết nối MySQL
$mysqli = new mysqli($host, $username, $password);

if ($mysqli->connect_error) {
    die("Kết nối thất bại: " . $mysqli->connect_error);
}

// Lấy danh sách các database
$sql = "SHOW DATABASES";
$result = $mysqli->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        try {
            $database = $row['Database'];

            if(str_starts_with($database, 'glx_') == false){
                continue;
            }

            // Bỏ qua các database mặc định
            if (in_array($database, ['mysql', 'information_schema', 'performance_schema', 'sys'])) {
                continue;
            }

            echo "<br><br>Đang xử lý database: $database\n";

            // Chọn database
            $mysqli->select_db($database);

            // Kiểm tra sự tồn tại của bảng bill_and_products
            $check_table = "SHOW TABLES LIKE 'menu_trees'";
            $result_check_table = $mysqli->query($check_table);
            if ($result_check_table->num_rows == 0) {
                echo "<br>Không tìm thấy bảng menu_trees trong database: $database\n";
                continue;
            }



            $sql  ="SELECT * FROM menu_trees WHERE link = '/member/order-item'";

            $ret1 = $mysqli->query($sql);

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($ret1);
//            echo "</pre>";
            if ($ret1->num_rows > 0) {
                while ($row = $ret1->fetch_assoc()) {
                    // Xử lý từng hàng dữ liệu ở đây
                    echo "<br/>\n";
                    print_r($row);
                }
            } else {
                echo "\n<br>Không tìm thấy dữ liệu.";
            }


            // Chạy lệnh RENAME TABLE
//            $rename_sql = "RENAME TABLE `bill_and_products` TO `order_items`;";
//            if ($mysqli->query($rename_sql) === TRUE) {
//                echo "<br>Đổi tên bảng thành công trong database: $database\n";
//            } else {
//                echo "<br>Lỗi đổi tên bảng trong database $database: " . $mysqli->error . "\n";
//            }
        } catch (Exception $e) {
            echo "<br>Lỗi: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "<br>Không tìm thấy database nào.\n";
}

// Đóng kết nối
$mysqli->close();
