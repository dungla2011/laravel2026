<?php
/**
 * Ký Số PDF - Payment Events
 * Parameters: ?evid=6&payment_type=trong_nuoc
 * Integrated with VGCA plugin + WebSocket for signature tracking
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);


require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);



$evid = $_GET['evid'] ?? null;
$payment_type = $_GET['payment_type'] ?? '';

if (!$evid) {
    die('Missing evid parameter');
}
require_once __DIR__ . '/lib_sign.php';
// Load helper functions from shared library
//require_once __DIR__ . '/lib_sign.php';

/**
 * Format signature information for display
 * Returns: (name or reason) - date
 */
function getStringSignShow($sig) {
    $signName = $sig['name'] ?: ($sig['reason'] ?? 'Chữ ký');
    $signDate = $sig['date'] ?? 'N/A';
    return "{$signName} - {$signDate}";
}

// Get PDF file path
$pdfPath = getFileNamePdf($evid, $payment_type);
$pdfName = basename($pdfPath);

// Check if signed version exists, if so use that instead
$signedPdfPath = str_replace('.pdf', '_signed.pdf', $pdfPath);
if (file_exists($signedPdfPath)) {
    $pdfPath = $signedPdfPath;
    $pdfName = basename($pdfPath);
}

// Check if file exists
if (!file_exists($pdfPath)) {
    die('PDF file not found: ' . htmlspecialchars($pdfPath));
}

// Extract signature information from PDF
$signatureInfo = ['signatures' => [], 'message' => 'Chưa có chữ ký'];

try {
    // Use PDFSignatureExtractor library
    // require_once __DIR__ . '/../../../testing/api/PDFSignatureExtractor.php';

    $extractor = new PDFSignatureExtractor($pdfPath);
    $extractedSigs = $extractor->extract();

    foreach ($extractedSigs as $idx => $sig) {
        $signatureInfo['signatures'][] = [
            'name' => $sig['Name'] ?: "Chữ ký #$idx",
            'date' => $sig['Date'] ?? date('d/m/Y H:i', filemtime($pdfPath)),
            'location' => $sig['Location'] ?? 'PDF Document',
            'reason' => $sig['Reason'] ?? 'Digital Signature',
            'contact' => $sig['ContactInfo'] ?? ''
        ];
    }

    if (count($signatureInfo['signatures']) > 0) {
        $signatureInfo['message'] = 'Đã ký số (' . count($signatureInfo['signatures']) . ' chữ ký)';
    }

} catch (Exception $e) {
    error_log("Error extracting signatures: " . $e->getMessage());
    $signatureInfo = [
        'signatures' => [],
        'message' => 'Lỗi đọc chữ ký'
    ];
}
// Handle signing callback from VGCA
if ($_POST['action'] ?? null === 'sign_callback') {
    header('Content-Type: application/json');
    $signStatus = $_POST['signStatus'] ?? 'unknown';
    $fileName = $_POST['fileName'] ?? '';

    if ($signStatus === '0') { // Success
        echo json_encode([
            'success' => true,
            'message' => 'PDF đã được ký số thành công',
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'signed'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Ký số thất bại: ' . $signStatus,
            'timestamp' => date('Y-m-d H:i:s'),
            'status' => 'failed'
        ]);
    }
    exit;
}

// Handle signing action - trigger VGCA plugin
if ($_POST['action'] ?? null === 'sign_pdf') {
    // This will be called via AJAX with callback
    // Just acknowledge the request here
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Khởi động ký số...',
        'timestamp' => date('Y-m-d H:i:s'),
        'evid' => $evid,
        'payment_type' => $payment_type
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ký Số PDF - Event <?php echo htmlspecialchars($evid); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            /* padding: 10px 0; */
        }
        .container-main {
            background: white;
            border-radius: 8px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 10px;
            /* max-width: 1400px; */
            height: calc(100vh - 20px);
            display: flex;
            flex-direction: column;
        }
        .header-section {
            border-bottom: 2px solid #667eea;
            padding-bottom: 8px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }
        .header-section h1 {
            color: #333;
            font-weight: bold;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.1rem;
            flex: 1;
        }
        .header-info {
            display: flex;
            gap: 20px;
            align-items: center;
            font-size: 0.9rem;
            color: #666;
            flex: 1;
        }
        .header-info span {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .header-actions {
            display: flex;
            gap: 8px;
        }
        .btn-sign {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            color: white;
            font-weight: 500;
            cursor: pointer;
            transition: transform 0.2s;
            font-size: 0.9rem;
        }
        .btn-sign:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }
        .btn-back {
            background: #6c757d;
            color: white;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .btn-back:hover {
            background: #5a6268;
            text-decoration: none;
            color: white;
        }
        .iframe-container {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            flex: 1;
            overflow: hidden;
            min-height: 0;
        }
        .iframe-container iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
        .signature-section {
            background: #fff3cd;
            padding: 5px 5px 2px 5px;
            border-radius: 6px;
            margin-bottom: 8px;
            border-left: 4px solid #ffc107;

        }
        .signature-section h6 {
            margin: 0 0 5px 0;
            color: #856404;
            font-size: 0.8rem;
        }
        .signature-item {
            display: inline-block;
            background: white;
            padding: 5px 8px;
            border-radius: 3px;
            margin-bottom: 4px;
            font-size: 0.75rem;
            border-left: 2px solid #ffc107;
            line-height: 1.3;
        }
        @media (max-width: 768px) {
            .header-section {
                flex-direction: column;
                align-items: flex-start;
            }
            .header-info {
                width: 100%;
                flex-wrap: wrap;
            }
            .header-actions {
                width: 100%;
                flex-direction: column;
            }
            .btn-sign, .btn-back {
                width: 100%;
                text-align: center;
            }
        }
        #ws-status {
            animation: pulse 1s infinite;
        }
        #ws-status.connected {
            color: #28a745 !important;
            animation: none;
        }
        #ws-status.error {
            color: #dc3545 !important;
            animation: none;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.6; }
        }
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
        }
        .toast {
            min-width: 300px;
            margin-bottom: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        #signing-status {
            border-radius: 6px;
            font-size: 0.9rem;
        }
        .btn-sign:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <div class="container-fluid container-main">
        <!-- Compact Header -->
        <div class="header-section">
            <h1>
                <i class="bi bi-file-pdf" style="color: #dc3545;"></i>
                <?php echo htmlspecialchars($pdfName); ?>
            </h1>

            <div class="header-info">
                <span>📊 Event: <?php echo htmlspecialchars($evid); ?></span>
                <span>🏷️ <?php echo htmlspecialchars($payment_type ?: 'Tất cả'); ?></span>
                <span>⏰ <?php echo date('d/m/Y H:i', filemtime($pdfPath)); ?></span>
                <span id="ws-status" style="font-size: 0.85rem; color: #999;"></span>
            </div>

            <div class="header-actions">
                <button class="btn-sign" onclick="signPDF()">
                    <i class="bi bi-pen"></i> Ký Số
                </button>
                <a href="/tool1/_site/event_mng/payment/download_payment_event.php?evid=<?php echo urlencode($evid); ?>&payment_type=<?php echo urlencode($payment_type); ?>" class="btn-back">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>

        <!-- Signing Status Alert -->
        <div id="signing-status" class="alert alert-info" style="margin-bottom: 10px; display: none;">
            <i class="bi bi-info-circle"></i>
        </div>

        <!-- Signature Info (List) -->
        <div class="signature-section">
            <i class="bi bi-check2-circle"></i>  <span> Danh sách chữ ký: </span>
            <?php if ($signatureInfo && !empty($signatureInfo['signatures'])): $cc = 1; ?>
                <?php foreach ($signatureInfo['signatures'] as $sig): ?>
                    <div class="signature-item">
                        <?php echo $cc++; echo ". " .  htmlspecialchars(getStringSignShow($sig)); ?>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="signature-item" style="color: #999; font-style: italic;">
                    Chưa có chữ ký
                </div>
            <?php endif; ?>
        </div>

        <!-- PDF Viewer (Main Focus) -->
        <div class="iframe-container">
            <iframe src="/tool1/_site/event_mng/payment/download_payment_file.php?have_sign=1&evid=<?php echo urlencode($evid); ?>&payment_type=<?php echo urlencode($payment_type); ?>&file=pdf"></iframe>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container" id="toastContainer"></div>

    <!-- Bootstrap JS FIRST -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- VGCA Plugin -->
    <script type="text/javascript" src="./vgcaplugin.js"></script>
    <script type="text/javascript" src="./websocket-error-handler.js"></script>

    <script>
    // WebSocket connection variables
    let wsConnection = null;
    let wsReconnectAttempts = 0;
    const wsMaxReconnectAttempts = 5;

    // Initialize WebSocket when page loads
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Page loaded - WebSocket connection temporarily disabled for testing');
        // connectWebSocket();  // TEMPORARILY DISABLED
    });

    // Connect to WebSocket server for signature notifications
    function connectWebSocket() {
        try {
            // Try localhost WebSocket server on port 8765
            wsConnection = new WebSocket('wss://127.0.0.1:8987/SignApproved');

            wsConnection.onopen = () => {
                console.log('✓ WebSocket connected to ws://localhost:8080');
                wsReconnectAttempts = 0;
                updateSigningStatus('connected', 'WebSocket kết nối');

                // Subscribe to signature events
                wsConnection.send(JSON.stringify({
                    type: 'subscribe',
                    channel: 'signatures',
                    evid: '<?php echo $evid; ?>',
                    payment_type: '<?php echo $payment_type; ?>'
                }));
            };

            wsConnection.onmessage = (event) => {
                console.log('📨 WebSocket message:', event.data);
                try {
                    const data = JSON.parse(event.data);

                    // Handle signature completed event
                    if (data.type === 'signature_completed') {
                        console.log('✅ Signature completed event received');
                        showToast('✅ PDF đã được ký số thành công!', 'success');

                        // Reload signatures after short delay
                        setTimeout(() => {
                            location.reload();
                        }, 1000);
                    }

                    // Handle status updates
                    if (data.type === 'status') {
                        updateSigningStatus('signing', data.message || 'Đang ký số...');
                    }
                } catch (e) {
                    console.error('Error parsing WebSocket message:', e);
                }
            };

            wsConnection.onerror = (error) => {
                console.error('❌ WebSocket error:', error);
                updateSigningStatus('error', 'Lỗi kết nối WebSocket');
            };

            wsConnection.onclose = () => {
                console.log('WebSocket disconnected');
                updateSigningStatus('disconnected', 'WebSocket mất kết nối');

                // Attempt to reconnect
                if (wsReconnectAttempts < wsMaxReconnectAttempts) {
                    wsReconnectAttempts++;
                    console.log(`Reconnecting... (${wsReconnectAttempts}/${wsMaxReconnectAttempts})`);
                    setTimeout(() => connectWebSocket(), 2000 * wsReconnectAttempts);
                }
            };
        } catch (e) {
            console.error('WebSocket connection failed:', e);
            updateSigningStatus('error', 'Không thể kết nối WebSocket');
        }
    }

    // Update signing status display
    function updateSigningStatus(status, message) {
        const statusDiv = document.getElementById('signing-status');
        if (!statusDiv) return;

        let statusClass = 'info';
        let icon = 'info-circle';

        switch(status) {
            case 'connected':
                statusClass = 'success';
                icon = 'check-circle';
                break;
            case 'signing':
                statusClass = 'warning';
                icon = 'hourglass-split';
                break;
            case 'error':
                statusClass = 'danger';
                icon = 'exclamation-circle';
                break;
            case 'disconnected':
                statusClass = 'secondary';
                icon = 'dash-circle';
                break;
        }

        statusDiv.innerHTML = `<i class="bi bi-${icon}"></i> ${message}`;
        statusDiv.className = `alert alert-${statusClass}`;
    }

    // Callback function for VGCA plugin signing
    function SignFileCallBack(rv) {
        console.log('VGCA SignFileCallBack response:', rv);

        try {
            const response = JSON.parse(rv);
            console.log('Sign response parsed:', response);

            if (response.Status == 0 || response.Status === '0') {
                // Success
                console.log('✅ Ký số thành công');
                showToast('✅ PDF đã được ký số thành công!', 'success');

                // Send callback to server
                fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'action=sign_callback&signStatus=0&fileName=' + encodeURIComponent('<?php echo addslashes($pdfName); ?>')
                })
                .then(r => r.json())
                .then(data => {
                    console.log('Server callback response:', data);

                    // Notify via WebSocket if connected
                    if (wsConnection && wsConnection.readyState === WebSocket.OPEN) {
                        wsConnection.send(JSON.stringify({
                            type: 'signature_completed',
                            evid: '<?php echo $evid; ?>',
                            payment_type: '<?php echo $payment_type; ?>',
                            timestamp: new Date().toISOString()
                        }));
                    }

                    // Reload after 1 second
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                });
            } else {
                // Failure
                console.error('❌ Ký số thất bại:', response.Message || response.Status);
                showToast('❌ Ký số thất bại: ' + (response.Message || response.Status), 'danger');
            }
        } catch (e) {
            console.error('Error parsing VGCA response:', e);
            showToast('❌ Lỗi xử lý kết quả ký số', 'danger');
        }
    }

    // Sign PDF using VGCA plugin
    function signPDF() {
        const evid = '<?php echo addslashes($evid); ?>';
        const paymentType = '<?php echo addslashes($payment_type); ?>';
        const pdfName = '<?php echo addslashes($pdfName); ?>';

        // Show confirmation
        if (!confirm('Bạn có chắc muốn ký số PDF này không?')) {
            return;
        }

        showToast('🔄 Khởi động ký số...', 'info');

        // First notify server
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'action=sign_pdf&evid=' + encodeURIComponent(evid) + '&payment_type=' + encodeURIComponent(paymentType)
        })
        .then(r => r.json())
        .then(data => {
            console.log('Server response:', data);
            showToast('🖊️ ' + data.message, 'info');

            // Build file URL via download_payment_file.php stream endpoint
            const fileUrl = window.location.origin + '/tool1/_site/event_mng/payment/download_payment_file.php?have_sign=1&evid=' +
                           encodeURIComponent(evid) + '&payment_type=' + encodeURIComponent(paymentType) + '&file=pdf';
            console.log('Signing file URL:', fileUrl);

            // Delay before calling VGCA
            setTimeout(() => {
                exc_sign_approved_with_url(fileUrl);
            }, 500);
        })
        .catch(e => {
            console.error('Error:', e);
            showToast('❌ Lỗi: ' + e.message, 'danger');
        });
    }

    // Call VGCA signing service
    function exc_sign_approved_with_url(file_url) {
        if (!file_url) {
            showToast('URL file không hợp lệ', 'danger');
            return;
        }

        const prms = {
            "FileUploadHandler": window.location.origin + "/tool1/_site/event_mng/payment/FileUploadHandler.php?evid=<?php echo urlencode($evid); ?>&payment_type=<?php echo urlencode($payment_type); ?>",
            "SessionId": "",
            "JWTToken": "",
            "FileName": file_url
        };

        console.log("Gọi VGCA ký file: " + file_url);
        const json_prms = JSON.stringify(prms);

        if (typeof vgca_sign_approved === 'function') {
            showToast('🔐 Kết nối tới VGCA plugin...', 'info');
            vgca_sign_approved(json_prms, SignFileCallBack);
        } else {
            showToast('⚠️ VGCA plugin chưa sẵn sàng. Vui lòng tải lại trang...', 'warning');
            console.error('vgca_sign_approved function not available');

            // Reset button
            const btn = document.querySelector('.btn-sign');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-pen"></i> Ký Số';
            }
        }
    }

    // Show toast notification
    function showToast(message, type = 'info') {
        const toastContainer = document.getElementById('toastContainer') || createToastContainer();

        const toast = document.createElement('div');
        const bgClass = type === 'success' ? 'bg-success' :
                       type === 'danger' ? 'bg-danger' :
                       type === 'warning' ? 'bg-warning' : 'bg-info';

        toast.className = `toast ${bgClass} text-white`;
        toast.setAttribute('role', 'alert');

        toast.innerHTML = `
            <div class="toast-body">
                ${message}
                <button type="button" class="btn-close btn-close-white ms-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        toastContainer.appendChild(toast);

        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        setTimeout(() => toast.remove(), 3000);
    }

    // Create toast container
    function createToastContainer() {
        const container = document.createElement('div');
        container.id = 'toastContainer';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }

    // Cleanup WebSocket on page unload
    window.addEventListener('beforeunload', () => {
        if (wsConnection) {
            wsConnection.close();
        }
    });
    </script>

    <?php
    /**
     * Format file size in human readable format
     */
    function formatFileSize($bytes) {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, 2) . ' ' . $units[$pow];
    }
    ?>
</body>
</html>
