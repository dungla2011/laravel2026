<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = "tailieuchuan.net";

require_once '/var/www/html/public/index.php';

use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\ImageManagerStatic as Image;


function addBorderDDD($cutImage)
{
// Create a new true color image with increased dimensions
    $borderImage = imagecreatetruecolor(imagesx($cutImage) + 4, imagesy($cutImage) + 4);

// Allocate the color for the border
    $borderColor = imagecolorallocate($borderImage, 221, 221, 221); // RGB for #ddd

// Draw a rectangle for the border
    imagerectangle($borderImage, 0, 0, imagesx($borderImage) - 1, imagesy($borderImage) - 1, $borderColor);
    imagerectangle($borderImage, 1, 1, imagesx($borderImage) - 2, imagesy($borderImage) - 2, $borderColor);

// Copy the original image into the new image, offsetting it by the border width
    imagecopy($borderImage, $cutImage, 2, 2, 0, 0, imagesx($cutImage), imagesy($cutImage));

// Save the image with the border
    imagepng($borderImage, 'cut_image.png');

// Clean up
    imagedestroy($cutImage);
    imagedestroy($borderImage);
}

$error = 0;
function ocrPhanCuoiAnh($file) {
    global $error;

    $image = imagecreatefrompng($file);

    $height = imagesy($image);
    $width = imagesx($image);

// Find the first horizontal line from the middle to the end of the image with color #ccc
    for ($y = intval($height * 2/3); $y < $height; $y++) {
        $isLineAllSameColor = true;
        for ($x = 0; $x < $width; $x++) {
            $rgb = imagecolorat($image, $x, $y);

            // Convert RGB to hex color
            $r = ($rgb >> 16) & 0xFF;
            $g = ($rgb >> 8) & 0xFF;
            $b = $rgb & 0xFF;

            $color = sprintf('#%02x%02x%02x', $r, $g, $b);

            if ($color != '#dddddd') {
                $isLineAllSameColor = false;
                break;
            }
        }

        if ($isLineAllSameColor) {
            // Cut the image from the found line to the end
            $cutImage = imagecrop($image, ['x' => 0, 'y' => $y, 'width' => $width, 'height' => $height - $y]);

            // Save the cut image
//            imagepng($cutImage, 'cut_image.png');
            addBorderDDD($cutImage);


            // Use Tesseract to recognize text from the cut image
            $text = (new TesseractOCR('cut_image.png'))
                ->lang('vie') // Use Vietnamese language
                ->run();

            $text = trim($text);
            echo "<br/>\n Txt = ";
            echo $text;
            $text = trim($text, ',:.\'"“');
            if(!$text)
                $error++;
            echo "<br/>\n Error: $error";
//            getch("... $error");

            return $text;
        }
    }
}

function ocrAllInDb()
{
    $mm = \App\Models\FileUpload::where("name", 'LIKE', "%bắt chữ%")->get();
    $cc = 0;
    foreach ($mm AS $obj){
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($obj->toArray() );
        echo "</pre>";
        $cc++;
        echo "<br/>\n  $cc. $obj->name ";
        echo "<br/>\n $obj->file_path ";

        if(file_exists($obj->file_path)){
            $txt = ocrPhanCuoiAnh($obj->file_path);

            if($txt){
                $obj->comment = $txt;
                $obj->save();
            }
//            getch("...1");
        }
//    getch("...2");
    }

}

ocrAllInDb();
return;

function deleteFileDB()
{
    $mm = \App\Models\FileUpload::where("name", 'LIKE', "%bắt chữ%")->get();
    $cc = 0;
    foreach ($mm AS $obj){
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($obj->toArray() );
        echo "</pre>";
        $cc++;
        echo "<br/>\n  $cc. $obj->name ";
        echo "<br/>\n $obj->file_path ";
//    getch("...1");
        unlink($obj->file_path);
        if(!file_exists($obj->file_path))
            \App\Models\FileCloud::find($obj->cloud_id)->forceDelete();
        $obj->forceDelete();
//    getch("...2");
    }

}

$tk = \App\Models\User::where("email", "dungla2011@gmail.com")->first()->getJWTUserToken();

DirListFullToArray("/share/duoi_hinh_bat_chu2", $arrFull);

$mFileOK = [];
$cc = 0;
foreach ($arrFull AS $file){
    $fileName = str_replace("Đáp án game ", "", $file);
    $fileName = str_replace("  ", " ", $fileName);

    if(strstr($file, '.done.wm.')){
        $cc++;

        echo "<br/>\n $cc ";
        $folder = explode(".", $fileName)[0];
        if(!($mFileOK[$folder] ?? null))
            $mFileOK[$folder] = [];
        $mFileOK[$folder][] = $fileName;
//        echo "<br/>\n $folder | $file";
        $bname = basename($fileName);

        $ct = file_get_contents($file);
        $mime = mime_content_type($file);


        if(\App\Models\FileUpload::where("name", $bname)->first()){
            echo "<br/>\n Da upload";
            continue;
        }

        $ret = \App\Models\FileUpload::uploadFileApiV2("https://" . $_SERVER['HTTP_HOST'] .  '/api/member-file/upload', $tk, $ct, $bname, $mime);
        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        print_r($ret);
        echo "</pre>";

//        getch("...");
    }
}

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mFileOK);
//echo "</pre>";
