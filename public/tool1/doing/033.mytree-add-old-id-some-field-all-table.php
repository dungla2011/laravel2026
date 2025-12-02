<?php

use Illuminate\Support\Facades\DB;


error_reporting(E_ALL);
ini_set('display_errors', 1);
define('DEF_TOOL_CMS', 1);
$_SERVER['SERVER_NAME'] = 'v5.mytree.vn';
require_once __DIR__.'/../../index.php';



// ========== MYSQL FIELD TYPE COPIER ==========
$fieldList = ['id', 'user_id', 'parent_id', 'parent_list', 'parent_all', 'cloud_id', 'image_list',
    'tree_id', 'tree_nodes_xy', 'child_of_second_married', 'married_with', 'list_child_x_y',
    'order_id'
    ];
$ignore_tables = ['rand_table'];

function getFieldDefinition($table, $field) {
    $sql = "
        SELECT
            COLUMN_TYPE,
            IS_NULLABLE,
            COLUMN_DEFAULT,
            EXTRA,
            COLUMN_COMMENT
        FROM information_schema.columns
        WHERE table_schema = DATABASE()
        AND table_name = ?
        AND column_name = ?
    ";

    $result = DB::select($sql, [$table, $field]);

    if (empty($result)) {
        throw new Exception("Field {$field} not found in table {$table}");
    }

    return $result[0];
}

function buildColumnDefinition($fieldInfo) {
    $definition = $fieldInfo->COLUMN_TYPE;

    // Add NULL/NOT NULL
    if ($fieldInfo->IS_NULLABLE === 'YES') {
        $definition .= ' NULL';
    } else {
        $definition .= ' NULL';
    }

    // Add DEFAULT
    if ($fieldInfo->COLUMN_DEFAULT !== null) {
        if (strtolower($fieldInfo->COLUMN_DEFAULT) === 'current_timestamp') {
            $definition .= ' DEFAULT CURRENT_TIMESTAMP';
        } elseif (is_numeric($fieldInfo->COLUMN_DEFAULT)) {
            $definition .= " DEFAULT {$fieldInfo->COLUMN_DEFAULT}";
        } else {
            $definition .= " DEFAULT {$fieldInfo->COLUMN_DEFAULT}";
        }
    } elseif ($fieldInfo->IS_NULLABLE === 'YES') {
        $definition .= ' DEFAULT NULL';
    }

    // Add EXTRA (AUTO_INCREMENT, etc.)
    if ($fieldInfo->EXTRA) {
        $definition .= " {$fieldInfo->EXTRA}";
    }

    // Add COMMENT
    if ($fieldInfo->COLUMN_COMMENT) {
        $definition .= " COMMENT '{$fieldInfo->COLUMN_COMMENT}'";
    }

    return $definition;
}

function addOldFieldColumn($table, $field) {
    echo "🔄 Thêm cột old_{$field} cho bảng {$table}...\n";

    global $ignore_tables;
    if(in_array($table, $ignore_tables)) {
        echo "⚠️ Bỏ qua bảng {$table}\n";
        return;
    }

    try {
        // Lấy định nghĩa field gốc
        $fieldInfo = getFieldDefinition($table, $field);
        echo "📋 Field gốc: {$fieldInfo->COLUMN_TYPE}\n";

        // Build định nghĩa column mới
        $columnDef = buildColumnDefinition($fieldInfo);

        $columnDef = str_replace('auto_increment', '', $columnDef);

        //Nếu cột có rồi thì bỏ qua:
        $existingColumn = DB::select("SHOW COLUMNS FROM `{$table}` LIKE 'old_{$field}'");
        if (!empty($existingColumn)) {
            echo "⚠️ Cột old_{$field} đã tồn tại trong bảng {$table}, bỏ qua...\n";
        }
        else{
            // Tạo câu lệnh ALTER
            $sql = "ALTER TABLE `{$table}` ADD COLUMN `old_{$field}` {$columnDef} AFTER `{$field}`";
            echo "🔧 SQL: {$sql}\n";
            // Thực thi
            DB::statement($sql);

            echo "\n Update all field now: ";
            DB::statement("UPDATE `$table` SET `old_$field` = `$field`");
            //Index old_field
            DB::statement("CREATE INDEX `idx_old_$field` ON `$table` (`old_$field`)");

            echo "✅ Đã thêm cột old_{$field} thành công\n\n";

        }



    } catch (Exception $e) {
        echo "❌ Lỗi: " . $e->getMessage() . "\n\n";
    }
}


//Tìm tất cả các tên bảng trong db
$tables = DB::select("SHOW TABLES");
$tables = array_map('current', $tables);

//$tables = ['users'];
$cc = 0;
foreach ($tables as $table) {

    $cc++;
    echo "\n $cc --- Đang xử lý bảng $table \n";

    foreach ($fieldList as $field) {
         addOldFieldColumn($table, $field);
    }
}



// Test function
// addOldFieldColumn('users', 'id');
// addOldFieldColumn('products', 'name');
// addOldFieldColumn('gia_phas', 'image_list');
//addOldFieldColumn('news', 'image_list');
?>
