<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Chat - Zalo Style')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS -->
    <style>
        :root {
            --zalo-primary: #0068ff;
            --zalo-secondary: #f0f2f5;
            --zalo-sidebar: #ffffff;
            --zalo-chat-bg: #f8f9fa;
            --zalo-message-sent: #0068ff;
            --zalo-message-received: #e4e6ea;
            --zalo-text-primary: #050505;
            --zalo-text-secondary: #65676b;
            --zalo-border: #dadde1;
            --zalo-hover: #f2f3f5;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--zalo-secondary);
            overflow: hidden;
        }

        .chat-container {
            height: 100vh;
            display: flex;
        }

        /* Sidebar */
        .chat-sidebar {
            width: 320px;
            background-color: var(--zalo-sidebar);
            border-right: 1px solid var(--zalo-border);
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 16px;
            border-bottom: 1px solid var(--zalo-border);
            background-color: var(--zalo-sidebar);
        }

        .sidebar-header h5 {
            color: var(--zalo-text-primary);
            margin: 0;
            font-weight: 600;
        }

        .search-box {
            padding: 12px 16px;
            border-bottom: 1px solid var(--zalo-border);
        }

        .search-input {
            width: 100%;
            padding: 8px 12px 8px 40px;
            border: 1px solid var(--zalo-border);
            border-radius: 20px;
            background-color: var(--zalo-secondary);
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--zalo-primary);
        }

        .search-icon {
            position: absolute;
            left: 28px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--zalo-text-secondary);
        }

        .conversations-list {
            flex: 1;
            overflow-y: auto;
        }

        .conversation-item {
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
        }

        .conversation-item:hover {
            background-color: var(--zalo-hover);
        }

        .conversation-item.active {
            background-color: var(--zalo-primary);
            color: white;
        }

        .conversation-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
        }

        .conversation-info {
            flex: 1;
            min-width: 0;
        }

        .conversation-name {
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-last-message {
            font-size: 13px;
            color: var(--zalo-text-secondary);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .conversation-time {
            font-size: 11px;
            color: var(--zalo-text-secondary);
            margin-left: 8px;
            white-space: nowrap;
            min-width: 80px;
            text-align: right;
        }

        /* Chat Interface */
        #chatInterface {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Chat Area */
        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background-color: var(--zalo-chat-bg);
            height: 100vh;
            overflow: hidden;
        }

        .chat-header {
            padding: 16px 20px;
            background-color: white;
            border-bottom: 1px solid var(--zalo-border);
            display: flex;
            align-items: center;
        }

        .chat-partner-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 12px;
            object-fit: cover;
        }

        .chat-partner-info h6 {
            margin: 0;
            font-weight: 600;
            color: var(--zalo-text-primary);
        }

        .chat-partner-status {
            font-size: 12px;
            color: var(--zalo-text-secondary);
        }

        .chat-messages {
            flex: 1;
            padding: 16px 20px;
            overflow-y: auto;
            overflow-x: hidden;
            background: linear-gradient(to bottom, #f8f9fa, #ffffff);
            min-height: 0; /* Important for flex child to shrink */
            scroll-behavior: smooth;
        }

        .message-group {
            margin-bottom: 16px;
        }

        .message-date {
            text-align: center;
            margin: 20px 0;
            font-size: 12px;
            color: var(--zalo-text-secondary);
        }

        .message {
            display: flex;
            margin-bottom: 8px;
            align-items: flex-end;
        }

        .message.sent {
            justify-content: flex-end;
        }

        .message.received {
            justify-content: flex-start;
        }

        .message-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            margin: 0 8px;
            object-fit: cover;
        }

        .message.sent .message-avatar {
            order: 2;
        }

        .message-content {
            max-width: 60%;
            padding: 8px 12px;
            border-radius: 18px;
            font-size: 14px;
            line-height: 1.4;
            position: relative;
            word-wrap: break-word;
        }

        .message.sent .message-content {
            background-color: var(--zalo-message-sent);
            color: white;
            border-bottom-right-radius: 4px;
            text-align: right;
        }

        .message.received .message-content {
            background-color: var(--zalo-message-received);
            color: var(--zalo-text-primary);
            border-bottom-left-radius: 4px;
            text-align: left;
        }

        /* Sender name styling */
        .message-sender-name {
            font-size: 12px;
            font-weight: 500;
            font-style: italic;
            margin-bottom: 2px;
            display: block;
            text-align: left;
        }

        .message.sent .message-sender-name {
            color: rgba(255, 255, 255, 0.8);
            text-align: right;
        }

        .message.received .message-sender-name {
            color: var(--zalo-primary);
            text-align: left;
        }

        /* Message text styling */
        .message-text {
            display: block;
            line-height: 1.4;
        }

        .message.sent .message-text {
            color: rgba(255, 255, 255, 0.95);
            text-align: right;
        }

        .message.received .message-text {
            color: var(--zalo-text-primary);
            text-align: left;
        }

        /* Self message specific styling */
        .message.self-message .message-content {
            text-align: right !important;
        }

        .message.self-message .message-text {
            text-align: right !important;
        }

        .message.self-message .message-sender-name {
            text-align: right !important;
        }

        /* Other message specific styling */
        .message.other-message .message-content {
            text-align: left !important;
        }

        .message.other-message .message-text {
            text-align: left !important;
        }

        .message.other-message .message-sender-name {
            text-align: left !important;
        }

        /* Override for sent messages that are self */
        .message.sent.self-message {
            justify-content: flex-end;
        }

        .message.sent.self-message .message-content {
            background-color: var(--zalo-message-sent);
            color: white;
            text-align: right !important;
        }

        /* Override for received messages that are from others */
        .message.received.other-message {
            justify-content: flex-start;
        }

        .message.received.other-message .message-content {
            background-color: var(--zalo-message-received);
            color: var(--zalo-text-primary);
            text-align: left !important;
        }

        /* Force text alignment */
        .message.sent.self-message .message-text {
            text-align: right !important;
            /*direction: rtl;*/
        }

        .message.received.other-message .message-text {
            text-align: left !important;
            direction: ltr;
        }

        .message-time {
            font-size: 10px;
            color: var(--zalo-text-secondary);
            margin: 0 8px;
            align-self: flex-end;
            white-space: nowrap;
            min-width: 70px;
        }

        .message.sent .message-time {
            order: 0;
        }

        /* Chat Input */
        .chat-input-area {
            padding: 16px 20px;
            background-color: white;
            border-top: 1px solid var(--zalo-border);
        }

        .chat-input-container {
            display: flex;
            align-items: flex-end;
            gap: 8px;
        }

        .chat-input {
            flex: 1;
            min-height: 40px;
            max-height: 120px;
            padding: 8px 16px;
            border: 1px solid var(--zalo-border);
            border-radius: 20px;
            resize: none;
            font-size: 14px;
            line-height: 1.4;
        }

        .chat-input:focus {
            outline: none;
            border-color: var(--zalo-primary);
        }

        .chat-actions {
            display: flex;
            gap: 8px;
        }

        .chat-action-btn {
            width: 40px;
            height: 40px;
            border: none;
            border-radius: 50%;
            background-color: var(--zalo-secondary);
            color: var(--zalo-text-secondary);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-action-btn:hover {
            background-color: var(--zalo-primary);
            color: white;
        }

        .send-btn {
            background-color: var(--zalo-primary);
            color: white;
        }

        .send-btn:disabled {
            background-color: var(--zalo-border);
            cursor: not-allowed;
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: transparent;
        }

        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Chat messages specific scrollbar */
        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: rgba(0,0,0,0.1);
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(0,0,0,0.3);
            border-radius: 4px;
        }

        .chat-messages::-webkit-scrollbar-thumb:hover {
            background: rgba(0,0,0,0.5);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .chat-sidebar {
                width: 100%;
                position: absolute;
                z-index: 1000;
                height: 100vh;
                transform: translateX(-100%);
                transition: transform 0.3s;
            }

            .chat-sidebar.show {
                transform: translateX(0);
            }

            .chat-main {
                width: 100%;
            }

            .message-content {
                max-width: 80%;
            }
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 20px;
            color: var(--zalo-text-secondary);
        }

        /* File message */
        .message-file {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 12px;
            border: 1px solid var(--zalo-border);
            border-radius: 8px;
            background-color: white;
            max-width: 250px;
            margin-top: 4px;
        }

        .message.sent .message-file {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .file-icon {
            width: 32px;
            height: 32px;
            background-color: var(--zalo-primary);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .message.sent .file-icon {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .file-info {
            flex: 1;
            min-width: 0;
        }

        .file-name {
            font-size: 13px;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .message.sent .file-name {
            color: rgba(255, 255, 255, 0.9);
        }

        .file-size {
            font-size: 11px;
            color: var(--zalo-text-secondary);
        }

        .message.sent .file-size {
            color: rgba(255, 255, 255, 0.7);
        }

        /* New conversation modal */
        .new-chat-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: var(--zalo-primary);
            color: white;
            border: none;
            font-size: 20px;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0, 104, 255, 0.3);
            transition: all 0.2s;
        }

        .new-chat-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 16px rgba(0, 104, 255, 0.4);
        }

        /* User search results */
        .user-search-results {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid var(--zalo-border);
            border-top: none;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
        }

        .user-search-item {
            padding: 8px 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .user-search-item:hover {
            background-color: var(--zalo-hover);
        }

        .user-search-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }

        .user-search-info {
            flex: 1;
        }

        .user-search-name {
            font-size: 14px;
            font-weight: 500;
        }

        .user-search-email {
            font-size: 12px;
            color: var(--zalo-text-secondary);
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="chat-container">
        @yield('content')
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>

    <!-- Custom JS -->
    <script>
        // CSRF Token setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Global chat functions
        window.ChatApp = {
            currentThreadId: null,
            currentPartnerId: null,
            messagesContainer: null,
            messageInput: null,

            init: function() {
                this.messagesContainer = $('.chat-messages');
                this.messageInput = $('.chat-input');
                this.bindEvents();
            },

            bindEvents: function() {
                // Send message on Enter
                $(document).on('keypress', '.chat-input', function(e) {
                    if (e.which === 13 && !e.shiftKey) {
                        e.preventDefault();
                        ChatApp.sendMessage();
                    }
                });

                // Send button click
                $(document).on('click', '.send-btn', function() {
                    ChatApp.sendMessage();
                });

                // File upload
                $(document).on('change', '.file-input', function() {
                    ChatApp.uploadFile(this.files[0]);
                });

                // Auto-resize textarea
                $(document).on('input', '.chat-input', function() {
                    this.style.height = 'auto';
                    this.style.height = Math.min(this.scrollHeight, 120) + 'px';
                });
            },

            sendMessage: function() {
                const content = this.messageInput.val().trim();
                if (!content || !this.currentThreadId || !this.currentPartnerId) return;

                const tempMessage = this.addTempMessage(content, 'sent');
                this.messageInput.val('');
                this.messageInput.css('height', 'auto');

                $.post('/api/chat/send', {
                    thread_id: this.currentThreadId,
                    content: content,
                    to_user_id: this.currentPartnerId
                })
                .done(function(response) {
                    tempMessage.remove();
                    ChatApp.addMessage(response.message, 'sent');
                    ChatApp.scrollToBottom();
                })
                .fail(function() {
                    tempMessage.find('.message-content').css('background-color', '#dc3545');
                    tempMessage.find('.message-content').append('<br><small>Gửi thất bại</small>');
                });
            },

            addTempMessage: function(content, type) {
                const messageHtml = `
                    <div class="message ${type} temp-message">
                        <div class="message-content">${this.escapeHtml(content)}</div>
                        <div class="message-time">Đang gửi...</div>
                    </div>
                `;
                $('#chatMessages').append(messageHtml);
                this.scrollToBottom();
                return $('#chatMessages').find('.temp-message').last();
            },

            addMessage: function(message, type) {
                const messageHtml = `
                    <div class="message ${type}">
                        ${type === 'received' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                        <div class="message-content">${this.escapeHtml(message.content)}</div>
                        <div class="message-time">${message.formatted_time}</div>
                        ${type === 'sent' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                    </div>
                `;
                this.messagesContainer.append(messageHtml);
                this.scrollToBottom();
            },

            scrollToBottom: function() {
                const chatMessages = $('#chatMessages');
                if (chatMessages.length) {
                    chatMessages.scrollTop(chatMessages[0].scrollHeight);
                }
            },

            escapeHtml: function(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            },

            formatFileSize: function(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        };

        // Initialize when document ready
        $(document).ready(function() {
            ChatApp.init();
        });
    </script>

    @stack('scripts')
</body>
</html>
