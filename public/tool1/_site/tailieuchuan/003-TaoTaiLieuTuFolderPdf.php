<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'tailieuchuan.net';

require "/var/www/html/public/index.php";

$fold = "/share/Sach-giao-khoa-Online/";

$m1 = scandir($fold);

$pidTheLoai = 46; //Sach giao khoa Bộ GD&ĐT


//Tìm các MydocmentCat có tên là Lớp...
$mydocCat = \App\Models\MyDocumentCat::where('name', 'like', 'Lớp%')->get();
//Lấy ra mảng ID lớp 1-12
$mIdLop = [];
foreach ($mydocCat AS $lop) {
    $lop1 = str_replace('Lớp ', '', $lop->name);
    $lop1 = str_replace(' ', '', $lop1);
    $lop1 = trim($lop1);
    if(is_numeric($lop1) && $lop1 > 0 && $lop1 < 13)
        $mIdLop[$lop1] = $lop->id;
}
echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
print_r($mIdLop);
echo "</pre>";

foreach ($m1 AS $lop) {

    if($lop == '.' || $lop == '..') continue;

    $lop = trim($lop);

    $fold1 = $fold . $lop . '/';

    $m2 = scandir($fold1);

    echo "<br/>\n --- LOP $lop";


    foreach ($m2 AS $lop1) {

        if ($lop1 == '.' || $lop1 == '..') continue;

        $file = $fold1 . $lop1;

        if(file_exists($file))
            echo "<br/>\n $file  ";
        else{
            continue;
        }

        $bname = basename($file);
        echo "<br/>\n";
        echo "\n Bname = $bname";


        if(isCli()){
            $idf = \App\Models\FileUpload::uploadFileLocalCopy($file, '', 165);
            echo "\n<br> ID File Upload: $idf";
            //Tim xem c tài liệu nào có idf này không
            if(
                $mydoc = \App\Models\MyDocument::where('file_list', $idf)->first()){
                echo "\n<br> Đã có $idf / $mydoc->id";
                continue;
            }

            //Tạo 1 tài liệu với tên naày
            $mydoc = new \App\Models\MyDocument();
            $mydoc->parent_id = $mIdLop[$lop];
            $mydoc->parent_extra = $pidTheLoai;
            $mydoc->name = $bname;
            $mydoc->file_list = $idf;
            $mydoc->save();

            $idF = \App\Models\MyDocument::genImageThumbFromPdfInFileList($mydoc->id);

            echo(" \n DONE ... $idF");
        }
    }
}
