#!/usr/bin/env node

/**
 * Screenshot Service Test
 * 
 * Test các tính năng của screenshot service
 * 
 * Chạy test:
 * node task-cli/screenshot-service-test.js
 */

const fs = require('fs');
const path = require('path');

const SERVICE_URL = 'http://localhost:3000';
const OUTPUT_DIR = path.join(__dirname, '..', 'storage', 'screenshots');

// Tạo output directory
if (!fs.existsSync(OUTPUT_DIR)) {
    fs.mkdirSync(OUTPUT_DIR, { recursive: true });
}

/**
 * Test 1: Simple HTML screenshot
 */
async function testSimpleHtml() {
    console.log('\n📝 Test 1: Simple HTML Screenshot');

    const html = `
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .card {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        h1 { color: #667eea; }
        p { color: #666; line-height: 1.6; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Screenshot Service Test</h1>
        <p>This is a test of the server-side screenshot service.</p>
        <p>Generated at: ${new Date().toLocaleString()}</p>
    </div>
</body>
</html>
    `;

    try {
        const response = await fetch(`${SERVICE_URL}/screenshot`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                html: html,
                width: 800,
                height: 600,
                format: 'png'
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${await response.text()}`);
        }

        const buffer = Buffer.from(await response.arrayBuffer());
        const outputPath = path.join(OUTPUT_DIR, 'test1-simple.png');
        fs.writeFileSync(outputPath, buffer);

        console.log(`✅ Success! Saved to: ${outputPath}`);
        console.log(`   Size: ${(buffer.length / 1024).toFixed(2)} KB`);

    } catch (error) {
        console.error('❌ Failed:', error.message);
    }
}

/**
 * Test 2: Large canvas (vượt giới hạn browser thông thường)
 */
async function testLargeCanvas() {
    console.log('\n📝 Test 2: Large Canvas (5000x3000px)');

    const html = `
<!DOCTYPE html>
<html>
<head>
    <style>
        body { margin: 0; padding: 0; }
        .large-canvas {
            width: 5000px;
            height: 3000px;
            background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: Arial, sans-serif;
        }
        .content {
            background: white;
            padding: 100px;
            border-radius: 50px;
            box-shadow: 0 50px 100px rgba(0,0,0,0.3);
        }
        h1 {
            font-size: 120px;
            margin: 0;
            color: #f5576c;
        }
        p {
            font-size: 60px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="large-canvas">
        <div class="content">
            <h1>5000 x 3000</h1>
            <p>This exceeds browser canvas limits!</p>
            <p>But works fine with Puppeteer 🚀</p>
        </div>
    </div>
</body>
</html>
    `;

    try {
        console.log('⏳ Processing large image (may take 5-10 seconds)...');

        const response = await fetch(`${SERVICE_URL}/screenshot`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                html: html,
                width: 5000,
                height: 3000,
                format: 'png',
                scale: 1
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${await response.text()}`);
        }

        const buffer = Buffer.from(await response.arrayBuffer());
        const outputPath = path.join(OUTPUT_DIR, 'test2-large.png');
        fs.writeFileSync(outputPath, buffer);

        console.log(`✅ Success! Saved to: ${outputPath}`);
        console.log(`   Size: ${(buffer.length / 1024 / 1024).toFixed(2)} MB`);

    } catch (error) {
        console.error('❌ Failed:', error.message);
    }
}

/**
 * Test 3: High DPI / Retina (scale = 2)
 */
async function testRetina() {
    console.log('\n📝 Test 3: Retina Quality (2x scale)');

    const html = `
<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            padding: 40px;
            background: #f5f5f5;
        }
        .card {
            background: white;
            padding: 60px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        h1 {
            font-size: 48px;
            font-weight: 700;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
        }
        .small-text {
            font-size: 14px;
            color: #999;
            letter-spacing: 1px;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Retina Quality Test</h1>
        <p class="small-text">Small text should be crisp and clear at 2x scale</p>
    </div>
</body>
</html>
    `;

    try {
        const response = await fetch(`${SERVICE_URL}/screenshot`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                html: html,
                width: 800,
                height: 600,
                format: 'png',
                scale: 2  // Retina quality
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${await response.text()}`);
        }

        const buffer = Buffer.from(await response.arrayBuffer());
        const outputPath = path.join(OUTPUT_DIR, 'test3-retina.png');
        fs.writeFileSync(outputPath, buffer);

        console.log(`✅ Success! Saved to: ${outputPath}`);
        console.log(`   Size: ${(buffer.length / 1024).toFixed(2)} KB`);
        console.log(`   Note: Image is 1600x1200px (2x scale)`);

    } catch (error) {
        console.error('❌ Failed:', error.message);
    }
}

/**
 * Test 4: JPEG with quality settings
 */
async function testJpeg() {
    console.log('\n📝 Test 4: JPEG Format (quality: 80)');

    const html = `
<!DOCTYPE html>
<html>
<head>
    <style>
        body { margin: 0; }
        .photo {
            width: 1200px;
            height: 800px;
            background: url('https://picsum.photos/1200/800') center/cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .overlay {
            background: rgba(0,0,0,0.5);
            color: white;
            padding: 40px;
            border-radius: 10px;
            font-family: Arial;
            font-size: 48px;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="photo">
        <div class="overlay">JPEG Quality Test</div>
    </div>
</body>
</html>
    `;

    try {
        const response = await fetch(`${SERVICE_URL}/screenshot`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                html: html,
                width: 1200,
                height: 800,
                format: 'jpeg',
                quality: 80
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${await response.text()}`);
        }

        const buffer = Buffer.from(await response.arrayBuffer());
        const outputPath = path.join(OUTPUT_DIR, 'test4-quality.jpg');
        fs.writeFileSync(outputPath, buffer);

        console.log(`✅ Success! Saved to: ${outputPath}`);
        console.log(`   Size: ${(buffer.length / 1024).toFixed(2)} KB`);

    } catch (error) {
        console.error('❌ Failed:', error.message);
    }
}

/**
 * Test 5: Health check
 */
async function testHealth() {
    console.log('\n📝 Test 5: Health Check');

    try {
        const response = await fetch(`${SERVICE_URL}/health`);
        const data = await response.json();

        console.log('✅ Service is healthy!');
        console.log('   Status:', data.status);
        console.log('   Browser:', data.browser);
        console.log('   Uptime:', Math.floor(data.uptime), 'seconds');
        console.log('   Memory:', (data.memory.heapUsed / 1024 / 1024).toFixed(2), 'MB');

    } catch (error) {
        console.error('❌ Service is down:', error.message);
    }
}

/**
 * Run all tests
 */
async function runAllTests() {
    console.log('🚀 Screenshot Service Test Suite');
    console.log('================================\n');

    // Check if service is running
    try {
        await fetch(`${SERVICE_URL}/health`);
    } catch (error) {
        console.error('❌ Service is not running!');
        console.error('   Please start the service first:');
        console.error('   npm start\n');
        process.exit(1);
    }

    console.log(`📡 Service URL: ${SERVICE_URL}`);
    console.log(`📁 Output directory: ${OUTPUT_DIR}`);

    // Run tests
    await testHealth();
    await testSimpleHtml();
    await testRetina();
    await testJpeg();
    await testLargeCanvas();  // Last because it's slow

    console.log('\n✅ All tests completed!');
    console.log(`📁 Check results in: ${OUTPUT_DIR}\n`);
}

// Run tests
runAllTests().catch(error => {
    console.error('Fatal error:', error);
    process.exit(1);
});
