<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Protection;

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

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

    $sheet->setCellValue([1, 1], "Chú ý: Địa chỉ email là KEY duy nhất cho mỗi user, bắt buộc có để tránh import trùng lặp!");

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
    $sheet->getStyle('A2:Z3')->getFill()
        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setARGB('eeeeee');

    $sheet->getStyle('A1:Z1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_RED);

// Save the spreadsheet to a file
    $writer = new Xlsx($spreadsheet);
//    $filePath = '/var/www/html/public/tool1/_site/event_mng/import_user_from_excel.xlsx';
    $writer->save($filePath);

//    echo "Excel file created: $filePath";
}

$file = "/share/user_info_template_import.xlsx";
@unlink($file);
createExcelFile($file);
if(!file_exists($file))
{
    die("Can not create file!");
}

// Allow user to download the file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="user_info_template_import.xlsx"');
header('Cache-Control: max-age=0');
readfile($file);
exit;

