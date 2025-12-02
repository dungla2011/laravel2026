<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

/**
 * Zalo API Service
 * 
 * Service class để tương tác với Zalo API
 * Có cache, retry logic, và error handling
 */
class ZaloApiService
{
    private $apiUrl;
    private $username;
    private $password;
    private $timeout;
    private $retryTimes;
    private $retryDelay;

    public function __construct()
    {
        $this->apiUrl = env('ZALO_API_URL', 'http://localhost:3000');
        $this->username = env('ZALO_API_USERNAME', 'admin');
        $this->password = env('ZALO_API_PASSWORD', 'admin123');
        $this->timeout = env('ZALO_API_TIMEOUT', 30);
        $this->retryTimes = 2;
        $this->retryDelay = 100;
    }

    /**
     * HTTP Client với auth
     */
    private function client()
    {
        return Http::withBasicAuth($this->username, $this->password)
            ->timeout($this->timeout)
            ->retry($this->retryTimes, $this->retryDelay);
    }

    /**
     * ==========================================
     * ACCOUNT MANAGEMENT
     * ==========================================
     */

    /**
     * Lấy danh sách tất cả accounts
     * Có cache 30 giây
     */
    public function getAllAccounts($useCache = true)
    {
        $cacheKey = 'zalo_accounts_list';
        
        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $response = $this->client()->get("{$this->apiUrl}/api/accounts");
        $data = $response->json();

        if ($useCache && $response->successful()) {
            Cache::put($cacheKey, $data, 30); // Cache 30 seconds
        }

        return $data;
    }

    /**
     * Lấy thông tin 1 account
     */
    public function getAccount($name)
    {
        $response = $this->client()->get("{$this->apiUrl}/api/accounts/{$name}");
        return $response->json();
    }

    /**
     * Tạo account mới
     */
    public function createAccount($name, $dbConfig = null)
    {
        $data = ['name' => $name];
        if ($dbConfig) {
            $data['dbConfig'] = $dbConfig;
        }

        $response = $this->client()->post("{$this->apiUrl}/api/accounts", $data);
        
        // Clear cache
        Cache::forget('zalo_accounts_list');
        
        return $response->json();
    }

    /**
     * Xóa account
     */
    public function deleteAccount($name)
    {
        $response = $this->client()->delete("{$this->apiUrl}/api/accounts/{$name}");
        
        // Clear cache
        Cache::forget('zalo_accounts_list');
        Cache::forget("zalo_account_{$name}");
        
        return $response->json();
    }

    /**
     * ==========================================
     * LOGIN / LOGOUT
     * ==========================================
     */

    /**
     * Tạo QR login
     */
    public function createQrLogin($accountName, $reLogin = false)
    {
        $data = $reLogin ? ['re_login' => true] : [];
        
        $response = $this->client()->post(
            "{$this->apiUrl}/api/accounts/{$accountName}/qr-login",
            $data
        );
        
        return $response->json();
    }

    /**
     * Kiểm tra trạng thái đăng nhập
     */
    public function getLoginStatus($accountName)
    {
        $response = $this->client()->get(
            "{$this->apiUrl}/api/accounts/{$accountName}/login-status"
        );
        
        return $response->json();
    }

    /**
     * Logout account
     */
    public function logout($accountName)
    {
        $response = $this->client()->post(
            "{$this->apiUrl}/api/accounts/{$accountName}/qr-logout"
        );
        
        return $response->json();
    }

    /**
     * Validate credentials
     */
    public function validateCredentials($accountName)
    {
        $response = $this->client()->post(
            "{$this->apiUrl}/api/accounts/{$accountName}/validate-credentials"
        );
        
        return $response->json();
    }

    /**
     * ==========================================
     * LISTENER
     * ==========================================
     */

    /**
     * Bắt đầu lắng nghe tin nhắn
     */
    public function startListening($accountName)
    {
        $response = $this->client()->post(
            "{$this->apiUrl}/api/accounts/{$accountName}/start-listening"
        );
        
        return $response->json();
    }

    /**
     * Dừng lắng nghe
     */
    public function stopListening($accountName)
    {
        $response = $this->client()->post(
            "{$this->apiUrl}/api/accounts/{$accountName}/stop-listening"
        );
        
        return $response->json();
    }

    /**
     * ==========================================
     * USER INFO
     * ==========================================
     */

    /**
     * Lấy thông tin user
     */
    public function getUserInfo($accountName)
    {
        $response = $this->client()->get(
            "{$this->apiUrl}/api/accounts/{$accountName}/user-info"
        );
        
        return $response->json();
    }

    /**
     * ==========================================
     * HELPER METHODS
     * ==========================================
     */

    /**
     * Full login workflow với polling
     * 
     * @param string $accountName
     * @param callable|null $onStatusChange Callback khi status thay đổi
     * @param int $maxAttempts Số lần poll tối đa (default 60)
     * @param int $pollInterval Giây giữa mỗi lần poll (default 3)
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function fullLoginWorkflow(
        $accountName, 
        $onStatusChange = null, 
        $maxAttempts = 60, 
        $pollInterval = 3
    ) {
        // Step 1: Create QR
        $qrResult = $this->createQrLogin($accountName);
        
        if (!($qrResult['success'] ?? false)) {
            return [
                'success' => false,
                'message' => 'Failed to create QR code',
                'data' => $qrResult
            ];
        }

        if (($qrResult['status'] ?? '') === 'already_logged_in') {
            return [
                'success' => true,
                'message' => 'Already logged in',
                'data' => $qrResult
            ];
        }

        Log::info("Zalo: QR created for {$accountName}", [
            'qr_url' => $qrResult['qrCodeFullUrl'] ?? null
        ]);

        // Step 2: Poll status
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            sleep($pollInterval);
            
            $statusResult = $this->getLoginStatus($accountName);
            $status = $statusResult['status'] ?? 'unknown';
            
            Log::debug("Zalo: Login status check", [
                'account' => $accountName,
                'attempt' => $attempt,
                'status' => $status
            ]);

            // Call callback if provided
            if ($onStatusChange && is_callable($onStatusChange)) {
                $onStatusChange($status, $statusResult, $attempt);
            }

            // Check terminal states
            if ($status === 'completed') {
                Log::info("Zalo: Login successful for {$accountName}");
                
                return [
                    'success' => true,
                    'message' => 'Login successful',
                    'data' => $statusResult
                ];
            }

            if (in_array($status, ['failed', 'qr_expired'])) {
                Log::warning("Zalo: Login failed for {$accountName}", [
                    'status' => $status,
                    'message' => $statusResult['message'] ?? 'Unknown error'
                ]);
                
                return [
                    'success' => false,
                    'message' => $statusResult['message'] ?? 'Login failed',
                    'data' => $statusResult
                ];
            }
        }

        // Timeout
        Log::error("Zalo: Login timeout for {$accountName}");
        
        return [
            'success' => false,
            'message' => 'Login timeout',
            'data' => ['attempts' => $maxAttempts]
        ];
    }

    /**
     * Ensure account is logged in and listening
     * 
     * @param string $accountName
     * @param bool $autoLogin Tự động login nếu chưa
     * @param bool $autoListen Tự động start listening nếu chưa
     * @return array ['success' => bool, 'message' => string]
     */
    public function ensureAccountReady($accountName, $autoLogin = true, $autoListen = true)
    {
        // Check account exists
        $accountInfo = $this->getAccount($accountName);
        
        if (!($accountInfo['success'] ?? false)) {
            return [
                'success' => false,
                'message' => 'Account not found',
                'data' => $accountInfo
            ];
        }

        $account = $accountInfo['account'] ?? [];
        $hasCredentials = $account['hasCredentials'] ?? false;
        $isListening = $account['isListening'] ?? false;

        // Check if needs login
        if (!$hasCredentials) {
            if (!$autoLogin) {
                return [
                    'success' => false,
                    'message' => 'Account not logged in',
                    'action_required' => 'login'
                ];
            }

            // Auto login
            Log::info("Zalo: Auto-login triggered for {$accountName}");
            
            $loginResult = $this->fullLoginWorkflow($accountName);
            
            if (!$loginResult['success']) {
                return $loginResult;
            }
        }

        // Check if needs listening
        if (!$isListening && $autoListen) {
            Log::info("Zalo: Auto-start listening for {$accountName}");
            
            $listenResult = $this->startListening($accountName);
            
            if (!($listenResult['success'] ?? false)) {
                return [
                    'success' => false,
                    'message' => 'Failed to start listening',
                    'data' => $listenResult
                ];
            }
        }

        return [
            'success' => true,
            'message' => 'Account ready',
            'hasCredentials' => true,
            'isListening' => true
        ];
    }

    /**
     * Lấy QR image URL
     */
    public function getQrImageUrl($accountName)
    {
        return "{$this->apiUrl}/api/qr/{$accountName}";
    }
}
