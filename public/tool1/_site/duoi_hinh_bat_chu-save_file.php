<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../index.php';

function isWhite($rgb) {
    return $rgb['red'] == 255 && $rgb['green'] == 255 && $rgb['blue'] == 255;
}

function addWatermark($file){
    // Load the stamp and the photo to apply the watermark to
    $im = imagecreatefrompng($file);

    // Set the path to the TTF font you want to use
    $font = __DIR__.'/tahoma.ttf';

    // Set the text color to white
    $color = imagecolorallocate($im, 255, 255, 255);

    // The text to add to the image
    $text = 'TaiLieuChuan.net';

    // The angle to rotate the text
    $angle = 0;

    // The coordinates where the text will be placed
    $x = 5;
    $y = 115;

    // Add the text to the image using TrueType fonts
    imagettftext($im, 10, $angle, $x, $y, $color, $font, $text);
//    imagestring($im, 10, $x, $y, $text, $color);

    $fileWm = str_replace(".done.",'.done.wm.',$file);
    // Save the image to file and free memory
    imagepng($im, $fileWm);
    imagedestroy($im);
}

//addWatermark('/share/duoi_hinh_bat_chu2/2.png');
//return;
function cropImageTopAndBotomTheSameColor($file) {
    $image = imagecreatefrompng($file); // Thay đổi hàm này tùy thuộc vào định dạng hình ảnh của bạn
    $width = imagesx($image);
    $height = imagesy($image);

    $top = 0;
    $bottom = $height - 1;

    // Check from top
    for ($y = 0; $y < $height; $y++) {
        $colorIndex = imagecolorat($image, 0, $y);
        $firstColor = imagecolorsforindex($image, $colorIndex);
        $allSameColor = true;
        for ($x = 1; $x < $width; $x++) {
            $colorIndex = imagecolorat($image, $x, $y);
            $color = imagecolorsforindex($image, $colorIndex);
            if ($color !== $firstColor) {
                $allSameColor = false;
                break;
            }
        }
        if (!$allSameColor) {
            $top = $y;
            break;
        }
    }

    // Check from bottom
    for ($y = $height - 1; $y >= 0; $y--) {
        $colorIndex = imagecolorat($image, 0, $y);
        $firstColor = imagecolorsforindex($image, $colorIndex);
        $allSameColor = true;
        for ($x = 1; $x < $width; $x++) {
            $colorIndex = imagecolorat($image, $x, $y);
            $color = imagecolorsforindex($image, $colorIndex);
            if ($color !== $firstColor) {
                $allSameColor = false;
                break;
            }
        }
        if (!$allSameColor) {
            $bottom = $y;
            break;
        }
    }

    // Create new image
    $newHeight = $bottom - $top + 1;
    $newImage = imagecreatetruecolor($width, $newHeight);
    imagecopy($newImage, $image, 0, 0, 0, $top, $width, $newHeight);
    imagepng($newImage, $file); // Save the cropped image

    // Clean up
    imagedestroy($newImage);
    imagedestroy($image);
}

//cropImageTopAndBotomTheSameColor('/share/duoi_hinh_bat_chu2/a1.png');
//return;

function cropWhiteZone($file)
{
    $image = new Imagick($file); // Thay đổi đường dẫn này tùy thuộc vào vị trí hình ảnh của bạn
    $image->trimImage(0); // Cắt vùng trắng viền quanh hình ảnh
    $image->writeImage($file); // Thay đổi đường dẫn này tùy thuộc vào nơi bạn muốn lưu hình ảnh đã cắt
    $image->clear();
    $image->destroy();

}

function cutFileVertical($file, $bname, $tenBai) {

    global $countOK;
    $image = imagecreatefrompng($file); // Thay đổi hàm này tùy thuộc vào định dạng hình ảnh của bạn
    $image = imagecreatefrompng($file); // Thay đổi hàm này tùy thuộc vào định dạng hình ảnh của bạn
    $width = imagesx($image);
    $height = imagesy($image);

    $dirPath = dirname($file);
//    $bname = basename($file);

    $whiteColumns = [];
    $previousColumnWasWhite = false;

    for ($x = 0; $x < $width; $x++) {
        $allWhite = true;
        for ($y = 0; $y < $height; $y++) {
            $colorIndex = imagecolorat($image, $x, $y);
            $color = imagecolorsforindex($image, $colorIndex);
            if (!isWhite($color)) {
                $allWhite = false;
                break;
            }
        }
        if ($allWhite && !$previousColumnWasWhite) {
            $whiteColumns[] = $x;
        }
        $previousColumnWasWhite = $allWhite;
    }

//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($whiteColumns);
//    echo "</pre>";

    $partNumber = 0;
    $previousWhiteColumn = 0;


    foreach ($whiteColumns as $whiteColumn) {
        $countOK++;
        $countOKFormat = sprintf("%02d", $countOK);
        $newImage = imagecreatetruecolor($whiteColumn - $previousWhiteColumn, $height);
        imagecopy($newImage, $image, 0, 0, $previousWhiteColumn, 0, $whiteColumn - $previousWhiteColumn, $height);
        $filePart = "$dirPath/$tenBai.done.$countOKFormat.png";
        imagepng($newImage, $filePart);
        imagedestroy($newImage);

        cropWhiteZone($filePart);
        $partNumber++;
        $previousWhiteColumn = $whiteColumn;
        addWatermark($filePart);
    }

    // Save the last part of the image
    if ($previousWhiteColumn < $width) {
        $countOK++;
        $countOKFormat = sprintf("%02d", $countOK);
        $newImage = imagecreatetruecolor($width - $previousWhiteColumn, $height);
        imagecopy($newImage, $image, 0, 0, $previousWhiteColumn, 0, $width - $previousWhiteColumn, $height);
        $filePart = "$dirPath/$tenBai.done.$countOKFormat.png";
        imagepng($newImage, $filePart);

        cropWhiteZone($filePart);
        imagedestroy($newImage);
        addWatermark($filePart);
    }

    imagedestroy($image);
}
//Cắt file theo chiều ngang tại các đường trắng ngang
function cutFileHoriz($file, $tenBai) {
    $image = imagecreatefrompng($file); // Thay đổi hàm này tùy thuộc vào định dạng hình ảnh của bạn
    $width = imagesx($image);
    $height = imagesy($image);

    $dirPath = dirname($file);

    $bname = basename($file);

    $whiteLines = [];
    $previousLineWasWhite = false;

    for ($y = 0; $y < $height; $y++) {
        $allWhite = true;
        for ($x = 0; $x < $width; $x++) {
            $colorIndex = imagecolorat($image, $x, $y);
            $color = imagecolorsforindex($image, $colorIndex);
            if (!isWhite($color)) {
                $allWhite = false;
                break;
            }
        }
        if ($allWhite && !$previousLineWasWhite) {
            $whiteLines[] = $y;
        }
        $previousLineWasWhite = $allWhite;
    }

    $partNumber = 0;
    $previousWhiteLine = 0;

    foreach ($whiteLines as $whiteLine) {
        $newImage = imagecreatetruecolor($width, $whiteLine - $previousWhiteLine);
        imagecopy($newImage, $image, 0, 0, 0, $previousWhiteLine, $width, $whiteLine - $previousWhiteLine);

        $filePart = "$dirPath/$bname.horiz.$partNumber.png";


        imagepng($newImage, $filePart);
        imagedestroy($newImage);

        cropWhiteZone($filePart);

        $partNumber++;
        $previousWhiteLine = $whiteLine;

        cutFileVertical($filePart, $bname, $tenBai);
    }

    if ($previousWhiteLine < $height) {
        $newImage = imagecreatetruecolor($width, $height - $previousWhiteLine);
        imagecopy($newImage, $image, 0, 0, 0, $previousWhiteLine, $width, $height - $previousWhiteLine);

        $filePart = "$dirPath/$bname.horiz.$partNumber.png";

        imagepng($newImage, $filePart);
        imagedestroy($newImage);

        cropWhiteZone($filePart);

        cutFileVertical($filePart, $bname, $tenBai);
    }


    imagedestroy($image);
}

//cutFileHoriz('/share/duoi_hinh_bat_chu2/1.png');
//cutFileVertical('/share/duoi_hinh_bat_chu2/part3.png');

//
//return;

$link = "https://giaiphapsmartphone.blogspot.com/p/game-uoi-hinh-bat-chu-ang-gay-hot-tren.html";

$ct = file_get_content_cache($link);

$xx = str_get_html($ct);

$countOK = 0;

foreach ($xx->find("a") AS $lk) {

    $tx =   trim($lk->text());
    if(str_starts_with($tx, "Đáp án game Đuổi Hình Bắt Chữ Phần")){
//        $tx = \LadLib\Common\cstring2::convert_codau_khong_dau($tx);
        $tx = str_replace(":" , ' - ', $tx );
        echo "<br/>\n$tx <br> - ";
        echo $lk->href;

        try{

            if(!str_contains($lk->href, "giaiphapsmartphone.blogspot"))
                continue;

            $countOK = 0;

            $ct2 = file_get_content_cache($lk->href, null, 5);
            $xx2 = str_get_html($ct2);
            $cc1 = 0;
            $cc2 = 0;
            foreach ($xx2->find("div.separator") AS $d1) {
                $cc1++;
                $img = $d1->find("img", 0)?->src;
                echo "<br/>\n $cc1. $img";
                $fileimg = "/share/duoi_hinh_bat_chu2/$tx.$cc1.png";
                if(!file_exists($fileimg))
                {
                    $ct4 = file_get_content_cache($img, null, 20);
                    outputW($fileimg, $ct4);
                }

                cropImageTopAndBotomTheSameColor($fileimg);
                cutFileHoriz($fileimg, $tx);

//                getch("...");

                if(0)
                if(file_exists($fileimg)){
                    $partWidth = 188;
                    $partHeight = 156;
                    $src = imagecreatefrompng($fileimg);
                    $dest = imagecreatetruecolor($partWidth, $partHeight);
                    $size = getimagesize($fileimg);
                    $width = $size[0];
                    $height = $size[1];

                    $nPart = floor($height / $partHeight);
                    $partHeight = $height / $nPart;

                    for ($i = 0; $i < floor($height / $partHeight); $i++) {
                        for ($j = 0; $j < $width / $partWidth; $j++) {
                            $dest = imagecreatetruecolor($partWidth, $partHeight);
                            imagecopy($dest, $src, 0, 0, $j * ($partWidth + 71), $i * $partHeight, $partWidth, $partHeight);
                            if($j * ($partWidth + 71) > $width)
                                continue;
                            $cc2++;
                            $filePart = "$fileimg.part_".sprintf("%02d", $cc2).".jpg";
                            imagejpeg($dest, $filePart, 100);

                            //Bo qua cac phan loi
                            if(filesize($filePart) < 3000) {
                                unlink($filePart);
                                $cc2--;
                            }
                        }
                    }
                }


            }
        }
        catch (Throwable $e) { // For PHP 7
            echo "<br/>\n Error1: ".$e->getMessage();
            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            print_r($e->getTraceAsString());
            echo "</pre>";
        }
        catch (Exception $exception){
            echo "<br/>\n Error2: ".$exception->getMessage();
        }
    }
}
