<?php
//$time = microtime(1);
//use App\Models\User_Meta;
//
//$GLOBALS['DISABLE_DEBUG_BAR'] = 0;
use App\Models\SiteMng;

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Try multiple temp directory options
$fold = null;
$temp_options = [
    sys_get_temp_dir() . "/glx_web",
    '/var/www/html/storage/temp',
    '/tmp/glx_web',
];

foreach ($temp_options as $temp_dir) {
    if (@mkdir($temp_dir, 0777, true) || @is_dir($temp_dir)) {
        if (is_writable($temp_dir)) {
            $fold = $temp_dir;
            echo "✓ Using temp dir: $fold\n";
            break;
        }
    }
}

if (!$fold) {
    echo "⚠️  Warning: Could not create any writable temp directory\n";
    // Use a failsafe - just use current temp without creating
    $fold = sys_get_temp_dir();
    echo "✓ Fallback to: $fold\n";
}

if(!file_exists($fold)) {
    die("Can not create $fold");
}
