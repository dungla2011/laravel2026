<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Protection;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '/var/www/html/public/index.php';

function createExcelFile($filePath)
{
//    if (file_exists($filePath))
//        return;

    $obj = new \App\Models\EventUserInfo();
    $meta = \App\Models\EventUserInfo::getMetaObj();
    if ($meta instanceof \App\Models\EventUserInfo_Meta) ;

    $mf = \App\Models\EventUserInfo::getArrayFieldList();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    $colIndex = 1;
    $mfImportDb = $meta->getFieldToImportExcel();
    foreach ($mfImportDb as $field => $nameAndSize) {
        $sheet->setCellValue([$colIndex++, 2], $field);
        $sheet->getColumnDimensionByColumn($colIndex - 1)->setWidth($nameAndSize['size']); // Set width as needed
    }

    // Set the height for multiple rows
    for ($row = 4; $row <= 1000; $row++) {
        $sheet->getRowDimension($row)->setRowHeight(22);
    }

    $colIndex = 1;
    //Dòng 2 là mô tả và set size cho cell
    foreach ($mfImportDb as $field => $nameAndSize) {
        $sheet->setCellValue([$colIndex++, 3], $nameAndSize['name']);
    }

// Đặt chế độ bảo vệ cho hai dòng đầu tiên
    $spreadsheet->getActiveSheet()->getStyle('A1:B3')->getProtection()
        ->setLocked(Protection::PROTECTION_PROTECTED);

// Cố định hai dòng đầu tiên
    $spreadsheet->getActiveSheet()->freezePane('A4');

// Bật chế độ bảo vệ cho toàn sheet (sẽ yêu cầu mật khẩu nếu muốn chỉnh sửa các ô đã khóa)
//    $spreadsheet->getActiveSheet()->getProtection()->setSheet(true);

// Freeze the first two rows
    $sheet->freezePane('A4');

    //Đổi màu nền 2 hàng đầu
    $sheet->getStyle('A1:Z3')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('cccccc');

// Save the spreadsheet to a file
    $writer = new Xlsx($spreadsheet);
//    $filePath = '/var/www/html/public/tool1/_site/event_mng/import_user_from_excel.xlsx';
    $writer->save($filePath);

//    echo "Excel file created: $filePath";
}

$cemail = getCurrentUserEmail();
$uid = getCurrentUserId();
$siteId = \App\Models\SiteMng::getSiteId();
$domain = \LadLib\Common\UrlHelper1::getDomainHostName();

$file = DEF_FILE_PATH_IMPORT_EXCEL . ".$siteId.$uid.xlsx";

$link = "https://events.dav.edu.vn" . str_replace("/var/www/html/public", "", $file);

createExcelFile($file);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        $allowed = ['xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'xls' => 'application/vnd.ms-excel'];
        $filename = $_FILES['file']['name'];
        $filetype = $_FILES['file']['type'];
        $filesize = $_FILES['file']['size'];

        // Verify file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if (!array_key_exists($ext, $allowed)) {
            die("Error: Please select a valid file format.");
        }

        // Verify file type
        if (in_array($filetype, $allowed)) {
            // Check file size - 5MB maximum
            if ($filesize > 5 * 1024 * 1024) {
                die("Error: File size is larger than the allowed limit.");
            }

            // Check whether file exists before uploading it
            if (file_exists("upload/" . $filename)) {
                echo $filename . " is already exists.";
            } else {
                move_uploaded_file($_FILES['file']['tmp_name'], "upload/" . $filename);
                echo "Your file was uploaded successfully.";
            }
        } else {
            echo "Error: There was a problem uploading your file. Please try again.";
        }
    } else {
        echo "Error: " . $_FILES['file']['error'];
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Excel File</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-12 ">
            Hướng dẫn:
            <br>
            <a href="<?php echo $link ?>" style="text-decoration: underline"> - Tải file Mẫu</a> và điền nội dung,
            <br>
            - Upload Để <b>Nhập vào DB</b>
            - <b>Email</b> sẽ là key duy nhất không trùng với trong DB, Nếu trùng email sẽ báo lỗi.
            <br>
            - Chú ý không sửa 2 hàng đầu của File Mẫu



            <h2 class="text-center"></h2>
            <form action="upload.php" method="post" enctype="multipart/form-data" class="form-horizontal">
                <div class="form-group">
                    <label for="file" class="col-sm-5 control-label">Choose Excel file:</label>

                    <input type="file" name="file" id="file" accept=".xlsx, .xls" class="form-control">
                    <input type="submit" value="Upload" class="btn">

                </div>

            </form>
        </div>
    </div>
</div>
</body>
</html>
