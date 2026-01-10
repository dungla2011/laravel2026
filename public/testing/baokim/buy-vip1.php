<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

use App\Helpers\BaoKimPaymentHelper;

require __DIR__.'/../../../vendor/autoload.php';
$app = require_once __DIR__.'/../../../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Lấy tất cả parameters từ request
$params = $request->all();

// Gọi hàm xử lý nạp tiền BaoKim
try {
    $uid = getCurrentUserId();
    $domain = \LadLib\Common\UrlHelper1::getDomainHostName();

    if (!$mrc_order_id = ($params['mrc_order_id'] ?? '')) {
        bl("Not valid?");
        die();
    }

    // Lấy amount_money từ request
    if (!$amount_money = ($params['total_amount'] ?? '')) {
        bl("Không có số tiền nạp?");
        die();
    }

    $amount_money = floatval($amount_money);
    if ($amount_money < 5000) {
        bl("Số tiền nạp tối thiểu là 5,000 VNĐ");
        die();
    }

    $siteId = \App\Models\SiteMng::getSiteId();
    $keyCache = "deposit_money.$siteId.".$mrc_order_id;
    BaoKimPaymentHelper::log("-------------- ");
    BaoKimPaymentHelper::log(" mrc_order_id = $mrc_order_id");
    BaoKimPaymentHelper::log(" amount_money = $amount_money");

    //Nếu url forward từ bk về:
    //Thanh toán xong, trở lại url:
    if (isset($params['created_at'])) {
        // Set page info
        $pageTitle = "Kết Quả Thanh Toán";
        $pageDescription = "Thông tin chi tiết giao dịch nạp tiền";
        
        // Include header
        include __DIR__.'/layout_header.php';
        
        // Gọi hàm xử lý thanh toán chung
        $result = BaoKimPaymentHelper::processPaymentSuccess($params);
        ?>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="content-card text-center">
                    <?php if ($result['success']): ?>
                        <?php 
                        $data = $result['data'];
                        $orderID = $data['order_id'];
                        $total_amount = $data['amount'];
                        ?>
                        
                        <?php if ($result['duplicate'] ?? false): ?>
                            <div class="icon-large">
                                <i class="fas fa-info-circle text-warning"></i>
                            </div>
                            <h2 class="mb-4">Giao Dịch Đã Xử Lý</h2>
                            <div class="alert alert-warning alert-custom">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Giao dịch này đã được xử lý trước đó
                            </div>
                        <?php else: ?>
                            <div class="icon-large">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <h2 class="mb-4">Nạp Tiền Thành Công!</h2>
                            <div class="alert alert-success alert-custom">
                                <i class="fas fa-check"></i> 
                                Giao dịch của bạn đã được xử lý thành công
                            </div>
                        <?php endif; ?>
                        
                        <div class="info-box">
                            <div class="row">
                                <div class="col-md-6 text-start">
                                    <strong><i class="fas fa-receipt"></i> Mã đơn hàng:</strong>
                                </div>
                                <div class="col-md-6 text-end">
                                    <?php echo htmlspecialchars($orderID); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="amount-display">
                            <i class="fas fa-coins"></i> 
                            <?php echo number_format($total_amount, 0, ',', '.'); ?> VNĐ
                        </div>
                        
                        <div class="mt-4">
                            <a href="/" class="btn-primary-custom">
                                <i class="fas fa-home"></i> Trở Lại Trang Chủ
                            </a>
                        </div>
                        
                    <?php else: ?>
                        <div class="icon-large">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <h2 class="mb-4">Có Lỗi Xảy Ra</h2>
                        <div class="alert alert-danger alert-custom">
                            <i class="fas fa-exclamation-circle"></i> 
                            <?php echo htmlspecialchars($result['message']); ?>
                        </div>
                        <div class="mt-4">
                            <a href="/" class="btn-primary-custom">
                                <i class="fas fa-home"></i> Trở Lại
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(isDebugIp()): ?>
                    <div class="debug-info mt-4 text-start">
                        <strong>Debug Info:</strong>
                        <pre><?php print_r($params); ?></pre>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php
        include __DIR__.'/layout_footer.php';
        die();
        
    } else {
        // Trang tạo đơn hàng mới
        $pageTitle = "Xác Nhận Thanh Toán";
        $pageDescription = "Vui lòng xác nhận thông tin trước khi thanh toán";
        
        include __DIR__.'/layout_header.php';

        $client = new \GuzzleHttp\Client(['timeout' => 20.0]);
        $options['query']['jwt'] = \BaoKimAPI2021::getToken();
        $total_amount = $amount_money;
        $options['form_params'] = [
            'mrc_order_id' => $params['mrc_order_id'],
            'total_amount' => $total_amount,
            'description' => $params['description'] ?? "Nạp tiền {$total_amount} VNĐ",
            'url_success' => 'https://' . $domain . '/testing/baokim/buy-vip1.php',
            'merchant_id' => '35589',
            'customer_email' => $params['customer_email'],
            'customer_phone' => $params['customer_phone'],
            'webhooks' => "https://$domain/testing/baokim/webhook1.php"
        ];

        $response = $client->request("POST", "https://api.baokim.vn/payment/api/v5/order/send", $options);
        $dataResponse = json_decode($response->getBody()->getContents());
        
        if (!isset($dataResponse->data)) {
            if($dataResponse->code ?? ''){
                if($dataResponse->code == 7){
                    $link = \Illuminate\Support\Facades\Cache::get($keyCache);
                    header("Location: $link");
                    die();
                }
            }
            ?>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="content-card text-center">
                        <div class="icon-large">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <h2 class="mb-4">Có Lỗi Xảy Ra</h2>
                        <div class="alert alert-danger alert-custom">
                            <?php echo htmlspecialchars(serialize($dataResponse->message)); ?>
                        </div>
                        <a href="shop.html" class="btn-primary-custom">
                            <i class="fas fa-arrow-left"></i> Trở Lại
                        </a>
                    </div>
                </div>
            </div>
            <?php
        } else if (isset($dataResponse->data->order_id)) {
            if (isset($dataResponse->data->payment_url)) {
                $linkBK = $dataResponse->data->payment_url;
                $total_amountV = number_format($total_amount, 0, ',', '.');

                // Tạo cache để lưu lại link thanh toán
                \Illuminate\Support\Facades\Cache::put($keyCache, $linkBK, 60 * 20);
                ?>
                
                <div class="row justify-content-center">
                    <div class="col-md-8">
                        <div class="content-card text-center">
                            <div class="icon-large">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>
                            <h2 class="mb-4">Xác Nhận Nạp Tiền</h2>
                            
                            <div class="info-box">
                                <p class="mb-2"><strong>Bạn đang nạp:</strong></p>
                                <div class="amount-display">
                                    <?php echo $total_amountV; ?> VNĐ
                                </div>
                                <p class="text-muted mb-0">vào tài khoản của bạn</p>
                            </div>
                            
                            <?php if (!auth()->id()): ?>
                                <div class="alert alert-warning alert-custom">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Bạn cần <a href="/login" class="alert-link">Đăng nhập</a> để nạp tiền
                                </div>
                            <?php else: ?>
                                <div class="mt-4">
                                    <a href="<?php echo $linkBK; ?>" class="btn-primary-custom btn-lg">
                                        <i class="fas fa-credit-card"></i> Tiếp Tục Thanh Toán
                                    </a>
                                </div>
                                <p class="text-muted mt-3">
                                    <small><i class="fas fa-shield-alt"></i> Thanh toán an toàn qua BaoKim</small>
                                </p>
                            <?php endif; ?>
                            
                            <?php if(isDebugIp()): ?>
                            <div class="debug-info mt-4 text-start">
                                <strong>Debug Info:</strong>
                                <pre><?php print_r($params); ?></pre>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <?php
            }
        }
        
        include __DIR__.'/layout_footer.php';
        die();
    }

} catch (Throwable $e) {
    $pageTitle = "Có Lỗi";
    $pageDescription = "Đã xảy ra lỗi trong quá trình xử lý";
    include __DIR__.'/layout_header.php';
    ?>
    
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="content-card text-center">
                <div class="icon-large">
                    <i class="fas fa-exclamation-triangle text-danger"></i>
                </div>
                <h2 class="mb-4">Có Lỗi Xảy Ra</h2>
                <div class="alert alert-danger alert-custom">
                    <?php echo htmlspecialchars($e->getMessage()); ?>
                </div>
                <a href="shop.html" class="btn-primary-custom">
                    <i class="fas fa-arrow-left"></i> Trở Lại
                </a>
                
                <?php if(isDebugIp()): ?>
                <div class="debug-info mt-4 text-start">
                    <strong>Stack Trace:</strong>
                    <pre><?php 
                    $strTrace = $e->getTraceAsString();
                    $m1 = explode("\n", $strTrace);
                    foreach ($m1 AS $line){
                        if(str_contains($line, '/vendor/'))
                            continue;
                        echo $line . "\n";
                    }
                    ?></pre>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php
    include __DIR__.'/layout_footer.php';
}
