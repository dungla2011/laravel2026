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

if($pid = request('pid'))
    $products = \App\Models\Product::where("parent_id", $pid)->get();
else
    $products = \App\Models\Product::all();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HZM - Danh sách sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-card {
            height: 100%;
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .product-img {
            height: 200px;
            object-fit: contain;
        }
        .price {
            font-weight: bold;
            color: #e91e63;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <h1 class="text-center mb-5">Danh sách sản phẩm</h1>

<div class="mb-4 text-center">
    <?php
    // List danh mục sản phẩm trong ProductFolder
    $mm = \App\Models\ProductFolder::all();
    $orgUrl = \LadLib\Common\UrlHelper1::getUriWithoutParam();
    $currentPid = request('pid', null);

    // Add an "All Products" button
    $isAllSelected = empty($currentPid);
    $countAll = \App\Models\Product::count();
    echo '<a href="' . $orgUrl . '" class="btn ' . ($isAllSelected ? 'btn-danger' : 'btn-primary') . ' m-1">Tất cả ('.$countAll.')</a>';

    foreach ($mm AS $dt) {
        $name = $dt->getName();
        $id = $dt->getId();
        $ccc = \App\Models\Product::where("parent_id", $id)->count();

        // Check if this folder is currently selected
        $isSelected = ($currentPid == $id);

        // Apply styling based on selection status
        if ($isSelected) {
            // Use danger (red) button for selected folder
            echo '<a href="' . $orgUrl . '?pid=' . $id . '" class="btn btn-danger m-1">';
            echo $name . ' (' . $ccc . ')';
            echo '</a>';
        } else {
            // Use primary (blue) button for non-selected folders
            echo '<a href="' . $orgUrl . '?pid=' . $id . '" class="btn btn-primary m-1">';
            echo $name . ' (' . $ccc . ')';
            echo '</a>';
        }
    }
    ?>
</div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">

<?php
// Loop through products and generate cards
foreach ($products as $product) {
//    $pad = " - Duy Tân";
//    if(str_contains($product->refer, 'songlong')){
//        $pad = " - Song Long";
//    }

    $pad = '';

    $img = $product->getThumbInImageList();
    if(!$img)
        continue;

    $price = $product->price;
    if(!$price)
        $price = 0;

//    $price = number_format(50000, 0, ',', '.'); // Format price with thousands separator

    if($img)
        $img = 'https://test2023.mytree.vn/'.$img;

    $linkTk = substr($product->refer, 0, 30).'...';
    echo '<div class="col">

            <div class="card product-card">

                <img src="' . htmlspecialchars($img) . '" class="card-img-top product-img" alt="' . htmlspecialchars($product->name) . '">
                <div class="card-body">
                    <h5 class="card-title">' . htmlspecialchars($product->name . $pad) . '</h5>
                    <p class="price">' . $price . ' K  <a href="/admin/product/edit/'.$product->id.'">...</a> </p>

                    <p class=""> <a target="_blank" href="'.$product->refer.'"> Link tham khảo <br> '.$linkTk.' </a> </p>
                </div>
            </div>
          </div>';
}

// Close the HTML structure
echo '</div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>';

