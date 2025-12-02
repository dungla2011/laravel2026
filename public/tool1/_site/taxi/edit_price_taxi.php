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

        usort($backupFiles, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

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

// Đọc và parse JSON
$jsonContent = file_get_contents($file);
$pricingData = json_decode($jsonContent, true);

if ($pricingData === null) {
    $pricingData = ['pricing' => []];
}

// Xử lý restore backup
if ($_POST && isset($_POST['restore']) && isset($_POST['backup_file'])) {
    $backupFile = $_POST['backup_file'];
    if (file_exists($backupFile)) {
        $backupContent = file_get_contents($backupFile);
        if ($backupContent !== false) {
            $pricingData = json_decode($backupContent, true);
            if ($pricingData !== null) {
                $message = "✅ Đã restore từ backup: " . basename($backupFile);
                $messageType = "success";
            } else {
                $message = "❌ File backup không đúng định dạng JSON!";
                $messageType = "error";
            }
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
if ($_POST && isset($_POST['save_pricing']) && !isset($_POST['restore'])) {
    // Tạo backup trước khi lưu
    if (file_exists($file)) {
        $originalContent = file_get_contents($file);
        if ($originalContent !== false) {
            createBackup($file, $backupDir, $originalContent);
        }
    }

    // Cập nhật từng pricing item
    for ($i = 1; $i <= 8; $i++) {
        if (isset($_POST["direction_$i"]) && isset($_POST["originalPrice_$i"]) && isset($_POST["discount_$i"])) {
            // Tìm item có id = $i
            foreach ($pricingData['pricing'] as &$item) {
                if ($item['id'] == $i) {
                    $item['direction'] = trim($_POST["direction_$i"]);
                    $item['originalPrice'] = (int)$_POST["originalPrice_$i"];
                    $item['discount'] = (int)$_POST["discount_$i"];
                    break;
                }
            }
        }
    }

    // Lưu JSON
    $newJsonContent = json_encode($pricingData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (file_put_contents($file, $newJsonContent) !== false) {
        $message = "✅ Đã lưu thành công! Backup được tạo tự động.";
        $messageType = "success";
    } else {
        $message = "❌ Lỗi khi lưu file!";
        $messageType = "error";
    }
}

// Lấy danh sách backup files
$backupFiles = getBackupFiles($backupDir);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pricing - Simple Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .price-group input{
            color: red;
            font-weight: bold;
        }
        .header {
            background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .nav-link {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            margin-right: 20px;
            font-size: 14px;
        }
        .nav-link:hover {
            color: white;
        }
        .content {
            padding: 30px;
        }
        .file-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            font-family: monospace;
            font-size: 13px;
        }
        .pricing-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .pricing-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 20px;
            background: #fff;
            transition: all 0.3s ease;
        }
        .pricing-card:hover {
            border-color: #007cba;
            box-shadow: 0 8px 25px rgba(0,124,186,0.15);
        }
        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e9ecef;
        }
        .card-id {
            background: #007cba;
            color: white;
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
        }
        .card-info {
            flex: 1;
        }
        .card-title {
            font-size: 16px;
            font-weight: 600;
            color: #333;
            margin: 0;
        }
        .card-subtitle {
            font-size: 12px;
            color: #6c757d;
            margin: 5px 0 0 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
            color: #495057;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ced4da;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.15s ease-in-out;
            box-sizing: border-box;
        }
        .form-control:focus {
            border-color: #007cba;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }
        .price-group {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .backup-section {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .backup-title {
            margin: 0 0 15px 0;
            font-size: 16px;
            font-weight: 600;
            color: #495057;
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
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            display: inline-block;
            transition: all 0.2s ease;
        }
        .btn-primary {
            background: #007cba;
            color: white;
        }
        .btn-primary:hover {
            background: #005a87;
        }
        .btn-success {
            background: #28a745;
            color: white;
        }
        .btn-success:hover {
            background: #218838;
        }
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        .btn-info:hover {
            background: #138496;
        }
        .btn-group {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }
        .message {
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 500;
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
        .day-card {
            border-left: 4px solid #ffc107;
        }
        .night-card {
            border-left: 4px solid #6f42c1;
        }
        .backup-info {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div>
            <a href="/admin" class="nav-link"><i class="fas fa-arrow-left"></i> Quay lại Admin</a>
            <a href="edit_price_taxi.php" class="nav-link"><i class="fas fa-code"></i> Edit JSON Raw</a>
        </div>
        <h1><i class="fas fa-edit"></i> Edit Pricing - Simple Form</h1>
    </div>

    <div class="content">
        <div class="file-info">
            <i class="fas fa-file-code"></i> <strong>File:</strong> <?php echo htmlspecialchars($file); ?>
        </div>

        <?php if (isset($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Backup Section -->
        <div class="backup-section">
            <h3 class="backup-title"><i class="fas fa-history"></i> Quản lý Backup</h3>
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
                    <button type="submit" name="restore" value="1" class="btn btn-info"
                            onclick="return confirm('Restore phiên bản này? Nội dung hiện tại sẽ bị thay thế!')">
                        <i class="fas fa-undo"></i> Restore
                    </button>
                </form>
            <?php else: ?>
                <div class="backup-info">
                    <em>Chưa có backup nào. Backup sẽ được tạo tự động khi bạn save.</em>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pricing Form -->
        <form method="POST">
            <div class="pricing-grid">
                <?php
                foreach ($pricingData['pricing'] as $pricing):
                    $isNightTime = $pricing['isNightTime'] ?? false;
                    $cardClass = $isNightTime ? 'night-card' : 'day-card';
                    ?>
                    <div class="pricing-card <?php echo $cardClass; ?>">
                        <div class="card-header">
                            <div class="card-id"><?php echo $pricing['id']; ?></div>
                            <div class="card-info">
                                <div class="card-title">
                                    <i class="<?php echo $pricing['directionIcon'] ?? 'fas fa-route'; ?>"></i>
                                    <?php echo htmlspecialchars($pricing['direction']); ?>
                                </div>
                                <div class="card-subtitle">
                                    <i class="<?php echo $pricing['carIcon'] ?? 'fas fa-car'; ?>"></i>
                                    <?php echo htmlspecialchars($pricing['carType']); ?> •
                                    <i class="<?php echo $pricing['timeIcon'] ?? 'fas fa-clock'; ?>"></i>
                                    <?php echo htmlspecialchars($pricing['timePeriod']); ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-route"></i> Hướng đi
                            </label>
                            <input type="text"
                                   name="direction_<?php echo $pricing['id']; ?>"
                                   class="form-control"
                                   value="<?php echo htmlspecialchars($pricing['direction']); ?>"
                                   required>
                        </div>

                        <div class="price-group">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-money-bill"></i> Giá gốc (k VNĐ)
                                </label>
                                <input type="number"
                                       name="originalPrice_<?php echo $pricing['id']; ?>"
                                       class="form-control"
                                       value="<?php echo $pricing['originalPrice']; ?>"
                                       min="0"
                                       required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-percent"></i> Giảm giá (k VNĐ)
                                </label>
                                <input type="number"
                                       name="discount_<?php echo $pricing['id']; ?>"
                                       class="form-control"
                                       value="<?php echo $pricing['discount']; ?>"
                                       min="0"
                                       required>
                            </div>
                        </div>

                        <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef; text-align: center; color: #6c757d; font-size: 13px;">
                            <strong>Giá sau giảm: <?php echo ($pricing['originalPrice'] - $pricing['discount']); ?>k VNĐ</strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="btn-group">
                <button type="submit" name="save_pricing" value="1" class="btn btn-success"
                        onclick="return confirm('Bạn có chắc muốn lưu tất cả thay đổi không?')">
                    <i class="fas fa-save"></i> Lưu Tất Cả
                </button>
                <button type="button" class="btn btn-primary" onclick="location.reload()">
                    <i class="fas fa-redo"></i> Reload
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // Tính giá sau giảm realtime
    document.addEventListener('input', function(e) {
        if (e.target.type === 'number' && (e.target.name.includes('originalPrice_') || e.target.name.includes('discount_'))) {
            const id = e.target.name.split('_')[1];
            const originalPriceInput = document.querySelector(`input[name="originalPrice_${id}"]`);
            const discountInput = document.querySelector(`input[name="discount_${id}"]`);
            const resultDiv = originalPriceInput.closest('.pricing-card').querySelector('div[style*="Giá sau giảm"] strong');

            if (originalPriceInput && discountInput && resultDiv) {
                const originalPrice = parseInt(originalPriceInput.value) || 0;
                const discount = parseInt(discountInput.value) || 0;
                const finalPrice = originalPrice - discount;
                resultDiv.textContent = `Giá sau giảm: ${finalPrice}k VNĐ`;

                // Highlight nếu giá âm
                if (finalPrice < 0) {
                    resultDiv.style.color = '#dc3545';
                } else {
                    resultDiv.style.color = '#28a745';
                }
            }
        }
    });
</script>
</body>
</html>
