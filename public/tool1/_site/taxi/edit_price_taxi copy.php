<?php

require_once "/var/www/html/public/index.php";

$file = "/var/www/html/public/data/pricing.json";
$backupDir = "/var/glx/weblog/taxi_price";

// Tạo thư mục backup nếu chưa có
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0755, true);
}

// Hàm tạo backup
function createBackup($originalFile, $backupDir, $content) {
    $timestamp = date('Y-m-d_H-i-s');
    $backupFile = $backupDir . "/pricing_" . $timestamp . ".json";
    return file_put_contents($backupFile, $content);
}

// Hàm lấy danh sách backup (max 100 file gần nhất)
function getBackupFiles($backupDir) {
    $files = [];
    if (is_dir($backupDir)) {
        $pattern = $backupDir . "/pricing_*.json";
        $backupFiles = glob($pattern);
        
        // Sắp xếp theo thời gian tạo, mới nhất trước
        usort($backupFiles, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });
        
        // Giới hạn 100 file
        $backupFiles = array_slice($backupFiles, 0, 100);
        
        foreach ($backupFiles as $file) {
            $files[] = [
                'path' => $file,
                'name' => basename($file),
                'time' => filemtime($file),
                'date' => date('d/m/Y H:i:s', filemtime($file)),
                'size' => filesize($file)
            ];
        }
    }
    return $files;
}

// Xử lý restore backup
if ($_POST && isset($_POST['restore']) && isset($_POST['backup_file'])) {
    $backupFile = $_POST['backup_file'];
    if (file_exists($backupFile)) {
        $backupContent = file_get_contents($backupFile);
        if ($backupContent !== false) {
            $message = "✅ Đã restore từ backup: " . basename($backupFile);
            $messageType = "success";
            $ct = $backupContent; // Hiển thị nội dung backup
        } else {
            $message = "❌ Lỗi khi đọc file backup!";
            $messageType = "error";
        }
    } else {
        $message = "❌ File backup không tồn tại!";
        $messageType = "error";
    }
}

// Xử lý khi form được submit để lưu
if ($_POST && isset($_POST['content']) && !isset($_POST['restore'])) {
    $content = $_POST['content'];
    
    // Validate JSON
    json_decode($content);
    if (json_last_error() === JSON_ERROR_NONE) {
        // Tạo backup trước khi lưu (nếu file gốc tồn tại)
        if (file_exists($file)) {
            $originalContent = file_get_contents($file);
            if ($originalContent !== false && $originalContent !== $content) {
                createBackup($file, $backupDir, $originalContent);
            }
        }
        
        // JSON hợp lệ, lưu file
        if (file_put_contents($file, $content) !== false) {
            $message = "✅ Đã lưu thành công! Backup được tạo tự động.";
            $messageType = "success";
        } else {
            $message = "❌ Lỗi khi lưu file!";
            $messageType = "error";
        }
    } else {
        $message = "❌ JSON không hợp lệ: " . json_last_error_msg();
        $messageType = "error";
    }
}

// Đọc nội dung file hiện tại (nếu chưa được set từ restore)
if (!isset($ct)) {
    $ct = file_get_contents($file);
    if ($ct === false) {
        $ct = "{}"; // File không tồn tại, tạo JSON rỗng
    }
}

// Lấy danh sách backup files
$backupFiles = getBackupFiles($backupDir);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Edit Pricing JSON</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 2px solid #007cba;
            padding-bottom: 10px;
        }
        .file-path {
            background: #f8f9fa;
            padding: 10px;
            border-left: 4px solid #007cba;
            margin: 10px 0;
            font-family: monospace;
            color: #666;
        }
        textarea {
            width: 100%;
            height: 400px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            border: 2px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            box-sizing: border-box;
            resize: vertical;
        }
        textarea:focus {
            border-color: #007cba;
            outline: none;
        }
        .button-group {
            margin-top: 15px;
            text-align: center;
        }
        button {
            background: #007cba;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 5px;
        }
        button:hover {
            background: #005a87;
        }
        .btn-format {
            background: #28a745;
        }
        .btn-format:hover {
            background: #218838;
        }
        .message {
            padding: 12px;
            border-radius: 4px;
            margin: 15px 0;
            font-weight: bold;
        }
        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .backup-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 15px;
            margin: 15px 0;
        }
        .backup-section h3 {
            margin-top: 0;
            color: #495057;
            font-size: 16px;
        }
        .backup-form {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: wrap;
        }
        .backup-select {
            flex: 1;
            min-width: 300px;
            padding: 8px 12px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-family: monospace;
            font-size: 12px;
        }
        .btn-restore {
            background: #17a2b8;
            padding: 8px 16px;
        }
        .btn-restore:hover {
            background: #138496;
        }
        .backup-info {
            font-size: 12px;
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <b>
            <a href="/admin" rel="noopener noreferrer">Quay lại Admin </a>
        </b>
        <b>📝 Edit Pricing JSON</h1>
        
        <div class="file-path">
            <strong>File:</strong> <?php echo htmlspecialchars($file); ?>
        </div>

        <?php if (isset($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Backup & Restore Section -->
        <div class="backup-section">
            <h3>📦 Quản lý Backup</h3>
            <div class="backup-info">
                <strong>Thư mục backup:</strong> <?php echo htmlspecialchars($backupDir); ?> 
                (<?php echo count($backupFiles); ?> phiên bản)
            </div>
            
            <?php if (!empty($backupFiles)): ?>
                <form method="POST" class="backup-form">
                    <select name="backup_file" class="backup-select" required>
                        <option value="">-- Chọn phiên bản để restore --</option>
                        <?php foreach ($backupFiles as $backup): ?>
                            <option value="<?php echo htmlspecialchars($backup['path']); ?>">
                                <?php echo htmlspecialchars($backup['date']); ?> 
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="restore" value="1" class="btn-restore" 
                            onclick="return confirm('Restore phiên bản này? Nội dung hiện tại sẽ bị thay thế!')">
                        🔄 Restore
                    </button>
                </form>
            <?php else: ?>
                <div class="backup-info">
                    <em>Chưa có backup nào. Backup sẽ được tạo tự động khi bạn save.</em>
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" onsubmit="return validateJSON()">
            <textarea name="content" id="jsonContent" placeholder="Nhập nội dung JSON..."><?php echo htmlspecialchars($ct); ?></textarea>
            
            <div class="button-group">
                <button type="button" class="btn-format" onclick="formatJSON()">🎨 Format JSON</button>
                <button type="submit">💾 Lưu File</button>
                <button type="button" onclick="location.reload()">🔄 Reload</button>
            </div>
        </form>
    </div>

    <script>
        function formatJSON() {
            const textarea = document.getElementById('jsonContent');
            try {
                const parsed = JSON.parse(textarea.value);
                textarea.value = JSON.stringify(parsed, null, 4);
                alert('✅ JSON đã được format!');
            } catch (e) {
                alert('❌ JSON không hợp lệ: ' + e.message);
            }
        }

        function validateJSON() {
            const textarea = document.getElementById('jsonContent');
            try {
                JSON.parse(textarea.value);
                return confirm('Bạn có chắc muốn lưu file không?');
            } catch (e) {
                alert('❌ JSON không hợp lệ: ' + e.message);
                return false;
            }
        }

        // Auto-resize textarea
        const textarea = document.getElementById('jsonContent');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.max(400, this.scrollHeight) + 'px';
        });
    </script>
</body>
</html>
