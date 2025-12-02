#!/bin/bash

# Fix Puppeteer to use system Chromium
# This speeds up installation and reduces disk space

echo "🔧 Fixing Puppeteer to use system Chromium"
echo "=========================================="
echo ""

# Check if running in correct directory
if [ ! -f "task-cli/screenshot-service.js" ]; then
    echo "❌ Error: Must run from Laravel root directory"
    echo "   cd /var/www/html && bash fix-puppeteer-chromium.sh"
    exit 1
fi

# Step 1: Install system Chromium
echo "1️⃣ Installing system Chromium..."
if command -v apt-get &> /dev/null; then
    apt-get update
    apt-get install -y chromium-browser chromium 2>/dev/null || snap install chromium 2>/dev/null
elif command -v yum &> /dev/null; then
    yum install -y chromium
else
    echo "⚠️  Unknown package manager, trying snap..."
    snap install chromium
fi

# Find Chromium path
CHROMIUM_PATH=$(which chromium || which chromium-browser || echo "/snap/bin/chromium")

if [ ! -f "$CHROMIUM_PATH" ]; then
    echo "❌ Chromium not found after installation!"
    exit 1
fi

echo "✅ Chromium installed: $CHROMIUM_PATH"
echo ""

# Step 2: Clean npm
echo "2️⃣ Cleaning npm cache..."
rm -rf node_modules package-lock.json
npm cache clean --force
echo "✅ Cache cleaned"
echo ""

# Step 3: Install without downloading Chromium
echo "3️⃣ Installing npm dependencies (without Chromium download)..."
PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true npm install
echo "✅ Dependencies installed"
echo ""

# Step 4: Set environment variable
echo "4️⃣ Setting PUPPETEER_EXECUTABLE_PATH..."
export PUPPETEER_EXECUTABLE_PATH="$CHROMIUM_PATH"

# Add to .env if not exists
if [ -f ".env" ]; then
    if ! grep -q "PUPPETEER_EXECUTABLE_PATH" .env; then
        echo "" >> .env
        echo "# Puppeteer Configuration" >> .env
        echo "PUPPETEER_EXECUTABLE_PATH=$CHROMIUM_PATH" >> .env
        echo "✅ Added to .env"
    else
        echo "✅ Already in .env"
    fi
fi
echo ""

# Step 5: Restart PM2 service
echo "5️⃣ Restarting PM2 service..."
pm2 delete screenshot-service 2>/dev/null || true
pm2 start task-cli/screenshot-service.js --name screenshot-service
pm2 save
echo "✅ Service restarted"
echo ""

# Step 6: Test
echo "6️⃣ Testing service..."
sleep 2
HEALTH=$(curl -s http://localhost:3000/health)
if echo "$HEALTH" | grep -q '"status":"ok"'; then
    echo "✅ Service is healthy!"
    echo "   Response: $HEALTH"
else
    echo "❌ Service health check failed"
    echo "   Response: $HEALTH"
    echo ""
    echo "Check logs: pm2 logs screenshot-service"
    exit 1
fi

echo ""
echo "=========================================="
echo "✅ Setup Complete!"
echo "=========================================="
echo ""
echo "Chromium path: $CHROMIUM_PATH"
echo ""
echo "PM2 Status:"
pm2 status
echo ""
echo "Test screenshot:"
echo "  curl https://mytree.vn/api/screenshot/health"
echo ""

# Quick fix for Puppeteer on production server
# Use system Chromium instead of downloading

echo "🔧 Fixing Puppeteer to use system Chromium..."

# Clean old installation
echo "Cleaning old files..."
rm -rf node_modules package-lock.json
npm cache clean --force

# Install Chromium from system
echo "Installing system Chromium..."
apt-get update
apt-get install -y chromium-browser chromium fonts-liberation libappindicator3-1

# Find Chromium path
CHROMIUM_PATH=$(which chromium-browser || which chromium || which google-chrome)

if [ -z "$CHROMIUM_PATH" ]; then
    echo "❌ Chromium not found!"
    exit 1
fi

echo "✅ Found Chromium at: $CHROMIUM_PATH"

# Install npm dependencies without downloading Chromium
echo "Installing npm packages (skipping Chromium download)..."
PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=true npm install

# Update screenshot-service.js to use system Chromium
echo "Updating screenshot-service.js..."

# Backup original
cp task-cli/screenshot-service.js task-cli/screenshot-service.js.bak

# Update puppeteer.launch to use system Chromium
sed -i "s|headless: true,|headless: true,\n        executablePath: '$CHROMIUM_PATH',|" task-cli/screenshot-service.js

echo "✅ Configuration updated!"
echo ""
echo "Chromium path: $CHROMIUM_PATH"
echo ""
echo "Next steps:"
echo "pm2 start task-cli/screenshot-service.js --name screenshot-service"
echo "pm2 logs screenshot-service"
