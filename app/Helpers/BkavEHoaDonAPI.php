<?php
/**
 * Script PHP để lấy danh sách hoá đơn từ Bkav eHoadon API
 * Sử dụng CmdType 853 để lấy danh sách hoá đơn theo ngày
 */

namespace App\Helpers;


?>


<?php
/**
 * Script PHP để lấy danh sách hoá đơn từ Bkav eHoadon API
 * Sử dụng CmdType 853 để lấy danh sách hoá đơn theo ngày
 */

class BkavEHoaDonAPI {
    private $partner_guid;
    private $partner_token;
    private $api_url;
    private $key;
    private $iv;

    public function __construct() {
        // Load .env file
        // $this->loadEnv(__DIR__ . '/.env');

        $this->partner_guid = env('PARTNER_GUID');
        $this->partner_token = env('PARTNER_TOKEN');
        $this->api_url = env('API_URL', 'https://ws.ehoadon.vn/WSPublicEHoaDon.asmx');

        // Parse token để lấy key và iv
        $token_parts = explode(':', $this->partner_token);
        $this->key = base64_decode($token_parts[0]);
        $this->iv = base64_decode($token_parts[1]);
    }

    /**
     * Load environment variables từ .env file
     */
    private function loadEnv($path) {
        if (!file_exists($path)) {
            throw new Exception("File .env không tìm thấy: $path");
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') === false || strpos($line, '#') === 0) {
                continue;
            }
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value, " \t\"'");
            putenv("$key=$value");
        }
    }

    /**
     * Hàm debug output - Chỉ in ra CLI, không in ra Web
     */
    private function echoDebug($message) {
        if (php_sapi_name() === 'cli') {
            echo $message;
        }
    }

    /**
     * Mã hóa dữ liệu theo chuẩn: JSON → Gzip → AES-256 → Base64
     */
    public function encryptData($data) {
        // Bước 1: Convert sang JSON string
        $json_data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $json_bytes = $json_data;

        $this->echoDebug("→ Encrypt Step 1 - JSON length: " . strlen($json_bytes) . " bytes\n");

        // Bước 2: Gzip compression
        $compressed = gzencode($json_bytes, 9);
        $this->echoDebug("→ Encrypt Step 2 - Gzip compressed length: " . strlen($compressed) . " bytes\n");

        // Bước 3: Padding và AES-256 encryption
        $padded_data = $this->pkcs7Pad($compressed, 16);
        $this->echoDebug("→ Encrypt Step 3 - Padded length: " . strlen($padded_data) . " bytes\n");

        $encrypted = openssl_encrypt($padded_data, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);
        $this->echoDebug("→ Encrypt Step 4 - AES encrypted length: " . strlen($encrypted) . " bytes\n");

        // Bước 4: Base64 encode
        $result = base64_encode($encrypted);
        $this->echoDebug("→ Encrypt Step 5 - Base64 encoded length: " . strlen($result) . " chars\n");

        return $result;
    }

    /**
     * Giải mã dữ liệu trả về
     */
    public function decryptData($encrypted_data) {
        try {
            // Kiểm tra xem có phải là thông báo lỗi không mã hóa không
            if (is_string($encrypted_data) &&
                (strpos($encrypted_data, 'Có lỗi') !== false ||
                 strpos($encrypted_data, 'Error') !== false ||
                 strpos($encrypted_data, '[!|') !== false)) {
                $this->echoDebug("⚠ Warning: Response contains error message (not encrypted)\n");
                return json_encode([
                    "Status" => 1,
                    "Object" => $encrypted_data,
                    "isOk" => false,
                    "isError" => true
                ]);
            }

            $this->echoDebug("→ Decrypting with AES-256-CBC...\n");
            $this->echoDebug("→ Key length: " . strlen($this->key) . " bytes\n");
            $this->echoDebug("→ IV length: " . strlen($this->iv) . " bytes\n");

            $decrypted = openssl_decrypt(base64_decode($encrypted_data), 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);

            $this->echoDebug("→ Decrypted raw bytes length: " . strlen($decrypted) . "\n");
            $this->echoDebug("→ First 50 bytes (hex): " . substr(bin2hex($decrypted), 0, 100) . "\n");

            // Kiểm tra xem có phải dữ liệu gzip không (magic number: 1f8b) - TRƯỚC khi unpad
            if (strlen($decrypted) >= 2 && substr($decrypted, 0, 2) === "\x1f\x8b") {
                $this->echoDebug("→ Data is gzip compressed, decompressing...\n");
                $decompressed = @gzdecode($decrypted);
                if ($decompressed === false) {
                    $this->echoDebug("→ Gzip decompression failed, trying unpad first...\n");
                    // Thử unpad trước khi decompress
                    try {
                        $unpadded = $this->pkcs7Unpad($decrypted);
                        $this->echoDebug("→ Unpadded successfully (PKCS7)\n");
                        $this->echoDebug("→ Unpadded length: " . strlen($unpadded) . "\n");
                        $decompressed = @gzdecode($unpadded);
                        if ($decompressed === false) {
                            $this->echoDebug("→ Gzip decompression still failed after unpad\n");
                            return json_encode([
                                "Status" => 1,
                                "Object" => "Gzip decompression failed",
                                "isOk" => false
                            ]);
                        }
                    } catch (Exception $e) {
                        $this->echoDebug("→ Unpad also failed: " . $e->getMessage() . "\n");
                        return json_encode([
                            "Status" => 1,
                            "Object" => "Data decompress/unpad failed",
                            "isOk" => false
                        ]);
                    }
                }
                $decrypted = $decompressed;
                $this->echoDebug("→ Decompressed length: " . strlen($decrypted) . "\n");
            } else {
                // Không phải gzip, thử unpad bình thường
                try {
                    $decrypted = $this->pkcs7Unpad($decrypted);
                    $this->echoDebug("→ Unpadded successfully (PKCS7)\n");
                    $this->echoDebug("→ Unpadded length: " . strlen($decrypted) . "\n");
                } catch (Exception $e) {
                    $this->echoDebug("→ PKCS7 unpad failed: " . $e->getMessage() . ", trying manual padding removal\n");
                    $decrypted = rtrim($decrypted, "\x00");
                }
            }

            // Kiểm tra xem dữ liệu có phải PDF nhị phân không (magic number: %PDF)
            if (substr($decrypted, 0, 4) === "%PDF") {
                $this->echoDebug("→ Data is PDF binary\n");
                return $decrypted;
            }

            $this->echoDebug("→ Decoded to UTF-8 successfully\n");

            return $decrypted;
        } catch (Exception $e) {
            $this->echoDebug("\n✗ Decryption Error!\n");
            $this->echoDebug("→ Error type: " . get_class($e) . "\n");
            $this->echoDebug("→ Error message: " . $e->getMessage() . "\n");
            $this->echoDebug("→ Encrypted data (first 100 chars): " . substr($encrypted_data, 0, 100) . "\n");
            throw $e;
        }
    }

    /**
     * PKCS7 Padding
     */
    private function pkcs7Pad($data, $block_size = 16) {
        $pad_len = $block_size - (strlen($data) % $block_size);
        return $data . str_repeat(chr($pad_len), $pad_len);
    }

    /**
     * PKCS7 Unpadding
     */
    private function pkcs7Unpad($data) {
        $pad_len = ord($data[strlen($data) - 1]);
        if ($pad_len > 0 && $pad_len <= 16) {
            return substr($data, 0, -$pad_len);
        }
        throw new Exception("Invalid PKCS7 padding");
    }

    /**
     * Gọi API ExecCommand
     */
    public function callApi($command_data) {
        $this->echoDebug(str_repeat("=", 80) . "\n");
        $this->echoDebug("DEBUG: API CALL INFORMATION\n");
        $this->echoDebug(str_repeat("=", 80) . "\n");
        $this->echoDebug("→ API URL: {$this->api_url}\n");
        $this->echoDebug("→ Partner GUID: {$this->partner_guid}\n");

        $this->echoDebug("\n→ Command Data (Input):\n");
        $this->echoDebug(json_encode($command_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n");
        $this->echoDebug("→ CmdType: " . $command_data['CmdType'] . "\n");

        // Mã hóa dữ liệu
        $encrypted_data = $this->encryptData($command_data);
        
        $this->echoDebug("\n→ Encrypted Data Length: " . strlen($encrypted_data) . " chars\n");
        $this->echoDebug("→ Encrypted Data (first 100 chars): " . substr($encrypted_data, 0, 100) . "...\n");

        // Tạo SOAP request
        $soap_body = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
               xmlns:xsd="http://www.w3.org/2001/XMLSchema"
               xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
    <soap:Body>
        <ExecCommand xmlns="http://tempuri.org/">
            <partnerGUID>{$this->partner_guid}</partnerGUID>
            <CommandData>{$encrypted_data}</CommandData>
        </ExecCommand>
    </soap:Body>
</soap:Envelope>
XML;

        $this->echoDebug("\n→ SOAP Request Body Length: " . strlen($soap_body) . " chars\n");

        $headers = [
            'Content-Type: text/xml; charset=utf-8',
            'SOAPAction: http://tempuri.org/ExecCommand'
        ];

        $this->echoDebug("→ Headers: " . json_encode($headers) . "\n");
        $this->echoDebug("\n→ Sending request...\n");

        // Gửi request
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $soap_body);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $this->echoDebug("\n→ Response Status Code: {$http_code}\n");
        $this->echoDebug("→ Response Length: " . strlen($response) . " chars\n");
        $this->echoDebug("→ Response (first 500 chars):\n" . substr($response, 0, 500) . "\n");

        if ($http_code == 200) {
            return $this->parseResponse($response);
        } else {
            $this->echoDebug("\n✗ API Error Response:\n{$response}\n");
            throw new Exception("API Error: $http_code");
        }
    }

    /**
     * Parse XML response và giải mã dữ liệu
     */
    public function parseResponse($xml_response) {
        $this->echoDebug("\n" . str_repeat("-", 80) . "\n");
        $this->echoDebug("DEBUG: PARSING RESPONSE\n");
        $this->echoDebug(str_repeat("-", 80) . "\n");

        try {
            $xml = simplexml_load_string($xml_response);
            $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
            $xml->registerXPathNamespace('ns', 'http://tempuri.org/');

            $results = $xml->xpath('.//ns:ExecCommandResult');

            if (count($results) > 0 && !empty($results[0])) {
                $result = (string)$results[0];

                $this->echoDebug("→ ExecCommandResult found\n");
                $this->echoDebug("→ Result text length: " . strlen($result) . " chars\n");
                $this->echoDebug("→ Result text (first 200 chars): " . substr($result, 0, 200) . "\n");

                $this->echoDebug("\n→ Decrypting data...\n");
                $decrypted = $this->decryptData($result);
                
                $this->echoDebug("→ Decrypted data length: " . strlen($decrypted) . " chars\n");
                $this->echoDebug("→ Decrypted data (first 500 chars): " . substr($decrypted, 0, 500) . "\n");

                $parsed = json_decode($decrypted, true);
                
                $this->echoDebug("→ JSON parsed successfully\n");
                $this->echoDebug("→ Status: " . ($parsed['Status'] ?? 'N/A') . "\n");
                $this->echoDebug("→ isOk: " . (($parsed['isOk'] ?? false) ? 'true' : 'false') . "\n");
                $this->echoDebug("→ isError: " . (($parsed['isError'] ?? false) ? 'true' : 'false') . "\n");

                return $parsed;
            } else {
                $this->echoDebug("✗ ExecCommandResult not found in response\n");
                throw new Exception("Không tìm thấy kết quả trong response");
            }
        } catch (Exception $e) {
            $this->echoDebug("✗ Parse Error: " . $e->getMessage() . "\n");
            throw $e;
        }
    }

    /**
     * Lấy danh sách hoá đơn theo ngày - CmdType 853
     */
    public function getInvoicesByDate($from_date, $to_date, $page_number = 1) {
        $command_data = [
            "CmdType" => 853,
            "CommandObject" => [
                "InvoiceDateFrom" => $from_date,
                "InvoiceDateTo" => $to_date,
                "PageNumber" => $page_number
            ]
        ];

        return $this->callApi($command_data);
    }

    /**
     * Lấy hoá đơn theo mẫu số, ký hiệu - CmdType 810
     */
    public function getInvoiceBySerial($invoice_form, $invoice_serial, $from_no, $to_no) {
        $command_data = [
            "CmdType" => 810,
            "CommandObject" => [
                "InvoiceForm" => $invoice_form,
                "InvoiceSerial" => $invoice_serial,
                "FromInvoiceNo" => $from_no,
                "ToInvoiceNo" => $to_no
            ]
        ];

        return $this->callApi($command_data);
    }

    /**
     * Lấy XML hoá đơn - CmdType 813 (lấy file XML theo MTC)
     */
    public function getInvoiceXml($mtc) {
        $command_data = [
            "CmdType" => 813,
            "CommandObject" => $mtc  // MTC là string trực tiếp
        ];

        return $this->callApi($command_data);
    }

    /**
     * Lấy PDF hoá đơn - CmdType 811
     */
    public function getInvoicePdf($mtc) {
        $command_data = [
            "CmdType" => 811,
            "CommandObject" => $mtc  // MTC là string trực tiếp, không phải object
        ];

        return $this->callApi($command_data);
    }

    /**
     * Lấy danh sách hoá đơn theo mã số thuế trong 1 khoảng thời gian
     *
     * @param string $tax_code Mã số thuế cần lấy
     * @param string $from_date Ngày bắt đầu (Y-m-d)
     * @param string $to_date Ngày kết thúc (Y-m-d)
     * @return array Danh sách hoá đơn hoặc lỗi
     */
    public function getInvoicesInPeriod($tax_code, $from_date, $to_date) {
        $all_invoices = [];
        $page = 1;

        echo "\n→ Lấy hoá đơn MST=$tax_code từ $from_date đến $to_date\n";

        while (true) {
            $result = $this->getInvoicesByDate($from_date, $to_date, $page);

            if (!$result['isOk'] || $result['Status'] != 0) {
                throw new Exception("Lỗi khi lấy danh sách hoá đơn (trang $page): " . ($result['Object'] ?? 'Unknown'));
            }

            $invoices = $result['Object'];
            if (is_string($invoices)) {
                $invoices = json_decode($invoices, true);
            }

            if (!is_array($invoices) || count($invoices) === 0) {
                break;
            }

            // Lọc theo mã số thuế
            $filtered = array_filter($invoices, function($inv) use ($tax_code) {
                $inv_data = $inv['Invoice'] ?? [];
                $buyer_tax = $inv_data['BuyerTaxCode'] ?? '';
                return $buyer_tax === $tax_code;
            });

            $all_invoices = array_merge($all_invoices, $filtered);
            echo "  Trang $page: " . count($invoices) . " hoá đơn, " . count($filtered) . " khớp MST (tổng: " . count($all_invoices) . ")\n";

            if (count($invoices) < 30) {
                break;
            }

            $page++;
        }

        return $all_invoices;
    }

    /**
     * Tải PDF hoá đơn từ danh sách gần nhất
     *
     * @param int|array $count_or_invoices Số lượng hoá đơn hoặc danh sách hoá đơn để tải
     * @param string $output_dir Thư mục để lưu PDF
     * @param string $filename_pattern Pattern tên file. Placeholders: {serial}, {number}, {mtc}, {date}
     * @param bool $getCache Có sử dụng cache không? (true = sử dụng, false = tải lại)
     * @return array Kết quả gồm ['success' => số thành công, 'error' => số lỗi, 'files' => danh sách file]
     */
    public function downloadInvoicePdfs($count_or_invoices = 10, $output_dir = 'invoices_pdf', $filename_pattern = '{serial}_{number}_{mtc}.pdf', $getCache = true) {
        $result = [
            'success' => 0,
            'error' => 0,
            'files' => [],
            'invoices' => []
        ];

        try {
            // Xác định danh sách hoá đơn để tải
            if (is_array($count_or_invoices)) {
                // Nếu input là mảng, dùng nó trực tiếp
                $invoices_to_download = $count_or_invoices;
                echo "\n→ Tải " . count($invoices_to_download) . " hoá đơn từ danh sách\n";
            } else {
                // Nếu input là số, lấy $count hoá đơn gần nhất từ tất cả các page
                $count = intval($count_or_invoices);
                echo "\n→ Bước 1: Lấy danh sách hoá đơn gần nhất...\n";

                $to_date = date('Y-m-d');
                $from_date = date('Y-m-d', strtotime('-2 years'));
//                $from_date = date('Y-m-d', strtotime('-2 months'));

                $invoices = [];
                $page = 1;

                // Loop qua tất cả các page
                while (true) {
                    echo "  Đang lấy trang $page...\n";
                    $api_result = $this->getInvoicesByDate($from_date, $to_date, $page);

                    if (!$api_result['isOk'] || $api_result['Status'] != 0) {
                        throw new Exception("Lỗi khi lấy danh sách hoá đơn (trang $page): " . ($api_result['Object'] ?? 'Unknown'));
                    }

                    $page_invoices = $api_result['Object'];
                    if (is_string($page_invoices)) {
                        $page_invoices = json_decode($page_invoices, true);
                    }

                    if (!is_array($page_invoices) || count($page_invoices) === 0) {
                        echo "  Kết thúc tại trang $page (không có dữ liệu)\n";
                        break;
                    }

                    echo "  Trang $page: " . count($page_invoices) . " hoá đơn\n";
                    $invoices = array_merge($invoices, $page_invoices);

                    // Nếu đã đủ $count hoá đơn, dừng
                    if (count($invoices) >= $count) {
                        echo "  Đã lấy đủ $count hoá đơn, dừng\n";
                        break;
                    }

                    // Nếu trang này có ít hơn 30 item, tức là page cuối
                    if (count($page_invoices) < 30) {
                        echo "  Trang $page có ít hơn 30 item, đây là page cuối\n";
                        break;
                    }

                    $page++;

                    // An toàn: không loop quá 100 page
                    if ($page > 100) {
                        echo "  ⚠ Đã lấy 100 page, dừng để an toàn\n";
                        break;
                    }
                }

                if (empty($invoices)) {
                    throw new Exception("Không tìm thấy hoá đơn");
                }

                echo "✓ Tìm thấy tổng " . count($invoices) . " hoá đơn\n";

                // Sắp xếp hoá đơn từ mới nhất đến cũ nhất (reverse)
                usort($invoices, function($a, $b) {
                    $date_a = strtotime($a['Invoice']['InvoiceDate'] ?? '1970-01-01');
                    $date_b = strtotime($b['Invoice']['InvoiceDate'] ?? '1970-01-01');
                    return $date_b - $date_a; // Mới nhất trên cùng
                });

                echo "✓ Sắp xếp theo ngày (mới nhất trên cùng)\n";

                // Lấy $count hoá đơn đầu tiên (đã sắp xếp từ mới → cũ)
                $invoices_to_download = array_slice($invoices, 0, $count);
            }

            $result['invoices'] = $invoices_to_download;

            echo "\n→ Sẽ tải " . count($invoices_to_download) . " hoá đơn:\n";
            foreach ($invoices_to_download as $idx => $inv_data) {
                $invoice = $inv_data['Invoice'] ?? [];
                $serial = $invoice['InvoiceSerial'] ?? 'N/A';
                $no = $invoice['InvoiceNo'] ?? 'N/A';
                $date = substr($invoice['InvoiceDate'] ?? '', 0, 10);
                $buyer = $invoice['BuyerUnitName'] ?? 'N/A';
                echo "  " . ($idx + 1) . ". $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . " ($date) - $buyer\n";
            }

            // Bước 2: Tạo thư mục để lưu PDF
            echo "\n→ Bước 2: Chuẩn bị thư mục lưu trữ...\n";

            if (!is_dir($output_dir)) {
                mkdir($output_dir, 0755, true);
                echo "✓ Tạo thư mục: $output_dir\n";
            } else {
                echo "✓ Thư mục đã tồn tại: $output_dir\n";
            }

            // Bước 3: Tải PDF từng hoá đơn
            echo "\n→ Bước 3: Tải PDF...\n";

            foreach ($invoices_to_download as $idx => $inv_data) {
                $invoice = $inv_data['Invoice'] ?? [];
                $serial = $invoice['InvoiceSerial'] ?? '';
                $no = $invoice['InvoiceNo'] ?? '';
                $mtc = $invoice['InvoiceCode'] ?? '';
                $date = substr($invoice['InvoiceDate'] ?? '', 0, 10);

                // Kiểm tra MTC hợp lệ
                if (empty($mtc)) {
                    echo "  ⚠ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": Không có MTC (hoá đơn chưa ký?), bỏ qua\n";
                    $result['error']++;
                    continue;
                }

                if (!preg_match('/^[A-Z0-9]+$/', $mtc)) {
                    echo "  ⚠ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": MTC không hợp lệ ($mtc), bỏ qua\n";
                    $result['error']++;
                    continue;
                }

                try {
                    // Gọi API tải PDF
                    $pdf_result = $this->getInvoicePdf($mtc);

                    if ($pdf_result['isOk'] && $pdf_result['Status'] == 0) {
                        $pdf_data = $pdf_result['Object'];

                        // Object có thể là JSON string, cần parse nó
                        if (is_string($pdf_data)) {
                            $pdf_json = json_decode($pdf_data, true);
                            if ($pdf_json && isset($pdf_json['PDF'])) {
                                $pdf_data = $pdf_json['PDF'];
                            }
                        }

                        // Giải mã base64
                        if (is_string($pdf_data)) {
                            $pdf_binary = base64_decode($pdf_data, true);
                        } else {
                            $pdf_binary = $pdf_data;
                        }

                        // Kiểm tra kết quả
                        if ($pdf_binary === false || empty($pdf_binary)) {
                            echo "  ✗ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": PDF data decode failed\n";
                            $result['error']++;
                            continue;
                        }

                        // Kiểm tra xem có phải file PDF hay không
                        if (substr($pdf_binary, 0, 4) !== "%PDF") {
                            echo "  ✗ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": Not a valid PDF file\n";
                            $result['error']++;
                            continue;
                        }

                        // Tạo tên file từ pattern
                        $filename = str_replace(
                            ['{serial}', '{number}', '{mtc}', '{date}'],
                            [$serial, str_pad($no, 4, '0', STR_PAD_LEFT), $mtc, $date],
                            $filename_pattern
                        );

                        $filepath = $output_dir . '/' . $filename;

                        // Kiểm tra cache: nếu file đã tồn tại và $getCache = true, skip
                        if ($getCache && file_exists($filepath)) {
                            $file_size = filesize($filepath);
                            $file_size_kb = round($file_size / 1024, 2);
                            echo "  ↻ [" . ($idx + 1) . "] $filename ($file_size_kb KB) - đã tồn tại\n";
                            echo "     Path: $filepath\n";
                            $result['success']++;
                            $result['files'][] = [
                                'filename' => $filename,
                                'path' => $filepath,
                                'size' => $file_size,
                                'size_kb' => $file_size_kb,
                                'cached' => true
                            ];
                            continue;
                        }

                        file_put_contents($filepath, $pdf_binary);

                        $file_size = filesize($filepath);
                        $file_size_kb = round($file_size / 1024, 2);

                        echo "  ✓ [" . ($idx + 1) . "] $filename ($file_size_kb KB)\n";
                        $result['success']++;
                        $result['files'][] = [
                            'filename' => $filename,
                            'path' => $filepath,
                            'size' => $file_size,
                            'size_kb' => $file_size_kb,
                            'cached' => false
                        ];
                    } else {
                        $error = $pdf_result['Object'] ?? 'Unknown error';
                        echo "  ✗ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": $error\n";
                        $result['error']++;
                    }
                } catch (Exception $e) {
                    echo "  ✗ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": " . $e->getMessage() . "\n";
                    $result['error']++;
                }
            }

        } catch (Exception $e) {
            echo "\n✗ Lỗi: " . $e->getMessage() . "\n";
            $result['error'] = is_array($count_or_invoices) ? count($count_or_invoices) : $count_or_invoices;
        }

        return $result;
    }

    /**
     * Tải XML hoá đơn từ danh sách
     *
     * @param int|array $count_or_invoices Số lượng hoá đơn hoặc danh sách hoá đơn để tải
     * @param string $output_dir Thư mục để lưu XML
     * @param string $filename_pattern Pattern tên file. Placeholders: {serial}, {number}, {mtc}, {date}
     * @param bool $getCache Có sử dụng cache không? (true = sử dụng, false = tải lại)
     * @return array Kết quả gồm ['success' => số thành công, 'error' => số lỗi, 'files' => danh sách file]
     */
    public function downloadInvoiceXmls($count_or_invoices = 10, $output_dir = 'invoices_xml', $filename_pattern = '{serial}_{number}_{mtc}.xml', $getCache = true) {
        $result = [
            'success' => 0,
            'error' => 0,
            'files' => [],
            'invoices' => []
        ];

        try {
            // Xác định danh sách hoá đơn để tải
            if (is_array($count_or_invoices)) {
                $invoices_to_download = $count_or_invoices;
                echo "\n→ Tải " . count($invoices_to_download) . " hoá đơn từ danh sách\n";
            } else {
                $count = intval($count_or_invoices);
                echo "\n→ Bước 1: Lấy danh sách hoá đơn gần nhất...\n";

                $to_date = date('Y-m-d');
                $from_date = date('Y-m-d', strtotime('-2 years'));
//                $from_date = date('Y-m-d', strtotime('-2 months'));

                $invoices = [];
                $page = 1;

                // Loop qua tất cả các page
                while (true) {
                    echo "  Đang lấy trang $page...\n";
                    $api_result = $this->getInvoicesByDate($from_date, $to_date, $page);

                    if (!$api_result['isOk'] || $api_result['Status'] != 0) {
                        throw new Exception("Lỗi khi lấy danh sách hoá đơn (trang $page): " . ($api_result['Object'] ?? 'Unknown'));
                    }

                    $page_invoices = $api_result['Object'];
                    if (is_string($page_invoices)) {
                        $page_invoices = json_decode($page_invoices, true);
                    }

                    if (!is_array($page_invoices) || count($page_invoices) === 0) {
                        echo "  Kết thúc tại trang $page (không có dữ liệu)\n";
                        break;
                    }

                    echo "  Trang $page: " . count($page_invoices) . " hoá đơn\n";
                    $invoices = array_merge($invoices, $page_invoices);

                    // Nếu đã đủ $count hoá đơn, dừng
                    if (count($invoices) >= $count) {
                        echo "  Đã lấy đủ $count hoá đơn, dừng\n";
                        break;
                    }

                    // Nếu trang này có ít hơn 30 item, tức là page cuối
                    if (count($page_invoices) < 30) {
                        echo "  Trang $page có ít hơn 30 item, đây là page cuối\n";
                        break;
                    }

                    $page++;

                    // An toàn: không loop quá 100 page
                    if ($page > 100) {
                        echo "  ⚠ Đã lấy 100 page, dừng để an toàn\n";
                        break;
                    }
                }

                if (empty($invoices)) {
                    throw new Exception("Không tìm thấy hoá đơn");
                }

                echo "✓ Tìm thấy tổng " . count($invoices) . " hoá đơn\n";

                // Sắp xếp hoá đơn từ mới nhất đến cũ nhất (reverse)
                usort($invoices, function($a, $b) {
                    $date_a = strtotime($a['Invoice']['InvoiceDate'] ?? '1970-01-01');
                    $date_b = strtotime($b['Invoice']['InvoiceDate'] ?? '1970-01-01');
                    return $date_b - $date_a; // Mới nhất trên cùng
                });

                echo "✓ Sắp xếp theo ngày (mới nhất trên cùng)\n";

                $invoices_to_download = array_slice($invoices, 0, $count);
            }

            $result['invoices'] = $invoices_to_download;

            echo "\n→ Sẽ tải " . count($invoices_to_download) . " hoá đơn:\n";
            foreach ($invoices_to_download as $idx => $inv_data) {
                $invoice = $inv_data['Invoice'] ?? [];
                $serial = $invoice['InvoiceSerial'] ?? 'N/A';
                $no = $invoice['InvoiceNo'] ?? 'N/A';
                $date = substr($invoice['InvoiceDate'] ?? '', 0, 10);
                $buyer = $invoice['BuyerUnitName'] ?? 'N/A';
                echo "  " . ($idx + 1) . ". $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . " ($date) - $buyer\n";
            }

            // Bước 2: Tạo thư mục để lưu XML
            echo "\n→ Bước 2: Chuẩn bị thư mục lưu trữ...\n";

            if (!is_dir($output_dir)) {
                mkdir($output_dir, 0755, true);
                echo "✓ Tạo thư mục: $output_dir\n";
            } else {
                echo "✓ Thư mục đã tồn tại: $output_dir\n";
            }

            // Bước 3: Tải XML từng hoá đơn
            echo "\n→ Bước 3: Tải XML...\n";

            foreach ($invoices_to_download as $idx => $inv_data) {
                $invoice = $inv_data['Invoice'] ?? [];
                $serial = $invoice['InvoiceSerial'] ?? '';
                $no = $invoice['InvoiceNo'] ?? '';
                $mtc = $invoice['InvoiceCode'] ?? '';
                $date = substr($invoice['InvoiceDate'] ?? '', 0, 10);

                if (empty($mtc)) {
                    echo "  ⚠ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": Không có MTC (hoá đơn chưa ký?), bỏ qua\n";
                    $result['error']++;
                    continue;
                }

                if (!preg_match('/^[A-Z0-9]+$/', $mtc)) {
                    echo "  ⚠ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": MTC không hợp lệ ($mtc), bỏ qua\n";
                    $result['error']++;
                    continue;
                }

                try {
                    // Gọi API tải XML
                    $xml_result = $this->getInvoiceXml($mtc);

                    if ($xml_result['isOk'] && $xml_result['Status'] == 0) {
                        $xml_data = $xml_result['Object'];

                        // Object có thể là JSON string
                        if (is_string($xml_data)) {
                            $xml_json = json_decode($xml_data, true);
                            if ($xml_json && isset($xml_json['XML'])) {
                                $xml_data = $xml_json['XML'];
                            }
                        }

                        // XML có thể được mã hóa Base64
                        if (is_string($xml_data)) {
                            $xml_content = base64_decode($xml_data, true);
                            if ($xml_content === false) {
                                $xml_content = $xml_data;
                            }
                        } else {
                            $xml_content = $xml_data;
                        }

                        // Kiểm tra kết quả
                        if (empty($xml_content)) {
                            echo "  ✗ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": XML data empty\n";
                            $result['error']++;
                            continue;
                        }

                        // Kiểm tra xem có phải XML hay không
                        if (strpos($xml_content, '<?xml') === false && strpos($xml_content, '<') === false) {
                            echo "  ✗ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": Not a valid XML file\n";
                            $result['error']++;
                            continue;
                        }

                        // Tạo tên file từ pattern
                        $filename = str_replace(
                            ['{serial}', '{number}', '{mtc}', '{date}'],
                            [$serial, str_pad($no, 4, '0', STR_PAD_LEFT), $mtc, $date],
                            $filename_pattern
                        );

                        $filepath = $output_dir . '/' . $filename;

                        // Kiểm tra cache: nếu file đã tồn tại và $getCache = true, skip
                        if ($getCache && file_exists($filepath)) {
                            $file_size = filesize($filepath);
                            $file_size_kb = round($file_size / 1024, 2);
                            echo "  ↻ [" . ($idx + 1) . "] $filename ($file_size_kb KB) - đã tồn tại\n";
                            echo "     Path: $filepath\n";
                            $result['success']++;
                            $result['files'][] = [
                                'filename' => $filename,
                                'path' => $filepath,
                                'size' => $file_size,
                                'size_kb' => $file_size_kb,
                                'cached' => true
                            ];
                            continue;
                        }

                        file_put_contents($filepath, $xml_content);

                        $file_size = filesize($filepath);
                        $file_size_kb = round($file_size / 1024, 2);

                        echo "  ✓ [" . ($idx + 1) . "] $filename ($file_size_kb KB)\n";
                        $result['success']++;
                        $result['files'][] = [
                            'filename' => $filename,
                            'path' => $filepath,
                            'size' => $file_size,
                            'size_kb' => $file_size_kb,
                            'cached' => false
                        ];
                    } else {
                        $error = $xml_result['Object'] ?? 'Unknown error';
                        echo "  ✗ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": $error\n";
                        $result['error']++;
                    }
                } catch (Exception $e) {
                    echo "  ✗ [" . ($idx + 1) . "] $serial-" . str_pad($no, 4, '0', STR_PAD_LEFT) . ": " . $e->getMessage() . "\n";
                    $result['error']++;
                }
            }

        } catch (Exception $e) {
            echo "\n✗ Lỗi: " . $e->getMessage() . "\n";
            $result['error'] = is_array($count_or_invoices) ? count($count_or_invoices) : $count_or_invoices;
        }

        return $result;
    }

    /**
     * Phân tích file XML hoá đơn để lấy thông tin cần thiết
     *
     * @param string $xml_file Đường dẫn đến file XML
     * @return array Thông tin hoá đơn được extract
     */
    public function parseInvoiceXml($xml_file) {
        if (!file_exists($xml_file)) {
            return [
                'success' => false,
                'error' => "File không tồn tại: $xml_file"
            ];
        }

        try {
            $xml_content = file_get_contents($xml_file);
            $xml = simplexml_load_string($xml_content);

            if ($xml === false) {
                return [
                    'success' => false,
                    'error' => 'Không thể parse XML'
                ];
            }

            // Lấy thông tin từ TTChung (Thông tin chung)
            $tt_chung = $xml->DLHDon->TTChung ?? null;
            $nd_hdon = $xml->DLHDon->NDHDon ?? null;
            $t_toan = $xml->DLHDon->NDHDon->TToan ?? null;

            // Thông tin cơ bản
            $serial = (string)($tt_chung->KHHDon ?? '');
            $so_hdon = (string)($tt_chung->SHDon ?? '');
            $ngay_lap = (string)($tt_chung->NLap ?? '');
            $loai_hdon = (string)($tt_chung->THDon ?? '');

            // Thông tin người bán
            $nban_ten = (string)($nd_hdon->NBan->Ten ?? '');
            $nban_mst = (string)($nd_hdon->NBan->MST ?? '');
            $nban_dchi = (string)($nd_hdon->NBan->DChi ?? '');

            // Thông tin người mua
            $nmua_ten = (string)($nd_hdon->NMua->Ten ?? '');
            $nmua_mst = (string)($nd_hdon->NMua->MST ?? '');
            $nmua_dchi = (string)($nd_hdon->NMua->DChi ?? '');

            // Thông tin tiền tệ
            $tien_te = (string)($tt_chung->DVTTe ?? 'VND');
            $ty_gia = (string)($tt_chung->TGia ?? '1');

            // Thông tin tổng tiền
            $tg_tc_thue = (string)($t_toan->TgTCThue ?? '0');
            $tg_t_thue = (string)($t_toan->TgTThue ?? '0');
            $tg_ttb_so = (string)($t_toan->TgTTTBSo ?? '0');
            $ttc_t_mai = (string)($t_toan->TTCKTMai ?? '0');

            // Mã CQT
            $mccqt = (string)($xml->DLHDon->MCCQT ?? '');

            // Hình thức thanh toán
            $httt = (string)($tt_chung->HTTToan ?? '');

            // Danh sách hàng hóa
            $items = [];
            $dsh_hhdv = $xml->DLHDon->NDHDon->DSHHDVu->HHDVu ?? null;
            if ($dsh_hhdv !== null) {
                if (is_object($dsh_hhdv)) {
                    $dsh_hhdv = [$dsh_hhdv];
                }

                foreach ($dsh_hhdv as $item) {
                    $items[] = [
                        'stt' => (string)($item->STT ?? ''),
                        'ten' => (string)($item->THHDVu ?? ''),
                        'sluong' => (string)($item->SLuong ?? ''),
                        'don_gia' => (string)($item->DGia ?? ''),
                        'thanh_tien' => (string)($item->ThTien ?? ''),
                        'thue_suat' => (string)($item->TSuat ?? '')
                    ];
                }
            }

            // Kiểm tra xem hoá đơn đã ký hay chưa
            $da_ky = $this->checkInvoiceSignatureStatus($xml);

            return [
                'success' => true,
                'filename' => basename($xml_file),
                'file_path' => $xml_file,
                'da_ky' => $da_ky,
                'thong_tin_chung' => [
                    'serial' => $serial,
                    'so_hdon' => $so_hdon,
                    'ngay_lap' => $ngay_lap,
                    'loai_hdon' => $loai_hdon,
                    'hthuc_thanh_toan' => $httt,
                    'tien_te' => $tien_te,
                    'ty_gia' => $ty_gia,
                    'mccqt' => $mccqt
                ],
                'nguoi_ban' => [
                    'ten' => $nban_ten,
                    'mst' => $nban_mst,
                    'dia_chi' => $nban_dchi
                ],
                'nguoi_mua' => [
                    'ten' => $nmua_ten,
                    'mst' => $nmua_mst,
                    'dia_chi' => $nmua_dchi
                ],
                'tong_tien' => [
                    'tong_tien_chiu_thue' => $tg_tc_thue,
                    'tong_tien_thue' => $tg_t_thue,
                    'tong_cong_thanh_toan' => $tg_ttb_so,
                    'ttck_t_mai' => $ttc_t_mai
                ],
                'hang_hoa_dich_vu' => $items
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => "Lỗi parse XML: " . $e->getMessage()
            ];
        }
    }

    /**
     * Kiểm tra xem hoá đơn đã ký hay chưa
     * Dựa vào: có DLQRCode (QR code), DLKy (chữ ký điện tử) hay không
     */
    private function checkInvoiceSignatureStatus($xml) {
        // Cách 1: Check xem có DLQRCode (QR code chứng tỏ đã ký)
        $dl_qrcode = $xml->DLHDon->DLQRCode ?? null;
        if ($dl_qrcode !== null) {
            $qrcode_value = (string)$dl_qrcode;
            if (!empty($qrcode_value)) {
                return true; // Đã ký
            }
        }

        // Cách 2: Check xem có chữ ký điện tử (signature data)
        $dl_ky = $xml->DLHDon->DLKy ?? null;
        if ($dl_ky !== null) {
            $ky_value = (string)$dl_ky;
            if (!empty($ky_value)) {
                return true; // Đã ký
            }
        }

        // Mặc định: chưa ký
        return false;
    }

    /**
     * Phân tích tất cả XML trong 1 thư mục
     *
     * @param string $directory Đường dẫn thư mục chứa XML
     * @return array Danh sách thông tin từ tất cả XML files
     */
    public function parseInvoicesXmlFromDirectory($directory) {
        if (!is_dir($directory)) {
            return [
                'success' => false,
                'error' => "Thư mục không tồn tại: $directory"
            ];
        }

        $results = [];
        $xml_files = glob($directory . '/*.xml');

        if (count($xml_files) === 0) {
            return [
                'success' => false,
                'error' => "Không tìm thấy file XML nào trong: $directory"
            ];
        }

        echo "Đang phân tích " . count($xml_files) . " file XML...\n\n";

        foreach ($xml_files as $xml_file) {
            $parsed = $this->parseInvoiceXml($xml_file);
            if ($parsed['success']) {
                $results[] = $parsed;
            }
        }

        return [
            'success' => true,
            'total_files' => count($xml_files),
            'total_parsed' => count($results),
            'invoices' => $results
        ];
    }
}

/**
 * Main function để demo lấy danh sách hoá đơn
 */

?>
