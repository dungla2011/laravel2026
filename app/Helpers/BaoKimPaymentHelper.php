<?php


namespace App\Helpers;

/**
 * Helper class để xử lý thanh toán BaoKim
 */
class BaoKimPaymentHelper
{
    /**
     * Ghi log cho BaoKim
     */
    public static function log($msg)
    {
        $logFile = "/var/glx/weblog/baokim_debug.log";
        $dateTime = date("Y-m-d H:i:s");
        file_put_contents($logFile, "[$dateTime] $msg\n", FILE_APPEND);
    }

    /**
     * Xử lý thanh toán thành công từ BaoKim
     * 
     * @param array $params - Tham số từ BaoKim callback
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public static function processPaymentSuccess($params)
    {
        try {
            // 1. Lấy checksum và loại bỏ khỏi params để verify
            $checksum = $params['checksum'] ?? '';
            $query = $params;
            unset($query['checksum']);

            $orderID = $params['mrc_order_id'] ?? '';
            $total_amount = floatval($params['total_amount'] ?? 0);
            $transId = $params['id'] ?? '';

            if (!$orderID || !$total_amount || !$transId) {
                self::log("❌ Missing required params: orderID=$orderID, total_amount=$total_amount, transId=$transId");
                return [
                    'success' => false,
                    'message' => 'Thiếu thông tin thanh toán'
                ];
            }

            // 2. Sort array các tham số theo key
            ksort($query);

            // 3. Verify checksum
            $myChecksum = hash_hmac('sha256', http_build_query($query), \BaoKimAPI2021::API_SECRET);
            
            if ($checksum != $myChecksum) {
                self::log("❌ Checksum not match: received=$checksum, calculated=$myChecksum");
                return [
                    'success' => false,
                    'message' => 'Checksum không hợp lệ'
                ];
            }

            // 4. Lấy user ID từ order ID
            $uid = explode(".", $orderID)[0];

            // 5. Kiểm tra xem transaction đã được xử lý chưa (tránh duplicate)
            $existingRecharge = \App\Models\UserRecharge::where('transaction_code', $transId)->first();

            if ($existingRecharge) {
                self::log("⚠️ Duplicate transaction detected: $transId - Already processed");
                return [
                    'success' => true,
                    'message' => 'Giao dịch đã được xử lý trước đó',
                    'duplicate' => true,
                    'data' => [
                        'recharge_id' => $existingRecharge->id,
                        'user_id' => $uid,
                        'amount' => $total_amount,
                        'order_id' => $orderID,
                        'transaction_id' => $transId
                    ]
                ];
            }

            // 6. Tạo record nạp tiền
            $recharge = \App\Models\UserRecharge::create([
                'user_id' => $uid,
                'amount' => $total_amount,
                'payment_method' => 'baokim',
                'transaction_code' => $transId,
                'mrc_order_id' => $orderID,
                'status' => 'completed',
                'paid_at' => now(),
                'completed_at' => now(),
                'gateway_response' => json_encode($params),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null
            ]);

            self::log("✅ Created UserRecharge ID: {$recharge->id} - User $uid - Amount: $total_amount - OrderID: {$orderID}");

            return [
                'success' => true,
                'message' => 'Nạp tiền thành công',
                'duplicate' => false,
                'data' => [
                    'recharge_id' => $recharge->id,
                    'user_id' => $uid,
                    'amount' => $total_amount,
                    'order_id' => $orderID,
                    'transaction_id' => $transId
                ]
            ];

        } catch (\Exception $e) {
            self::log("❌ Error processing payment: " . $e->getMessage());
            self::log("❌ Stack trace: " . $e->getTraceAsString());
            
            return [
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate callback từ BaoKim webhook
     * 
     * @param array $params - POST data từ webhook
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public static function processWebhookCallback($params)
    {
        try {
            self::log("=== WEBHOOK CALLBACK START ===");
            self::log("Params: " . json_encode($params));

            $orderID = $params['mrc_order_id'] ?? '';
            $total_amount = floatval($params['total_amount'] ?? 0);
            $transId = $params['id'] ?? '';
            $stat = $params['stat'] ?? '';

            if (!$orderID || !$total_amount || !$transId) {
                self::log("❌ Missing required params");
                return [
                    'success' => false,
                    'message' => 'Missing required parameters'
                ];
            }

            // Chỉ xử lý khi thanh toán thành công (stat = 'c')
            if ($stat !== 'c') {
                self::log("⚠️ Payment not completed. Status: $stat");
                return [
                    'success' => false,
                    'message' => "Payment status: $stat"
                ];
            }

            // Gọi hàm xử lý chung - params đã có checksum sẵn
            $result = self::processPaymentSuccess($params);

            self::log("=== WEBHOOK CALLBACK END === Result: " . json_encode($result));

            return $result;

        } catch (\Exception $e) {
            self::log("❌ Webhook error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Webhook processing error: ' . $e->getMessage()
            ];
        }
    }
}
