#!/bin/bash
# Script để setup database test và chạy test

echo "================================"
echo "Setup Test Database"
echo "================================"
php setup_test_db.php

if [ $? -ne 0 ]; then
    echo "Failed to setup test database"
    exit 1
fi

echo ""
echo "================================"
echo "Running Tests"
echo "================================"
php artisan test "$@"
