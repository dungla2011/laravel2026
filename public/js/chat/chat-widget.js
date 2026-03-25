/**
 * Chat Widget - Embeddable Floating Chat
 * =======================================
 * Nhúng vào bất kỳ trang web nào:
 *
 *   <script>
 *     window.ChatWidgetConfig = {
 *       apiBase:           'https://your-api.com',   // URL gốc của API
 *       messagesEndpoint:  '/api/messages',           // GET  → lấy lịch sử
 *       sendEndpoint:      '/api/chat',               // POST → gửi tin nhắn
 *       botName:           'Trợ lý AI',
 *       primaryColor:      '#0084ff',
 *       welcomeMessage:    'Xin chào! Tôi có thể giúp gì cho bạn?',
 *       placeholder:       'Nhập tin nhắn...',
 *       headers: {                                    // header tuỳ chọn (auth, v.v.)
 *         'Authorization': 'Bearer YOUR_TOKEN'
 *       },
 *       // Mapping field trong JSON trả về (tuỳ chỉnh nếu API khác chuẩn)
 *       messagesField:    'messages',       // mảng messages trong response history
 *       roleField:        'role',           // "user" | "assistant"
 *       contentField:     'content',
 *       timestampField:   'timestamp',
 *       replyField:       'reply',          // field chứa câu trả lời từ /send
 *       convIdField:      'conversation_id',
 *       msgField:         'message',        // field tên khi POST tin nhắn
 *     };
 *   </script>
 *   <script src="chat-widget.js"></script>
 *
 * ─── Định dạng API mặc định ────────────────────────────────────────────────
 *
 * GET /api/messages  →  { "messages": [ { "role": "user"|"assistant",
 *                                          "content": "...",
 *                                          "timestamp": "ISO8601" } ],
 *                          "conversation_id": "abc" }
 *
 * POST /api/chat     Body: { "message": "...", "conversation_id": "abc" }
 *                →  { "reply": "...", "conversation_id": "abc" }
 */
(function (global, doc) {
    'use strict';

    /* ─── CONFIG ──────────────────────────────────────────────────────────────── */
    var cfg           = global.ChatWidgetConfig || {};
    var API_BASE      = (cfg.apiBase          || '').replace(/\/$/, '');
    var MESSAGES_API  = cfg.messagesEndpoint  || '/api/messages';
    var SEND_API      = cfg.sendEndpoint      || '/api/chat';
    var BOT_NAME      = cfg.botName           || 'Trợ lý AI';
    var PLACEHOLDER   = cfg.placeholder       || 'Nhập tin nhắn...';
    var WELCOME_MSG   = cfg.welcomeMessage    !== undefined
        ? cfg.welcomeMessage
        : 'Xin chào! Tôi có thể giúp gì cho bạn?';
    var PRIMARY_COLOR = cfg.primaryColor      || '#0084ff';
    var EXTRA_HEADERS = cfg.headers           || {};

    /* JSON field mapping */
    var F_MESSAGES  = cfg.messagesField   || 'messages';
    var F_ROLE      = cfg.roleField       || 'role';
    var F_CONTENT   = cfg.contentField    || 'content';
    var F_TIMESTAMP = cfg.timestampField  || 'timestamp';
    var F_REPLY     = cfg.replyField      || 'reply';
    var F_CONV_ID   = cfg.convIdField     || 'conversation_id';
    var F_MSG_FIELD = cfg.msgField        || 'message';

    /* ─── SHADOW DOM REFS ───────────────────────────────────────────────────────── */
    var shadowRoot, hostEl;

    /* ─── STATE ───────────────────────────────────────────────────────────────── */
    var state = {
        open:           false,
        historyLoaded:  false,
        sending:        false,
        conversationId: null,
    };

    /* ─── HELPERS ─────────────────────────────────────────────────────────────── */
    function escHtml(str) {
        return String(str)
            .replace(/&/g,  '&amp;')
            .replace(/</g,  '&lt;')
            .replace(/>/g,  '&gt;')
            .replace(/"/g,  '&quot;')
            .replace(/'/g,  '&#39;');
    }

    /* Convert newlines + URLs to safe HTML */
    function formatContent(text) {
        var safe = escHtml(text);
        /* linkify URLs */
        safe = safe.replace(
            /(https?:\/\/[^\s<>"']+)/g,
            '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>'
        );
        /* preserve newlines */
        safe = safe.replace(/\n/g, '<br>');
        return safe;
    }

    function formatTime(ts) {
        if (!ts) return '';
        try {
            var d = new Date(ts);
            return d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
        } catch (e) { return ''; }
    }

    function darkenColor(hex, pct) {
        /* darken a #rrggbb color by pct percent */
        hex = hex.replace('#', '');
        if (hex.length === 3) {
            hex = hex[0]+hex[0]+hex[1]+hex[1]+hex[2]+hex[2];
        }
        var r = Math.max(0, parseInt(hex.slice(0,2),16) - Math.round(255 * pct / 100));
        var g = Math.max(0, parseInt(hex.slice(2,4),16) - Math.round(255 * pct / 100));
        var b = Math.max(0, parseInt(hex.slice(4,6),16) - Math.round(255 * pct / 100));
        return '#' + [r,g,b].map(function(v){ return v.toString(16).padStart(2,'0'); }).join('');
    }

    var DARK_COLOR = darkenColor(PRIMARY_COLOR, 12);

    /* ─── INJECT CSS ──────────────────────────────────────────────────────────── */
    function injectCSS() {
        var style = doc.createElement('style');
        style.textContent = [
            /* :host targets the shadow host element (the fixed root container) */
            ':host{display:block;position:fixed;bottom:24px;right:24px;z-index:2147483647;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;font-size:14px;line-height:1.5;}',
            '*, *::before, *::after{box-sizing:border-box;}',

            /* ── FAB toggle button ── */
            '#cw-fab{width:56px;height:56px;border-radius:50%;background:'+PRIMARY_COLOR+';border:none;cursor:pointer;',
            'box-shadow:0 4px 16px rgba(0,0,0,.25);display:flex;align-items:center;justify-content:center;',
            'transition:transform .2s,box-shadow .2s,background .2s;outline:none;padding:0;}',
            '#cw-fab:hover{background:'+DARK_COLOR+';transform:scale(1.07);box-shadow:0 6px 20px rgba(0,0,0,.3);}',
            '#cw-fab svg{width:28px;height:28px;fill:#fff;}',

            /* ── Chat panel ── */
            '#cw-panel{position:absolute;bottom:70px;right:0;width:360px;max-width:calc(100vw - 32px);',
            'height:520px;max-height:calc(100vh - 110px);background:#fff;border-radius:16px;',
            'box-shadow:0 8px 40px rgba(0,0,0,.18);display:flex;flex-direction:column;overflow:hidden;',
            'transform-origin:bottom right;transition:transform .22s cubic-bezier(.4,0,.2,1),opacity .22s;',
            'transform:scale(.85);opacity:0;pointer-events:none;}',
            '#cw-panel.cw-open{transform:scale(1);opacity:1;pointer-events:all;}',

            /* ── Header ── */
            '#cw-header{background:'+PRIMARY_COLOR+';padding:14px 16px;display:flex;align-items:center;',
            'justify-content:space-between;flex-shrink:0;}',
            '#cw-header-info{display:flex;align-items:center;gap:10px;}',
            '#cw-avatar{width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.25);',
            'display:flex;align-items:center;justify-content:center;flex-shrink:0;}',
            '#cw-avatar svg{width:22px;height:22px;fill:#fff;}',
            '#cw-botname{color:#fff;font-weight:600;font-size:15px;}',
            '#cw-close{background:none;border:none;cursor:pointer;color:rgba(255,255,255,.85);',
            'font-size:20px;line-height:1;padding:4px 6px;border-radius:6px;transition:background .15s,color .15s;}',
            '#cw-close:hover{background:rgba(255,255,255,.2);color:#fff;}',

            /* ── Messages area ── */
            '#cw-messages{flex:1;overflow-y:auto;padding:16px 14px;display:flex;flex-direction:column;gap:10px;}',
            '#cw-messages::-webkit-scrollbar{width:5px;}',
            '#cw-messages::-webkit-scrollbar-track{background:transparent;}',
            '#cw-messages::-webkit-scrollbar-thumb{background:#d0d0d0;border-radius:99px;}',

            /* ── Message bubbles ── */
            '.cw-msg{display:flex;flex-direction:column;max-width:82%;}',
            '.cw-msg.cw-user{align-self:flex-end;align-items:flex-end;}',
            '.cw-msg.cw-bot{align-self:flex-start;align-items:flex-start;}',
            '.cw-bubble{padding:9px 13px;border-radius:18px;word-break:break-word;line-height:1.5;}',
            '.cw-user .cw-bubble{background:'+PRIMARY_COLOR+';color:#fff;border-bottom-right-radius:4px;}',
            '.cw-bot  .cw-bubble{background:#f0f2f5;color:#1a1a1a;border-bottom-left-radius:4px;}',
            '.cw-bubble a{color:inherit;text-decoration:underline;}',
            '.cw-time{font-size:11px;color:#999;margin-top:3px;padding:0 4px;}',

            /* ── Typing indicator ── */
            '#cw-typing{display:none;align-self:flex-start;}',
            '#cw-typing.cw-show{display:flex;}',
            '#cw-typing .cw-bubble{background:#f0f2f5;padding:10px 14px;display:flex;gap:5px;align-items:center;}',
            '.cw-dot{width:7px;height:7px;border-radius:50%;background:#aaa;',
            'animation:cw-bounce 1.2s infinite ease-in-out;}',
            '.cw-dot:nth-child(1){animation-delay:0s;}',
            '.cw-dot:nth-child(2){animation-delay:.2s;}',
            '.cw-dot:nth-child(3){animation-delay:.4s;}',
            '@keyframes cw-bounce{0%,60%,100%{transform:translateY(0);}30%{transform:translateY(-6px);}}',

            /* ── Error message ── */
            '.cw-error{align-self:center;font-size:12px;color:#e53935;background:#fdecea;',
            'padding:6px 12px;border-radius:99px;}',

            /* ── Input area ── */
            '#cw-input-area{display:flex;align-items:flex-end;gap:8px;padding:10px 12px;',
            'border-top:1px solid #eee;flex-shrink:0;background:#fff;}',
            '#cw-input{flex:1;border:1.5px solid #e0e0e0;border-radius:22px;padding:9px 14px;',
            'font-size:14px;font-family:inherit;resize:none;outline:none;max-height:120px;overflow-y:auto;',
            'line-height:1.5;transition:border-color .2s;}',
            '#cw-input:focus{border-color:'+PRIMARY_COLOR+';}',
            '#cw-send{width:40px;height:40px;border-radius:50%;background:'+PRIMARY_COLOR+';border:none;',
            'cursor:pointer;display:flex;align-items:center;justify-content:center;flex-shrink:0;',
            'transition:background .2s,transform .15s;outline:none;}',
            '#cw-send:hover:not(:disabled){background:'+DARK_COLOR+';transform:scale(1.06);}',
            '#cw-send:disabled{background:#ccc;cursor:not-allowed;}',
            '#cw-send svg{width:18px;height:18px;fill:#fff;}',

            /* ── Unread badge ── */
            '#cw-badge{position:absolute;top:-4px;right:-4px;min-width:18px;height:18px;',
            'background:#e53935;border-radius:99px;color:#fff;font-size:11px;font-weight:700;',
            'display:flex;align-items:center;justify-content:center;padding:0 4px;',
            'pointer-events:none;display:none;}',

            /* ── Responsive small screen ── */
            '@media(max-width:420px){',
            ':host{bottom:12px;right:12px;}',
            '#cw-panel{width:calc(100vw - 24px);height:calc(100vh - 90px);bottom:68px;right:0;}',
            '}',
        ].join('');
        shadowRoot.appendChild(style);
    }

    /* ─── INJECT HTML ─────────────────────────────────────────────────────────── */
    var panel, messagesEl, inputEl, sendBtn, fabBtn, typingEl, badgeEl;

    function injectHTML() {
        var container = doc.createElement('div');
        container.innerHTML = [
            /* Panel */
            '<div id="cw-panel" role="dialog" aria-modal="true" aria-label="'+escHtml(BOT_NAME)+'">',
            /* Header */
            '<div id="cw-header">',
            '<div id="cw-header-info">',
            '<div id="cw-avatar">',
            '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">',
            '<path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>',
            '<path fill="rgba(255,255,255,.7)" d="M8 10h8M8 14h5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" fill="none"/>',
            '</svg>',
            '</div>',
            '<span id="cw-botname">'+escHtml(BOT_NAME)+'</span>',
            '</div>',
            '<button id="cw-close" aria-label="Đóng">&times;</button>',
            '</div>',
            /* Messages */
            '<div id="cw-messages" role="log" aria-live="polite" aria-atomic="false">',
            '<div id="cw-typing">',
            '<div class="cw-bubble">',
            '<span class="cw-dot"></span>',
            '<span class="cw-dot"></span>',
            '<span class="cw-dot"></span>',
            '</div>',
            '</div>',
            '</div>',
            /* Input */
            '<div id="cw-input-area">',
            '<textarea id="cw-input" rows="1" placeholder="'+escHtml(PLACEHOLDER)+'" aria-label="Nhập tin nhắn"></textarea>',
            '<button id="cw-send" aria-label="Gửi" title="Gửi" disabled>',
            '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">',
            '<path d="M2.01 21L23 12 2.01 3 2 10l15 2-15 2z"/>',
            '</svg>',
            '</button>',
            '</div>',
            '</div>',
            /* FAB */
            '<button id="cw-fab" aria-label="'+escHtml(BOT_NAME)+'">',
            /* Chat icon */
            '<svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">',
            '<path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>',
            '</svg>',
            '<span id="cw-badge"></span>',
            '</button>',
        ].join('');

        shadowRoot.appendChild(container);

        /* Cache refs via shadow root querySelector */
        panel      = shadowRoot.querySelector('#cw-panel');
        messagesEl = shadowRoot.querySelector('#cw-messages');
        inputEl    = shadowRoot.querySelector('#cw-input');
        sendBtn    = shadowRoot.querySelector('#cw-send');
        fabBtn     = shadowRoot.querySelector('#cw-fab');
        typingEl   = shadowRoot.querySelector('#cw-typing');
        badgeEl    = shadowRoot.querySelector('#cw-badge');
    }

    /* ─── RENDER HELPERS ──────────────────────────────────────────────────────── */
    function createBubble(role, content, timestamp) {
        var isUser  = (role === 'user');
        var wrapper = doc.createElement('div');
        wrapper.className = 'cw-msg ' + (isUser ? 'cw-user' : 'cw-bot');

        var bubble = doc.createElement('div');
        bubble.className = 'cw-bubble';
        bubble.innerHTML = formatContent(content);

        wrapper.appendChild(bubble);

        if (timestamp) {
            var time = doc.createElement('div');
            time.className = 'cw-time';
            time.textContent = formatTime(timestamp);
            wrapper.appendChild(time);
        }

        return wrapper;
    }

    function appendBubble(role, content, timestamp) {
        var el = createBubble(role, content, timestamp);
        /* insert before typing indicator */
        messagesEl.insertBefore(el, typingEl);
        scrollBottom();
        return el;
    }

    function appendError(msg) {
        var el = doc.createElement('div');
        el.className = 'cw-error';
        el.textContent = msg || 'Đã có lỗi xảy ra. Vui lòng thử lại.';
        messagesEl.insertBefore(el, typingEl);
        scrollBottom();
        /* auto-remove after 5s */
        setTimeout(function () {
            if (el.parentNode) el.parentNode.removeChild(el);
        }, 5000);
    }

    function showTyping() {
        typingEl.classList.add('cw-show');
        scrollBottom();
    }

    function hideTyping() {
        typingEl.classList.remove('cw-show');
    }

    function scrollBottom() {
        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    /* ─── AUTO-RESIZE TEXTAREA ────────────────────────────────────────────────── */
    function resizeInput() {
        inputEl.style.height = 'auto';
        inputEl.style.height = Math.min(inputEl.scrollHeight, 120) + 'px';
    }

    /* ─── BADGE ───────────────────────────────────────────────────────────────── */
    var unreadCount = 0;
    function incrementBadge() {
        unreadCount++;
        badgeEl.textContent = unreadCount > 9 ? '9+' : unreadCount;
        badgeEl.style.display = 'flex';
    }
    function clearBadge() {
        unreadCount = 0;
        badgeEl.style.display = 'none';
    }

    /* ─── API: LOAD HISTORY ───────────────────────────────────────────────────── */
    function loadHistory() {
        return fetch(API_BASE + MESSAGES_API, {
            method: 'GET',
            headers: Object.assign({ 'Accept': 'application/json' }, EXTRA_HEADERS),
        })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                var msgs = data[F_MESSAGES];
                if (data[F_CONV_ID]) state.conversationId = data[F_CONV_ID];

                if (Array.isArray(msgs) && msgs.length > 0) {
                    msgs.forEach(function (m) {
                        appendBubble(
                            m[F_ROLE]      || 'assistant',
                            m[F_CONTENT]   || '',
                            m[F_TIMESTAMP] || null
                        );
                    });
                } else if (WELCOME_MSG) {
                    appendBubble('assistant', WELCOME_MSG, new Date().toISOString());
                }
            })
            .catch(function () {
                /* On history fetch failure, just show welcome message if configured */
                if (WELCOME_MSG) {
                    appendBubble('assistant', WELCOME_MSG, new Date().toISOString());
                }
            });
    }

    /* ─── API: SEND MESSAGE ───────────────────────────────────────────────────── */
    function sendMessage(text) {
        if (state.sending || !text.trim()) return;
        state.sending = true;
        sendBtn.disabled = true;

        var now = new Date().toISOString();
        appendBubble('user', text, now);

        showTyping();

        var body = {};
        body[F_MSG_FIELD] = text;
        if (state.conversationId) body[F_CONV_ID] = state.conversationId;

        fetch(API_BASE + SEND_API, {
            method: 'POST',
            headers: Object.assign({
                'Content-Type': 'application/json',
                'Accept':       'application/json',
            }, EXTRA_HEADERS),
            body: JSON.stringify(body),
        })
            .then(function (res) {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(function (data) {
                hideTyping();
                var reply = data[F_REPLY];
                if (data[F_CONV_ID]) state.conversationId = data[F_CONV_ID];

                if (reply) {
                    appendBubble('assistant', reply, new Date().toISOString());
                    /* If panel is closed, show badge */
                    if (!state.open) incrementBadge();
                }
            })
            .catch(function () {
                hideTyping();
                appendError('Không gửi được tin nhắn. Vui lòng thử lại.');
            })
            .finally(function () {
                state.sending = false;
                /* re-enable send if there's text */
                sendBtn.disabled = !inputEl.value.trim();
            });
    }

    /* ─── OPEN / CLOSE ────────────────────────────────────────────────────────── */
    function openPanel() {
        state.open = true;
        panel.classList.add('cw-open');
        /* update FAB icon to X */
        fabBtn.querySelector('svg').innerHTML = '<path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>';
        clearBadge();
        inputEl.focus();

        if (!state.historyLoaded) {
            state.historyLoaded = true;
            loadHistory();
        } else {
            scrollBottom();
        }
    }

    function closePanel() {
        state.open = false;
        panel.classList.remove('cw-open');
        /* restore FAB icon to chat bubble */
        fabBtn.querySelector('svg').innerHTML = '<path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>';
    }

    /* ─── BIND EVENTS ─────────────────────────────────────────────────────────── */
    function bindEvents() {
        /* Toggle open/close */
        fabBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            state.open ? closePanel() : openPanel();
        });

        /* Close button */
        shadowRoot.querySelector('#cw-close').addEventListener('click', closePanel);

        /* Enable/disable send button based on input */
        inputEl.addEventListener('input', function () {
            resizeInput();
            sendBtn.disabled = !inputEl.value.trim();
        });

        /* Send on Enter (Shift+Enter = new line) */
        inputEl.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                doSend();
            }
        });

        /* Send button click */
        sendBtn.addEventListener('click', doSend);

        /* Close panel when clicking outside.
           Clicks inside Shadow DOM retarget e.target to the shadow host,
           so checking e.target !== hostEl catches all external clicks. */
        doc.addEventListener('click', function (e) {
            if (state.open && e.target !== hostEl && !hostEl.contains(e.target)) {
                closePanel();
            }
        });

        /* Keyboard: Escape to close */
        doc.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && state.open) closePanel();
        });
    }

    function doSend() {
        var text = inputEl.value.trim();
        if (!text || state.sending) return;
        inputEl.value = '';
        inputEl.style.height = 'auto';
        sendBtn.disabled = true;
        sendMessage(text);
    }

    /* ─── INIT ────────────────────────────────────────────────────────────────── */
    function init() {
        /* Create host element and attach Shadow DOM.
           Everything inside the shadow is isolated from host page CSS. */
        hostEl = doc.createElement('div');
        hostEl.id = 'cw-chat-widget';
        doc.body.appendChild(hostEl);
        shadowRoot = hostEl.attachShadow({ mode: 'open' });

        injectCSS();
        injectHTML();
        bindEvents();
    }

    if (doc.readyState === 'loading') {
        doc.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})(window, document);
