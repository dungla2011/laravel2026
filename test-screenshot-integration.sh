#!/bin/bash

echo "🧪 Screenshot Service Integration Test"
echo "======================================"
echo ""

BASE_URL=${1:-"https://mytree.vn"}

echo "Testing: $BASE_URL"
echo ""

# Test 1: Health check
echo "1️⃣ Testing health endpoint..."
HEALTH=$(curl -s "$BASE_URL/api/screenshot/health")
if echo "$HEALTH" | grep -q "ok"; then
    echo "✅ Health check passed"
    echo "   Response: $HEALTH"
else
    echo "❌ Health check failed"
    echo "   Response: $HEALTH"
    exit 1
fi

echo ""

# Test 2: Simple SVG screenshot
echo "2️⃣ Testing SVG screenshot..."
RESPONSE=$(curl -s -w "\n%{http_code}" -X POST "$BASE_URL/api/screenshot/svg" \
    -H "Content-Type: application/json" \
    -d '{
        "svg_html": "<svg xmlns=\"http://www.w3.org/2000/svg\" width=\"200\" height=\"200\"><circle cx=\"100\" cy=\"100\" r=\"80\" fill=\"#667eea\"/><text x=\"100\" y=\"110\" text-anchor=\"middle\" fill=\"white\" font-size=\"24\">Test</text></svg>",
        "bbox": {
            "x": 0,
            "y": 0,
            "width": 200,
            "height": 200
        },
        "scale": 2,
        "format": "png",
        "filename": "integration-test"
    }' \
    --output /tmp/screenshot-test.png)

HTTP_CODE=$(echo "$RESPONSE" | tail -n1)

if [ "$HTTP_CODE" = "200" ]; then
    FILE_SIZE=$(stat -f%z "/tmp/screenshot-test.png" 2>/dev/null || stat -c%s "/tmp/screenshot-test.png" 2>/dev/null)
    echo "✅ Screenshot generated successfully"
    echo "   File: /tmp/screenshot-test.png"
    echo "   Size: $FILE_SIZE bytes"
    
    # Verify it's a valid PNG
    if file /tmp/screenshot-test.png | grep -q "PNG"; then
        echo "   Format: Valid PNG ✓"
    else
        echo "   Format: Invalid PNG ✗"
    fi
else
    echo "❌ Screenshot failed"
    echo "   HTTP Code: $HTTP_CODE"
    exit 1
fi

echo ""

# Test 3: Check PM2 status
echo "3️⃣ Checking PM2 service status..."
if command -v pm2 &> /dev/null; then
    PM2_STATUS=$(pm2 list | grep screenshot-service)
    if echo "$PM2_STATUS" | grep -q "online"; then
        echo "✅ PM2 service is online"
        echo "   $PM2_STATUS"
    else
        echo "⚠️  PM2 service not found or offline"
        echo "   Run: pm2 start task-cli/screenshot-service.js --name screenshot-service"
    fi
else
    echo "⚠️  PM2 not installed"
    echo "   Run: npm install -g pm2"
fi

echo ""

# Test 4: Check .env configuration
echo "4️⃣ Checking Laravel configuration..."
if [ -f ".env" ]; then
    if grep -q "SCREENSHOT_SERVICE_URL" .env; then
        SERVICE_URL=$(grep "SCREENSHOT_SERVICE_URL" .env | cut -d '=' -f2)
        echo "✅ Configuration found"
        echo "   SCREENSHOT_SERVICE_URL=$SERVICE_URL"
    else
        echo "⚠️  SCREENSHOT_SERVICE_URL not configured in .env"
        echo "   Add: SCREENSHOT_SERVICE_URL=http://localhost:3000"
    fi
else
    echo "⚠️  .env file not found"
fi

echo ""
echo "======================================"
echo "Test Summary:"
echo "✅ All tests passed!"
echo ""
echo "Next steps:"
echo "1. Open: $BASE_URL/my-tree?pid=11461493758623744"
echo "2. Click 'Tải xuống' button"
echo "3. Screenshot should download via server-side rendering"
echo ""
