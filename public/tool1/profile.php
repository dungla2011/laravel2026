<?php
$marks = [];
function mark($label) {
    global $marks;
    $marks[$label] = microtime(1);
    $elapsed = microtime(1) - array_values($marks)[0];
    echo "[" . count($marks) . "] $label: " . number_format($elapsed, 4) . "s\n";
}

mark('START');

require __DIR__.'/../../vendor/autoload.php';
mark('AUTOLOAD');

$app = require_once __DIR__.'/../../bootstrap/app.php';
mark('APP BOOTSTRAP');

$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
mark('KERNEL MAKE');

$request = \Illuminate\Http\Request::capture();
mark('REQUEST CAPTURE');

echo "=== STARTING KERNEL HANDLE (this is where the slowness is) ===\n";
$response = $kernel->handle($request);
mark('KERNEL HANDLE');

$kernel->terminate($request, $response);
mark('KERNEL TERMINATE');

echo "\n=== TOTAL TIME ===\n";
$total = microtime(1) - array_values($marks)[0];
echo "Total: " . number_format($total, 4) . "s\n";
?>


