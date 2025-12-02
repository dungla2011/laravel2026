<?php
// Disable output truncation for exceptions
ini_set('error_reporting', E_ALL);
ini_set('display_errors', '1');

// Custom exception handler to show full trace
set_exception_handler(function (Throwable $e) {
    echo "\n=== FULL EXCEPTION TRACE ===\n";
    echo "File: " . $e->getFile() . "\n";
    echo "Line: " . $e->getLine() . "\n";
    echo "Message: " . $e->getMessage() . "\n\n";
    echo "=== FULL STACK TRACE ===\n";
    echo $e->getTraceAsString();
    echo "\n=== END TRACE ===\n";
});

// Run PHPUnit test
$command = 'php artisan test --filter=testClickSetFieldMultiValueTagList';
passthru($command);
?>
