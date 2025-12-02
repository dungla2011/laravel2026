<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'hateco.mytree.vn';

error_reporting(E_ALL);
ini_set('display_errors', 1);
define('DEF_TOOL_CMS', 1);

require_once '../../index.php';

// Get the API client and construct the service object.
$jsonFile = '/var/glx/credentials_dungbkhn_google_sheet.json';

if (! file_exists($jsonFile)) {
    exit('file not found!');
}

$client = new Google_Client();
$client->setApplicationName('Google Drive API PHP Quickstart');
//$client->setScopes(Google_Service_Drive::DRIVE);
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');

//$client->setScopes(array('https://www.googleapis.com/auth/drive.file'));
//$client->setAuthConfig(__DIR__.'/credentials.json');
$client->setAuthConfig($jsonFile);

$service = new \Google_Service_Sheets($client);

$spreadsheetId = '1vVuK1VaFLlLzKrmP13vTPPk0Ioab5sK4';
$spreadsheetId = '19igfHSStJ5MYaB1xyV99HWZ0IsUsWWGN9km3MAFABZU';
//$spreadsheetId = "1glgSisCJ8SjeVZp7-Sp7bW0KBBzegc7Xb4KLdT5w1Yk";
$spreadsheetId = '1w5vKGq0Z2tpYE9WRsvETtJAefr_EZYFRfZWTThymKDw';

//Gộp thành 1 cột
$configHoTen = 1;

//2 cột riêng
//$configHoTen = 2;

//$spreadsheet = $service->spreadsheets->get($spreadsheetId);
//var_dump($spreadsheet);

$range = 'cao đẳng';
//$range = 'TRung cấp';
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$rows = $response->getValues();
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($rows);
//echo "</pre>";
$line = $cc = 0;
foreach ($rows as $r1) {
    echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
    print_r($r1);
    echo '</pre>';

    $line++;

    echo "<br/>\n LINE = $line";

    if (! ($r1 ?? '')) {
        continue;
    }

    if (! ($r1[0] ?? '')) {

        echo "<br/>\n Error1";

        continue;
    }
    if (! is_numeric($r1[0])) {
        echo "<br/>\n Error2";

        continue;
    }

    if (! ($r1[1] ?? '') || ! ($r1[1])) {
        echo "<br/>\n Error3";

        continue;
    }

    //    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
    //    print_r($r1);
    //    echo "</pre>";

    $cc++;
    echo "<br/>\n DONE  ==== $cc ";

    $obj = new \App\Models\HatecoCertificate();
    //    $obj = new stdClass();

    $i = 1;
    if ($configHoTen == 1) {
        $hoten = trim($r1[$i]);
        $mHoTen = explode(' ', $hoten);
        $ten = end($mHoTen);
        $obj->ten = $ten;
        array_pop($mHoTen);
        $obj->ho = implode(' ', $mHoTen);
    } else {
        $obj->ho = $r1[$i] ?? '';
        $i++;
        $obj->ten = $r1[$i] ?? '';
    }
    $i++;
    $date = $r1[$i] ?? '';

    if (strstr($date, '19') === false && strstr($date, '20') === false) {
        exit(" Ngày tháng không phù hợp: $date");
    }

    $obj->ngay_sinh = $r1[$i] ?? '';
    $obj->ngay_sinh = str_replace('/', '-', $obj->ngay_sinh);
    $obj->ngay_sinh = nowy(strtotime($obj->ngay_sinh));

    $i++;
    $obj->noi_sinh = $r1[$i] ?? '';
    $i++;
    $obj->gioi_tinh = $r1[$i] ?? '';
    $i++;
    $obj->dan_toc = $r1[$i] ?? '';
    $i++;
    $obj->quoc_tich = $r1[$i] ?? '';
    $i++;
    $obj->nganh_nghe = $r1[$i] ?? '';
    $i++;
    echo "<br/>\n Trinh do: $i ";
    $obj->trinh_do = $r1[$i] ?? '';
    $i++;
    $obj->hinh_thuc = $r1[$i] ?? '';
    $i++;
    $obj->nam_tot_nghiep = $r1[$i] ?? '';
    $i++;

    if (strstr($obj->nam_tot_nghiep, '19') === false && strstr($obj->nam_tot_nghiep, '20') === false) {
        exit(" Năm tốt nghiệp ko phù hợp: $obj->nam_tot_nghiep");
    }

    $obj->xep_loai = $r1[$i] ?? '';
    $i++;
    $obj->so_qd_cong_nhan_tn = $r1[$i] ?? '';
    $i++;

    $obj->ngay_qd = $r1[$i] ?? '';
    $i++;

    $obj->ngay_qd = str_replace('/', '-', $obj->ngay_qd);
    $obj->ngay_qd = nowy(strtotime($obj->ngay_qd));

    $obj->ngay_cap_bang = $r1[$i] ?? '';
    $i++;
    $obj->ngay_cap_bang = str_replace('/', '-', $obj->ngay_cap_bang);
    $obj->ngay_cap_bang = nowy(strtotime($obj->ngay_cap_bang));
    $obj->so_hieu_bang_tn = $r1[$i] ?? '';
    $i++;

    if ($objx = \App\Models\HatecoCertificate::where('so_hieu_bang_tn', $obj->so_hieu_bang_tn)->first()) {
        echo "<br/>\nĐã insert: $objx->id ";

        continue;
    }

    $obj->so_vao_goc_cap_bang = $r1[$i] ?? '';
    $i++;
    $obj->co_so_dao_tao = $r1[$i] ?? '';
    $i++;

    //    dump($obj);

    $obj->save();

    echo "<br/>\n Insert OK!";
    echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
    print_r($obj->toArray());
    echo '</pre>';
}
