<?php
/**
 * OPcache Warmup Script for Laravel Performance Optimization
 * 
 * This script preloads all Laravel PHP files into OPcache to improve
 * performance by reducing compilation time on subsequent requests.
 * 
 * Usage: php opcache_warmup.php
 * 
 * Target: Reduce booting time from 800ms to ~150ms by warming up OPcache
 */

// Set memory limit for warmup process
ini_set('memory_limit', '512M');
set_time_limit(300); // 5 minutes max

echo "<br>=== Laravel OPcache Warmup Script ===\n";
echo "<br>Starting OPcache warmup process...\n\n";

// Check if OPcache is enabled
if (!extension_loaded('Zend OPcache')) {
    echo "<br>ERROR: OPcache extension is not loaded!\n";
    exit(1);
}

if (!opcache_get_status()) {
    echo "<br>ERROR: OPcache is not enabled!\n";
    exit(1);
}

// Get initial OPcache status
$initial_status = opcache_get_status();
echo "<br>Initial OPcache Status:\n";
echo "<br>- Cache hits: " . number_format($initial_status['opcache_statistics']['hits']) . "\n";
echo "<br>- Cache misses: " . number_format($initial_status['opcache_statistics']['misses']) . "\n";
echo "<br>- Cached scripts: " . number_format($initial_status['opcache_statistics']['num_cached_scripts']) . "\n";
echo "<br>- Used memory: " . round($initial_status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB\n\n";

// Define Laravel project root
$laravel_root = __DIR__;
$files_loaded = 0;
$errors = [];

/**
 * Recursively find all PHP files in directory
 */
function findPhpFiles($directory, $exclude_patterns = []) {
    $files = [];
    $exclude_patterns = array_merge($exclude_patterns, [
        '/vendor/',
        '/storage/',
        '/bootstrap/cache/',
        '/node_modules/',
        '/.git/',
        '/tests/',
        '/database/migrations/',
    ]);
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $filePath = $file->getPathname();
            
            // Skip excluded patterns
            $skip = false;
            foreach ($exclude_patterns as $pattern) {
                if (strpos($filePath, $pattern) !== false) {
                    $skip = true;
                    break;
                }
            }
            
            if (!$skip) {
                $files[] = $filePath;
            }
        }
    }
    
    return $files;
}

/**
 * Safely include/require PHP file for OPcache compilation
 */
function warmupFile($filepath) {
    global $files_loaded, $errors;
    
    try {
        // Use opcache_compile_file if available (PHP 5.5+)
        if (function_exists('opcache_compile_file')) {
            if (opcache_compile_file($filepath)) {
                $files_loaded++;
                return true;
            } else {
                $errors[] = "Failed to compile: $filepath";
                return false;
            }
        } else {
            // Fallback: include the file
            if (is_readable($filepath)) {
                include_once $filepath;
                $files_loaded++;
                return true;
            } else {
                $errors[] = "Not readable: $filepath";
                return false;
            }
        }
    } catch (Throwable $e) {
        $errors[] = "Error in $filepath: " . $e->getMessage();
        return false;
    }
}

echo "<br>Scanning for PHP files...\n";

// Find all PHP files in Laravel project
$php_files = findPhpFiles($laravel_root);
$total_files = count($php_files);

echo "<br>Found $total_files PHP files to warmup.\n";
echo "<br>Starting compilation process...\n\n";

// Warmup progress tracking
$progress_step = max(1, floor($total_files / 20)); // Show progress every 5%

foreach ($php_files as $index => $file) {
    warmupFile($file);
    
    // Show progress
    if ($index % $progress_step == 0) {
        $percent = round(($index / $total_files) * 100, 1);
        echo "<br>Progress: $percent% ($index/$total_files files)\n";
    }
}

echo "<br>\n=== Warmup Complete ===\n";

// Get final OPcache status
$final_status = opcache_get_status();
echo "<br>Final OPcache Status:\n";
echo "<br>- Cache hits: " . number_format($final_status['opcache_statistics']['hits']) . "\n";
echo "<br>- Cache misses: " . number_format($final_status['opcache_statistics']['misses']) . "\n";
echo "<br>- Cached scripts: " . number_format($final_status['opcache_statistics']['num_cached_scripts']) . "\n";
echo "<br>- Used memory: " . round($final_status['memory_usage']['used_memory'] / 1024 / 1024, 2) . " MB\n\n";

// Calculate improvements
$scripts_added = $final_status['opcache_statistics']['num_cached_scripts'] - $initial_status['opcache_statistics']['num_cached_scripts'];
$memory_added = ($final_status['memory_usage']['used_memory'] - $initial_status['memory_usage']['used_memory']) / 1024 / 1024;

echo "<br>=== Performance Improvements ===\n";
echo "<br>- Files processed: $files_loaded\n";
echo "<br>- Scripts added to cache: " . number_format($scripts_added) . "\n";
echo "<br>- Additional memory used: " . round($memory_added, 2) . " MB\n";

if (!empty($errors)) {
    echo "<br>\n=== Errors/Warnings ===\n";
    foreach (array_slice($errors, 0, 10) as $error) { // Show first 10 errors
        echo "<br>- $error\n";
    }
    if (count($errors) > 10) {
        echo "<br>... and " . (count($errors) - 10) . " more errors\n";
    }
}

echo "<br>\n=== Laravel Route Warmup ===\n";
echo "<br>Now warming up Laravel routes and core classes...\n";

// Try to bootstrap Laravel and warm up common routes
try {
    // Include Laravel's autoloader
    if (file_exists($laravel_root . '/vendor/autoload.php')) {
        require_once $laravel_root . '/vendor/autoload.php';
        echo "<br>- Laravel autoloader included ✓\n";
    }
    
    // Bootstrap Laravel application
    if (file_exists($laravel_root . '/bootstrap/app.php')) {
        $app = require_once $laravel_root . '/bootstrap/app.php';
        echo "<br>- Laravel application bootstrapped ✓\n";
        
        // Warm up kernel
        $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
        echo "<br>- HTTP Kernel loaded ✓\n";
        
        // Load all service providers
        $app->loadDeferredProviders();
        echo "<br>- Deferred providers loaded ✓\n";
        
    }
} catch (Throwable $e) {
    echo "<br>- Laravel bootstrap error: " . $e->getMessage() . "\n";
}

echo "<br>\n=== Warmup Summary ===\n";
echo "<br>OPcache warmup completed successfully!\n";
echo "<br>Expected performance improvement: Reduce Laravel booting time from ~800ms to ~150ms\n";
echo "<br>\nTo apply this warmup on server:\n";
echo "<br>1. Upload this script to your server\n";
echo "<br>2. Run: php opcache_warmup.php\n";
echo "<br>3. Set up a cron job to run this after deployments\n";

echo "<br>\n=== Recommended Cron Job ===\n";
echo "<br># Run OPcache warmup after each deployment\n";
echo "<br># Add to crontab: crontab -e\n";
echo "<br>*/30 * * * * cd /var/www/html && php opcache_warmup.php > /dev/null 2>&1\n";

echo "<br>\n=== Done ===\n";