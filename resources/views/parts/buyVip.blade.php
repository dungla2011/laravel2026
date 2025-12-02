<style>
    .select_product input {
        display: none;
    }

    .select_product {
        color: royalblue;
        background-color: white;
        display: inline-block;
        padding: 8px 15px;
        margin: 10px;
        /*font-weight: bold;*/
        border: 1px solid royalblue;
        border-radius: 10px;
        font-size: 80%;
    }
</style>
<div class="container pt-3" style="">

    <?php
    try{

        $uid = auth()->id();
//        if(!$uid)
//            loi("Bạn cần <a href='/login'> Đăng nhập </a> để mua gói VIP");

        $uid = getCurrentUserId();
        ?>

    <div style="margin: 0 auto; max-width: auto; text-align: center; margin-top: 10px">

            <?php
            $siteId = \App\Models\SiteMng::getSiteId();

            setLogFile("/var/glx/weblog/baokim_$siteId.log");

            $params = request()->all();
            $domain = \LadLib\Common\UrlHelper1::getDomainHostName();
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($params);
//            echo "</pre>";

            //Post mrc_order_id
        if ($mrc_order_id = ($params['mrc_order_id'] ?? '')){
            ol00("-------------- ");
            ol00(" mrc_order_id1 = $mrc_order_id");
            $prodId = explode("-", $mrc_order_id)[1];
            if (!$prodId || !is_numeric($prodId))
                loi("Not valid product id?");
            if (!$prod = \App\Models\Product::find($prodId))
                loi("Not found product Id!");

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
        if (isset($dataResponse->data)) {
        if (isset($dataResponse->data->order_id)) {
        if (isset($dataResponse->data->payment_url)) {
            $linkBK = $dataResponse->data->payment_url;

            $total_amountV = number_formatvn0($total_amount);
            ?>
        <div class='jumbotron' style='max-width: 500px; margin: 20px auto'>
            <h4 style='text-align: center'> Bạn đang chọn gói {{$total_amountV}} đồng <br> <br>
                    <?php
                if (!auth()->id()){
                    bl("Bạn cần <a href='/login'> Đăng nhập </a> để mua gói VIP");
                }
                else{
                    ?>
                <a href='{{$linkBK}}'>
                    <button
                        style='padding: 10px 20px; border-radius: 5px; background-color: #4b66a9; color: white; border: 0px'>
                        Tiếp tục
                    </button>
                </a>
                    <?php
                }
                    ?>
            </h4>
        </div>
            <?php
        }
        }
        } else {
            loi(serialize($dataResponse->message));
        }
        }
        }else{
            ?>
        <form method="post" action="/buy-vip" id="form-action" onsubmit="">
            <input type="hidden" name="description" value="Mua gói download" readonly>
            <input type="hidden" name="customer_email" value="">
            <input type="hidden" id="customer_phone" name="customer_phone" value=''>

            <h5 style="font-size: 130%; margin-bottom: 20px">
                <b>
                    CHỌN GÓI MUA

                </b>
            </h5>

                <?php
                $time = time();
                $m1 = \App\Models\Product::where("status", 1)->orderBy('price', 'asc')->get();
                $cc = 0;
                foreach ($m1 AS $obj) {

                    if ('4s' != ($_COOKIE["refx"] ?? '')) {
                        if ($obj->price == 10000)
                            continue;
                    } else
                        if ($obj->price == 20000)
                            continue;
                    $cc++;
                    $price = number_formatvn0($obj->price);


                    $select = '';
                    if ($cc == 1)
                        $select = 'checked';
//                    <img src='/images/icon/dot-blink2.gif' style='width: 30px' alt=''>
//                    <br>
                    echo "<div class='select_product'>

                            <input $select id='input_prod_$obj->id' type='radio' name='mrc_order_id' value='$uid.$time-$obj->id'>
                        <label class='' for='input_prod_$obj->id$'>

                          <b>
                          $obj->name
                          </b> ";

//                    if($obj->param1)
//                        echo "<br>Số lượng: <b> $obj->param1 </b>";

                    echo "<br>Giá: <b>$price </b> đồng</label> </div> ";
                }
                ?>

            {{--            <p style="font-size: 80%"> Hết thời hạn, dữ liệu vẫn được giữ nguyên và xem được, nhưng không thêm xóa sửa được cho đến khi mua gói gia hạn! </p>--}}

            <p class="mt-4">
                <button type="submit" class="btn btn-primary pm_submit" name="submit"
                        style=""
                >TIẾP TỤC
                </button>
            </p>
        </form>
            <?php
        }

            ?>

    </div>

        <?php

    }
    catch (Throwable $e) { // For PHP 7
        bl("Có lỗi: " . $e->getMessage(), "<a href='/buy-vip'> Trở lại </a>");
//            echo "<pre>";
//            print_r($e->getTraceAsString());
//            echo "</pre>";
    } catch (\Exception $e) {
        bl("Có lỗi 2: " . $e->getMessage(), "<a href='/buy-vip'> Trở lại </a>");
        {
//                echo "<pre>";
//                print_r($e->getTraceAsString());
//                echo "</pre>";
        }
    }
    ?>

</div>


<script>

    window.addEventListener('load', function () {

        $(".select_product").on("click", function () {
            $('.select_product input:checked').parent().css("color", 'white');
            $(".select_product").css("color", 'royalblue');
            $(".select_product").css("background-color", 'white');
            $(".select_product").find('input').prop('checked', false);

            $(this).css("color", 'white');
            $(this).css("background-color", 'royalblue');
            $(this).find('input').prop('checked', true);
            // $('.select_product input:checked').parent().css("color", 'yellow');
        });

        $('.select_product input:checked').parent().css("background-color", 'royalblue');
        $('.select_product input:checked').parent().css("color", 'white');
    })


</script>
