<?php
/**
 * Network Performance Optimizer
 * Optimize network-related bottlenecks
 */

echo "<br>=== Network Performance Optimization ===\n";

// 1. Check DNS resolution speed
echo "<br>1. DNS Performance Test:\n";
$dns_servers = ['8.8.8.8', '1.1.1.1', 'localhost'];
foreach ($dns_servers as $dns) {
    $start = microtime(true);
    $result = gethostbyname('google.com');
    $dns_time = (microtime(true) - $start) * 1000;
    echo "<br>   DNS resolve time: " . round($dns_time, 2) . "ms\n";
}

// 2. Disable network calls if possible
echo "<br>\n2. Network Optimization Suggestions:\n";
echo "<br>   - Use local CDN/cache for external resources\n";
echo "<br>   - Implement HTTP caching headers\n";
echo "<br>   - Use connection pooling\n";
echo "<br>   - Disable external API calls in dev/staging\n";

// 3. Check if code makes external calls
echo "<br>\n3. Code Analysis for External Calls:\n";
$external_patterns = [
    'file_get_contents' => 'HTTP requests',
    'curl_exec' => 'cURL requests', 
    'fopen' => 'File/URL opening',
    'simplexml_load_file' => 'XML loading',
    'json_decode' => 'Possible API responses'
];

$code_files = glob(__DIR__ . '/../app/**/*.php');
$found_calls = [];

foreach ($code_files as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        foreach ($external_patterns as $pattern => $description) {
            if (strpos($content, $pattern) !== false) {
                $found_calls[$pattern][] = basename($file);
            }
        }
    }
}

foreach ($found_calls as $pattern => $files) {
    echo "<br>   Found '$pattern' in: " . implode(', ', array_unique($files)) . "\n";
}

// 4. Laravel-specific optimizations
echo "<br>\n4. Laravel Network Optimizations:\n";
echo "<br>   php artisan config:cache    # Cache configuration\n";
echo "<br>   php artisan route:cache     # Cache routes\n";
echo "<br>   php artisan view:cache      # Cache views\n";
echo "<br>   composer dump-autoload -o   # Optimize autoloader\n";

// 5. Check for slow external services
echo "<br>\n5. Common External Services to Check:\n";
$services = [
    'https://api.github.com' => 'GitHub API',
    'https://httpbin.org/delay/1' => 'Test slow service',
    'https://www.google.com' => 'Google (baseline)'
];

foreach ($services as $url => $description) {
    $start = microtime(true);
    $headers = @get_headers($url, 1);
    $time = (microtime(true) - $start) * 1000;
    $status = $headers ? 'OK' : 'FAILED';
    echo "<br>   $description: " . round($time, 2) . "ms ($status)\n";
}

echo "<br>\n=== Optimization Complete ===\n";
?>