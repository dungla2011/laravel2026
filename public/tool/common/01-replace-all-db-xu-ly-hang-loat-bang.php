<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Kết nối đến MySQL server
$host = 'localhost';
$username = 'admin';
$password = '...';

if(!isCli()){
    die("NOT CLI");
}

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

            // Bỏ qua các database mặc định
            if (in_array($database, ['mysql', 'information_schema', 'performance_schema', 'sys'])) {
                continue;
            }

            echo "<br><br>Đang xử lý database: $database\n";

            // Chọn database
            $mysqli->select_db($database);

            // Kiểm tra sự tồn tại của bảng bill_and_products
            $check_table = "SHOW TABLES LIKE 'bill_and_products'";
            $result_check_table = $mysqli->query($check_table);
            if ($result_check_table->num_rows == 0) {
                echo "<br>Không tìm thấy bảng bill_and_products trong database: $database\n";
                continue;
            }

            // Chạy lệnh UPDATE
            $update_sql = "UPDATE `model_meta_infos` SET table_name_model = 'order_items' WHERE `table_name_model` = 'bill_and_products';";
            if ($mysqli->query($update_sql) === TRUE) {
                echo "<br>Cập nhật thành công trong database: $database\n";
            } else {
                echo "<br>Lỗi cập nhật trong database $database: " . $mysqli->error . "\n";
            }

            // Chạy lệnh RENAME TABLE
            $rename_sql = "RENAME TABLE `bill_and_products` TO `order_items`;";
            if ($mysqli->query($rename_sql) === TRUE) {
                echo "<br>Đổi tên bảng thành công trong database: $database\n";
            } else {
                echo "<br>Lỗi đổi tên bảng trong database $database: " . $mysqli->error . "\n";
            }
        } catch (Exception $e) {
            echo "<br>Lỗi: " . $e->getMessage() . "\n";
        }
    }
} else {
    echo "<br>Không tìm thấy database nào.\n";
}

// Đóng kết nối
$mysqli->close();
