@extends('chat.layout')

@section('title', 'Chat - Danh sách cuộc trò chuyện')

@section('content')

<!-- Sidebar -->
<div class="chat-sidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-comments me-2"></i>Chat</h5>
            <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newChatModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>

    <!-- Search Box -->
    <div class="search-box position-relative">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="search-input" placeholder="Tìm kiếm trong danh sách..." id="searchConversations">

        <!-- User search results -->
        <div class="user-search-results" id="userSearchResults" style="display: none;"></div>
    </div>

    <!-- Conversations List -->
    <div class="conversations-list" id="conversationsList">
        @forelse($conversations as $conversation)

            <?php
//            dump($conversation);
            ?>

            <div class="conversation-item"
                 data-thread-id="{{ $conversation->thread_id }}"
                 data-partner-name="{{ $conversation->d_name_other }}"
                 onclick="openConversation('{{ $conversation->thread_id }}', '{{ $conversation->d_name_other }}')">


{{--                <img src="--}}
{{--                /tpl_modernize/assets/images/svgs/icon-user-male.svg--}}
{{--                "--}}
{{--                     class="conversation-avatar"--}}
{{--                     alt="">--}}

                <div class="conversation-info">
                    <?php
                    if($conversation->type){
//                        $conversation->type = $conversation->type == '1' ? '(G)' : '';
                    } else {
//                        $conversation->type = '';
                    }
                    ?>
                    <div class="conversation-name" style=" {{  $conversation->type == 0 ? 'color: red' : 'green' }}"> {{ $conversation->g_name ?? $conversation->d_name_other }} </div>
                    <div class="conversation-last-message">
                        {{ Str::limit($conversation->last_message, 100) }}
                    </div>
                </div>

                <div class="conversation-time">
                    {{ $conversation->last_message_time ? \Carbon\Carbon::parse($conversation->last_message_time)->format('H:i d/m') : '' }}
                </div>
            </div>
        @empty
            <div class="text-center p-4 text-muted">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <p>Chưa có cuộc trò chuyện nào...</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newChatModal">
                    Bắt đầu chat
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Main Chat Area -->
<div class="chat-main" id="chatMainArea">
    <DIV style="position: fixed; top: 5px; right: 10px; font-size: 80%">
        <b>
        <a href="/chat?channel_name=vovo">..1..</a>
        </b>
        <b>
        <a href="/chat?channel_name=mmi">..2..</a>
        </b>
    </DIV>
{{--    <div class="d-flex align-items-center justify-content-center h-100 text-muted" id="welcomeScreen" style="display: none">--}}
{{--        <div class="text-center">--}}
{{--            <i class="fas fa-comments fa-4x mb-3"></i>--}}
{{--            <h4>Chào mừng đến với Chat</h4>--}}
{{--            <p>Chọn một cuộc trò chuyện để bắt đầu nhắn tin</p>--}}
{{--        </div>--}}
{{--    </div>--}}

    <!-- Chat Interface (Hidden initially) -->
    <div id="chatInterface" style="display: none;">
        <!-- Chat Header -->
        <div class="chat-header">
            <img src="/tpl_modernize/assets/images/svgs/icon-user-male.svg"
                 class="chat-partner-avatar"
                 alt=""
                 id="chatPartnerAvatar">

            <div class="chat-partner-info">
                <h6 id="chatPartnerName">Partner Name</h6>
                <div class="chat-partner-status">
                    <i class="fas fa-circle text-success" style="font-size: 8px;"></i>
                    Đang hoạt động
                </div>
            </div>

            <div class="ms-auto">
                <button class="btn btn-sm btn-outline-secondary me-2" onclick="toggleChatInfo()">
                    <i class="fas fa-info-circle"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="clearChat()">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div class="chat-messages" id="chatMessages">
            <div class="loading" id="messagesLoading">
                <i class="fas fa-spinner fa-spin"></i> Đang tải tin nhắn...
            </div>
        </div>

        <!-- Chat Input -->
        <div class="chat-input-area">
            <div class="chat-input-container">
                <div class="chat-actions">
                    <button class="chat-action-btn" onclick="triggerFileUpload()" title="Gửi file">
                        <i class="fas fa-paperclip"></i>
                    </button>
                    <button class="chat-action-btn" onclick="triggerImageUpload()" title="Gửi ảnh">
                        <i class="fas fa-image"></i>
                    </button>
                </div>

                <textarea class="chat-input"
                          placeholder="Nhập tin nhắn..."
                          rows="1"
                          id="messageInput"></textarea>

                <button class="chat-action-btn send-btn" id="sendBtn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>

            <!-- Hidden file inputs -->
            <input type="file" id="fileInput" style="display: none;" accept="*/*">
            <input type="file" id="imageInput" style="display: none;" accept="image/*">
        </div>
    </div>
</div>

<!-- New Chat Modal -->
<div class="modal fade" id="newChatModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bắt đầu cuộc trò chuyện mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Tìm kiếm người dùng:</label>
                    <input type="text" class="form-control" id="userSearchInput" placeholder="Nhập tên, email hoặc username...">
                </div>
                <div id="userSearchModalResults"></div>
            </div>
        </div>
    </div>
</div>

<div>
<?php

//        dump($conversations);
?>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;
    let currentPage = 1;
    let isLoading = false;
    let hasMoreMessages = true;
    let messageRefreshInterval;

    // Search conversations
    $('#searchConversations').on('input', function() {
        const query = $(this).val().trim().toLowerCase();

        if (query.length === 0) {
            // Hiển thị tất cả conversations và xóa highlight
            $('.conversation-item').show();
            $('.conversation-name, .conversation-last-message').each(function() {
                const originalText = $(this).data('original-text') || $(this).text();
                $(this).html(originalText);
            });
            $('#userSearchResults').hide();
            updateSearchResults(0, $('.conversation-item').length);
            return;
        }

        let visibleCount = 0;
        const totalCount = $('.conversation-item').length;

        // Tìm kiếm trong danh sách conversations hiện tại
        $('.conversation-item').each(function() {
            const $nameElement = $(this).find('.conversation-name');
            const $messageElement = $(this).find('.conversation-last-message');

            // Lưu text gốc nếu chưa có
            if (!$nameElement.data('original-text')) {
                $nameElement.data('original-text', $nameElement.text());
            }
            if (!$messageElement.data('original-text')) {
                $messageElement.data('original-text', $messageElement.text());
            }

            const originalName = $nameElement.data('original-text').toLowerCase();
            const originalMessage = $messageElement.data('original-text').toLowerCase();

            // Kiểm tra xem query có match với tên hoặc tin nhắn cuối không
            if (originalName.includes(query) || originalMessage.includes(query)) {
                $(this).show();
                visibleCount++;

                // Highlight text
                const highlightedName = highlightText($nameElement.data('original-text'), query);
                const highlightedMessage = highlightText($messageElement.data('original-text'), query);

                $nameElement.html(highlightedName);
                $messageElement.html(highlightedMessage);
            } else {
                $(this).hide();
                // Reset về text gốc
                $nameElement.html($nameElement.data('original-text'));
                $messageElement.html($messageElement.data('original-text'));
            }
        });

        // Ẩn user search results vì chúng ta đang tìm trong conversations
        $('#userSearchResults').hide();

        // Cập nhật số lượng kết quả
        updateSearchResults(visibleCount, totalCount);
    });

    // Hàm highlight text
    function highlightText(text, query) {
        if (!query) return text;

        const regex = new RegExp(`(${escapeRegex(query)})`, 'gi');
        return text.replace(regex, '<mark style="background-color: yellow; padding: 1px 2px; border-radius: 2px;">$1</mark>');
    }

    // Hàm escape regex characters
    function escapeRegex(string) {
        return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    // Hàm cập nhật số lượng kết quả tìm kiếm
    function updateSearchResults(visible, total) {
        let $resultInfo = $('#searchResultInfo');

        if ($resultInfo.length === 0) {
            $resultInfo = $('<div id="searchResultInfo" class="text-center p-2 text-muted" style="font-size: 12px; border-bottom: 1px solid #f0f0f0;"></div>');
            $('.conversations-list').prepend($resultInfo);
        }

        if (visible === total) {
            $resultInfo.hide();
        } else {
            $resultInfo.show().text(`Hiển thị ${visible}/${total} cuộc trò chuyện`);
        }
    }

    // Search users in modal
    $('#userSearchInput').on('input', function() {
        const query = $(this).val().trim();

        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            if (query.length >= 2) {
                searchUsers(query, '#userSearchModalResults');
            } else {
                $('#userSearchModalResults').empty();
            }
        }, 300);
    });

    // Hide search results when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.search-box').length) {
            $('#userSearchResults').hide();
        }
    });

    function searchUsers(query, resultsContainer) {
        $.get('/api/chat/search-users', { q: query })
            .done(function(response) {
                let html = '';

                if (response.users.length > 0) {
                    response.users.forEach(function(user) {
                        html += `
                            <div class="user-search-item" onclick="startConversation(${user.id})">
                                <img src="${user.avatar}" class="user-search-avatar" alt="${user.name}">
                                <div class="user-search-info">
                                    <div class="user-search-name">${user.name}</div>
                                    <div class="user-search-email">${user.email}</div>
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = '<div class="text-center p-3 text-muted">Không tìm thấy người dùng</div>';
                }

                $(resultsContainer).html(html);
            })
            .fail(function() {
                $(resultsContainer).html('<div class="text-center p-3 text-danger">Lỗi tìm kiếm</div>');
            });
    }

    // Open conversation via AJAX
    window.openConversation = function(threadId, partnerName) {
        // Update active conversation in sidebar
        $('.conversation-item').removeClass('active');
        $(`.conversation-item[data-thread-id="${threadId}"]`).addClass('active');

        // Set current conversation data
        ChatApp.currentThreadId = threadId;
        ChatApp.currentPartnerId = null; // Will be determined from messages

        // Show chat interface
        $('#welcomeScreen').hide();
        $('#chatInterface').show();

        // Update chat header
        $('#chatPartnerName').text(partnerName || 'Unknown');

        // Load messages
        loadMessages(threadId);

        // Mark as read
        markAsRead(threadId);

        // Clear previous interval and set new one
        if (messageRefreshInterval) {
            clearInterval(messageRefreshInterval);
        }
        messageRefreshInterval = setInterval(function() {
            loadNewMessages(threadId);
        }, 5000);
    };

    function loadMessages(threadId, page = 1) {
        if (isLoading) return;

        isLoading = true;
        $('#messagesLoading').show();

        $.get('/api/chat/messages', {
            thread_id: threadId,
            page: page
        })
        .done(function(response) {
            if (page === 1) {
                $('#chatMessages').empty();
            }

            if (response.messages.length > 0) {
                let messagesHtml = '';
                let currentDate = '';

                // Set partner ID from first message
                if (response.messages.length > 0 && !ChatApp.currentPartnerId) {
                    const firstMessage = response.messages[0];
                    ChatApp.currentPartnerId = firstMessage.uid_from == {{ $user->id }} ? firstMessage.id_to : firstMessage.uid_from;
                }

                response.messages.forEach(function(message) {
                    // Add date separator if needed
                    if (message.formatted_date !== currentDate) {
                        messagesHtml += `<div class="message-date">${message.formatted_date}</div>`;
                        currentDate = message.formatted_date;
                    }

                    // Determine message type based on is_self field
                    const messageType = message.is_self == 1 ? 'sent' : 'received';

                    // Create message HTML
                    if (message.msg_type === 'file') {
                        messagesHtml += createFileMessage(message, messageType);
                    } else {
                        messagesHtml += createTextMessage(message, messageType);
                    }
                });

                if (page === 1) {
                    $('#chatMessages').html(messagesHtml);
                    // Scroll to bottom for new conversation
                    setTimeout(function() {
                        ChatApp.scrollToBottom();
                    }, 100);
                } else {
                    // For pagination, maintain scroll position
                    const chatMessages = $('#chatMessages');
                    const oldScrollHeight = chatMessages[0].scrollHeight;
                    chatMessages.prepend(messagesHtml);
                    const newScrollHeight = chatMessages[0].scrollHeight;
                    chatMessages.scrollTop(newScrollHeight - oldScrollHeight);
                }

                hasMoreMessages = response.has_more;
            } else if (page === 1) {
                $('#chatMessages').html('<div class="text-center p-4 text-muted">Chưa có tin nhắn nào</div>');
            }
        })
        .fail(function() {
            if (page === 1) {
                $('#chatMessages').html('<div class="text-center p-4 text-danger">Lỗi tải tin nhắn</div>');
            }
        })
        .always(function() {
            isLoading = false;
            $('#messagesLoading').hide();
        });
    }

    function loadNewMessages(threadId) {
        // Simple implementation - in production you'd track last message ID
        $.get('/api/chat/messages', {
            thread_id: threadId,
            page: 1
        })
        .done(function(response) {
            // Check if there are new messages and append them
            // This is simplified - you'd need to compare with existing messages
        });
    }

    function createTextMessage(message, type) {
        // Kiểm tra xem tin nhắn có phải của chính user không
        const isSelfMessage = message.is_self == 1;

        return `
            <div class="message ${type} ${isSelfMessage ? 'self-message' : 'other-message'}">
                ${type === 'received' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}

                <div class="message-content">
                    ${!isSelfMessage ? `
                        <span class="message-sender-name">
                            ${escapeHtml(message.d_name)}
                        </span>
                    ` : ''}
                    <span class="message-text">${(message.content)}</span>
                </div>
                <div class="message-time"> ${message.formatted_time} </div>
                ${type === 'sent' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
            </div>
        `;
    }

    function createFileMessage(message, type) {
        let fileInfo = {};
        try {
            fileInfo = JSON.parse(message.log || '{}');
        } catch (e) {
            fileInfo = {};
        }

        const isImage = fileInfo.file_type && fileInfo.file_type.startsWith('image/');
        const isSelfMessage = message.is_self == 1;

        if (isImage) {
            return `
                <div class="message ${type} ${isSelfMessage ? 'self-message' : 'other-message'}">
                    ${type === 'received' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                    <div class="message-content">
                        ${!isSelfMessage ? `
                            <span class="message-sender-name">
                                ${escapeHtml(message.d_name)}
                            </span>
                        ` : ''}
                        <img src="${fileInfo.file_url}" style="max-width: 200px; border-radius: 8px;" alt="${message.content}">
                        <div style="font-size: 12px; margin-top: 4px; opacity: 0.8;">${message.content}</div>
                    </div>
                    <div class="message-time">x2 ${message.formatted_time}</div>
                    ${type === 'sent' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                </div>
            `;
        } else {
            return `
                <div class="message ${type} ${isSelfMessage ? 'self-message' : 'other-message'}">
                    ${type === 'received' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                    <div class="message-content">
                        ${!isSelfMessage ? `
                            <span class="message-sender-name">
                                ${escapeHtml(message.d_name)}
                            </span>
                        ` : ''}
                        <div class="message-file">
                            <div class="file-icon">
                                <i class="fas fa-file"></i>
                            </div>
                            <div class="file-info">
                                <div class="file-name">${message.content}</div>
                                <div class="file-size">${formatFileSize(fileInfo.file_size || 0)}</div>
                            </div>
                            <a href="${fileInfo.file_url}" download class="btn btn-sm btn-primary">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                    </div>
                    <div class="message-time">x3 ${message.formatted_time}</div>
                    ${type === 'sent' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                </div>
            `;
        }
    }

    function markAsRead(threadId) {
        $.post('/api/chat/mark-read', {
            thread_id: threadId
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML.replace(/\n/g, '<br>');
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // File upload functions
    window.triggerFileUpload = function() {
        $('#fileInput').click();
    };

    window.triggerImageUpload = function() {
        $('#imageInput').click();
    };

    $('#fileInput, #imageInput').on('change', function() {
        const file = this.files[0];
        if (file) {
            uploadFile(file);
        }
    });

    function uploadFile(file) {
        if (!ChatApp.currentThreadId || !ChatApp.currentPartnerId) {
            alert('Vui lòng chọn cuộc trò chuyện trước');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('thread_id', ChatApp.currentThreadId);
        formData.append('to_user_id', ChatApp.currentPartnerId);

        // Show uploading message
        const tempMessage = $(`
            <div class="message sent temp-message">
                <div class="message-content">
                    <i class="fas fa-spinner fa-spin"></i> Đang tải lên ${file.name}...
                </div>
                <div class="message-time">Đang gửi...</div>
            </div>
        `);
        $('#chatMessages').append(tempMessage);
        ChatApp.scrollToBottom();

        $.ajax({
            url: '/api/chat/upload',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                tempMessage.remove();
                if (response.success) {
                    const messageHtml = createFileMessage(response.message, 'sent');
                    $('#chatMessages').append(messageHtml);
                    ChatApp.scrollToBottom();
                }
            },
            error: function() {
                tempMessage.find('.message-content').html('<i class="fas fa-exclamation-triangle"></i> Lỗi tải file');
            }
        });
    }

    window.startConversation = function(userId) {
        $.post('/api/chat/start-conversation', { user_id: userId })
            .done(function(response) {
                if (response.success) {
                    // Close modal
                    $('#newChatModal').modal('hide');

                    // Reload conversations list
                    location.reload();
                }
            })
            .fail(function() {
                alert('Không thể bắt đầu cuộc trò chuyện');
            });
    };

    window.toggleChatInfo = function() {
        alert('Tính năng thông tin chat sẽ được phát triển');
    };

    window.clearChat = function() {
        if (confirm('Bạn có chắc muốn xóa toàn bộ tin nhắn?')) {
            alert('Tính năng xóa chat sẽ được phát triển');
        }
    };

    // Scroll to load more messages
    $('#chatMessages').on('scroll', function() {
        const chatMessages = $(this);
        const scrollTop = chatMessages.scrollTop();
        const scrollHeight = chatMessages[0].scrollHeight;
        const clientHeight = chatMessages[0].clientHeight;

        // Load more when scrolled to top (with 50px threshold)
        if (scrollTop <= 50 && hasMoreMessages && !isLoading && ChatApp.currentThreadId) {
            currentPage++;
            loadMessages(ChatApp.currentThreadId, currentPage);
        }
    });

    // Override ChatApp sendMessage to work with current conversation
    ChatApp.sendMessage = function() {
        const content = $('#messageInput').val().trim();
        if (!content || !this.currentThreadId || !this.currentPartnerId) return;

        const tempMessage = this.addTempMessage(content, 'sent');
        $('#messageInput').val('');
        $('#messageInput').css('height', 'auto');

        $.post('/api/chat/send', {
            thread_id: this.currentThreadId,
            content: content,
            to_user_id: this.currentPartnerId
        })
        .done(function(response) {
            tempMessage.remove();
            // Ensure the message is marked as self message
            response.message.is_self = 1;
            const messageHtml = createTextMessage(response.message, 'sent');
            $('#chatMessages').append(messageHtml);
            ChatApp.scrollToBottom();
        })
        .fail(function() {
            tempMessage.find('.message-content').css('background-color', '#dc3545');
            tempMessage.find('.message-content').append('<br><small>Gửi thất bại</small>');
        });
    };

    // Auto refresh conversations every 30 seconds
    setInterval(function() {
        // Refresh conversation list without reloading page
        // This can be implemented later for real-time updates
    }, 30000);
});
</script>
@endpush
