<?php
/**
 * Performance Profiler - Find bottlenecks in non-DB code
 * Add this to your slow request to identify the issue
 */

class PerformanceProfiler {
    private static $checkpoints = [];
    private static $start_time;

    public static function start($label = 'start') {
        self::$start_time = microtime(true);
        self::$checkpoints[$label] = microtime(true);
        error_log("PROFILER START: $label");
    }

    public static function checkpoint($label) {
        $current_time = microtime(true);
        $since_start = ($current_time - self::$start_time) * 1000;

        $last_checkpoint = end(self::$checkpoints);
        $since_last = ($current_time - $last_checkpoint) * 1000;

        self::$checkpoints[$label] = $current_time;

        error_log("PROFILER: $label - " . round($since_start, 2) . "ms total, " . round($since_last, 2) . "ms since last");
    }

    public static function end($label = 'end') {
        self::checkpoint($label);
        $total_time = (microtime(true) - self::$start_time) * 1000;
        error_log("PROFILER END: Total time " . round($total_time, 2) . "ms");

        // Show breakdown
        $prev_time = self::$start_time;
        foreach (self::$checkpoints as $checkpoint_label => $time) {
            if ($checkpoint_label === 'start') continue;
            $duration = ($time - $prev_time) * 1000;
            error_log("PROFILER BREAKDOWN: $checkpoint_label took " . round($duration, 2) . "ms");
            $prev_time = $time;
        }
    }
}

// Usage example - Add these lines to your slow request:

/*
// At the very beginning of your request
PerformanceProfiler::start('request_start');

// After Laravel bootstrap
PerformanceProfiler::checkpoint('laravel_boot');

// After loading config
PerformanceProfiler::checkpoint('config_loaded');

// After session start
PerformanceProfiler::checkpoint('session_started');

// After middleware
PerformanceProfiler::checkpoint('middleware_done');

// After controller logic
PerformanceProfiler::checkpoint('controller_done');

// After view rendering
PerformanceProfiler::checkpoint('view_rendered');

// At the end
PerformanceProfiler::end('request_complete');
*/

// Alternative: Quick one-liner profiler
function profile_point($label) {
    static $start_time, $last_time;

    if (!$start_time) {
        $start_time = $last_time = microtime(true);
        error_log("PROFILE START: $label");
        return;
    }

    $current = microtime(true);
    $total = ($current - $start_time) * 1000;
    $since_last = ($current - $last_time) * 1000;

    error_log("PROFILE: $label - {$total}ms total, {$since_last}ms since last");
    $last_time = $current;
}

// Even simpler - just add this line wherever you want to check timing:
function quick_time($msg = '') {
    static $start;
    if (!$start) $start = microtime(true);
    $elapsed = (microtime(true) - $start) * 1000;
    error_log("TIME: $msg - " . round($elapsed, 2) . "ms");
}

echo "<br>Performance profiler loaded. Add timing calls to your code.\n";
?>

<?php
// Quick system check
echo "<br>\n=== Quick System Performance Check ===\n";

// 1. File I/O test
echo "<br>1. File I/O Performance:\n";
$start = microtime(true);
file_put_contents('/tmp/test_write.txt', str_repeat('x', 1000));
$write_time = (microtime(true) - $start) * 1000;

$start = microtime(true);
$content = file_get_contents('/tmp/test_write.txt');
$read_time = (microtime(true) - $start) * 1000;
unlink('/tmp/test_write.txt');

echo "<br>   File write: " . round($write_time, 2) . "ms\n";
echo "<br>   File read: " . round($read_time, 2) . "ms\n";

// 2. Network test
echo "<br>\n2. Network Performance:\n";
$start = microtime(true);
$headers = @get_headers('https://www.google.com', 1);
$network_time = (microtime(true) - $start) * 1000;
echo "<br>   DNS + HTTP: " . round($network_time, 2) . "ms\n";

// 3. CPU test
echo "<br>\n3. CPU Performance:\n";
$start = microtime(true);
for ($i = 0; $i < 100000; $i++) {
    md5($i);
}
$cpu_time = (microtime(true) - $start) * 1000;
echo "<br>   MD5 hash 100k times: " . round($cpu_time, 2) . "ms\n";

// 4. Memory test
echo "<br>\n4. Memory Performance:\n";
$start = microtime(true);
$array = [];
for ($i = 0; $i < 10000; $i++) {
    $array[] = str_repeat('x', 100);
}
$memory_time = (microtime(true) - $start) * 1000;
echo "<br>   Allocate 1MB array: " . round($memory_time, 2) . "ms\n";
echo "<br>   Peak memory: " . round(memory_get_peak_usage(true) / 1024 / 1024, 2) . "MB\n";

//Đọc /etc/resolv.conf

echo "<br>\nContents of /etc/resolv.conf:\n";
if (file_exists('/etc/resolv.conf')) {
    echo "<pre>";
    echo file_get_contents('/etc/resolv.conf');
    echo "</pre>";
} else {
    echo "<br>   /etc/resolv.conf not found.\n";
}

echo "<br>\n=== Compare these numbers between servers ===\n";
?>
