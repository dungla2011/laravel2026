<?php
/**
 * Simple test to debug file path issues
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Test basic functionality
$test = [
    'message' => 'Test endpoint working',
    'files_dir' => dirname(__DIR__) . '/files',
    'real_path' => realpath(dirname(__DIR__) . '/files'),
    'timestamp' => date('Y-m-d H:i:s')
];

// List files
$filesDir = dirname(__DIR__) . '/files';
$test['files_exist'] = is_dir($filesDir);

if (is_dir($filesDir)) {
    $files = scandir($filesDir);
    $test['files'] = array_filter($files, function($f) {
        return $f !== '.' && $f !== '..';
    });
    $test['file_count'] = count($test['files']);
}

// Try to get filename from query
$filename = $_GET['filename'] ?? '';
if ($filename) {
    $filepath = $filesDir . '/' . $filename;
    $test['requested_file'] = $filename;
    $test['full_path'] = $filepath;
    $test['file_exists'] = file_exists($filepath);
    $test['is_file'] = is_file($filepath);
    $test['readable'] = is_readable($filepath);
    
    if (file_exists($filepath)) {
        $test['file_size'] = filesize($filepath);
        $test['file_time'] = date('Y-m-d H:i:s', filemtime($filepath));
    }
}

echo json_encode($test, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
?>
