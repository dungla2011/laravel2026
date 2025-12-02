<?php
/**
 * Test script to verify payment export and JSON metadata creation
 * Usage: php test_payment_export.php <event_id> [payment_type]
 */

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Get event ID from CLI argument
$evid = isset($argv[1]) ? $argv[1] : null;
$payment_type = isset($argv[2]) ? $argv[2] : '';

if (!$evid) {
    echo "Usage: php test_payment_export.php <event_id> [payment_type]\n";
    echo "Example: php test_payment_export.php 1 trong_nuoc\n";
    exit(1);
}

echo "Testing payment export for Event ID: $evid\n";
echo "Payment type filter: " . ($payment_type ?: 'all') . "\n";
echo str_repeat("=", 60) . "\n";

// Import the functions from download_payment_event.php
require 'public/tool1/_site/event_mng/download_payment_event.php';

try {
    // Get payments data
    $payments = getPaymentsData($evid, $payment_type);
    echo "\n✓ Retrieved " . $payments->count() . " payment records\n";
    
    if ($payments->count() === 0) {
        echo "⚠ No payments found for event $evid\n";
        exit(0);
    }
    
    // Display sample payment data
    echo "\nSample payment record:\n";
    if ($payments->count() > 0) {
        $sample = $payments->first();
        echo "  - ID: " . $sample->id . "\n";
        echo "  - Name: " . ($sample->_last_name ?? '') . " " . ($sample->_first_name ?? '') . "\n";
        echo "  - Amount: " . ($sample->payed ?? 0) . "\n";
        echo "  - Deduction: " . ($sample->khau_tru ?? 0) . "\n";
        echo "  - Net: " . (($sample->payed ?? 0) - ($sample->khau_tru ?? 0)) . "\n";
    }
    
    // Test JSON metadata preparation
    $paymentData = [];
    foreach ($payments as $payment) {
        $paymentData[] = [
            'id' => $payment->id ?? null,
            'user_event_id' => $payment->user_event_id ?? null,
            'first_name' => $payment->_first_name ?? null,
            'last_name' => $payment->_last_name ?? null,
            'payed' => $payment->payed ?? 0,
            'khau_tru' => $payment->khau_tru ?? 0,
            'thuc_nhan' => ($payment->payed ?? 0) - ($payment->khau_tru ?? 0),
            'tax_number' => $payment->tax_number ?? null,
            'bank_acc_number' => $payment->bank_acc_number ?? null,
            'bank_name_text' => $payment->bank_name_text ?? null,
            'payment_type' => $payment->payment_type ?? null,
        ];
    }
    
    $metadata = [
        'evid' => $evid,
        'created_at' => date('Y-m-d H:i:s'),
        'total_payments' => count($paymentData),
        'payments' => $paymentData,
    ];
    
    $metadataJson = json_encode($metadata, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    echo "\n✓ JSON metadata prepared\n";
    echo "  - Total records: " . count($paymentData) . "\n";
    echo "  - JSON size: " . strlen($metadataJson) . " bytes\n";
    
    // Check /share/pdf_event_bill directory
    $pdfDir = '/share/pdf_event_bill';
    if (!is_dir($pdfDir)) {
        echo "\n⚠ Directory $pdfDir does not exist\n";
        echo "  Creating directory...\n";
        if (@mkdir($pdfDir, 0755, true)) {
            echo "  ✓ Directory created\n";
        } else {
            echo "  ✗ Failed to create directory\n";
            exit(1);
        }
    }
    
    // Test writing JSON file
    $jsonFilePath = $pdfDir . '/ThanhToan_Event_' . $evid . '.json';
    $bytesWritten = file_put_contents($jsonFilePath, $metadataJson);
    
    if ($bytesWritten !== false) {
        echo "\n✓ JSON metadata written to: $jsonFilePath\n";
        echo "  - File size: $bytesWritten bytes\n";
        
        // Verify file can be read back
        $readBack = file_get_contents($jsonFilePath);
        $decoded = json_decode($readBack, true);
        
        if ($decoded && is_array($decoded)) {
            echo "✓ JSON file verified - successfully decoded\n";
            echo "  - Decoded total_payments: " . $decoded['total_payments'] . "\n";
            echo "  - Decoded event ID: " . $decoded['evid'] . "\n";
            echo "  - First payment name: " . ($decoded['payments'][0]['last_name'] ?? 'N/A') . " " . ($decoded['payments'][0]['first_name'] ?? 'N/A') . "\n";
        } else {
            echo "✗ JSON decoding failed\n";
            exit(1);
        }
    } else {
        echo "\n✗ Failed to write JSON file to: $jsonFilePath\n";
        exit(1);
    }
    
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "✓ Payment export test PASSED\n";
    echo "\nJSON metadata is ready for PDF embedding\n";
    
} catch (\Exception $e) {
    echo "\n✗ Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    exit(1);
}
