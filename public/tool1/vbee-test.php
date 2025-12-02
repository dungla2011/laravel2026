<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'test2023.mytree.vn';

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

// Handle AJAX requests
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    $requestId = $_POST['request_id'] ?? $_GET['request_id'] ?? '';
    $text = $_POST['text'] ?? '';
    $voice = $_POST['voice'] ?? 'hn_female_ngochuyen_full_48k-fhg';
    
    require_once __DIR__ . '/api_handler.php';
    exit;
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VBee TTS - Text to Speech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            padding: 10px 24px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.4);
        }
        .btn-success {
            background: #10b981;
            border: none;
            border-radius: 8px;
        }
        .btn-success:hover {
            background: #059669;
        }
        .btn-info {
            background: #3b82f6;
            border: none;
            border-radius: 8px;
        }
        .btn-info:hover {
            background: #1d4ed8;
        }
        .result-section {
            margin-top: 30px;
            display: none;
        }
        .result-section.show {
            display: block;
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .spinner-border {
            color: #667eea;
        }
        .text-input {
            border-radius: 8px;
            border: 2px solid #e5e7eb;
            padding: 12px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }
        .text-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        .audio-player {
            border-radius: 12px;
            background: #f3f4f6;
            padding: 15px;
            margin: 15px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 14px;
        }
        .status-processing {
            background: #fef3c7;
            color: #92400e;
        }
        .status-success {
            background: #d1fae5;
            color: #065f46;
        }
        .status-error {
            background: #fee2e2;
            color: #7f1d1d;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            flex-wrap: wrap;
        }
        .navbar {
            background: rgba(255, 255, 255, 0.95) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .navbar-brand {
            font-weight: 700;
            color: #667eea !important;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light mb-4">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-music-note-beamed"></i> VBee TTS
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Docs</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Input Card -->
                <div class="card mb-4">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">
                            <i class="bi bi-mic-fill"></i> Text to Speech Converter
                        </h4>
                        
                        <div class="mb-3">
                            <label class="form-label fw-500">Văn bản</label>
                            <textarea id="inputText" class="form-control text-input" rows="4" placeholder="Nhập văn bản cần chuyển thành âm thanh..." required></textarea>
                            <small class="text-muted">Tối đa 1000 ký tự</small>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-500">Giọng nói</label>
                            <select id="voiceSelect" class="form-select text-input">
                                <option value="hn_female_ngochuyen_full_48k-fhg">Nữ - Ngọc Huyền</option>
                                <option value="hn_male_giasang_full_48k-fhg">Nam - Gia Sáng</option>
                                <option value="hn_female_linhyeu_full_48k-fhg">Nữ - Linh Yêu</option>
                            </select>
                        </div>

                        <button id="convertBtn" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-play-circle"></i> Chuyển Đổi
                        </button>
                    </div>
                </div>

                <!-- Result Card -->
                <div class="card result-section" id="resultSection">
                    <div class="card-body p-4">
                        <h4 class="card-title mb-4">
                            <i class="bi bi-check-circle-fill"></i> Kết Quả
                        </h4>

                        <!-- Loading State -->
                        <div id="loadingState" class="text-center d-none">
                            <div class="spinner-border" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">
                                <span id="loadingText">Đang xử lý...</span>
                                <span id="retryCount" class="d-none">
                                    (Lần kiểm tra: <span id="retryNum">1</span>)
                                </span>
                            </p>
                        </div>

                        <!-- Success State -->
                        <div id="successState" class="d-none">
                            <div class="mb-3">
                                <span class="status-badge status-success" id="statusBadge">
                                    <i class="bi bi-check-lg"></i> Ready
                                </span>
                            </div>

                            <p class="text-muted mb-2"><strong>Request ID:</strong></p>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="requestIdDisplay" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="copyBtn">
                                    <i class="bi bi-files"></i>
                                </button>
                            </div>

                            <p class="text-muted mb-2"><strong>Audio URL:</strong></p>
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" id="audioUrlDisplay" readonly>
                                <button class="btn btn-outline-secondary" type="button" id="copyUrlBtn">
                                    <i class="bi bi-files"></i>
                                </button>
                            </div>

                            <div class="audio-player">
                                <audio id="audioPlayer" controls style="width: 100%; margin-bottom: 10px;"></audio>
                                <div class="action-buttons">
                                    <button id="playBtn" class="btn btn-success btn-sm" style="display: none;">
                                        <i class="bi bi-play-circle"></i> Play
                                    </button>
                                    <button id="downloadBtn" class="btn btn-info btn-sm">
                                        <i class="bi bi-download"></i> Download
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Error State -->
                        <div id="errorState" class="d-none">
                            <div class="alert alert-danger mb-3">
                                <i class="bi bi-exclamation-triangle-fill"></i>
                                <strong id="errorMessage"></strong>
                            </div>
                            <button id="retryBtn" class="btn btn-warning">
                                <i class="bi bi-arrow-clockwise"></i> Thử Lại
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const convertBtn = document.getElementById('convertBtn');
        const inputText = document.getElementById('inputText');
        const voiceSelect = document.getElementById('voiceSelect');
        const resultSection = document.getElementById('resultSection');
        const loadingState = document.getElementById('loadingState');
        const successState = document.getElementById('successState');
        const errorState = document.getElementById('errorState');
        const audioPlayer = document.getElementById('audioPlayer');
        const downloadBtn = document.getElementById('downloadBtn');
        const retryBtn = document.getElementById('retryBtn');
        const copyBtn = document.getElementById('copyBtn');
        const copyUrlBtn = document.getElementById('copyUrlBtn');
        const playBtn = document.getElementById('playBtn');

        let currentRequestId = '';
        let retryCount = 0;
        let maxRetries = 30;
        let pollInterval = null;

        // Convert button click
        convertBtn.addEventListener('click', async () => {
            const text = inputText.value.trim();
            if (!text) {
                alert('Vui lòng nhập văn bản');
                return;
            }

            convertBtn.disabled = true;
            convertBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Converting...';
            
            try {
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: `action=request&text=${encodeURIComponent(text)}&voice=${encodeURIComponent(voiceSelect.value)}`
                });

                const data = await response.json();
                
                if (data.success && data.request_id) {
                    currentRequestId = data.request_id;
                    retryCount = 0;
                    showLoading();
                    pollResult();
                } else {
                    showError(data.message || 'Lỗi khi gửi yêu cầu');
                }
            } catch (error) {
                showError('Lỗi kết nối: ' + error.message);
            } finally {
                convertBtn.disabled = false;
                convertBtn.innerHTML = '<i class="bi bi-play-circle"></i> Chuyển Đổi';
            }
        });

        // Poll result
        function pollResult() {
            pollInterval = setInterval(async () => {
                retryCount++;
                document.getElementById('retryNum').textContent = retryCount;

                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `action=getresult&request_id=${encodeURIComponent(currentRequestId)}`
                    });

                    const data = await response.json();

                    if (data.success && data.audio_url) {
                        clearInterval(pollInterval);
                        showSuccess(data);
                    } else if (data.processing) {
                        // Still processing
                        document.getElementById('loadingText').textContent = 'Đang xử lý...';
                        document.getElementById('retryCount').classList.remove('d-none');
                    } else if (data.error) {
                        clearInterval(pollInterval);
                        showError(data.error);
                    }
                } catch (error) {
                    if (retryCount >= maxRetries) {
                        clearInterval(pollInterval);
                        showError('Timeout: Xử lý quá lâu');
                    }
                }
            }, 2000);
        }

        // Show loading state
        function showLoading() {
            resultSection.classList.add('show');
            loadingState.classList.remove('d-none');
            successState.classList.add('d-none');
            errorState.classList.add('d-none');
        }

        // Show success state
        function showSuccess(data) {
            loadingState.classList.add('d-none');
            successState.classList.remove('d-none');
            errorState.classList.add('d-none');
            resultSection.classList.add('show');

            document.getElementById('requestIdDisplay').value = currentRequestId;
            document.getElementById('audioUrlDisplay').value = data.audio_url;
            audioPlayer.src = data.audio_url;
            document.getElementById('statusBadge').textContent = '✓ Ready';
        }

        // Show error state
        function showError(message) {
            loadingState.classList.add('d-none');
            successState.classList.add('d-none');
            errorState.classList.remove('d-none');
            resultSection.classList.add('show');

            document.getElementById('errorMessage').textContent = message;
        }

        // Download button
        downloadBtn.addEventListener('click', () => {
            if (audioPlayer.src) {
                const a = document.createElement('a');
                a.href = audioPlayer.src;
                a.download = `audio-${currentRequestId}.mp3`;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            }
        });

        // Copy buttons
        copyBtn.addEventListener('click', () => {
            document.getElementById('requestIdDisplay').select();
            document.execCommand('copy');
            alert('Copied!');
        });

        copyUrlBtn.addEventListener('click', () => {
            document.getElementById('audioUrlDisplay').select();
            document.execCommand('copy');
            alert('Copied!');
        });

        // Retry button
        retryBtn.addEventListener('click', () => {
            retryCount = 0;
            showLoading();
            pollResult();
        });
    </script>
</body>
</html>


