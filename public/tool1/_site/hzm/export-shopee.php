<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);
//$_SERVER['SERVER_NAME'] = '';

function setDomainHostNameGlx1($hname)
{
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $hname;
}
setDomainHostNameGlx1('quantri.hzmplastic.com.vn');
require_once "/var/www/html/public/index.php";

$products = \App\Models\Product::all();

// Start generating the HTML table
echo '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Danh sách sản phẩm</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>
<body>
    <h1>Danh sách sản phẩm</h1>
    <table>
        <thead>
            <tr>
                <th>Ngành hàng</th>
                <th>Tên sản phẩm</th>
                <th>Mô tả sản phẩm</th><th>SKU sản phẩm</th><th>Mã sản phẩm</th><th>Tên nhóm phân loại hàng 1</th><th>Tên phân loại hàng cho nhóm phân loại hàng 1</th><th>Hình ảnh mỗi phân loại</th><th>Tên nhóm phân loại hàng 2</th><th>Tên phân loại hàng cho nhóm phân loại hàng 2</th><th>Giá</th><th>Kho hàng</th><th>SKU phân loại</th><th>Size Chart Template</th><th>Size Chart Image</th><th>Ảnh bìa</th><th>Hình ảnh sản phẩm 1</th><th>Hình ảnh sản phẩm 2</th><th>Hình ảnh sản phẩm 3</th><th>Hình ảnh sản phẩm 4</th><th>Hình ảnh sản phẩm 5</th><th>Hình ảnh sản phẩm 6</th><th>Hình ảnh sản phẩm 7</th><th>Hình ảnh sản phẩm 8</th><th>Cân nặng</th><th>Chiều dài</th><th>Chiều rộng</th><th>Chiều cao</th><th>Hỏa Tốc</th><th>Nhanh</th><th>Tiết kiệm</th><th>Hàng Cồng Kềnh</th><th>Ngày chuẩn bị hàng cho đặt trước (Pre-order DTS)</th>
            </tr>
        </thead>
        <tbody>';

// Loop through products and generate table rows
$index = 1;
foreach ($products as $product) {

    $pad = " - Duy Tân";
    $type1 = 1;
    if(str_contains($product->refer, 'songlong')){
        $type1 = 2;
        $pad = " - Song Long";
    }


    $img = $product->getThumbInImageList();
    if(!$img)
        continue;
    $price = $product->price;
    $price = 50000;
    if($img)
        $img = 'https://test2023.mytree.vn/'.$img;
//    $type1 = trim($pad);
//    $type1 = trim($type1);
//    $type1 = trim($type1, "-");
//    $type1 = trim($type1);
//    $type1 = (str_replace("-", "", $type1));
//    $type1 = strtolower(str_replace(" ", "-", $type1));

    $size = "Kích thước";
    $LMS = "L";

    $weight = 2000;
    $dai = 10;
    $rong = 20;
    $cao = 30;
    $moNhanh = 'Mở';
    echo "<tr>
            <td>1</td>
            <td>$product->name $pad</td>
            <td>Ghế nhựa $pad, sản phẩm hàng đầu chất lượng, sản xuất trên dây truyền tiên tiến, kiểm định đầy đủ về độ  bền, độ mài mòn, chịu nhiệt...</td>
            <td></td>
            <td>$product->id</td>
            <td>màu</td>
            <td>xanh</td>

            <td>$img</td>
            <td>$size</td>
            <td>$LMS</td>
            <td>$price</td>
            <td>100</td>
            <td></td>
            <td></td>
            <td></td>
            <td>$img</td>
            <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            <td>$weight</td>
            <td>$dai</td>
            <td>$rong</td>
            <td>$cao</td>



          </tr>";
    $index++;
}

// Close the HTML structure
echo '</tbody>
    </table>
</body>
</html>';

