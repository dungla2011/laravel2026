#!/usr/bin/env node

/**
 * Screenshot Service - Server-side DOM to Image
 * Sử dụng Puppeteer để render HTML thành PNG với viewport không giới hạn
 * 
 * Cài đặt:
 * npm install puppeteer express body-parser
 * 
 * Chạy server:
 * node task-cli/screenshot-service.js
 * 
 * API Endpoint:
 * POST http://localhost:3000/screenshot
 * Body: { html, width, height, scale }
 */

const puppeteer = require('puppeteer');
const express = require('express');
const bodyParser = require('body-parser');
const fs = require('fs');
const path = require('path');

const app = express();
const PORT = 3000;

// Tăng giới hạn body size cho HTML lớn
app.use(bodyParser.json({ limit: '50mb' }));
app.use(bodyParser.urlencoded({ limit: '50mb', extended: true }));

// CORS headers
app.use((req, res, next) => {
    res.header('Access-Control-Allow-Origin', '*');
    res.header('Access-Control-Allow-Methods', 'POST, OPTIONS');
    res.header('Access-Control-Allow-Headers', 'Content-Type');
    next();
});

let browser = null;

// Khởi tạo browser khi start server
async function initBrowser() {
    // Tự động detect system chromium
    const chromiumPath = process.env.PUPPETEER_EXECUTABLE_PATH || 
                         '/snap/bin/chromium' || 
                         '/usr/bin/chromium' || 
                         '/usr/bin/chromium-browser';
    
    const launchOptions = {
        headless: true,
        args: [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',
            '--disable-accelerated-2d-canvas',
            '--disable-gpu',
            // Cho phép canvas size lớn
            '--max-old-space-size=4096',
            '--js-flags=--max-old-space-size=4096'
        ]
    };
    
    // Sử dụng system chromium nếu có
    if (chromiumPath && require('fs').existsSync(chromiumPath)) {
        launchOptions.executablePath = chromiumPath;
        console.log('✅ Using system Chromium:', chromiumPath);
    } else {
        console.log('✅ Using bundled Chromium');
    }
    
    browser = await puppeteer.launch(launchOptions);
    console.log('✅ Puppeteer browser initialized');
}

/**
 * POST /screenshot
 * Body:
 * {
 *   html: string,           // HTML content
 *   width: number,          // Width in pixels (default: 1200)
 *   height: number,         // Height in pixels (default: auto)
 *   scale: number,          // Scale factor (default: 1)
 *   format: 'png'|'jpeg',   // Image format (default: png)
 *   quality: number,        // JPEG quality 0-100 (default: 90)
 *   fullPage: boolean       // Capture full page (default: true)
 * }
 * 
 * Response: Image binary (PNG/JPEG)
 */
app.post('/screenshot', async (req, res) => {
    const startTime = Date.now();
    let page = null;

    try {
        const {
            html,
            width = 1200,
            height = null,
            scale = 1,
            format = 'png',
            quality = 90,
            fullPage = true,
            backgroundColor = '#ffffff'
        } = req.body;

        if (!html) {
            return res.status(400).json({ error: 'HTML content is required' });
        }

        console.log(`📸 Screenshot request: ${width}x${height || 'auto'}, scale: ${scale}, format: ${format}`);

        // Tạo page mới
        page = await browser.newPage();

        // Set viewport - quan trọng cho kích thước
        await page.setViewport({
            width: Math.floor(width),
            height: height ? Math.floor(height) : 800,
            deviceScaleFactor: scale
        });

        // Load HTML content
        await page.setContent(html, {
            waitUntil: 'networkidle0', // Đợi tất cả network requests
            timeout: 30000
        });

        // Đợi fonts load (nếu có)
        await page.evaluateHandle('document.fonts.ready');

        // Lấy actual height nếu fullPage
        let screenshotHeight = height;
        if (fullPage && !height) {
            screenshotHeight = await page.evaluate(() => {
                return Math.max(
                    document.body.scrollHeight,
                    document.body.offsetHeight,
                    document.documentElement.clientHeight,
                    document.documentElement.scrollHeight,
                    document.documentElement.offsetHeight
                );
            });
        }

        // Screenshot options
        const screenshotOptions = {
            type: format,
            fullPage: fullPage,
            omitBackground: backgroundColor === 'transparent'
        };

        if (format === 'jpeg') {
            screenshotOptions.quality = quality;
        }

        // Capture screenshot
        const screenshot = await page.screenshot(screenshotOptions);

        const duration = Date.now() - startTime;
        console.log(`✅ Screenshot completed in ${duration}ms, size: ${screenshot.length} bytes`);

        // Return image
        res.set('Content-Type', `image/${format}`);
        res.set('Content-Length', screenshot.length);
        res.send(screenshot);

    } catch (error) {
        console.error('❌ Screenshot error:', error);
        res.status(500).json({
            error: error.message,
            stack: process.env.NODE_ENV === 'development' ? error.stack : undefined
        });
    } finally {
        if (page) {
            await page.close();
        }
    }
});

/**
 * POST /screenshot-element
 * Chụp một element cụ thể trong DOM
 */
app.post('/screenshot-element', async (req, res) => {
    let page = null;

    try {
        const {
            html,
            selector,
            width = 1200,
            scale = 1,
            format = 'png',
            quality = 90,
            padding = 0
        } = req.body;

        if (!html || !selector) {
            return res.status(400).json({ error: 'HTML and selector are required' });
        }

        console.log(`📸 Element screenshot: selector="${selector}", scale: ${scale}`);

        page = await browser.newPage();

        await page.setViewport({
            width: Math.floor(width),
            height: 800,
            deviceScaleFactor: scale
        });

        await page.setContent(html, {
            waitUntil: 'networkidle0',
            timeout: 30000
        });

        await page.evaluateHandle('document.fonts.ready');

        // Tìm element
        const element = await page.$(selector);
        if (!element) {
            throw new Error(`Element not found: ${selector}`);
        }

        // Screenshot element
        const screenshot = await element.screenshot({
            type: format,
            quality: format === 'jpeg' ? quality : undefined
        });

        console.log(`✅ Element screenshot completed, size: ${screenshot.length} bytes`);

        res.set('Content-Type', `image/${format}`);
        res.send(screenshot);

    } catch (error) {
        console.error('❌ Element screenshot error:', error);
        res.status(500).json({ error: error.message });
    } finally {
        if (page) {
            await page.close();
        }
    }
});

/**
 * GET /health
 * Health check endpoint
 */
app.get('/health', (req, res) => {
    res.json({
        status: 'ok',
        browser: browser ? 'connected' : 'disconnected',
        uptime: process.uptime(),
        memory: process.memoryUsage()
    });
});

/**
 * GET /
 * API documentation
 */
app.get('/', (req, res) => {
    res.json({
        service: 'Screenshot Service',
        version: '1.0.0',
        endpoints: {
            'POST /screenshot': 'Capture full page screenshot',
            'POST /screenshot-element': 'Capture specific element',
            'GET /health': 'Health check'
        },
        examples: {
            screenshot: {
                url: 'POST http://localhost:3000/screenshot',
                body: {
                    html: '<html><body><h1>Hello</h1></body></html>',
                    width: 1200,
                    height: 800,
                    scale: 2,
                    format: 'png'
                }
            }
        }
    });
});

// Graceful shutdown
process.on('SIGINT', async () => {
    console.log('\n🛑 Shutting down...');
    if (browser) {
        await browser.close();
    }
    process.exit(0);
});

// Start server
(async () => {
    try {
        await initBrowser();
        app.listen(PORT, () => {
            console.log(`🚀 Screenshot service running on http://localhost:${PORT}`);
            console.log(`📖 API docs: http://localhost:${PORT}/`);
        });
    } catch (error) {
        console.error('❌ Failed to start server:', error);
        process.exit(1);
    }
})();
