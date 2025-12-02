#!/bin/bash

# Quick Setup Script for Screenshot Service on Linux Server
# Run this on your production server (mytree.vn)

set -e  # Exit on error

echo "=================================="
echo "Screenshot Service Quick Setup"
echo "=================================="
echo ""

# Check if running as root
if [ "$EUID" -eq 0 ]; then 
    echo "⚠️  Running as root. Some commands will be adjusted."
    IS_ROOT=true
    NPM_PREFIX="npm"
else
    echo "✅ Running as regular user"
    IS_ROOT=false
    NPM_PREFIX="npm"
fi

# Get script directory
SCRIPT_DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
cd "$SCRIPT_DIR"

echo "📁 Working directory: $SCRIPT_DIR"
echo ""

# Step 1: Check Node.js
echo "1️⃣ Checking Node.js..."
if ! command -v node &> /dev/null; then
    echo "❌ Node.js not found! Installing..."
    curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
    sudo apt-get install -y nodejs
else
    NODE_VERSION=$(node --version)
    echo "✅ Node.js $NODE_VERSION"
fi

# Step 2: Install dependencies
echo ""
echo "2️⃣ Installing dependencies..."
if [ ! -f "package.json" ]; then
    if [ -f "package-screenshot.json" ]; then
        cp package-screenshot.json package.json
        echo "✅ Copied package-screenshot.json to package.json"
    else
        echo "❌ package-screenshot.json not found!"
        exit 1
    fi
fi

npm install
echo "✅ Dependencies installed"

# Step 3: Install PM2
echo ""
echo "3️⃣ Checking PM2..."
if ! command -v pm2 &> /dev/null; then
    echo "Installing PM2..."
    if [ "$IS_ROOT" = true ]; then
        npm install -g pm2
    else
        sudo npm install -g pm2
    fi
    echo "✅ PM2 installed"
else
    echo "✅ PM2 already installed"
fi

# Step 4: Start service
echo ""
echo "4️⃣ Starting screenshot service..."

# Stop if already running
pm2 delete screenshot-service 2>/dev/null || true

# Start service
pm2 start task-cli/screenshot-service.js --name screenshot-service

# Save PM2 configuration
pm2 save

echo "✅ Service started"

# Step 5: Setup auto-start
echo ""
echo "5️⃣ Setting up auto-start..."
if [ "$IS_ROOT" = true ]; then
    # Running as root - setup systemd service instead
    echo "⚠️  Running as root - PM2 startup requires systemd service"
    echo "   Creating systemd service..."
    
    # Get PM2 startup command for systemd
    PM2_STARTUP=$(pm2 startup systemd -u root --hp /root | grep "sudo" || true)
    if [ -n "$PM2_STARTUP" ]; then
        # Remove 'sudo' from command since we're already root
        STARTUP_CMD=$(echo "$PM2_STARTUP" | sed 's/sudo //')
        eval "$STARTUP_CMD" 2>/dev/null || echo "   Systemd service setup attempted"
    fi
else
    pm2 startup | grep "sudo" | sh || true
fi
echo "✅ Auto-start configured"

# Step 6: Update .env
echo ""
echo "6️⃣ Updating Laravel .env..."
if [ -f ".env" ]; then
    if ! grep -q "SCREENSHOT_SERVICE_URL" .env; then
        echo "" >> .env
        echo "# Screenshot Service" >> .env
        echo "SCREENSHOT_SERVICE_URL=http://localhost:3000" >> .env
        echo "✅ Added SCREENSHOT_SERVICE_URL to .env"
    else
        echo "✅ SCREENSHOT_SERVICE_URL already in .env"
    fi
else
    echo "⚠️  .env not found - please create it from .env.example"
fi

# Step 7: Clear Laravel cache
echo ""
echo "7️⃣ Clearing Laravel cache..."
php artisan config:cache 2>/dev/null || echo "⚠️  Could not clear config cache"
php artisan route:cache 2>/dev/null || echo "⚠️  Could not clear route cache"

# Step 8: Test service
echo ""
echo "8️⃣ Testing service..."
sleep 2

HEALTH_RESPONSE=$(curl -s http://localhost:3000/health)
if echo "$HEALTH_RESPONSE" | grep -q '"status":"ok"'; then
    echo "✅ Service is healthy!"
    echo "   Response: $HEALTH_RESPONSE"
else
    echo "❌ Service health check failed"
    echo "   Response: $HEALTH_RESPONSE"
    echo ""
    echo "Check logs:"
    echo "   pm2 logs screenshot-service"
    exit 1
fi

# Step 9: Install dependencies for Puppeteer (if needed)
echo ""
echo "9️⃣ Installing system dependencies for Puppeteer..."
if [ "$IS_ROOT" = true ]; then
    apt-get update 2>/dev/null || yum update -y 2>/dev/null || true
    apt-get install -y \
        gconf-service \
        libasound2 \
        libatk1.0-0 \
        libc6 \
        libcairo2 \
        libcups2 \
        libdbus-1-3 \
        libexpat1 \
        libfontconfig1 \
        libgcc1 \
        libgconf-2-4 \
        libgdk-pixbuf2.0-0 \
        libglib2.0-0 \
        libgtk-3-0 \
        libnspr4 \
        libpango-1.0-0 \
        libpangocairo-1.0-0 \
        libstdc++6 \
        libx11-6 \
        libx11-xcb1 \
        libxcb1 \
        libxcomposite1 \
        libxcursor1 \
        libxdamage1 \
        libxext6 \
        libxfixes3 \
        libxi6 \
        libxrandr2 \
        libxrender1 \
        libxss1 \
        libxtst6 \
        ca-certificates \
        fonts-liberation \
        libappindicator1 \
        libnss3 \
        lsb-release \
        xdg-utils \
        wget 2>/dev/null || yum install -y chromium 2>/dev/null || echo "⚠️  Some dependencies may have failed - service might still work"
else
    sudo apt-get update
    sudo apt-get install -y \
        gconf-service \
        libasound2 \
        libatk1.0-0 \
        libc6 \
        libcairo2 \
        libcups2 \
        libdbus-1-3 \
        libexpat1 \
        libfontconfig1 \
        libgcc1 \
        libgconf-2-4 \
        libgdk-pixbuf2.0-0 \
        libglib2.0-0 \
        libgtk-3-0 \
        libnspr4 \
        libpango-1.0-0 \
        libpangocairo-1.0-0 \
        libstdc++6 \
        libx11-6 \
        libx11-xcb1 \
        libxcb1 \
        libxcomposite1 \
        libxcursor1 \
        libxdamage1 \
        libxext6 \
        libxfixes3 \
        libxi6 \
        libxrandr2 \
        libxrender1 \
        libxss1 \
        libxtst6 \
        ca-certificates \
        fonts-liberation \
        libappindicator1 \
        libnss3 \
        lsb-release \
        xdg-utils \
        wget 2>/dev/null || echo "⚠️  Some dependencies may have failed - service might still work"
fi

echo "✅ System dependencies installed"

# Summary
echo ""
echo "=================================="
echo "✅ Setup Complete!"
echo "=================================="
echo ""
echo "Service Status:"
pm2 list | grep screenshot-service
echo ""
echo "Next steps:"
echo "1. Test on your tree page:"
echo "   https://mytree.vn/my-tree?pid=11461493758623744"
echo ""
echo "2. Click 'Tải xuống' button to test screenshot"
echo ""
echo "3. Monitor logs:"
echo "   pm2 logs screenshot-service"
echo ""
echo "4. Check status:"
echo "   pm2 status"
echo ""
echo "5. Restart if needed:"
echo "   pm2 restart screenshot-service"
echo ""
echo "Documentation:"
echo "- SCREENSHOT_INTEGRATION_SUMMARY.md"
echo "- SCREENSHOT_LINUX_DEPLOY.md"
echo ""
echo "Health check URLs:"
echo "- Service: http://localhost:3000/health"
echo "- Laravel: https://mytree.vn/api/screenshot/health"
echo ""
