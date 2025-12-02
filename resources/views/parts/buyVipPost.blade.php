<?php
try {
    $uid = getCurrentUserId();
    $params = request()->all();
    $domain = \LadLib\Common\UrlHelper1::getDomainHostName();

    if (!$mrc_order_id = ($params['mrc_order_id'] ?? ''))
        return;

    $siteId = \App\Models\SiteMng::getSiteId();

    $keyCache = "buy_vip.$siteId.".$mrc_order_id;

    $siteId = \App\Models\SiteMng::getSiteId();
    setLogFile("/var/glx/weblog/baokim_$siteId.log");
    ol00("-------------- ");

//
//    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//    print_r($params);
//    echo "</pre>";
//
//    die();

    ol00(" mrc_order_id = $mrc_order_id");
    $prodId = explode("-", $mrc_order_id)[1];


    if (!$prodId || !is_numeric($prodId))
        loi("Not valid product id?");
    if (!$prod = \App\Models\Product::find($prodId))
        loi("Not found product Id!");

    if($prod->status != 1)
        loi("Product not active: $prodId");

    //Nếu url forward từ bk về:
    //Thanh toán xong, trở lại url:
    if (isset($params['created_at'])) {
        //$urlSuccess = "https://".DOMAIN_MAIN."/?created_at=2021-09-01+10%3A22%3A27&id=330169&mrc_order_id=4sh.3.1630496449.8666&stat=c&total_amount=10000.00&txn_id=107492&updated_at=2021-09-01+10%3A23%3A37&checksum=b33a17d541b16ab118fc4e0fd100e8d9a9fcf98f36bc9ba775f004bf0f9db721";
        $urlSuccess = \LadLib\Common\UrlHelper1::getFullUrl();

        //1. load array các tham số trên url_success,
        // loại bỏ trường checksum cũng như các tham số của merchant (không do bảo kim truyền)
        $parts = parse_url($urlSuccess);
        parse_str($parts['query'], $query);
        $checksum = $query['checksum'];
        unset($query['checksum']);

        $orderID = $params['mrc_order_id'];

        if ($odx = \App\Models\OrderInfo::where('transaction_id_local', $orderID)->first()) {
            ol00("Đơn hàng đã thanh toán thành công: $orderID");
            bl("Đơn hàng đã thanh toán thành công: $orderID", "<a href='/'> TRỞ LẠI </a> ");
            return;
        }

            //2. sort array các tham số theo key
        ksort($query);
        $total_amount = $params['total_amount'];
        if ($prod->price != intval($total_amount)) {
            loi("Price not valid?");
        }

        $uid = explode(".", $mrc_order_id)[0];


        //4. Tạo và so sánh checksum
        $myChecksum = hash_hmac('sha256', http_build_query($query), \BaoKimAPI2021::API_SECRET);
        $transId = $params['id'];
        if ($checksum == $myChecksum) {

//            \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($uid, $orderID, $total_amount, $prod, $transId, request()->getClientIp());
            $orderStd = new \stdClass();
            $orderStd->id = $orderID;
            $orderStd->user_id = $uid;
            $orderStd->transaction_id_local = $mrc_order_id;
            $orderStd->money = $total_amount;
            $orderStd->transaction_id_remote = $transId;

//                \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($uid, $orderID, $total_amount, $prod, $transId, request()->getClientIp());
            \App\Http\Controllers\OrderItemController::addOrderItemFromOrder($orderStd, $prod, request()->getClientIp(), clsBaoKim::$name);

            ol00("DONE BILL: ");
            echo "<br/>\n";
            tb("Đã toán thành công: $orderID , Số tiền : $total_amount", "<a href='/'> TRỞ LẠI </a> ");
            //echo("<h2  style='text-align: center'></a></h2>");
            echo "<br/>\n";
        } else {
            bl("Error: Not valid checksum payment?");
        }
    } else {
        $client = new \GuzzleHttp\Client(['timeout' => 20.0]);
        $options['query']['jwt'] = \BaoKimAPI2021::getToken();
        $total_amount = $prod->price;
        $options['form_params'] = [
            'mrc_order_id' => $params['mrc_order_id'],// . ".$prod",
            'total_amount' => $total_amount,
            'description' => $params['description'],
            'url_success' => 'https://' . $domain . '/buy-vip',
            //      'bpm_id' => '97',
            'merchant_id' => '35589',
            //            'accept_qrpay'=>1,
            'customer_email' => $params['customer_email'],
            'customer_phone' => $params['customer_phone'],
            'webhooks' => "https://$domain/webhookBk"
            //        'customer_name' => 'Nguyen Van A',
            //        'customer_address' => '102, Thái Thịnh, phường Trung Liệt, quận Đống Đa.'
        ];

        //echo '<pre>'.print_r($options, true).'</pre>';die();
        //https://api.baokim.vn/payment/
        $response = $client->request("POST", "https://api.baokim.vn/payment/api/v5/order/send", $options);
        //$response = $client->request("POST", "https://dev-api.baokim.vn/payment/api/v5/order/send", $options);
        $dataResponse = json_decode($response->getBody()->getContents());
        if (!isset($dataResponse->data)) {
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($options);
//            echo "</pre>";
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($dataResponse);
//            echo "</pre>";

            if($dataResponse->code ?? ''){
                if($dataResponse->code == 7){
                    $link = \Illuminate\Support\Facades\Cache::get($keyCache);


                    bl(" <a href='$link' style='color: royalblue'> Tiếp tục Thanh toán: <b> $prod->name </b> </a> hoặc  <a  style='color: royalblue' href='/buy-vip'> Trở lại </a> ");
                    return;
                }
            }

            bl(serialize($dataResponse->message) , " <a href='/buy-vip'> Trở lại </a>");
        }
        else
            if (isset($dataResponse->data->order_id)) {
                if (isset($dataResponse->data->payment_url)) {
                    $linkBK = $dataResponse->data->payment_url;
                    $total_amountV = number_formatvn0($total_amount);

                    echo "<h4 data-code-pos='9834758934785934' style='text-align: center'> Bạn đang chọn gói <b> $prod->name </b> <br> $total_amountV VND </h4>";

                    //Tạo 1 cache để lưu lại thông tin gói vip mà user đang chọn
                    \Illuminate\Support\Facades\Cache::put($keyCache, $linkBK, 60 * 20);

                    if (!auth()->id()) {
                        echo ("Bạn cần <a href='/login'> Đăng nhập </a> để mua gói VIP");
                    } else {
                        echo("<a href='$linkBK' class='btn btn-primary rounded-pill'> Tiếp tục </a>");
                    }
                }
            }

    }


} catch (Throwable $e) { // For PHP 7
    bl("Có lỗi: " . $e->getMessage(), "<a href='/buy-vip'> Trở lại </a>");

    if(isDebugIp()){
        $strTrace = $e->getTraceAsString();
        $m1 = explode("\n", $strTrace);
        if(0){
            echo "\n<div style='text-align: left; font-size: 60%'>";
            foreach ($m1 AS $line){
                if(str_contains($line, '/vendor/'))
                    continue;
                echo "<br/>\n -- $line";
    //        echo "<pre>";
    //        print_r($e->getTraceAsString());
    //        echo "</pre>";
            }
            echo "\n</div>";
        }
    }
}
?>
