<?php

namespace App\Helpers;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;



class ZaloHelper
{
    private $apiUrl;
    private $timeout = 30;
    private $username = null;
    private $password = null;

    /**
     * Constructor
     *
     * @param string $apiUrl - API base URL
     * @param string $username - Username for authentication (optional)
     * @param string $password - Password for authentication (optional)
     */
    public function __construct($apiUrl = 'http://localhost:30000', $username = null, $password = null)
    {
        $this->apiUrl = rtrim($apiUrl, '/');
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Gửi tin nhắn Zalo
     *
     * @param string $accountName - Tên account (ví dụ: 'abc')
     * @param string $threadId - ID nhóm hoặc UID người dùng
     * @param string $message - Nội dung tin nhắn
     * @return array - Response từ API
     */
    public function sendMessage($accountName, $threadId, $message)
    {
        // Validate inputs
        if (empty($accountName) || empty($threadId) || empty($message)) {
            return [
                'success' => false,
                'error' => 'Missing required parameters: accountName, threadId, message'
            ];
        }

        // Build URL
        $url = "{$this->apiUrl}/api/accounts/{$accountName}/send-message/thread/{$threadId}";

        // Prepare request data
        $data = [
            'message' => $message
        ];

        // Send request
        return $this->makeRequest('POST', $url, $data);
    }

    /**
     * Gửi tin nhắn cho nhóm
     *
     * @param string $accountName - Tên account
     * @param string $groupId - ID nhóm
     * @param string $message - Nội dung tin nhắn
     * @return array - Response
     */
    public function sendGroupMessage($accountName, $groupId, $message)
    {
        return $this->sendMessage($accountName, $groupId, $message);
    }

    /**
     * Gửi tin nhắn cho người dùng (1-1)
     *
     * @param string $accountName - Tên account
     * @param string $userId - UID người dùng
     * @param string $message - Nội dung tin nhắn
     * @return array - Response
     */
    public function sendPrivateMessage($accountName, $userId, $message)
    {
        return $this->sendMessage($accountName, $userId, $message);
    }

    /**
     * Make HTTP request using cURL
     *
     * @param string $method - HTTP method (GET, POST, PUT, DELETE)
     * @param string $url - Request URL
     * @param array $data - Request data
     * @return array - Response
     */
    /**
     * Đợi đến khi được phép gửi request (rate limit 10 giây/request, dùng Cache chung giữa các thread).
     * Trả về false nếu chờ quá 3 phút.
     */
    private function waitForRateLimit(): bool
    {
        $cacheKey   = 'zalo_last_request_time';
        $minInterval = 10;      // giây tối thiểu giữa 2 request
        $maxWait     = 180;     // 3 phút timeout
        $poll        = 100000;  // 0.1 giây (microseconds)

        $startWait = microtime(true);

        while (true) {
            $last    = (float) Cache::get($cacheKey, 0);
            $elapsed = microtime(true) - $last;

            if ($elapsed >= $minInterval) {
                // Đánh dấu thời điểm request ngay để thread khác phải chờ
                Cache::put($cacheKey, microtime(true), 300);
                return true;
            }

            if (microtime(true) - $startWait >= $maxWait) {
                Log::warning("ZaloHelper: waitForRateLimit timeout sau {$maxWait}s, bỏ qua request.");
                return false;
            }

            usleep($poll);
        }
    }

    private function makeRequest($method, $url, $data = [])
    {
        if (!$this->waitForRateLimit()) {
            return [
                'success' => false,
                'error'   => 'Zalo rate limit: timeout chờ quá 3 phút, bỏ qua request.'
            ];
        }

        try {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            // Set headers
            $headers = [
                'Content-Type: application/json',
                'Accept: application/json'
            ];

            // Add Basic Authentication if credentials provided
            if (!empty($this->username) && !empty($this->password)) {
                $authHeader = 'Authorization: Basic ' . base64_encode("{$this->username}:{$this->password}");
                $headers[] = $authHeader;
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            // Set request body for POST/PUT
            if (!empty($data) && in_array($method, ['POST', 'PUT'])) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }

            // Execute request
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);

            // Check for cURL errors
            if ($error) {
                return [
                    'success' => false,
                    'error' => "cURL Error: {$error}",
                    'http_code' => $httpCode
                ];
            }

            // Parse response
            $result = json_decode($response, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return [
                    'success' => false,
                    'error' => 'Invalid JSON response',
                    'http_code' => $httpCode,
                    'raw_response' => $response
                ];
            }

            return array_merge($result, ['http_code' => $httpCode]);

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Set custom timeout
     *
     * @param int $timeout - Timeout in seconds
     */
    public function setTimeout($timeout)
    {
        $this->timeout = (int)$timeout;
    }

    /**
     * Get API URL
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->apiUrl;
    }

    /**
     * Set authentication credentials
     *
     * @param string $username
     * @param string $password
     */
    public function setAuth($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Clear authentication credentials
     */
    public function clearAuth()
    {
        $this->username = null;
        $this->password = null;
    }

    /**
     * Tìm user từ số điện thoại
     *
     * @param string $accountName - Tên account
     * @param string $phoneNumber - Số điện thoại (0912345678 hoặc 84912345678)
     * @return array - Response từ API
     */
    public function findUserByPhone($accountName, $phoneNumber)
    {
        // Validate phone
        if (empty($accountName) || empty($phoneNumber)) {
            return [
                'success' => false,
                'error' => 'Missing required parameters: accountName, phoneNumber'
            ];
        }

        // Build URL
        $url = "{$this->apiUrl}/api/accounts/{$accountName}/find-user/{$phoneNumber}";

        // Send GET request
        return $this->makeRequest('GET', $url);
    }

    /**
     * Alias for findUserByPhone
     *
     * @param string $accountName - Tên account
     * @param string $phoneNumber - Số điện thoại
     * @return array - Response
     */
    public function findUser($accountName, $phoneNumber)
    {
        return $this->findUserByPhone($accountName, $phoneNumber);
    }
}
