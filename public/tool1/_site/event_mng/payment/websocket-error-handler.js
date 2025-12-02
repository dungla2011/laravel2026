/**
 * WebSocket Error Handler
 * Xử lý lỗi kết nối WebSocket và hiển thị alert dialog
 */

// Lưu trữ original functions
let originalVgcaSignApproved = null;

// Wrap hàm vgca_sign_approved để thêm error handling
function wrapVgcaFunctions() {
    if (typeof vgca_sign_approved === 'function') {
        originalVgcaSignApproved = vgca_sign_approved;

        // Tạo wrapper function với error handling
        window.vgca_sign_approved = function(jsonParams, callback) {
            console.log('[WebSocket Error Handler] Wrapping vgca_sign_approved');

            // Tạo wrapper callback
            const wrappedCallback = function(result) {
                try {
                    const response = typeof result === 'string' ? JSON.parse(result) : result;
                    console.log('[WebSocket Error Handler] Response:', response);

                    // Kiểm tra lỗi trong response
                    if (response.Error || response.Message?.includes('failed')) {
                        showWebSocketErrorAlert(response.Error || response.Message);
                    }

                    // Gọi callback gốc
                    if (callback) {
                        callback(result);
                    }
                } catch (e) {
                    console.error('[WebSocket Error Handler] Error processing response:', e);
                    if (callback) {
                        callback(result);
                    }
                }
            };

            // Thêm global error handler cho WebSocket
            const originalWebSocket = window.WebSocket;
            window.WebSocket = class extends originalWebSocket {
                constructor(url, ...args) {
                    super(url, ...args);

                    // Intercept onerror
                    const originalOnerror = this.onerror;
                    this.onerror = function(event) {
                        console.error('[WebSocket Error Handler] WebSocket Error:', event);
                        showWebSocketErrorAlert(`Kiểm tra VGCA Service đã bật chưa: ${url}`);

                        if (originalOnerror) {
                            originalOnerror.call(this, event);
                        }
                    };

                    // Intercept onclose để detect disconnect errors
                    const originalOnclose = this.onclose;
                    this.onclose = function(event) {
                        if (event.code !== 1000) { // 1000 = normal close
                            console.warn('[WebSocket Error Handler] WebSocket Closed Abnormally:', {
                                code: event.code,
                                reason: event.reason,
                                url: url
                            });
                            showWebSocketErrorAlert(`WebSocket disconnected: ${url}\nCode: ${event.code}\nReason: ${event.reason || 'Unknown'}`);
                        }

                        if (originalOnclose) {
                            originalOnclose.call(this, event);
                        }
                    };
                }
            };

            try {
                // Gọi original function
                return originalVgcaSignApproved.call(this, jsonParams, wrappedCallback);
            } catch (error) {
                console.error('[WebSocket Error Handler] Exception:', error);
                showWebSocketErrorAlert(`Exception occurred: ${error.message}`);
            }
        };
    }
}

// Hàm hiển thị alert dialog cho lỗi WebSocket
function showWebSocketErrorAlert(errorMessage) {
    console.error('[WebSocket Error] ' + errorMessage);

    // Tạo dialog HTML
    const dialogId = 'websocket-error-dialog-' + Date.now();
    const dialogHtml = `
        <div id="${dialogId}" style="
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            border: 2px solid #dc3545;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
            z-index: 99999;
            min-width: 400px;
            max-width: 600px;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        ">
            <div style="
                background: #dc3545;
                color: white;
                padding: 15px 20px;
                border-radius: 6px 6px 0 0;
                font-weight: bold;
                font-size: 16px;
                display: flex;
                align-items: center;
                gap: 10px;
            ">
                <span style="font-size: 20px;">⚠️</span>
                <span>Kiểm tra VGCA Service đã bật chưa</span>
            </div>
            <div style="
                padding: 20px;
                max-height: 300px;
                overflow-y: auto;
                color: #333;
                font-size: 14px;
                line-height: 1.5;
                word-break: break-word;
            ">
                <p><strong>Error:</strong></p>
                <p style="
                    background: #f8f9fa;
                    padding: 10px;
                    border-left: 4px solid #dc3545;
                    border-radius: 4px;
                    font-family: 'Courier New', monospace;
                    font-size: 13px;
                    margin: 0;
                    white-space: pre-wrap;
                ">${escapeHtml(errorMessage)}</p>
                <p style="margin-top: 15px; margin-bottom: 0; color: #666; font-size: 13px;">
                    <strong>Tips:</strong>
                    <ul style="margin: 5px 0; padding-left: 20px;">
                        <li>Kiểm tra VGCA Service đã bật chưa (port 8987) </li>
                        <li>Check your internet connection</li>
                        <li>Verify that HTTPS is being used (wss:// not ws://)</li>
                        <li>Check browser console for more details (F12)</li>
                    </ul>
                </p>
            </div>
            <div style="
                padding: 15px 20px;
                border-top: 1px solid #eee;
                text-align: right;
                background: #f8f9fa;
                border-radius: 0 0 6px 6px;
            ">
                <button onclick="
                    const dialog = document.getElementById('${dialogId}');
                    if (dialog) dialog.remove();
                    const overlay = document.getElementById('${dialogId}-overlay');
                    if (overlay) overlay.remove();
                " style="
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 8px 20px;
                    border-radius: 4px;
                    cursor: pointer;
                    font-size: 14px;
                    font-weight: 500;
                ">Close</button>
            </div>
        </div>
        <div id="${dialogId}-overlay" style="
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 99998;
        "></div>
    `;

    // Thêm dialog vào DOM
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = dialogHtml;
    document.body.appendChild(tempDiv.firstElementChild);
    document.body.appendChild(tempDiv.firstElementChild); // Append overlay
}

// Hàm escape HTML để tránh XSS
function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

// Kích hoạt khi document ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', wrapVgcaFunctions);
} else {
    // Script loaded sau khi DOM ready
    setTimeout(wrapVgcaFunctions, 100);
    setTimeout(wrapVgcaFunctions, 500);  // Retry in case vgcaplugin.js hasn't loaded yet
}

// Retry wrapping nếu vgca_sign_approved chưa tồn tại
let retryCount = 0;
const retryInterval = setInterval(() => {
    if (typeof vgca_sign_approved === 'function' && !originalVgcaSignApproved) {
        console.log('[WebSocket Error Handler] vgca_sign_approved found, wrapping...');
        wrapVgcaFunctions();
        clearInterval(retryInterval);
    }
    retryCount++;
    if (retryCount > 50) { // Retry cho 5 giây
        clearInterval(retryInterval);
        console.warn('[WebSocket Error Handler] Could not find vgca_sign_approved after retries');
    }
}, 100);
