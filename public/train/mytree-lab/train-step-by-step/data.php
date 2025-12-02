<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'mytree.vn';

//$_SERVER['SERVER_NAME'] = '';
require_once "/var/www/html/public/index.php";



$id = 'pq827823';

if(request('id'))
    $id = request('id');
$id0 = $id = qqgetIdFromRand_($id);
//$id0 = 2597;

//Lay ra parent_id cua id nay
$pid0 = \App\Models\GiaPha::find($id)->parent_id;


//echo "<br/>\n ID = $id0";

$gp = new \App\Models\GiaPha();

$mm = $gp->getAllTreeDeepCTE($id0, "id, parent_id, name, married_with, gender");

//Chuyen het name tu tieng Viet sang tieng viet khong dau
foreach ($mm as $k => $v) {
    $mm[$k]['name'] = \LadLib\Common\cstring2::convert_codau_khong_dau($v['name']);
    //Tìm các elm có married_with = $id0, thì gán parent_id = 0
    if ($v['married_with'] == $id0) {
        $mm[$k]['parent_id'] = $pid0;
    }

}


$mm[] = ['id' => $pid0, 'parent_id' => null, 'married_with' => null, 'gender' => 1, 'name' => 'Root'];
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mm);
//echo "</pre>";
echo json_encode($mm, JSON_PRETTY_PRINT);
return;


//return;
//
//function convertFamilyTreeIds($data)
//{
//    // Tạo map để lưu trữ ID mới theo ID cũ
//    $idMap = [];
//
//    // Tìm node gốc (parent_id = 0)
//    $root = null;
//    foreach ($data as $person) {
//        if ($person['parent_id'] == 0) {
//            $root = $person;
//            break;
//        }
//    }
//
//    if (!$root) {
//        return [];
//    }
//
//    // Khởi tạo ID map với node gốc
//    $idMap[$root['id']] = '0';
//
//    // Tạo map con cho mỗi parent_id
//    $childrenMap = [];
//    foreach ($data as $person) {
//        if ($person['parent_id'] != 0) {
//            if (!isset($childrenMap[$person['parent_id']])) {
//                $childrenMap[$person['parent_id']] = [];
//            }
//            $childrenMap[$person['parent_id']][] = $person;
//        }
//    }
//
//    // Hàm đệ quy để gán ID mới cho từng node
//    function assignNewIds($parentId, $oldParentId, &$childrenMap, &$idMap)
//    {
//        if (!isset($childrenMap[$oldParentId])) {
//            return;
//        }
//
//        $childIndex = 1;
//        foreach ($childrenMap[$oldParentId] as $child) {
//            // Tạo ID mới dựa trên parent ID
//            $newId = $parentId . '.' . $childIndex;
//            $idMap[$child['id']] = $newId;
//
//            // Đệ quy cho con của node hiện tại
//            assignNewIds($newId, $child['id'], $childrenMap, $idMap);
//
//            $childIndex++;
//        }
//    }
//
//    // Bắt đầu gán ID từ con của root
//    assignNewIds('0', $root['id'], $childrenMap, $idMap);
//
//    // Cập nhật dữ liệu với ID mới
//    $result = [];
//    foreach ($data as $person) {
//        $newPerson = $person;
//        if ($person['parent_id'] == 0) {
//            // Xử lý node gốc
//            $newPerson['old_id'] = $person['id'];
//            $newPerson['id'] = '0';
//            $newPerson['parent_id'] = '0';
//        } else {
//            // Xử lý các node khác
//            $newPerson['old_id'] = $person['id'];
//            $newPerson['id'] = $idMap[$person['id']] ?? $person['id'];
//            $newPerson['parent_id'] = $idMap[$person['parent_id']] ?? $person['parent_id'];
//        }
//
//        // Cập nhật married_with nếu có
//        if ($person['married_with'] !== null) {
//            $newPerson['married_with'] = $idMap[$person['married_with']] ?? $person['married_with'];
//        }
//
//        $result[] = $newPerson;
//    }
//
//    // Sắp xếp kết quả theo ID mới
//    usort($result, function ($a, $b) {
//        return strcmp($a['id'], $b['id']);
//    });
//
//    return $result;
//}

// Ví dụ sử dụng:
$newData = convertFamilyTreeIds($mm);
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($newData);
//echo "</pre>";

//ob_clean();
echo json_encode($newData, JSON_PRETTY_PRINT);


?>
