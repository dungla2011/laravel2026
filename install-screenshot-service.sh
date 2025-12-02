#!/bin/bash

echo "🚀 Screenshot Service Installation"
echo "=================================="
echo ""

# Check if Node.js is installed
if ! command -v node &> /dev/null; then
    echo "❌ Node.js is not installed!"
    echo "   Please install Node.js first: https://nodejs.org/"
    exit 1
fi

echo "✅ Node.js version: $(node --version)"
echo "✅ npm version: $(npm --version)"
echo ""

# Copy package.json
echo "📦 Setting up package.json..."
if [ -f "package-screenshot.json" ]; then
    cp package-screenshot.json package.json
    echo "✅ package.json created"
else
    echo "❌ package-screenshot.json not found!"
    exit 1
fi

# Install dependencies
echo ""
echo "📥 Installing dependencies (this may take a few minutes)..."
npm install

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Installation completed!"
    echo ""
    echo "📖 Next steps:"
    echo "   1. Start the service:"
    echo "      npm start"
    echo ""
    echo "   2. Test the service:"
    echo "      npm test"
    echo ""
    echo "   3. Open demo page:"
    echo "      http://localhost:8000/demo-screenshot.html"
    echo ""
    echo "   4. Read documentation:"
    echo "      cat SCREENSHOT_SERVICE.md"
    echo ""
else
    echo ""
    echo "❌ Installation failed!"
    echo "   Please check the errors above"
    exit 1
fi
