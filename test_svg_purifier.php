<?php
/**
 * Test SVG with HTMLPurifier
 */

require "vendor/autoload.php";
$app = require_once 'bootstrap/app.php';

use App\Support\HTMLPurifierSupport;

$svg_code = <<<'SVG'
<svg width="150" height="90" style="filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));">
    <ellipse cx="75" cy="45" rx="65" ry="35" fill="#fff" stroke="orange" stroke-width="3" style="transform: skewX(-15deg); transform-origin: 75px 45px;"></ellipse>
    <text x="75" y="56" font-size="30" text-anchor="middle" fill="orange" letter-spacing="2" style="font-family: Arial, sans-serif;">GLX</text>
</svg>
SVG;

echo "=== SVG HTML Purifier Test ===\n\n";
echo "Input SVG:\n";
echo $svg_code . "\n\n";

echo "=== Testing with HTMLPurifierSupport::clean() ===\n";
$cleaned = HTMLPurifierSupport::clean($svg_code);

echo "Output:\n";
echo $cleaned . "\n\n";

// Also test if transform made it through
if (strpos($cleaned, 'transform') !== false) {
    echo "✅ SUCCESS: 'transform' style attribute is preserved!\n";
} else {
    echo "❌ FAILED: 'transform' style attribute was removed\n";
}

// Test individual components
echo "\n=== Testing individual SVG elements ===\n";

$test_cases = [
    'svg with style filter' => '<svg style="filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));"></svg>',
    'ellipse with transform' => '<ellipse cx="75" cy="45" rx="65" ry="35" fill="#fff" stroke="orange" stroke-width="3" style="transform: skewX(-15deg); transform-origin: 75px 45px;"></ellipse>',
    'text with font-family' => '<text x="75" y="56" font-size="30" text-anchor="middle" fill="orange" letter-spacing="2" style="font-family: Arial, sans-serif;">GLX</text>',
    'animate element' => '<animate attributename="cx" from="0" to="100" dur="3s" repeatcount="indefinite"></animate>',
];

foreach ($test_cases as $name => $code) {
    echo "\n Test: $name\n Input:  $code\n";
    $result = HTMLPurifierSupport::clean($code);
    echo " Output: $result\n";
    
    if ($result !== $code) {
        echo " ⚠️  Some attributes were modified\n";
    }
}
?>
