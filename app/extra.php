<?php

use App\Models\SiteMng;

define('DEF_LRV_DEFAULT_QUOTA_NODE_MONITOR', 5);
define('DEF_LRV_DEFAULT_QUOTA_NODE_USER_MYTREE', 100);

//            -6=>'Hoàn trả',
//            -5=>'Thông tin sai',
//            -4=>'Sai số điện thoại',
//            -3=>'Không liên lạc được',
//            -2=>'Khách Hủy',
define('DEF_SHOP_STATUS_TELE_DON_HUY_BOI_SHOP', -7);
define('DEF_SHOP_STATUS_TELE_HOAN_TRA_LAI_HANG', -6);
define('DEF_SHOP_STATUS_TELE_THONG_TIN_KHACH_SAI', -5);
define('DEF_SHOP_STATUS_TELE_SAI_SO_PHONE', -4);
define('DEF_SHOP_STATUS_TELE_KHONG_LIEN_LAC_DUOC', -3);
define('DEF_SHOP_STATUS_TELE_KHACH_HUY_DON', -2);
define('DEF_SHOP_STATUS_TELE_KHACH_CHOT_DON', 1);
define('DEF_SHOP_STATUS_TELE_DA_THONG_TIN_DON_SANG_SHIP', 2);
define('DEF_SHOP_STATUS_TELE_DA_DON_DA_GIAO_DEN_SHIP', 3);
define('DEF_SHOP_STATUS_TELE_SHIP_DONE_TO_KHACH_HANG', 6);

define('DEF_HR_TYPE_MESSAGE_TASK_IMAGE', 2);

class clsExOrderGHTK
{
    public $id;            //111

    public $pick_name;     //HCM-nội thành

    public $pick_address;  //590 CMT8 P.11

    public $pick_province; //TP. Hồ Chí Minh

    public $pick_district; //Quận 3

    public $pick_ward;     //Phường 1

    public $pick_tel;      //0966616368

    public $tel;           //0902066768

    public $name;          //GHTK - HCM - Noi Thanh

    public $address;       //123 nguyễn chí thanh

    public $province;      //TP. Hồ Chí Minh

    public $district;      //Quận 1

    public $ward;          //Phường Bến Nghé

    public $hamlet;        //Khác :

    public $is_freeship;   //1

    public $pick_date;     //2016-09-30

    public $pick_money;    // $: 47000,

    public $note;          //Khối lượng tính cước tối đa: 1.00 kg

    public $value;         // $: 30000,

    public $transport;     //fly

    public $pick_option;   //cod

    public $pick_session;   // $: 2,

    public $tags = [1];          // $: [1]

    //$tk = 'c2b026C82De84d303791c39Aa36F5Eabdb701951';
    //$urlApi = 'https://services.giaohangtietkiem.vn';

    //TK dungla2011@gmail.com
    //$tk = 'eadce514b1a54938634128bbb374b38d09220f2e';
    //$urlApi = 'https://services-staging.ghtklab.com';
    public static $token = 'eadce514b1a54938634128bbb374b38d09220f2e';

    public static $api = 'https://services-staging.ghtklab.com';

    public static function postDon($info, $api_key_ship)
    {
        $curl = curl_init();
        $urlApi = self::$api;

        //$tk = self::$token;

        $tk = $api_key_ship;

        curl_setopt_array($curl, [
            CURLOPT_URL => "$urlApi/services/shipment/order",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $info,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Token: $tk",
                'Content-Length: '.strlen($info),
            ],
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        //echo '\n\n - REMOTE Response: ' . $response;

        $ret = json_decode($response);
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($ret);
        //        echo "</pre>";

        return $ret;
    }
}

class clsSalaryExtra
{
}

function checkMailValidNcbd($email)
{
    $email = strtolower($email);
    $email = trim($email);
    if(!$email)
        return 0;
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return 0;
    }
    if(strpos($email, '@') === false) {
        return 0;
    }
    //nếu trước @ là số thì không gửi
    if(is_numeric(substr($email, 0, strpos($email, '@')))) {
        return 0;
    }
    if(strstr($email, '@khongcomail') || strstr($email, '@khongcoemail')) {
        return 0;
    }

    return 1;
}

function sendMailNcbd($toAddress, $subject, $body, $attachFile = null){

    $toAddress = trim($toAddress);
    $toAddress = str_replace(" ", "", $toAddress);

    //Kiểm tra toAddress để tránh gửi mail nhầm
    if(strpos($toAddress, '@') === false) {
        return 0;
    }

    if(!checkMailValidNcbd($toAddress)) {
        return 0;
    }


    $obj = new \App\Components\ClassMailV2();
    $obj->Username = explode(',', env('NCBD_ACC'))[0];

    //Chua co cho luu password
    $obj->Password = dfh1b(explode(',', env('NCBD_ACC'))[1]);

    $obj->Host = "smtp.office365.com";
    $obj->Port = "587";
    $obj->SMTPSecure = 'tls';

//    echo "<br/>\n $obj->Username / $obj->Password";

//    return;
//$obj->From = "dungla2011@gmail.com";
    $obj->From = $obj->Username;



    $obj->FromName = \App\Models\SiteMng::getInstance()->site_code;
    //$obj->attachFile = ['/var/glx/upload_file_glx/user_files/siteid_36/000/002/2899/2899' => 'f1.txt'];

    $obj->toAddress = $toAddress;
    $obj->Body = $body;
    $obj->Subject = $subject;
    $admin = SiteMng::getEmailAdmin(1);
    $obj->addReplyTo($admin);

    if (! $obj->sendMailGlx()) {
        loi($obj->ErrorInfo);
    }

    return 1;
}
