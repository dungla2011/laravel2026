<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '/var/www/html/vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
$file = '/var/www/html/public/tool1/_site/galaxycloud/KinhDoanh2024Galaxy2.xlsx';

$strDungLA = "
C7-27.7.23-TruongNoiTru
2012-Giang-D540-121.160
2012-MrHung-121.194
C7-27.7.23
C7-PhongThuy6-D1500-121.176
C7-aHung-15.12.23
C7-aHung-cyber-070223-3
C7-aHung-free-mng
C7-dungln-D150-121.117-rs-0306
Cen7-aHung-01.04.24
Cen7.aHung-230724
U18-ItPlus01-D620-121.191_new
U18-ItPlus02-D490-121.192
U20-duc-fsi-binaco-05.01.23
U20-mrkhanh-170922
U21-anvui-15.8.23-1
U21-anvui-15.8.23-2
U21-anvui-15.8.23-3
U21-anvui-21.02.24
U21-anvui-21.02.24-2
U21-anvui-21.02.24-3
U22-aHung-07.03.24
U22-aHung-15.12.23
U22-aHung-21.11.24-01
U22-aHung-21.11.24-02
U22-aHung-22.1.24_rs_23.3.24
U22-aHung-230724-1
U22-aHung-230724-2
U22-aHung-Re-install-281024-230724
U22-anvui-17.2.24
U22-anvui-7.11.24
U22-anvui-7.11.24-2
U22-anvui-phim-19.2.24
U22-mrKhanh-update-010324
Ubuntu18-Mr.Giang-19042021-216.162
Ubuntu18-MrKhanh-22022021-121.154
W2016-MrDung-28022022-216.46
W2019-aHung-17.1.24
Win-Giang-D1650-121.151
Win2012-MrManhAds-14062022-216.53
u22-anpham-19.4.24-thaythe-sv-cu
u22-mrThanh-15.11.23
u22-qui-2024
w10-anvui-160824
w2019-mrDuc-fsi-20230719-binaco-rs";

$strDungLA2 = "anvui,fsi,mrthang,qui,baochi,mrhung,cyber,khanh,anphammedia,phongthuy,dna";
$mmDungLA2 = explode(",", $strDungLA2);

$mmDungLA = explode("\n", $strDungLA);
//
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mmDungLA);
//echo "</pre>";

echo "<br/>\n Tỷ lệ % Kinh doanh  2024";

// Load the spreadsheet file
$spreadsheet = IOFactory::load($file);

// Get the first sheet
$sheet = $spreadsheet->getActiveSheet();

// Initialize an array to hold the data
$data = [];

// Iterate through rows
foreach ($sheet->getRowIterator() as $row) {
    $cellIterator = $row->getCellIterator();
    $cellIterator->setIterateOnlyExistingCells(false); // Loop through all cells, even if they are empty

    $rowData = [];
    foreach ($cellIterator as $cell) {
        $rowData[] = $cell->getValue();
    }

    // Add the row data to the main data array
    $data[] = $rowData;
}

// Print the data
//print_r($data);
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($data);
//echo "</pre>";
$oneRow = $data[2];
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($oneRow);
//echo "</pre>";
$mMonthName = [];
$nCol = 28;
$cc = 0;
for($j = 0; $j< $nCol;$j+=2) {

    echo "-" . $oneRow[$j] ;
    $mMonthName[$cc] = $oneRow[$j];
    $cc++;
}

$mm = [];

for ($i = 4; $i < count($data); $i++) {

    $oneRow = $data[$i];

    for($j = 0; $j< $nCol;$j+=2) {


        $k = round($j/2);

//        echo "<br>$i, $j ($k). " . $oneRow[$j] . " -> " . $oneRow[$j + 1] . "\n";

       if(!($mm[$k] ?? 0)){
           $mm[$k] = [];
       }
        $mm[$k][] = [$oneRow[$j] , $oneRow[$j + 1]];

    }

}

$tongTienThucAll = 0;
$tongTienDungAll = 0;
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mmDungLA2);
//echo "</pre>";
$cc = 0;
foreach ($mm AS $thang => $m1){

    $thangX = 12 - $thang;
    $thangName = $mMonthName[$cc];

    $cc++;
    echo "<hr/>\n Tháng $thangName <br>";
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($m1);
//    echo "</pre>";

    $tongTienDung = 0;
    $tongTienThuc = 0;

    echo "<br/>\n <table border='1'>";
    echo "\n<tr>";
    echo "<td> </td>";
    echo "<td> </td>";
    echo "<td> DungLA  </td>";
    echo "<td>  ThucTT </td>";
    echo "\n</tr>";
    foreach ($m1 AS $m11){
        $name = $m11[0];
        $money = $m11[1];
        echo "\n<tr>";
        if($name)
            $name = trim($name);
        else
            continue;
        echo " <td> $name </td> <td> $money </td>";

        $name0 = strtolower($name);
        $isDungLA = false;
        if($name){
            foreach ($mmDungLA2 AS $tmp1){
                if(str_contains($name0, $tmp1)){
                    $isDungLA = 1;
                    break;
                }
            }
        }

        $tienDung = $tienThuc = 0;
//        if(in_array($name, $mmDungLA))
        if($name != "TOTAL")
        if($isDungLA || str_contains($strDungLA, $name))
        {
            $tienDung = $money;
            $tongTienDung+=$money;
        }
        else{
            $tienThuc = $money;
            $tongTienThuc+=$money;
        }

        echo "<td> $tienDung  </td>";
        echo "<td>  $tienThuc </td>";
        echo "\n</tr>";
    }
    echo "\n<tr>";
    echo "<td> </td>";
    echo "<td> </td>";
    echo "<td> $tongTienDung  </td>";
    echo "<td>  $tongTienThuc </td>";
    echo "\n</tr>";
    echo "\n<tr>";
    echo "<td> </td>";
    echo "<td> </td>";
    $pcDung = number_format($tongTienDung / ($tongTienDung + $tongTienThuc) * 100);
    $pcThuc = number_format($tongTienThuc / ($tongTienDung + $tongTienThuc) * 100);

    $tongTienThucAll+= $tongTienThuc;
    $tongTienDungAll+= $tongTienDung;
    echo "<td> <b> $pcDung % </b></td>";
    echo "<td>  <b> $pcThuc % </b></td>";
    echo "\n</tr>";
    echo "\n</table>";


//    $name = $m1[0];
//    $money = $m1[1];
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($m1[0]);
//    echo "</pre>";
//
//    echo "<br/>\n $name => $money";

}

$tongDoanhThuAll = $tongTienThucAll + $tongTienDungAll;

echo "<br/>\n";

echo "<hr/>\n";

echo "<br/>\n Tỷ lệ % Kinh doanh  2024";
echo "<br/>Tỷ lệ DungLA \n <b> " . number_format($tongTienDungAll / $tongDoanhThuAll * 100) . "%  ( $tongTienDungAll / $tongDoanhThuAll )</b>";
echo "<br/>Tỷ lệ ThucTT \n <b>" . number_format($tongTienThucAll / $tongDoanhThuAll * 100) . "% </b> ( $tongTienThucAll / $tongDoanhThuAll )";

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mm);
//echo "</pre>";

?>
