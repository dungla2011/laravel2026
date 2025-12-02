<?php
$uid = getCurrentUserId();
$siteId = \App\Models\SiteMng::getSiteId();
setLogFile("/var/glx/weblog/baokim_$siteId.log");
$params = request()->all();
$domain = \LadLib\Common\UrlHelper1::getDomainHostName();

?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    {{
    \App\Models\SiteMng::getTitle()
    }}
@endsection

@section('meta-description')
    <?php
    \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('content')

    <style>
        .pricing-container {
            background: white;
            padding: 30px 10px;
            border-radius: 12px;
            margin: 20px 0;
            border: 1px solid #e2e8f0;
        }

        .free_count{
            margin: 0px 2px;
            color: white;
            background-color: #ccc;
            border-radius: 5px;
            padding: 1px 8px    ;
        }

        .vip_count{
            margin: 0px 2px;
            border-radius: 5px;
            color: white;
            background-color: orange;
            padding: 1px 8px    ;
        }

        .pricing-header {
            text-align: center;

        }

        .pricing-header h2 {
            font-size: 1.8rem;
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 10px;
        }

        .pricing-header p {
            color: #718096;
            font-size: 1rem;
        }

        .current-limit {
            background: #f7fafc;
            color: #4a5568;
            padding: 8px 16px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 10px;
            font-size: 0.9rem;
            font-weight: 500;
            border: 1px solid #e2e8f0;
        }

        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 15px;
            /*max-width: 1200px;*/
            margin: 0 auto;
        }

        .pricing-card {
            background: white;
            border: 1px solid #eee!important;
            border-radius: 12px;
            padding: 20px 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
        }

        .pricing-card:hover {
            border-color: #4a5568;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .pricing-card.selected {
            border-color: #2d3748;
            background: #f7fafc;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .pricing-card.free-card {
            border-color: #cbd5e0;
            background: #f7fafc;
        }

        .pricing-card.free-card:hover {
            border-color: #a0aec0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .pricing-card.popular {
            border-color:orangered;
            position: relative;
            background: #f7fafc;
        }

        .popular-badge {
            position: absolute;
            top: -12px;
            right: 15px;
            background: orange;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }

        .package-icon {
            font-size: 2.5rem;
            text-align: center;
            margin-bottom: 12px;
        }

        .package-name {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2d3748;
            text-align: center;
        }

        .package-price {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2d3748;
            margin-bottom: 5px;
            text-align: center;
        }

        .pricing-card.free-card .package-price {
            color: #4a5568;
        }

        .package-period {
            color: #a0aec0;
            font-size: 0.8rem;
            margin-bottom: 15px;
            text-align: center;
        }

        .package-features {
            list-style: none;
            padding: 0;
            margin: 15px 0 0 0;
        }

        .package-features li {
            padding: 3px 0;
            color: #4a5568;
            font-size: 0.8rem;
            display: flex;
            /*align-items: center;*/
        }

        .package-features li::before {
            content: '✓';
            color: #2d3748;
            font-weight: bold;
            margin-right: 8px;
        }

        .btn-register {
            width: 100%;
            padding: 12px 20px;
            margin: 5px 0;     /* Margin trên dưới */
            background: orange;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-register:hover {
            /*background: #1a202c;*/
            transform: translateY(-2px);
        }

        .pricing-card.free-card .btn-register {
            background: #718096;
        }

        .pricing-card.free-card .btn-register:hover {
            background: #4a5568;
        }

        .pricing-card.popular .btn-register {
            background: royalblue;

        }

        .pricing-card.popular .btn-register:hover {
            background: royalblue;
        }

        .select_product input {
            display: none;
        }

        @media (max-width: 768px) {
            .pricing-grid {
                grid-template-columns: 1fr;
            }

            .pricing-header h2 {
                font-size: 1.5rem;
            }
        }
    </style>

    <div class="container mt-5">

        <div class="pricing-container">
            <div class="pricing-header mb-3" data-code-pos='ppp17600530813801'>
                <h2>{{ __('monitor.choose_package') }}</h2>
                <span data-code-pos='ppp17600608580351'>
                {{ __('monitor.current_limit', ['count' => \App\Models\MonitorUser::getCurrentNumberMonitorAllow($uid)]) }} - {{ __('monitor.upgrade_unlock') }}
                </span>
            </div>

            <div style="font-size: 130%">
                <div class="container pt-3">
                    <?php
                    try{

                        ?>

                    <div style="margin: 0px auto; margin-top: 10px">
                            <?php

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
                                tb(__('monitor.payment_success', ['order_id' => $orderID, 'amount' => $total_amount]), "<a href='/'> " . __('monitor.go_home') . " </a> ");
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
                        <div class='jumbotron' style='max-width: 500px; margin: 20px auto' data-code-pos='ppp17600608935881'>
                            <h4 style='text-align: center'> {{ __('monitor.you_selected_package', ['amount' => $total_amountV]) }} <br> <br>
                                    <?php
                                if (!auth()->id()){
                                    bl(__('monitor.need_login', ['login_link' => '<a href="/login">' . __('monitor.login') . '</a>']));
                                }
                                else{
                                    ?>
                                <a href='{{$linkBK}}'>
                                    <button
                                        style='padding: 10px 20px; border-radius: 5px; background-color: orange; color: white; border: 0px'>
                                        {{ __('monitor.continue') }}
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
                        <form method="post" action="/buy-vip" id="form-action">
                            @csrf
                            <input type="hidden" name="description" value="{{ __('monitor.buy_vip_description') }}" readonly>
                            <input type="hidden" name="customer_email" value="{{ auth()->user()->email ?? '' }}">
                            <input type="hidden" id="customer_phone" name="customer_phone" value='{{ auth()->user()->phone ?? '' }}'>

                            <div class="pricing-grid">
                                <!-- Gói Free - Không submit form -->
                                <div class="pricing-card free-card">
                                    <div class="package-icon">🎁</div>
                                    <h3 class="package-name">{{ __('monitor.free_package', ['count' =>  DEF_MONITOR_DEFAULT_FREE_QUOTA ]) }}</h3>
                                    <div class="package-price">{{ __('monitor.free') }}</div>
                                    <div class="package-period">{{ __('monitor.trial') }}</div>

                                    <button type="button" class="btn-register" onclick="handleFreePackage(event)">
                                        {{ __('monitor.in_use') }}
                                    </button>

                                    <ul class="package-features">
                                        <li>{{ __('monitor.monitors_count', ['count' =>  DEF_MONITOR_DEFAULT_FREE_QUOTA ]) }}</li>
                                        <li> {!!   __('monitor.time_interval_free',['count' => 5]) !!} </li>
                                        <li>{{ __('monitor.ping_monitor') }}</li>
                                        <li>{{ __('monitor.web_monitor') }}</li>
                                        <hr style='margin: 10px 0px'>
                                        <li>{{ __('monitor.email_alert') }}</li>
                                        <li>{{ __('monitor.app_alert') }}</li>
                                        <li style='text-decoration: line-through'>{{ __('monitor.send_consecutive_notification') }} </li>
                                    </ul>
                                </div>

                                <?php
                                $time = time();
                                $m1 = \App\Models\Product::where("status", 1)->orderBy('price', 'asc')->get();
                                $cc = 0;
                                $icons = ['⚡', '🔥', '🔥', '🔥'];

                                foreach ($m1 AS $obj) {
//                                    if ('4s' != ($_COOKIE["refx"] ?? '')) {
//                                        if ($obj->price == 10000)
//                                            continue;
//                                    } else {
//                                        if ($obj->price == 20000)
//                                            continue;
//                                    }

                                    $price = number_formatvn0($obj->price);
                                    $select = '';
                                    if ($cc == 0)
                                        $select = 'checked';

                                    $icon = $icons[$cc] ?? '⭐';
                                    $isPopular = ($cc == 1) ? 'popular' : ''; // Gói thứ 2 là popular

                                    echo "<div class='pricing-card $isPopular' data-card-id='$obj->id' data-pos='09879898677989'>";

                                    if ($isPopular) {
                                        echo "<div class='popular-badge'>" . __('monitor.hot') . "</div>";
                                    }

                                    echo "<div class='package-icon'>$icon</div>";
                                    echo "<h3 class='package-name'>$obj->name</h3>";
                                    echo "<div class='package-price'>$price<sup>đ</sup></div>";
                                    echo "<div class='package-period'>" . __('monitor.one_month') . "</div>";
                                    echo "<input $select id='input_prod_$obj->id' type='radio' name='mrc_order_id' value='$uid.$time-$obj->id'>";
                                    echo "<label for='input_prod_$obj->id' style='display:none;'></label>";
                                    echo "<button type='button' class='btn-register' onclick='selectPackage($obj->id)'>" . __('monitor.register') . "</button>";

                                    echo "<ul class='package-features' data-pos='3475934795'>";
                                    if ($limitNode = $obj->getMonitorLimitNodes()) {
                                        echo "<li  style='color: orange' >". __('monitor.monitors_count', ['count' =>  $limitNode ])  ."</li>";
                                    }
                                    else
                                        echo "<li  style='color: orange'>". __('monitor.monitors_count', ['count' =>  DEF_MONITOR_DEFAULT_FREE_QUOTA ])  ."</li>";
                                    echo "<li>" . __('monitor.time_interval', ['count' => 1]) . "</li>";
                                    echo "<li>" . __('monitor.ping_monitor') . "</li>";
                                    echo "<li>" . __('monitor.web_monitor') . "</li>";
                                    echo "<li>" . __('monitor.content_monitor') . "</li>";
                                    echo "<li>" . __('monitor.port_monitor') . "</li>";
                                    echo "<li>" . __('monitor.database_monitor') . "</li>";
                                    echo "<li>" . __('monitor.ssl_monitor') . "</li>";

                                    echo "<hr style='margin: 10px 0px'>";

                                    echo "<li style='color: orange'>" . __('monitor.send_consecutive_notification') . "</li>";
                                    echo "<li>" . __('monitor.email_alert') . "</li>";
                                    echo "<li>" . __('monitor.app_alert') . "</li>";
                                    echo "<li>" . __('monitor.telegram_alert') . "</li>";
                                    echo "<li>" . __('monitor.web_hook_alert') . "</li>";
                                    echo "<li>" . __('monitor.api_access') . "</li>";

                                    echo "</ul>";

                                    echo "</div>";
                                    $cc++;
                                }
                                ?>
                            </div>
                        </form>
                            <?php
                        }

                            ?>

                    </div>

                        <?php

                    }
                    catch (Throwable $e) { // For PHP 7
                        bl(__('monitor.error_occurred', ['message' => $e->getMessage()]), "<a href='/buy-vip'> " . __('monitor.go_back') . " </a>");
//            echo "<pre>";
//            print_r($e->getTraceAsString());
//            echo "</pre>";
                    } catch (\Exception $e) {
                        bl(__('monitor.error_occurred_2', ['message' => $e->getMessage()]), "<a href='/buy-vip'> " . __('monitor.go_back') . " </a>");
                        {
//                echo "<pre>";
//                print_r($e->getTraceAsString());
//                echo "</pre>";
                        }
                    }
                    ?>

                </div>
            </div>
        </div>
    </div>


    <script>
        // Handle Free package click
        function handleFreePackage(event) {
            event.preventDefault();
            event.stopPropagation();
            alert('{{ __('monitor.free_package_message') }}');
            return false;
        }

        // Handle package selection and submit
        function selectPackage(productId) {
            // Select the radio button
            $('#input_prod_' + productId).prop('checked', true);

            // Highlight the card
            $(".pricing-card:not(.free-card)").removeClass("selected");
            $("[data-card-id='" + productId + "']").addClass("selected");

            // Submit the form
            $('#form-action').submit();
        }

        window.addEventListener('load', function () {
            // Click vào card để highlight (không submit)
            $(".pricing-card:not(.free-card)").on("click", function (e) {
                // Nếu click vào button thì không xử lý ở đây
                if ($(e.target).hasClass('btn-register')) {
                    return;
                }

                // Remove selected class from all paid cards
                $(".pricing-card:not(.free-card)").removeClass("selected");

                // Add selected class to clicked card
                $(this).addClass("selected");

                // Check the radio button inside this card
                $(this).find('input[type="radio"]').prop('checked', true);
            });

            // Auto-select first paid card on load
            $(".pricing-card:not(.free-card)").first().addClass("selected");
            $(".pricing-card:not(.free-card)").first().find('input[type="radio"]').prop('checked', true);
        });
    </script>

@endsection
