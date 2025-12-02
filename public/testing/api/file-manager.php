<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');
/**
 * File Management API for PDF Signing
 * Endpoint: /testing/api/
 */

// Start output buffering to catch any errors
ob_start();
//SetTime zone ha noi


// Set error handler to catch everything
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    $error = [
        'error' => $errstr,
        'errno' => $errno,
        'file' => $errfile,
        'line' => $errline
    ];
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'PHP Error', 'details' => $error]);
    exit;
}, E_ALL);

set_exception_handler(function($e) {
    ob_end_clean();
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Exception: ' . $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
    exit;
});

error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in output

// Headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    ob_end_clean();
    exit();
}

// Configuration
define('FILES_DIR', dirname(__DIR__) . '/files');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
define('ALLOWED_TYPES', ['pdf', 'doc', 'docx', 'xls', 'xlsx']);

// Include PDF Signature Extractor library
require_once __DIR__ . '/PDFSignatureExtractor.php';

// Ensure files directory exists
if (!is_dir(FILES_DIR)) {
    mkdir(FILES_DIR, 0755, true);
}

// Route
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'upload-file':
        handleUploadFile();
        break;
    case 'list-files':
        handleListFiles();
        break;
    case 'delete-file':
        handleDeleteFile();
        break;
    case 'download-file':
        handleDownloadFile();
        break;
    case 'get-signatures':
        handleGetSignatures();
        break;
    case 'debug-pdf':
        handleDebugPdf();
        break;
    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Action not found']);
        break;
}

// End output buffering and flush
ob_end_flush();

/**
 * Upload File Handler
 */
function handleUploadFile() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    error_log('FILES: ' . print_r($_FILES, true));

    if (!isset($_FILES['files'])) {
        echo json_encode(['success' => false, 'message' => 'No files provided', 'debug' => $_FILES]);
        return;
    }

    $uploadedFiles = [];
    $errors = [];

    foreach ($_FILES['files']['tmp_name'] as $key => $tmpName) {
        $filename = basename($_FILES['files']['name'][$key]);
        $filesize = $_FILES['files']['size'][$key];
        $filetype = $_FILES['files']['type'][$key];

        error_log("Processing file: $filename, tmp: $tmpName, size: $filesize");

        // Validation
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, ALLOWED_TYPES)) {
            $errors[] = "File $filename: Định dạng không được hỗ trợ";
            continue;
        }

        if ($filesize > MAX_FILE_SIZE) {
            $errors[] = "File $filename: Dung lượng vượt quá 50MB";
            continue;
        }

        // Generate unique filename if exists
        $targetFile = FILES_DIR . '/' . $filename;
        $counter = 1;
        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
        while (file_exists($targetFile)) {
            $targetFile = FILES_DIR . '/' . $nameWithoutExt . '_' . $counter . '.' . $ext;
            $counter++;
        }

        error_log("Target file: $targetFile, FILES_DIR exists: " . (is_dir(FILES_DIR) ? 'yes' : 'no') . ", is_uploaded: " . (is_uploaded_file($tmpName) ? 'yes' : 'no'));

        // Move uploaded file
        if (move_uploaded_file($tmpName, $targetFile)) {
            chmod($targetFile, 0644);
            $uploadedFiles[] = basename($targetFile);
            error_log("File uploaded successfully: $targetFile");
        } else {
            $errors[] = "File $filename: Lỗi upload";
            error_log("Failed to move file from $tmpName to $targetFile");
        }
    }

    echo json_encode([
        'success' => count($uploadedFiles) > 0,
        'uploaded' => $uploadedFiles,
        'errors' => $errors,
        'message' => count($uploadedFiles) . ' file được upload thành công'
    ]);
}

/**
 * List Files Handler
 */
function handleListFiles() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $files = [];

    if (is_dir(FILES_DIR)) {
        $dirContents = scandir(FILES_DIR);
        
        foreach ($dirContents as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $filepath = FILES_DIR . '/' . $item;
            
            if (is_file($filepath)) {
                $ext = strtolower(pathinfo($item, PATHINFO_EXTENSION));
                
                if (in_array($ext, ALLOWED_TYPES)) {
                    $files[] = [
                        'name' => $item,
                        'size' => filesize($filepath),
                        'date' => filemtime($filepath) * 1000, // Convert to milliseconds
                        'ext' => $ext,
                        'path' => $filepath
                    ];
                }
            }
        }
    }

    // Sort by date (newest first)
    usort($files, function($a, $b) {
        return $b['date'] - $a['date'];
    });

    echo json_encode([
        'success' => true,
        'files' => $files,
        'count' => count($files)
    ]);
}

/**
 * Delete File Handler
 */
function handleDeleteFile() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $input = json_decode(file_get_contents('php://input'), true);
    $filename = $input['filename'] ?? '';

    if (empty($filename)) {
        echo json_encode(['success' => false, 'message' => 'Filename required']);
        return;
    }

    // Security: Prevent directory traversal
    if (strpos($filename, '/') !== false || strpos($filename, '\\') !== false || $filename === '.' || $filename === '..') {
        echo json_encode(['success' => false, 'message' => 'Invalid filename']);
        return;
    }

    $filepath = FILES_DIR . '/' . $filename;

    // Check if file exists and is in files directory
    if (!file_exists($filepath) || !is_file($filepath)) {
        echo json_encode(['success' => false, 'message' => 'File not found']);
        return;
    }

    // Check if file is in our directory (security)
    if (realpath($filepath) !== realpath(FILES_DIR . '/' . $filename)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file path']);
        return;
    }

    // Delete file
    if (unlink($filepath)) {
        echo json_encode([
            'success' => true,
            'message' => 'File deleted successfully',
            'filename' => $filename
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete file']);
    }
}

/**
 * Download File Handler
 */
function handleDownloadFile() {
    $filename = $_GET['filename'] ?? '';

    if (empty($filename)) {
        http_response_code(400);
        echo 'Filename required';
        return;
    }

    // Security: Prevent directory traversal
    if (strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
        http_response_code(403);
        echo 'Invalid filename';
        return;
    }

    $filepath = FILES_DIR . '/' . $filename;

    if (!file_exists($filepath) || !is_file($filepath)) {
        http_response_code(404);
        echo 'File not found';
        return;
    }

    // Security: Check real path
    if (realpath($filepath) !== realpath(FILES_DIR . '/' . $filename)) {
        http_response_code(403);
        echo 'Forbidden';
        return;
    }

    // Determine MIME type
    $mimeTypes = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    $mimeType = $mimeTypes[$ext] ?? 'application/octet-stream';

    // Send file
    header('Content-Type: ' . $mimeType);
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filepath));
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    readfile($filepath);
}

/**
 * Debug PDF Handler - Show first 2000 bytes of PDF
 */
function handleDebugPdf() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $filename = $_GET['filename'] ?? '';

    if (empty($filename)) {
        echo json_encode(['success' => false, 'message' => 'Filename required']);
        return;
    }

    $filepath = FILES_DIR . '/' . $filename;
    $filepath = str_replace('\\', '/', $filepath);

    error_log("DEBUG PDF: filepath = $filepath");
    error_log("DEBUG PDF: file_exists = " . (file_exists($filepath) ? 'YES' : 'NO'));
    error_log("DEBUG PDF: is_file = " . (is_file($filepath) ? 'YES' : 'NO'));

    if (!file_exists($filepath)) {
        // List available files
        $files = scandir(FILES_DIR);
        echo json_encode([
            'success' => false,
            'message' => 'File not found',
            'filepath' => $filepath,
            'files_dir' => FILES_DIR,
            'available_files' => array_filter($files, function($f) { return $f !== '.' && $f !== '..'; })
        ]);
        return;
    }

    $pdfContent = file_get_contents($filepath);
    if ($pdfContent === false) {
        echo json_encode(['success' => false, 'message' => 'Could not read file']);
        return;
    }

    // Return first 2000 bytes and some debug info
    echo json_encode([
        'success' => true,
        'filename' => $filename,
        'filepath' => $filepath,
        'file_size' => filesize($filepath),
        'first_100_bytes' => substr($pdfContent, 0, 100),
        'sample_content' => base64_encode(substr($pdfContent, 0, 2000)),
        'contains_creator' => strpos($pdfContent, '/Creator') !== false,
        'contains_author' => strpos($pdfContent, '/Author') !== false,
        'contains_sig' => strpos($pdfContent, '/Sig') !== false,
        'contains_signature_text' => (
            preg_match('/(?:Đã\s+ký|Ký)\s+(?:bởi|do):/u', $pdfContent) ||
            preg_match('/Signature:/i', $pdfContent) ||
            preg_match('/Signed by:/i', $pdfContent)
        )
    ]);
}

/**
 * Get Signatures from PDF Handler
 */
function handleGetSignatures() {
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        return;
    }

    $filename = $_GET['filename'] ?? '';

    if (empty($filename)) {
        echo json_encode([
            'success' => true,
            'filename' => '',
            'signatures' => [],
            'count' => 0,
            'message' => 'No filename provided'
        ]);
        return;
    }

    // Security: Prevent directory traversal
    if (strpos($filename, '/') !== false || strpos($filename, '\\') !== false) {
        echo json_encode([
            'success' => true,
            'filename' => $filename,
            'signatures' => [],
            'count' => 0,
            'message' => 'Invalid filename'
        ]);
        return;
    }

    $filepath = FILES_DIR . '/' . $filename;
    
    // For Windows, also try with backslashes
    $filepath = str_replace('\\', '/', $filepath);

    error_log("DEBUG: Checking signatures for file: $filepath");
    error_log("DEBUG: File exists: " . (file_exists($filepath) ? 'YES' : 'NO'));
    error_log("DEBUG: Is file: " . (is_file($filepath) ? 'YES' : 'NO'));
    error_log("DEBUG: FILES_DIR: " . FILES_DIR);
    error_log("DEBUG: Real path: " . (file_exists($filepath) ? realpath($filepath) : 'N/A'));

    if (!file_exists($filepath) || !is_file($filepath)) {
        error_log("ERROR: File not found: $filepath");
        // Return empty signatures instead of error
        echo json_encode([
            'success' => true,
            'filename' => $filename,
            'signatures' => [],
            'count' => 0,
            'message' => 'File not found or no signatures',
            'debug' => [
                'filepath' => $filepath,
                'files_dir' => FILES_DIR,
                'file_exists' => file_exists($filepath),
                'is_file' => is_file($filepath)
            ]
        ]);
        return;
    }

    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if ($ext === 'pdf') {
        error_log("DEBUG: Extracting signatures from PDF: $filename");
        $signatures = extractPdfSignatures($filepath);
        error_log("DEBUG: Found " . count($signatures) . " signatures");
        echo json_encode([
            'success' => true,
            'filename' => $filename,
            'signatures' => $signatures,
            'count' => count($signatures)
        ]);
    } else {
        echo json_encode([
            'success' => true,
            'filename' => $filename,
            'signatures' => [],
            'count' => 0,
            'message' => 'Only PDF files are supported',
            'extension' => $ext
        ]);
    }
}

/**
 * Extract signatures from PDF file using PDFSignatureExtractor
 */
function extractPdfSignatures($filepath) {
    $signatures = [];

    try {
        error_log("DEBUG: Starting extractPdfSignatures for: $filepath");
        
        // Use PDFSignatureExtractor library
        $extractor = new PDFSignatureExtractor($filepath);
        $extractedSigs = $extractor->extract();
        
        error_log("DEBUG: PDFSignatureExtractor found " . count($extractedSigs) . " signatures");
        
        // Convert from extractor format to our format
        foreach ($extractedSigs as $idx => $sig) {
            $signatures[] = [
                'name' => $sig['Name'] ?? '',
                'timestamp' => $sig['Date'] ?? date('d/m/Y H:i', filemtime($filepath)),
                'location' => $sig['Location'] ?? 'PDF Document',
                'reason' => $sig['Reason'] ?? 'Digital Signature',
                'contact' => $sig['ContactInfo'] ?? '',
                'type' => $sig['SignatureType'] ?? '',
                'source' => 'PDFSignatureExtractor'
            ];
            error_log("DEBUG: Added signature: " . $sig['Name']);
        }

    } catch (Exception $e) {
        error_log("ERROR extracting signatures: " . $e->getMessage());
        // Return empty array if extraction fails
    }

    return $signatures;
}

/**
 * Extract signature details from signature object
 */
function extractSignatureDetails($sigObject) {
    $sig = [
        'name' => '',
        'timestamp' => date('d/m/Y H:i'),
        'location' => 'PDF Document',
        'reason' => 'Digital Signature',
        'source' => 'PDF signature object'
    ];

    // Extract M (modification date)
    if (preg_match('/\/M\s*\(\s*([^)]+)\s*\)/', $sigObject, $matches)) {
        $dateStr = $matches[1];
        error_log("DEBUG: Found M field: $dateStr");
        // Parse PDF date format: D:YYYYMMDDHHmmSS
        if (preg_match('/D:(\d{4})(\d{2})(\d{2})(\d{2})(\d{2})(\d{2})/', $dateStr, $dateMatches)) {
            $sig['timestamp'] = "{$dateMatches[3]}/{$dateMatches[2]}/{$dateMatches[1]} {$dateMatches[4]}:{$dateMatches[5]}:{$dateMatches[6]}";
        }
    }

    // Extract Name
    if (preg_match('/\/Name\s*\(\s*([^)]+)\s*\)/', $sigObject, $matches)) {
        $name = trim($matches[1], ' ()');
        $sig['name'] = $name;
        error_log("DEBUG: Found Name field: $name");
    }

    // Extract Reason
    if (preg_match('/\/Reason\s*\(\s*([^)]+)\s*\)/', $sigObject, $matches)) {
        $sig['reason'] = trim($matches[1], ' ()');
    }

    // Extract Location
    if (preg_match('/\/Location\s*\(\s*([^)]+)\s*\)/', $sigObject, $matches)) {
        $sig['location'] = trim($matches[1], ' ()');
    }

    return $sig;
}

