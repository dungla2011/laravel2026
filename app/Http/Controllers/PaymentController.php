<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Models\Payment;

class PaymentController extends BaseController
{
    protected Payment $data;

    public function __construct(Payment $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    public function tree_index()
    {
        $objMeta = $this->data::getMetaObj();

        return view('admin.default-tree', compact('objMeta'));
    }


    /**
     * Display deposit form (shop.html equivalent)
     */
    public function depositForm()
    {
        return view('public.deposit');
    }

    /**
     * Process deposit payment (buy-vip1.php equivalent)
     */
    public function depositPayment()
    {
        $params = request()->all();

        // Load BaoKim Payment Helper
        $helper = \App\Helpers\BaoKimPaymentHelper::class;

        try {
            $uid = getCurrentUserId();
            $domain = \LadLib\Common\UrlHelper1::getDomainHostName();

            if (!$mrc_order_id = ($params['mrc_order_id'] ?? '')) {
                return response('Not valid order ID', 400);
            }

            // Lấy amount_money từ request
            if (!$amount_money = ($params['total_amount'] ?? '')) {
                return response('Không có số tiền nạp', 400);
            }

            $amount_money = floatval($amount_money);
            if ($amount_money < 5000) {
                return response('Số tiền nạp tối thiểu là 5,000 VNĐ', 400);
            }

            $siteId = \App\Models\SiteMng::getSiteId();
            $keyCache = "deposit_money.$siteId.".$mrc_order_id;
            $helper::log("-------------- ");
            $helper::log(" mrc_order_id = $mrc_order_id");
            $helper::log(" amount_money = $amount_money");

            // Nếu là callback từ BaoKim về (có created_at)
            if (isset($params['created_at'])) {
                // Xử lý thanh toán thành công
                $result = $helper::processPaymentSuccess($params);
                
                return view('public.deposit_result', [
                    'result' => $result,
                    'params' => $params
                ]);
            } else {
                // Tạo đơn hàng mới với BaoKim
                $client = new \GuzzleHttp\Client(['timeout' => 20.0]);
                $options['query']['jwt'] = \BaoKimAPI2021::getToken();
                $total_amount = $amount_money;
                $options['form_params'] = [
                    'mrc_order_id' => $params['mrc_order_id'],
                    'total_amount' => $total_amount,
                    'description' => $params['description'] ?? "Nạp tiền {$total_amount} VNĐ",
                    'url_success' => 'https://' . $domain . '/deposit/payment',
                    'merchant_id' => '35589',
                    'customer_email' => $params['customer_email'],
                    'customer_phone' => $params['customer_phone'],
                    'webhooks' => "https://$domain/deposit/webhook"
                ];

                $response = $client->request("POST", "https://api.baokim.vn/payment/api/v5/order/send", $options);
                $dataResponse = json_decode($response->getBody()->getContents());
                
                if (!isset($dataResponse->data)) {
                    if($dataResponse->code ?? ''){
                        if($dataResponse->code == 7){
                            $link = \Illuminate\Support\Facades\Cache::get($keyCache);
                            return redirect($link);
                        }
                    }
                    
                    return view('public.deposit_error', [
                        'message' => $dataResponse->message ?? 'Unknown error'
                    ]);
                }
                
                if (isset($dataResponse->data->order_id) && isset($dataResponse->data->payment_url)) {
                    $linkBK = $dataResponse->data->payment_url;
                    $total_amountV = number_format($total_amount, 0, ',', '.');

                    // Tạo cache để lưu lại link thanh toán
                    \Illuminate\Support\Facades\Cache::put($keyCache, $linkBK, 60 * 20);
                    
                    return view('public.deposit_confirm', [
                        'linkBK' => $linkBK,
                        'total_amount' => $total_amount,
                        'total_amountV' => $total_amountV,
                        'params' => $params
                    ]);
                }
            }

        } catch (\Throwable $e) {
            $helper::log("❌ Payment exception: " . $e->getMessage());
            
            return view('public.deposit_error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Handle BaoKim webhook callback (webhook1..php equivalent)
     */
    public function depositWebhook()
    {
        $params = request()->all();
        $helper = \App\Helpers\BaoKimPaymentHelper::class;

        $helper::log("=== WEBHOOK RECEIVED ===");
        $helper::log("Request method: " . request()->method());
        $helper::log("Params: " . json_encode($params));

        try {
            // Xử lý webhook callback từ BaoKim
            $result = $helper::processWebhookCallback($params);

            if ($result['success']) {
                return response()->json([
                    'code' => 0,
                    'message' => 'OK',
                    'data' => $result['data'] ?? []
                ], 200);
            } else {
                return response()->json([
                    'code' => 1,
                    'message' => $result['message'],
                    'data' => []
                ], 400);
            }

        } catch (\Throwable $e) {
            $helper::log("❌ Webhook exception: " . $e->getMessage());
            $helper::log("Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'code' => 2,
                'message' => 'Internal server error',
                'data' => []
            ], 500);
        }
    }
}
