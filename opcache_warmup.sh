#!/bin/bash

# OPcache Warmup Script for Laravel Performance
# Target: Reduce booting time from 800ms to 150ms

echo "<br>=== Laravel OPcache Warmup Script ==="
echo "<br>Starting warmup process for mon.lad.vn..."

# Navigate to Laravel root
cd /var/www/html

echo "<br>1. Running Laravel optimization commands..."
php artisan config:cache
php artisan route:cache  
php artisan view:cache
php artisan event:cache
composer dump-autoload --optimize --classmap-authoritative

echo "<br>2. Warming up OPcache by hitting key routes..."

# Define common routes to warm up
routes=(
    "/"
    "/api/health"
    "/login"
    "/dashboard"
    "/monitor"
    "/config"
)

echo "<br>Hitting routes to warm up OPcache..."
for route in "${routes[@]}"; do
    echo "<br>- Warming up: $route"
    curl -s -k "https://mon.lad.vn$route" > /dev/null 2>&1
    sleep 0.1
done

echo "<br>3. Loading all PHP files for OPcache compilation..."

# Use find to get all PHP files and compile them
find /var/www/html -name "*.php" \
    -not -path "*/vendor/*" \
    -not -path "*/storage/*" \
    -not -path "*/node_modules/*" \
    -not -path "*/tests/*" \
    -not -path "*/.git/*" \
    | head -500 \
    | while read file; do
        php -l "$file" > /dev/null 2>&1
    done

echo "<br>4. Generating additional traffic for OPcache warmup..."

# Generate some artificial traffic to warm up cache
for i in {1..20}; do
    curl -s -k "https://mon.lad.vn/" > /dev/null 2>&1 &
    curl -s -k "https://mon.lad.vn/api/monitor/status" > /dev/null 2>&1 &
    sleep 0.05
done

wait # Wait for background processes

echo "<br>5. Checking OPcache status..."
php -r "
if (function_exists('opcache_get_status')) {
    \$status = opcache_get_status();
    if (\$status) {
        echo 'OPcache Status:' . PHP_EOL;
        echo '- Cache hits: ' . number_format(\$status['opcache_statistics']['hits']) . PHP_EOL;
        echo '- Cache misses: ' . number_format(\$status['opcache_statistics']['misses']) . PHP_EOL;
        echo '- Cached scripts: ' . number_format(\$status['opcache_statistics']['num_cached_scripts']) . PHP_EOL;
        echo '- Used memory: ' . round(\$status['memory_usage']['used_memory'] / 1024 / 1024, 2) . ' MB' . PHP_EOL;
        echo '- Hit rate: ' . round(\$status['opcache_statistics']['opcache_hit_rate'], 2) . '%' . PHP_EOL;
    } else {
        echo 'OPcache is disabled' . PHP_EOL;
    }
} else {
    echo 'OPcache extension not found' . PHP_EOL;
}
"

echo "<br>"
echo "<br>=== Warmup Complete ==="
echo "<br>Expected improvement: Laravel booting time should reduce from 800ms to ~150ms"
echo "<br>Monitor performance using Laravel debugbar"
echo "<br>"
echo "<br>To run this regularly, add to cron:"
echo "<br>*/30 * * * * /var/www/html/opcache_warmup.sh > /var/log/opcache_warmup.log 2>&1"
echo "<br>"