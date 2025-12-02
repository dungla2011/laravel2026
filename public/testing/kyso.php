<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý File PDF - Ký Số</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .container-main {
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            padding: 15px;
            max-width: 1200px;
        }
        .header-section {
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-section h1 {
            color: #333;
            font-weight: bold;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1.3rem;
        }
        @media (max-width: 768px) {
            .header-section h1 {
                font-size: 1.1rem;
            }
            .btn-primary {
                padding: 4px 8px !important;
                font-size: 0.85rem !important;
            }
        }
        @media (max-width: 480px) {
            .header-section h1 {
                font-size: 0.95rem;
            }
            .header-section h1 i {
                font-size: 1rem;
            }
            .btn-primary {
                padding: 3px 6px !important;
                font-size: 0.75rem !important;
            }
        }
        .upload-area {
            display: none;
        }
        .upload-area:hover {
            display: none;
        }
        .upload-area.dragover {
            display: none;
        }
        .upload-area p {
            display: none;
        }
        #fileInput {
            display: none;
        }
        .file-list-header {
            background: #f8f9fa;
            padding: 8px 10px;
            border-radius: 6px;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
            font-size: 0.9rem;
        }
        .file-row {
            display: grid;
            grid-template-columns: 40px 1fr 120px 120px 100px 90px;
            gap: 10px;
            align-items: center;
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
            transition: background 0.3s;
            font-size: 0.9rem;
        }
        @media (max-width: 992px) {
            .file-row {
                grid-template-columns: 35px 1fr 100px 100px 80px 80px;
                gap: 8px;
                padding: 6px 8px;
                font-size: 0.85rem;
            }
        }
        @media (max-width: 768px) {
            .file-row {
                grid-template-columns: 30px 1fr 70px 70px 60px 60px;
                gap: 6px;
                padding: 5px 6px;
                font-size: 0.8rem;
            }
            .file-list-header {
                display: none;
            }
        }
        @media (max-width: 480px) {
            .file-row {
                grid-template-columns: 25px 1fr 50px 50px 50px 50px;
                gap: 5px;
                padding: 4px 5px;
                font-size: 0.75rem;
            }
            .btn-sign, .btn-delete {
                padding: 4px 6px !important;
                font-size: 0.7rem !important;
            }
            .btn-sign i, .btn-delete i {
                font-size: 0.8rem !important;
            }
        }
        .file-row:hover {
            background: #f8f9ff;
        }
        .file-name-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            cursor: pointer;
            word-break: break-all;
        }
        .file-name-link:hover {
            text-decoration: underline;
            color: #764ba2;
        }
        .file-size {
            color: #666;
            font-size: 0.95rem;
        }
        .file-date {
            color: #999;
            font-size: 0.9rem;
        }
        .btn-action {
            padding: 6px 12px;
            font-size: 0.85rem;
            margin: 0 3px;
            border-radius: 5px;
        }
        .btn-sign {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            transition: transform 0.2s;
        }
        .btn-sign:hover {
            transform: scale(1.05);
            color: white;
        }
        .btn-delete {
            background: #ff6b6b;
            border: none;
            color: white;
        }
        .btn-delete:hover {
            background: #ff5252;
            color: white;
        }
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }
        .empty-state i {
            font-size: 4rem;
            color: #ddd;
            margin-bottom: 20px;
        }
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 20px;
        }
        .loading-spinner.show {
            display: block;
        }
        .upload-btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.9rem;
            transition: transform 0.2s;
        }
        .upload-btn:hover {
            transform: translateY(-2px);
            color: white;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
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
        .progress-wrapper {
            display: none;
            margin: 10px 0;
        }
        .progress-wrapper.show {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container container-main">
        <!-- Header with Upload Button -->
        <div class="header-section d-flex justify-content-between align-items-center">
            Demo:
            Ký số file pdf online: Bấm nút [Ký file], file sẽ được download về PC
            <br>
            VSCA service ký và Up lại pdf đã ký lên Web, nếu cần có thể Build Agent ở PC để trung gian xử lý
            <br>

            <button class="btn btn-sm btn-primary" onclick="document.getElementById('fileInput').click()" title="Upload file">
                <i class="bi bi-cloud-arrow-up"></i> Upload
            </button>
            <input type="file" id="fileInput" multiple accept=".pdf,.doc,.docx,.xls,.xlsx" style="display:none;">
        </div>

        <!-- Progress Bar -->
        <div class="progress-wrapper" id="progressWrapper">
            <div class="progress">
                <div class="progress-bar progress-bar-striped progress-bar-animated"
                     id="progressBar" role="progressbar" style="width: 0%"></div>
            </div>
            <small id="progressText" class="text-muted"></small>
        </div>

        <!-- Loading Spinner -->
        <div class="loading-spinner" id="loadingSpinner">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <p class="mt-2">Đang tải danh sách file...</p>
        </div>

        <!-- File List Header -->
        <div class="file-list-header">
            <div class="file-row">
                <div>STT</div>
                <div>Tên File</div>
                <div>Kích Thước</div>
                <div>Ngày Tạo</div>
                <div class="text-center">Ký File</div>
                <div class="text-center">Xoá</div>
            </div>
        </div>

        <!-- File List -->
        <div id="fileList"></div>

        <!-- Empty State -->
        <div class="empty-state" id="emptyState" style="display: none;">
            <i class="bi bi-inbox"></i>
            <p>Chưa có file nào. Hãy upload file để bắt đầu!</p>
        </div>
    </div>

    <!-- Toast Notifications -->
    <div class="toast-container" id="toastContainer"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="./vgcaplugin.js"></script>
    <script type="text/javascript" src="./websocket-error-handler.js"></script>
    <script>
        const API_BASE = '/testing/api/file-manager.php';
        const FILES_DIR = '/testing/files';

        // Callback function for signing
        function SignFileCallBack(rv) {
            var received_msg = JSON.parse(rv);
            console.log('Sign response:', received_msg);
            
            if (received_msg.Status == 0) {
                showToast('✓ Ký số thành công!', 'success');
                // Update UI if needed
                setTimeout(() => {
                    loadFiles(); // Reload file list
                }, 1000);
            } else {
                showToast('✗ Lỗi ký số: ' + (received_msg.Message || 'Unknown error'), 'danger');
            }
        }

        // Function to sign file via VGCA plugin
        function exc_sign_approved_with_url(file_url) {
            if (!file_url) {
                showToast('URL file không hợp lệ', 'danger');
                return;
            }

            var prms = {
                "FileUploadHandler": "https://lad.vn/testing/FileUploadHandler.php",
                "SessionId": "",
                "JWTToken": "",
                "FileName": file_url
            };

            console.log("Ký file từ URL: " + file_url);
            var json_prms = JSON.stringify(prms);
            
            if (typeof vgca_sign_approved === 'function') {
                vgca_sign_approved(json_prms, SignFileCallBack);
            } else {
                showToast('Plugin VGCA chưa sẵn sàng. Vui lòng chờ...', 'warning');
            }
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            loadFiles();
            setupFileInput();
        });

        // Setup File Input
        function setupFileInput() {
            const fileInput = document.getElementById('fileInput');
            fileInput.addEventListener('change', (e) => {
                handleFiles(e.target.files);
                fileInput.value = ''; // Reset input
            });
        }

        // Handle File Upload
        function handleFiles(files) {
            if (files.length === 0) return;

            const formData = new FormData();
            let totalSize = 0;
            const maxSize = 50 * 1024 * 1024; // 50MB

            for (let file of files) {
                console.log('File:', file.name, 'Type:', file.type, 'Size:', file.size);
                totalSize += file.size;
                if (totalSize > maxSize) {
                    showToast('Tổng dung lượng file vượt quá 50MB', 'danger');
                    return;
                }
                formData.append('files[]', file);
            }

            console.log('FormData entries:');
            for (let [key, value] of formData) {
                console.log(key, value);
            }

            uploadFiles(formData);
        }

        // Upload Files
        function uploadFiles(formData) {
            const progressWrapper = document.getElementById('progressWrapper');
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');

            progressWrapper.classList.add('show');
            progressBar.style.width = '0%';
            progressText.textContent = 'Đang upload...';

            const xhr = new XMLHttpRequest();

            xhr.upload.addEventListener('progress', (e) => {
                if (e.lengthComputable) {
                    const percentComplete = (e.loaded / e.total) * 100;
                    progressBar.style.width = percentComplete + '%';
                    progressText.textContent = `${Math.round(percentComplete)}%`;
                }
            });

            xhr.addEventListener('load', () => {
                console.log('Response status:', xhr.status, 'Text:', xhr.responseText);
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    console.log('Response data:', response);
                    if (response.success) {
                        showToast('Upload thành công!', 'success');
                        progressWrapper.classList.remove('show');
                        loadFiles();
                    } else {
                        showToast(response.message || 'Lỗi upload', 'danger');
                    }
                } else {
                    showToast('Lỗi upload file', 'danger');
                }
            });

            xhr.addEventListener('error', () => {
                showToast('Lỗi kết nối', 'danger');
                progressWrapper.classList.remove('show');
            });

            xhr.open('POST', API_BASE + '?action=upload-file');
            xhr.send(formData);
        }

        // Load Files
        function loadFiles() {
            const loadingSpinner = document.getElementById('loadingSpinner');
            loadingSpinner.classList.add('show');

            fetch(API_BASE + '?action=list-files')
                .then(response => response.json())
                .then(data => {
                    loadingSpinner.classList.remove('show');

                    if (data.success && data.files.length > 0) {
                        renderFiles(data.files);
                        document.getElementById('emptyState').style.display = 'none';
                    } else {
                        document.getElementById('fileList').innerHTML = '';
                        document.getElementById('emptyState').style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    loadingSpinner.classList.remove('show');
                    showToast('Lỗi tải danh sách file', 'danger');
                });
        }

        // Render Files
        function renderFiles(files) {
            const fileList = document.getElementById('fileList');
            fileList.innerHTML = '';

            files.forEach((file, index) => {
                const row = document.createElement('div');
                row.className = 'file-row';

                const fileDate = new Date(file.date).toLocaleDateString('vi-VN', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });

                // Create row HTML first
                row.innerHTML = `
                    <div class="text-center"><strong>${index + 1}</strong></div>
                    <div>
                        <a href="#" class="file-name-link" onclick="openFile('${file.name}'); return false;">
                            <i class="bi bi-file-pdf"></i> ${file.name}
                        </a>
                        <div id="sig-${index}" class="signature-info" style="margin-top: 5px; font-size: 0.85rem; color: #666;"></div>
                    </div>
                    <div class="file-size">${formatFileSize(file.size)}</div>
                    <div class="file-date">${fileDate}</div>
                    <div class="text-center">
                        <button class="btn btn-action btn-sign" onclick="signFile('${file.name}')"
                                title="Ký số file này">
                            <i class="bi bi-pen"></i> Ký Số
                        </button>
                    </div>
                    <div class="text-center d-none">
                        <button class="btn btn-action btn-delete" onclick="deleteFile('${file.name}')"
                                title="Xoá file này">
                            <i class="bi bi-trash"></i> Xoá
                        </button>
                    </div>
                `;

                fileList.appendChild(row);

                // Load signatures for this file
                loadFileSignatures(file.name, index);
            });
        }

        // Load and display signatures for a file
        function loadFileSignatures(filename, index) {
            const url = API_BASE + '?action=get-signatures&filename=' + encodeURIComponent(filename);
            console.log('Loading signatures from:', url);
            
            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status);
                    console.log('Response headers:', response.headers.get('content-type'));
                    return response.text(); // Get raw text first
                })
                .then(text => {
                    console.log('Raw response:', text);
                    const data = JSON.parse(text); // Parse JSON
                    const sigDiv = document.getElementById('sig-' + index);
                    
                    if (data.success && data.signatures && data.signatures.length > 0) {
                        let sigHtml = '<div style="margin-top: 5px; padding: 5px 0; border-top: 1px solid #eee;">';
                        data.signatures.forEach(sig => {
                            sigHtml += `
                                <div style="font-size: 0.8rem; color: #666; margin: 2px 0;">
                                    <i class="bi bi-check-circle" style="color: #28a745;"></i>
                                    <strong>Đã ký bởi:</strong> ${sig.name || sig.reason}<br>
                                    <span style="margin-left: 18px; font-size: 0.75rem; color: #999;">
                                        ${sig.timestamp}
                                    </span>
                                </div>
                            `;
                        });
                        sigHtml += '</div>';
                        sigDiv.innerHTML = sigHtml;
                    } else {
                        sigDiv.innerHTML = '<div style="font-size: 0.8rem; color: #999; margin-top: 5px;"><i class="bi bi-dash-circle"></i> Chưa có chữ ký</div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading signatures:', error);
                    const sigDiv = document.getElementById('sig-' + index);
                    if (sigDiv) {
                        sigDiv.innerHTML = '<div style="font-size: 0.8rem; color: #999;">Không thể đọc chữ ký</div>';
                    }
                });
        }

        // Open File (View PDF)
        function openFile(filename) {
            const fileUrl = FILES_DIR + '/' + filename;
            window.open(fileUrl, '_blank');
        }

        // Sign File
        function signFile(filename) {
            const fileName = filename;

            // Show confirmation
            if (!confirm(`Ký số file: ${filename}?`)) {
                return;
            }

            showToast('Đang chuẩn bị ký file: ' + filename, 'info');

            // Build full URL to the file
            const fileUrl = window.location.origin + FILES_DIR + '/' + filename;
            
            console.log('Signing file URL:', fileUrl);

            // Call VGCA signing service
            setTimeout(() => {
                exc_sign_approved_with_url(fileUrl);
            }, 500);
        }

        // Delete File
        function deleteFile(filename) {
            if (!confirm(`Bạn chắc chắn muốn xoá file: ${filename}?`)) {
                return;
            }

            fetch(API_BASE + '?action=delete-file', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ filename: filename })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('File đã xoá thành công!', 'success');
                    loadFiles();
                } else {
                    showToast(data.message || 'Lỗi xoá file', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Lỗi xoá file', 'danger');
            });
        }

        // Format File Size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
        }

        // Show Toast Notification
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer');

            const toast = document.createElement('div');
            toast.className = `toast bg-${type === 'success' ? 'success' : type === 'danger' ? 'danger' : 'info'} text-white`;
            toast.setAttribute('role', 'alert');
            toast.setAttribute('aria-live', 'assertive');
            toast.setAttribute('aria-atomic', 'true');

            toast.innerHTML = `
                <div class="toast-body d-flex align-items-center gap-2">
                    <i class="bi bi-${
                        type === 'success' ? 'check-circle' :
                        type === 'danger' ? 'exclamation-circle' :
                        'info-circle'
                    }"></i>
                    <span>${message}</span>
                    <button type="button" class="btn-close btn-close-white ms-auto"
                            data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            `;

            toastContainer.appendChild(toast);

            const bsToast = new bootstrap.Toast(toast);
            bsToast.show();

            toast.addEventListener('hidden.bs.toast', () => {
                toast.remove();
            });
        }
    </script>
</body>
</html>
