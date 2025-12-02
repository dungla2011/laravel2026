<?php
/**
 * Download payment files (Excel, PDF)
 * URL: download_payment_file.php?evid=6&payment_type=trong_nuoc&file=excel
 */

// Get parameters
$evid = $_GET['evid'] ?? null;
$payment_type = $_GET['payment_type'] ?? '';
$file_type = $_GET['file'] ?? 'excel'; // 'excel' or 'pdf'
$haveSign = isset($_GET['have_sign']) ? true : false;

if (!$evid) {
    http_response_code(400);
    die('Missing evid parameter');
}

// Load helper functions from shared library
require_once __DIR__ . '/lib_sign.php';

// Determine file path
if ($file_type === 'excel') {
    $filePath = getFileNameExcel($evid, $payment_type);
    $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
    $disposition = 'attachment; filename="' . basename($filePath) . '"';
} else if ($file_type === 'pdf') {
    $filePath = getFileNamePdf($evid, $payment_type, $haveSign);
    if(file_exists($filePath)) {
        // File exists
    } else if($haveSign) {
        // Fallback to unsigned PDF if signed not found
        $filePath = getFileNamePdf($evid, $payment_type, false);
    }
    $mimeType = 'application/pdf';
    // PDF: inline (view online) instead of attachment (download)
    $disposition = 'inline; filename="' . basename($filePath) . '"';
} else {
    http_response_code(400);
    die('Invalid file type. Use "excel" or "pdf"');
}

// Check if file exists
if (!file_exists($filePath)) {
    http_response_code(404);
    die('File not found: ' . htmlspecialchars($filePath));
}

// Check file size
$fileSize = filesize($filePath);
if ($fileSize === false || $fileSize <= 0) {
    http_response_code(500);
    die('Cannot read file size');
}

// Stream file to browser
header('Content-Type: ' . $mimeType);
header('Content-Length: ' . $fileSize);
header('Content-Disposition: ' . $disposition);
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Read and output file in chunks
$chunkSize = 8192;
$handle = fopen($filePath, 'rb');
if (!$handle) {
    http_response_code(500);
    die('Cannot open file for reading');
}

while (!feof($handle)) {
    $chunk = fread($handle, $chunkSize);
    if ($chunk === false) {
        break;
    }
    echo $chunk;
}

fclose($handle);
exit;
?>
