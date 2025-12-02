<?php
/**
 * FileUploadHandler - Converted from Python to PHP
 * Xử lý upload file cho hệ thống ký số
 */

// Load helper functions from shared library
require_once __DIR__ . '/lib_sign.php';

$evid = isset($_GET['evid']) ? intval($_GET['evid']) : 0;
$payment_type = isset($_GET['payment_type']) ? $_GET['payment_type'] : '';

// Configuration
$UPLOAD_DIR = "/var/glx/weblog/pdf_event_bill/ev_id_" . $evid;
$USER_FILES_DIR = "/var/glx/weblog/pdf_event_bill/ev_id_" . $evid;
// Create directories if not exist
@mkdir($UPLOAD_DIR, 0755, true);
@mkdir($USER_FILES_DIR, 0755, true);

// Set headers
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json; charset=utf-8');

// Get request info
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/FileUploadHandler.php', '', $path);
if (empty($path)) $path = '/';

// Log request
error_log("[" . date('d/M/Y H:i:s') . "] 📨 {$method} {$path}");

try {
    if ($method === 'POST') {
        handlePost($path);
    } elseif ($method === 'GET') {
        handleGet($path);
    } else {
        sendJsonResponse(['status' => 'error', 'message' => 'Method not allowed'], 405);
    }
} catch (Exception $ex) {
    error_log("❌ Error: " . $ex->getMessage());
    sendJsonResponse(['status' => 'error', 'message' => $ex->getMessage()], 500);
}

function handlePost($path) {
    global $UPLOAD_DIR, $USER_FILES_DIR;
    
    error_log("  → Xử lý POST request");
    
    // Get content type
    $content_type = $_SERVER['CONTENT_TYPE'] ?? '';
    
    // Check if multipart or JSON
    if (strpos($content_type, 'multipart/form-data') !== false) {
        handleMultipartUpload();
    } elseif (strpos($content_type, 'application/json') !== false) {
        $body = file_get_contents('php://input');
        $data = json_decode($body, true);
        
        if ($path === '/finalize-server-sign') {
            error_log("  → Xử lý /finalize-server-sign request");
            handleFinalizeServerSign($data);
        } elseif ($path === '/prepare-pdf') {
            error_log("  → Xử lý /prepare-pdf request");
            handlePreparePdf($data);
        } elseif ($path === '/finalize-signature') {
            error_log("  → Xử lý /finalize-signature request");
            handleFinalizeSignature($data);
        } elseif ($path === '/embed-signature') {
            error_log("  → Xử lý /embed-signature request");
            handleEmbedSignature($data);
        } elseif ($path === '/sign') {
            error_log("  → Xử lý /sign request");
            handleSignRequest($data);
        } else {
            sendJsonResponse(['status' => 'error', 'message' => 'Unknown path'], 404);
        }
    } else {
        sendJsonResponse(['status' => 'error', 'message' => 'Invalid content type'], 400);
    }
}

function handleMultipartUpload() {
    global $USER_FILES_DIR;
    
    error_log("  → Xử lý multipart upload");
    
    if (!isset($_FILES['uploadfile'])) {
        sendJsonResponse([
            'Status' => false,
            'Message' => 'No file found',
            'FileName' => '',
            'FileServer' => ''
        ]);
        return;
    }
    
    $file = $_FILES['uploadfile'];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        sendJsonResponse([
            'Status' => false,
            'Message' => 'Upload error: ' . $file['error'],
            'FileName' => '',
            'FileServer' => ''
        ]);
        return;
    }
    
    // Use standard filename format from lib_sign.php helper function
    global $evid, $payment_type;
    $baseFileName = getFileNamePdf($evid, $payment_type);
    $filename = str_replace('.pdf', '_signed.pdf', basename($baseFileName));
    
    error_log("  → Generated filename: {$filename}");
    
    $file_path = $USER_FILES_DIR . '/' . $filename;
    
    // Check if file exists - rename old file with timestamp, keep current filename for new file
    if (file_exists($file_path)) {
        $pathinfo = pathinfo($file_path);
        $old_filename = $pathinfo['filename'] . '_' . date('YmdHis') . '.' . $pathinfo['extension'];
        $old_file_path = $USER_FILES_DIR . '/' . $old_filename;
        rename($file_path, $old_file_path);
        error_log("  → Old file renamed to: {$old_filename}");
    }
    
    // Move uploaded file with original filename (overwrite/replace)
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        sendJsonResponse([
            'Status' => false,
            'Message' => 'Failed to move file',
            'FileName' => '',
            'FileServer' => ''
        ]);
        return;
    }
    
    // Build response URL
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $file_server_url = "{$protocol}://{$host}/download/{$filename}";
    
    error_log("  ✅ File uploaded: {$filename}");
    
    sendJsonResponse([
        'Status' => true,
        'Message' => '',
        'FileName' => $filename,
        'FileServer' => $file_server_url
    ]);
}


function handlePreparePdf($data) {
    global $UPLOAD_DIR;
    
    $pdf_bytes_b64 = $data['pdf_bytes'] ?? '';
    $filename = $data['filename'] ?? 'document.pdf';
    
    // Decode PDF
    try {
        $pdf_bytes = base64_decode($pdf_bytes_b64, true);
        if ($pdf_bytes === false) {
            throw new Exception('Invalid base64 PDF');
        }
    } catch (Exception $e) {
        sendJsonResponse(['status' => 'error', 'message' => 'Invalid PDF: ' . $e->getMessage()]);
        return;
    }
    
    // Save temp input
    $temp_input = $UPLOAD_DIR . '/temp_prepare_input.pdf';
    file_put_contents($temp_input, $pdf_bytes);
    
    $prepared_pdf = $UPLOAD_DIR . '/prepared_' . $filename;
    
    // Call prepare_pdf.py
    $cmd = "python prepare_pdf.py " . escapeshellarg($temp_input) . " " . escapeshellarg($prepared_pdf) . " 8192";
    error_log("  [CMD] " . $cmd);
    
    $output = [];
    $returnCode = 0;
    exec($cmd . " 2>&1", $output, $returnCode);
    $output_text = implode("\n", $output);
    
    error_log("  Return code: {$returnCode}");
    
    if ($returnCode === 0 && file_exists($prepared_pdf)) {
        $prepared_bytes = file_get_contents($prepared_pdf);
        
        // Extract ByteRange from output
        $byte_range = [0, 0, 0, 0];
        if (preg_match('/ByteRange: \[(\d+), (\d+), (\d+), (\d+)\]/', $output_text, $matches)) {
            $byte_range = [(int)$matches[1], (int)$matches[2], (int)$matches[3], (int)$matches[4]];
        }
        
        $response = [
            'status' => 'success',
            'message' => 'PDF prepared for signing',
            'prepared_pdf' => base64_encode($prepared_bytes),
            'byte_range' => $byte_range,
            'prepared_filename' => basename($prepared_pdf)
        ];
        
        error_log("  ✅ PDF prepared: " . basename($prepared_pdf));
        sendJsonResponse($response);
    } else {
        $error_msg = implode("\n", $output) ?: "Unknown error";
        sendJsonResponse(['status' => 'error', 'message' => "Prepare failed: {$error_msg}"]);
    }
    
    @unlink($temp_input);
}

function handleFinalizeSignature($data) {
    global $UPLOAD_DIR;
    
    $prepared_filename = $data['prepared_filename'] ?? '';
    $signature = $data['signature'] ?? '';
    $byte_range = $data['byte_range'] ?? [];
    $output_filename = $data['output_filename'] ?? 'signed.pdf';
    
    $prepared_pdf = $UPLOAD_DIR . '/' . basename($prepared_filename);
    
    if (!file_exists($prepared_pdf)) {
        sendJsonResponse(['status' => 'error', 'message' => "Prepared PDF not found: {$prepared_filename}"]);
        return;
    }
    
    // Decode signature
    try {
        $sig_bytes = base64_decode($signature, true);
        if ($sig_bytes === false) {
            throw new Exception('Invalid base64 signature');
        }
        $sig_hex = bin2hex($sig_bytes);
    } catch (Exception $e) {
        sendJsonResponse(['status' => 'error', 'message' => 'Invalid signature: ' . $e->getMessage()]);
        return;
    }
    
    $output_pdf = $UPLOAD_DIR . '/' . $output_filename;
    
    // Call insert_signature.py
    $cmd = "python insert_signature.py " . 
        escapeshellarg($prepared_pdf) . " " . 
        escapeshellarg($output_pdf) . " " . 
        escapeshellarg($sig_hex) . " " . 
        escapeshellarg(json_encode($byte_range));
    
    error_log("  [CMD] insert_signature.py");
    
    $output = [];
    $returnCode = 0;
    exec($cmd . " 2>&1", $output, $returnCode);
    
    error_log("  Return code: {$returnCode}");
    
    if ($returnCode === 0 && file_exists($output_pdf)) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $signed_url = "{$protocol}://{$host}/download/{$output_filename}";
        
        $response = [
            'status' => 'success',
            'message' => 'Signature finalized successfully',
            'signed_url' => $signed_url,
            'signed_file' => $output_filename
        ];
        
        error_log("  ✅ Signature finalized: {$output_filename}");
        sendJsonResponse($response);
    } else {
        $error_msg = implode("\n", $output) ?: "Unknown error";
        sendJsonResponse(['status' => 'error', 'message' => "Finalize failed: {$error_msg}"]);
    }
}

function handleFinalizeServerSign($data) {
    global $UPLOAD_DIR, $USER_FILES_DIR;
    
    $filename = $data['filename'] ?? '';
    $signature_b64 = $data['signature'] ?? '';
    
    $file_path = $USER_FILES_DIR . '/' . basename($filename);
    
    if (!file_exists($file_path)) {
        sendJsonResponse(['status' => 'error', 'message' => "File not found: {$filename}"], 404);
        return;
    }
    
    // Convert base64 signature to hex
    try {
        $sig_bytes = base64_decode($signature_b64, true);
        if ($sig_bytes === false) {
            throw new Exception('Invalid base64 signature');
        }
        $sig_hex = bin2hex($sig_bytes);
    } catch (Exception $e) {
        sendJsonResponse(['status' => 'error', 'message' => 'Invalid signature: ' . $e->getMessage()], 400);
        return;
    }
    
    // Step 1: Prepare PDF
    $temp_prepared = $UPLOAD_DIR . '/temp_prepared_' . basename($filename);
    
    $cmd = "python prepare_pdf.py " . escapeshellarg($file_path) . " " . escapeshellarg($temp_prepared) . " 8192";
    $output = [];
    $returnCode = 0;
    exec($cmd . " 2>&1", $output, $returnCode);
    
    error_log("  [prepare_pdf.py] Return code: {$returnCode}");
    
    if ($returnCode !== 0 || !file_exists($temp_prepared)) {
        $error_msg = implode("\n", $output) ?: "Prepare failed";
        sendJsonResponse(['status' => 'error', 'message' => "Prepare failed: {$error_msg}"], 500);
        return;
    }
    
    // Step 2: Insert signature
    $output_filename = str_replace('.pdf', '_signed.pdf', basename($filename));
    $output_file = $USER_FILES_DIR . '/' . $output_filename;
    
    $cmd = "python insert_signature.py " . 
        escapeshellarg($temp_prepared) . " " . 
        escapeshellarg($output_file) . " " . 
        escapeshellarg($sig_hex) . " " . 
        escapeshellarg("8192");
    
    $output = [];
    $returnCode = 0;
    exec($cmd . " 2>&1", $output, $returnCode);
    
    error_log("  [insert_signature.py] Return code: {$returnCode}");
    
    if ($returnCode !== 0 || !file_exists($output_file)) {
        $error_msg = implode("\n", $output) ?: "Insert failed";
        sendJsonResponse(['status' => 'error', 'message' => "Insert failed: {$error_msg}"], 500);
        return;
    }
    
    // Success
    $response = [
        'status' => 'success',
        'signed_file' => $output_filename,
        'message' => "Signed: {$output_filename}"
    ];
    
    error_log("  ✅ Signed: {$output_filename}");
    sendJsonResponse($response);
    
    // Cleanup
    @unlink($temp_prepared);
}

function handleEmbedSignature($data) {
    sendJsonResponse(['status' => 'error', 'message' => 'Not implemented']);
}

function handleSignRequest($data) {
    sendJsonResponse(['status' => 'error', 'message' => 'Not implemented']);
}

function handleGet($path) {
    global $UPLOAD_DIR, $USER_FILES_DIR;
    
    // Handle /list-files
    if ($path === '/list-files') {
        error_log("  → Xử lý /list-files request");
        handleListFiles();
        return;
    }
    
    // Handle /download/<filename>
    if (strpos($path, '/download/') === 0) {
        $filename = substr($path, 10);
        $file_path = $USER_FILES_DIR . '/' . basename($filename);
        
        if (file_exists($file_path) && is_file($file_path)) {
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($file_path));
            header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
            header('Access-Control-Allow-Origin: *');
            readfile($file_path);
            exit;
        } else {
            sendJsonResponse(['status' => 'error', 'message' => 'File not found'], 404);
            return;
        }
    }
    
    // Handle /Upload/ or /Download/
    if (strpos($path, '/Upload/') === 0) {
        $filename = substr($path, 8);
        $file_path = $UPLOAD_DIR . '/' . basename($filename);
    } elseif (strpos($path, '/Download/') === 0) {
        $filename = substr($path, 10);
        $file_path = $UPLOAD_DIR . '/' . basename($filename);
    } else {
        // Serve static files from sample folder
        $file_path = __DIR__ . '/sample' . ($path === '/' ? '/test-bc.htm' : $path);
        
        if (file_exists($file_path) && is_file($file_path)) {
            $content_type = 'text/html';
            $ext = pathinfo($file_path, PATHINFO_EXTENSION);
            
            switch ($ext) {
                case 'js': $content_type = 'application/javascript'; break;
                case 'pdf': $content_type = 'application/pdf'; break;
                case 'css': $content_type = 'text/css'; break;
            }
            
            header('Content-Type: ' . $content_type);
            header('Access-Control-Allow-Origin: *');
            readfile($file_path);
        } else {
            header('HTTP/1.0 404 Not Found');
            echo '<h1>404 Not Found</h1>';
        }
        exit;
    }
    
    if (file_exists($file_path) && is_file($file_path)) {
        header('Content-Type: application/octet-stream');
        header('Content-Length: ' . filesize($file_path));
        header('Access-Control-Allow-Origin: *');
        readfile($file_path);
    } else {
        sendJsonResponse(['status' => 'error', 'message' => 'File not found'], 404);
    }
}

function handleListFiles() {
    global $USER_FILES_DIR;
    
    $files = [];
    
    if (is_dir($USER_FILES_DIR)) {
        $items = scandir($USER_FILES_DIR);
        
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            
            $file_path = $USER_FILES_DIR . '/' . $item;
            
            if (is_file($file_path) && strtolower(pathinfo($item, PATHINFO_EXTENSION)) === 'pdf') {
                $files[] = [
                    'name' => $item,
                    'size' => filesize($file_path),
                    'modified' => filemtime($file_path)
                ];
            }
        }
    }
    
    // Sort by name
    usort($files, function($a, $b) {
        return strcmp($a['name'], $b['name']);
    });
    
    error_log("  ✅ List files: " . count($files) . " PDFs");
    
    sendJsonResponse([
        'status' => 'success',
        'files' => $files
    ]);
}

function sendJsonResponse($data, $httpCode = 200) {
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
?>
