/**
 * Screenshot Client - Replace dom-to-image with server-side rendering
 * 
 * Usage:
 * 1. Start screenshot service: node task-cli/screenshot-service.js
 * 2. Replace domtoimage.toPng() with ScreenshotClient.toPng()
 * 
 * Example:
 * ScreenshotClient.toPng(element, { scale: 2, width: 1200 })
 *   .then(dataUrl => { ... })
 *   .catch(error => { ... });
 */

class ScreenshotClient {
    constructor(options = {}) {
        this.serviceUrl = options.serviceUrl || 'http://localhost:3000';
        this.defaultOptions = {
            scale: 1,
            format: 'png',
            quality: 90,
            fullPage: true,
            ...options
        };
    }

    /**
     * Convert DOM element to PNG (server-side)
     * API tương thích với domtoimage.toPng()
     * 
     * @param {HTMLElement} element - Element to capture
     * @param {Object} options - Screenshot options
     * @returns {Promise<string>} Data URL of the image
     */
    async toPng(element, options = {}) {
        return this._capture(element, { ...options, format: 'png' });
    }

    /**
     * Convert DOM element to JPEG (server-side)
     * API tương thích với domtoimage.toJpeg()
     * 
     * @param {HTMLElement} element - Element to capture
     * @param {Object} options - Screenshot options
     * @returns {Promise<string>} Data URL of the image
     */
    async toJpeg(element, options = {}) {
        return this._capture(element, { ...options, format: 'jpeg' });
    }

    /**
     * Convert DOM element to Blob (server-side)
     * API tương thích với domtoimage.toBlob()
     * 
     * @param {HTMLElement} element - Element to capture
     * @param {Object} options - Screenshot options
     * @returns {Promise<Blob>} Image blob
     */
    async toBlob(element, options = {}) {
        const dataUrl = await this._capture(element, options);
        return this._dataUrlToBlob(dataUrl);
    }

    /**
     * Core capture method
     */
    async _capture(element, options = {}) {
        const startTime = Date.now();

        try {
            // Merge options
            const opts = { ...this.defaultOptions, ...options };

            // Get element's HTML and styles
            const { html, width, height } = this._prepareElement(element);

            console.log(`📤 Sending screenshot request: ${width}x${height}, scale: ${opts.scale}`);

            // Send to server
            const response = await fetch(`${this.serviceUrl}/screenshot`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    html: html,
                    width: opts.width || width,
                    height: opts.height || height,
                    scale: opts.scale,
                    format: opts.format,
                    quality: opts.quality,
                    fullPage: opts.fullPage,
                    backgroundColor: opts.backgroundColor || '#ffffff'
                })
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.error || `Server error: ${response.status}`);
            }

            // Convert to data URL
            const blob = await response.blob();
            const dataUrl = await this._blobToDataUrl(blob);

            const duration = Date.now() - startTime;
            console.log(`✅ Screenshot completed in ${duration}ms`);

            return dataUrl;

        } catch (error) {
            console.error('❌ Screenshot error:', error);
            throw error;
        }
    }

    /**
     * Prepare element HTML with all styles
     */
    _prepareElement(element) {
        // Clone element để không ảnh hưởng DOM gốc
        const clone = element.cloneNode(true);

        // Get computed styles
        const styles = this._getComputedStyles(element);

        // Get dimensions
        const rect = element.getBoundingClientRect();
        const width = Math.ceil(rect.width);
        const height = Math.ceil(rect.height);

        // Wrap trong HTML document hoàn chỉnh
        const html = `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            width: ${width}px;
            height: ${height}px;
            overflow: hidden;
        }
        ${styles}
    </style>
</head>
<body>
    ${clone.outerHTML}
</body>
</html>
        `.trim();

        return { html, width, height };
    }

    /**
     * Extract all computed styles from element and its children
     */
    _getComputedStyles(element) {
        const styles = [];
        const elements = [element, ...element.querySelectorAll('*')];

        elements.forEach((el, index) => {
            const computed = window.getComputedStyle(el);
            const selector = this._generateSelector(el, index);

            // Chỉ lấy các style quan trọng
            const importantProps = [
                'display', 'position', 'top', 'left', 'right', 'bottom',
                'width', 'height', 'margin', 'padding', 'border',
                'background', 'background-color', 'background-image',
                'color', 'font-family', 'font-size', 'font-weight',
                'line-height', 'text-align', 'vertical-align',
                'transform', 'opacity', 'z-index', 'overflow',
                'white-space', 'word-wrap', 'flex', 'grid'
            ];

            const styleStr = importantProps
                .map(prop => {
                    const value = computed.getPropertyValue(prop);
                    return value ? `${prop}: ${value};` : '';
                })
                .filter(Boolean)
                .join(' ');

            if (styleStr) {
                styles.push(`${selector} { ${styleStr} }`);
            }
        });

        return styles.join('\n');
    }

    /**
     * Generate CSS selector for element
     */
    _generateSelector(element, index) {
        if (element.id) {
            return `#${element.id}`;
        }
        if (element.className) {
            const classes = element.className.split(' ').filter(Boolean);
            if (classes.length > 0) {
                return `.${classes.join('.')}`;
            }
        }
        return `[data-screenshot-index="${index}"]`;
    }

    /**
     * Convert Blob to Data URL
     */
    _blobToDataUrl(blob) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onloadend = () => resolve(reader.result);
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });
    }

    /**
     * Convert Data URL to Blob
     */
    _dataUrlToBlob(dataUrl) {
        return fetch(dataUrl).then(res => res.blob());
    }
}

// Export as singleton
const screenshotClient = new ScreenshotClient();

// Compatibility API - drop-in replacement for dom-to-image
window.domtoimage = window.domtoimage || {
    toPng: (element, options) => screenshotClient.toPng(element, options),
    toJpeg: (element, options) => screenshotClient.toJpeg(element, options),
    toBlob: (element, options) => screenshotClient.toBlob(element, options)
};

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ScreenshotClient;
}
