<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\OrderInfo;
use App\Models\OrderItem;
use App\Models\User;


class OrderItemController extends BaseController
{
    protected OrderItem $data;

    public function __construct(OrderItem $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
        parent::__construct();
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }

    /**
     * @param $newOrder
     * @param $prod
     * @param $ip
     * @return void
     */
    static function addOrderItemFromOrder($newOrder, $prod = null, $ip = null, $vendor = null)
    {
//        $newOrder = new OrderInfo();

        $uid = $newOrder->user_id;
        $orderID = $newOrder->transaction_id_local;
        $total_amount = $newOrder->money;
        $transId = $newOrder->transaction_id_remote;

//        $new->transaction_id_local, $new->money, $prod, $new->transaction_id_remote

        ol00("Check order, UID = $uid , $orderID, $total_amount, $transId, $ip");

        if (!$od = \App\Models\OrderInfo::where('transaction_id_local', $orderID)->first()) {
            ol00("Add new order");
            $od = new \App\Models\OrderInfo();
            $od->user_id = $uid;
            $od->transaction_id_local = $orderID;
            $od->transaction_id_remote = $transId;
            $od->remote_ip = $ip;
            $od->money = $total_amount;
            $od->vendor_pay = $vendor;
            if($newOrder->created_at ?? '')
                $od->created_at = $newOrder->created_at;
            $od->save();
        }
        else{
//            ol00("See order insert before $od->id");
        }

        if (!$billProd = \App\Models\OrderItem::where(['order_id' => $od->id])->first()) {

            $billProd = new \App\Models\OrderItem();
            $billProd->order_id = $od->id;

            if($prod){
                $billProd->product_id = $prod->id;
                $billProd->price = $prod->price;
                $billProd->param1 = $prod->param1;
            }
            else
                $billProd->price = $total_amount;


            $billProd->created_at = $od->created_at;
            $billProd->quantity = 1;

            $billProd->user_id = $uid;

            if($newOrder->tmp_ngold ?? ''){
                $billProd->tmp_ngold = $newOrder->tmp_ngold;
            }
            if($newOrder->reason ?? ''){
                $billProd->note = $newOrder->reason;
            }
            if($newOrder->created_at ?? '')
                $billProd->created_at = $newOrder->created_at;
            $billProd->save();
            ol00("Add new bill ");
        }

//            ol00("See bill insert before $billProd->id !");
    }

    public function buyVip()
    {
//        return view('index.fullcr.buyVip');

        return $this->getViewLayout();
    }

    public function momoNotify()
    {
        \clsMomo::momoNotifyOrReturnWeb(request()->all());
    }

    public function momoReturn()
    {
//        return view('index.fullcr.buyVip');

        return $this->getViewLayout();
    }

    public function webHookBK()
    {
        setLogFile("/var/glx/weblog/baokim.log");
        try {
            $jsonWebhookData = file_get_contents("php://input");

//            $jsonWebhookData =  '{"order":{"id":6030176,"user_id":10848938,"mrc_order_id":"165.1713303349-58","txn_id":3004807,"ref_no":null,"merchant_id":35589,"total_amount":10000,"description":"Goi download","items":null,"url_success":"https:\/\/fullcr.mytree.vn\/buy-vip","url_cancel":null,"url_detail":"https:\/\/www.baokim.vn","stat":"c","lang":"vi","type":1,"bpm_id":"297","accept_qrpay":1,"accept_bank":1,"accept_cc":1,"accept_ib":1,"accept_ewallet":1,"accept_installments":1,"email":"admin@glx.com.vn","name":"CONG TY TNHH CONG NGHE SO GALAXY VIET NAM","webhooks":"https:\/\/fullcr.mytree.vn\/webhookBk","customer_name":"Kh\u00e1ch h\u00e0ng BK","customer_email":"hotrokhachhang@baokim.vn","customer_phone":"84964967186","customer_address":"So 311-313, Duong Truong Chinh, Phuong Khuong Mai, Quan Thanh Xuan, Thanh pho Ha Noi, Viet Nam.","created_at":"2024-04-17 04:35:51","updated_at":"2024-04-17 04:36:30"},"txn":{"id":3004807,"reference_id":"BK_6030176_3004807_5ZD","user_id":10848938,"merchant_id":35589,"order_id":6030176,"mrc_order_id":"165.1713303349-58","total_amount":10088,"amount":10000,"fee_amount":0,"bank_fee_amount":88,"bank_fix_fee_amount":0,"fee_payer":1,"bank_fee_payer":1,"auth_code":null,"auth_time":"","ref_no":null,"bpm_id":"297","bank_ref_no":"3004807","bpm_type":13,"gateway":"","stat":1,"init_token":null,"description":"Goi download","customer_email":"hotrokhachhang@baokim.vn","customer_phone":"84964967186","completed_at":"2024-04-17 04:36:30","created_at":"2024-04-17 04:35:59","updated_at":"2024-04-17 04:36:30","deleted_at":null},"dataToken":null,"sign":"6374bd72c6e158affa8ed94569fb17504dc094b98d4e261febc470d12ef1c1e6"}';
            $input = json_decode($jsonWebhookData);
//
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($input);
//            echo "</pre>";
//
//            die();

//"{\"order\":{\"id\":330571,\"user_id\":10848938,\"mrc_order_id\":\"4sh.1630469510\",\"txn_id\":107552,\"ref_no\":null,\"merchant_id\":35589,\"total_amount\":10000,\"description\":\"M\\u00f4 t\\u1ea3 \\u0111\\u01a1n h\\u00e0ng glx\",\"items\":null,\"url_success\":\"https:\\\/\\\/galaxycloud.vn\\\/test\\\/baokim2021\\\/demo-ok1.php\",\"url_cancel\":null,\"url_detail\":\"https:\\\/\\\/www.baokim.vn\",\"stat\":\"c\",\"lang\":\"vi\",\"type\":1,\"bpm_id\":297,\"accept_qrpay\":1,\"accept_bank\":1,\"accept_cc\":1,\"email\":\"admin@glx.com.vn\",\"name\":\"C\\u00d4NG TY TNHH C\\u00d4NG NGH\\u1ec6 S\\u1ed0 GALAXY VI\\u1ec6T NAM\",\"webhooks\":\"https:\\\/\\\/galaxycloud.vn\\\/tool\\\/billing\\\/baokim.html\",\"customer_name\":\"Kh\\u00e1ch h\\u00e0ng BK\",\"customer_email\":\"dungla2011@gmail.com\",\"customer_phone\":\"84902066768\",\"customer_address\":\"102 Th\\u00e1i Th\\u1ecbnh, Qu\\u1eadn \\u0110\\u1ed1ng \\u0110a, H\\u00e0 N\\u1ed9i.\",\"created_at\":\"2021-09-01 11:11:56\",\"updated_at\":\"2021-09-01 11:12:29\"},\"txn\":{\"id\":107552,\"reference_id\":\"BK_330571_107552_4JC\",\"user_id\":10848938,\"merchant_id\":35589,\"order_id\":330571,\"mrc_order_id\":\"4sh.1630469510\",\"total_amount\":10088,\"amount\":10000,\"fee_amount\":0,\"bank_fee_amount\":88,\"bank_fix_fee_amount\":0,\"fee_payer\":1,\"bank_fee_payer\":1,\"auth_code\":null,\"auth_time\":\"\",\"ref_no\":null,\"bpm_id\":297,\"bank_ref_no\":\"BK_330571_107552_4JC\",\"bpm_type\":13,\"stat\":1,\"gateway\":\"MSB\",\"description\":\"M\\u00f4 t\\u1ea3 \\u0111\\u01a1n h\\u00e0ng glx\",\"customer_email\":\"dungla2011@gmail.com\",\"customer_phone\":\"84902066768\",\"init_token\":null,\"completed_at\":\"2021-09-01 11:12:29\",\"created_at\":\"2021-09-01 11:12:05\",\"updated_at\":\"2021-09-01 11:12:29\",\"deleted_at\":null},\"dataToken\":[],\"sign\":\"7fca724be7c9e15807795961471344d7bdd1bfb58e105c57a231e999836b6b93\"}";
            if (isset($input->order) && isset($input->order->id)) {

                $ip = request()->getClientIp();

                ol00("-------------------------");
                ol00("$ip , INPUTBK = $jsonWebhookData");

                $order = $input->order;

                if (isset($order->mrc_order_id)) {
//Xác minh signature trên webhook với PHP
//Decode dữ liệu webhook notification nhận được từ Bảo Kim
//$jsonWebhookData = '{"order":{order data},"txn":{txn data},"sign":"baokim sign"}';
                    $webhookData = json_decode($jsonWebhookData, true);
//Get và remove trường sign ra khỏi dữ liệu
                    $baokimSign = $webhookData['sign'];
                    unset($webhookData['sign']);

//Chuyển dữ liệu đã remove sign về lại dạng json và sử dụng thuật toán hash sha256 để tạo signature với secret key
                    $signData = json_encode($webhookData);
                    $secret = \BaoKimAPI2021::API_SECRET;
                    $mySign = hash_hmac('sha256', $signData, $secret);

//So sánh chữ ký bạn tạo ra với chữ ký bảo kim gửi sang, nếu khớp thì verify thành công
                    if ($baokimSign == $mySign) {
                        ol00("signature ok!");
                    } else {
                        loi("signature not valid!");
                    }
                    $mrc_order_id = $order->mrc_order_id;
                    $prodId = explode("-", $mrc_order_id)[1];

                    $uid = explode(".", $mrc_order_id)[0];

                    if(!is_numeric($uid))
                        loi("Not valid uid: $uid");

                    if(!User::find($uid))
                        loi("Not found uid: $uid");

                    if (!$prodId || !is_numeric($prodId)) {
                        loi("Not valid product id1?");
                    }
                    if (!$prod = \App\Models\Product::find($prodId)) {
                        loi("Not found product Id2!");
                    }

                    $transIdRemote = $order->id;
                    $total_amount = intval($order->total_amount);

                    $orderStd = new \stdClass();
                    $orderStd->user_id = $uid;
                    $orderStd->transaction_id_local = $mrc_order_id;
                    $orderStd->money = $total_amount;
                    $orderStd->transaction_id_remote = $transIdRemote;

//                    \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($uid, $mrc_order_id, $total_amount, $prod, $transIdRemote, request()->getClientIp());
                    \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($orderStd, $prod, request()->getClientIp(), \clsBaoKim::$name);
                    ol00(" Done OrderId: $mrc_order_id, $transIdRemote, $total_amount");

                } else {
                    loi("Some error?");
                }
            }
            else{
                die("Not input pr?");
            }
            die("{\"err_code\": \"0\", \"message\": \"Done Transaction!\"}");

        } catch (\Throwable $e) { // For PHP 7
            ol00("***Error: " . $e->getMessage());
            die("{\"err_code\": \"101\", \"message\": \"".$e->getMessage()."\"}");
        } catch (\Exception $e) {
            ol00("***Error: " . $e->getMessage());
            die("{\"err_code\": \"101\", \"message\": \"".$e->getMessage()."\"}");
        }
    }
}
