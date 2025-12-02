<?php

use App\Models\OrderShip;

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../index.php';

//$os = new OrderShip();
//$os->vendor_id = 2;
//$os->order_id = 1;
//$os->fee = 2;
//$os->remote_label = '111';
//$os->remote_tracking_id = '222';
//if(!$os->save()){
//    die("can not save");
//}
//
//die("xxx");

//
//$curl = curl_init($url. '/authentication-request-sample');
//
//curl_setopt_array($curl, array(
//    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//    CURLOPT_RETURNTRANSFER => true,
//    CURLOPT_HTTPHEADER => array(
//        "Token: $tk",
//    ),
//));
//
//$response = curl_exec($curl);
//curl_close($curl);
//
//echo 'Response: ' . $response;
//$ret = json_decode($response);
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($ret);
//echo "</pre>";
//

//tinhGiaOk();
//return;

//$tk = 'c2b026C82De84d303791c39Aa36F5Eabdb701951';
$urlApi = 'https://services.giaohangtietkiem.vn';

//TK dungla2011@gmail.com
$tk = 'eadce514b1a54938634128bbb374b38d09220f2e';
$urlApi = 'https://services-staging.ghtklab.com';

postDonOK();

//deleteDon();

function deleteDon()
{

    global $tk;
    global $urlApi;

    $id = '2134234234';
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => "$urlApi/services/shipment/cancel/partner_id:$id",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER => [
            "Token: $tk",
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    echo 'Response: '.$response;

    $ret = json_decode($response);
    echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
    print_r($ret);
    echo '</pre>';

}

function postDonOK()
{

    global $tk;
    global $urlApi;

    $order = '
{
    "products": [
        {
            "name": "sách",
            "weight": 0.5,
            "quantity": 10,
            "product_code": "11"
        },
        {
            "name": "báo",
            "weight": 0.2,
            "quantity": 1,
            "product_code": "12"
        }
    ],
    "order": {
        "id": "111",
        "pick_name": "HCM-nội thành",
        "pick_address": "590 CMT8 P.11",
        "pick_province": "TP. Hồ Chí Minh",
        "pick_district": "Quận 3",
        "pick_ward": "Phường 1",
        "pick_tel": "0966616368",
        "tel": "0902066768",
        "name": "GHTK - HCM - Noi Thanh",
        "address": "123 nguyễn chí thanh",
        "province": "TP. Hồ Chí Minh",
        "district": "Quận 1",
        "ward": "Phường Bến Nghé",
        "hamlet": "Khác",
        "is_freeship": "1",
        "pick_date": "2016-09-30",
        "pick_money": 47000,
        "note": "Khối lượng tính cước tối đa: 1.00 kg",
        "value": 30000,
        "transport": "fly",
        "pick_option": "cod",
        "pick_session": 2,
        "tags": [
            1
        ]
    }
}';

    $order = '{
  "products": [
    {
      "name": "Xịt Mọc Tóc Herber - hãng_marlay",
      "weight": 0,
      "quantity": 1,
      "product_code": 219
    },
    {
      "name": "Xịt Miệng - loại_bạc hà",
      "weight": 0.1,
      "quantity": 5,
      "product_code": 220
    }
  ],
  "order": {
    "id": "34",
    "pick_name": " Công ty DH",
    "pick_address": " Linh Đàm",
    "pick_province": " Hà Nội",
    "pick_district": " Thanh Xuân",
    "pick_ward": " Phường ABC",
    "pick_tel": " 0999999999",
    "tel": "968686868",
    "name": "anh Hoàng",
    "address": "Số 111, Tổ 1, đường Lê Hồng Phong, ",
    "province": "Thành phố Hồ Chí Minh",
    "district": "Quận 10",
    "ward": "Phường 02",
    "hamlet": "",
    "is_freeship": 0,
    "pick_date": "",
    "pick_money": 1720,
    "note": "",
    "value": 1720,
    "transport": "fly",
    "pick_option": "cod",
    "pick_session": "2",
    "tags": ""
  }
}';

    $order = '{
  "products": [
    {
      "name": "Xịt Mọc Tóc Herber - hãng_marlay",
      "weight": 0,
      "quantity": 1,
      "product_code": 219
    },
    {
      "name": "Xịt Miệng - loại_bạc hà",
      "weight": 0.1,
      "quantity": 5,
      "product_code": 220
    }
  ],
  "order": {
    "id": "211",
    "pick_name": " Công ty DH",
    "pick_address": "Số 111, đường Lê Hồng Phong",
    "pick_province": "Thành phố Hồ Chí Minh",
    "pick_district": "Quận 10",
    "pick_ward": "Phường 2",
    "pick_tel": "0999999999",
    "tel": "0968686868",
    "name": "Trần Anh",
    "address": "số 54 Đường Nguyễn Đổng Chi",
    "province": "Thành phố Hà Nội",
    "district": "Quận Nam Từ Liêm",
    "ward": "Phường Cầu Diễn",
    "hamlet": "khác",
    "is_freeship": 0,
    "pick_date": "",
    "pick_money": 1720,
    "note": "",
    "value": 1720,
    "transport": "fly",
    "pick_option": "cod",
    "pick_session": "2",
    "tags": ""
  }
}';

    $curl = curl_init();

    curl_setopt_array($curl, [
        CURLOPT_URL => "$urlApi/services/shipment/order",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $order,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            "Token: $tk",
            'Content-Length: '.strlen($order),
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    echo 'Response: '.$response;

    $ret = json_decode($response);
    echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
    print_r($ret);
    echo '</pre>';
}

function tinhGiaOk()
{

    global $tk;
    global $urlApi;

    $data = [
        //    "pick_province" => "Hà Nội",
        //    "pick_district" => "Quận Hoàn kiếm",
        'pick_province' => 'TPHCM',
        'pick_district' => 'Quận 2',
        'province' => 'Hà nội',
        'district' => 'Quận Cầu Giấy',
        'address' => 'P.503 tòa nhà Auu Việt, số 1 Lê Đức Thọ',
        'weight' => 1000,
        'value' => 30000,
        'transport' => 'road',
        //        "transport" => "fly",
        //        "deliver_option" => "xteam",
        'tags' => [1],
    ];
    $curl = curl_init();
    $urlApi = 'https://services-staging.ghtklab.com';
    $urlApi = 'https://services.giaohangtietkiem.vn';
    curl_setopt_array($curl, [
        CURLOPT_URL => "$urlApi/services/shipment/fee?".http_build_query($data),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_HTTPHEADER => [
            "Token: $tk",
        ],
    ]);

    $response = curl_exec($curl);
    curl_close($curl);

    //    echo 'Response: ' . $response;

    $ret = json_decode($response);
    echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
    print_r($ret);
    echo '</pre>';

}
