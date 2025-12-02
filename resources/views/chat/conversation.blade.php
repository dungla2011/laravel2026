@extends('chat.layout')

@section('title', 'Chat với ' . ($partner ? $partner->getNameTitle() : 'Unknown'))

@section('content')
<!-- Sidebar -->
<div class="chat-sidebar">
    <!-- Header -->
    <div class="sidebar-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5><i class="fas fa-comments me-2"></i>Chat</h5>
            <div>
                <a href="{{ route('chat.index') }}" class="btn btn-sm btn-outline-secondary me-2">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#newChatModal">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Search Box -->
    <div class="search-box position-relative">
        <i class="fas fa-search search-icon"></i>
        <input type="text" class="search-input" placeholder="Tìm kiếm cuộc trò chuyện..." id="searchConversations">

        <!-- User search results -->
        <div class="user-search-results" id="userSearchResults" style="display: none;"></div>
    </div>

    <!-- Conversations List -->
    <div class="conversations-list" id="conversationsList">
        @forelse($conversations as $conversation)
            <div class="conversation-item {{ $conversation->thread_id == $threadId ? 'active' : '' }}"
                 data-thread-id="{{ $conversation->thread_id }}"
                 data-partner-id="{{ $conversation->partner ? $conversation->partner->id : '' }}"
                 onclick="openConversation('{{ $conversation->thread_id }}')">

                <img src="{{ $conversation->partner_avatar }}"
                     class="conversation-avatar"
                     alt="{{ $conversation->partner_name }}">

                <div class="conversation-info">
                    <div class="conversation-name">{{ $conversation->partner_name }}</div>
                    <div class="conversation-last-message">
                        {{ Str::limit($conversation->last_message, 50) }}
                    </div>
                </div>

                <div class="conversation-time">
                    {{ $conversation->last_message_time ? \Carbon\Carbon::parse($conversation->last_message_time)->format('H:i') : '' }}
                </div>
            </div>
        @empty
            <div class="text-center p-4 text-muted">
                <i class="fas fa-comments fa-3x mb-3"></i>
                <p>Chưa có cuộc trò chuyện nào!</p>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newChatModal">
                    Bắt đầu chat
                </button>
            </div>
        @endforelse
    </div>
</div>

<!-- Main Chat Area -->
<div class="chat-main">
    @if($partner)
        <!-- Chat Header -->
        <div class="chat-header">
            <img src="{{ $partner->avatar ?? '/tpl_modernize/assets/images/svgs/icon-user-male.svg' }}"
                 class="chat-partner-avatar"
                 alt="{{ $partner->getNameTitle() }}">

            <div class="chat-partner-info">
                <h6>{{ $partner->getNameTitle() }}</h6>
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
    @else
        <div class="d-flex align-items-center justify-content-center h-100 text-muted">
            <div class="text-center">
                <i class="fas fa-exclamation-triangle fa-4x mb-3"></i>
                <h4>Không tìm thấy cuộc trò chuyện</h4>
                <p>Cuộc trò chuyện này có thể đã bị xóa hoặc bạn không có quyền truy cập</p>
                <a href="{{ route('chat.index') }}" class="btn btn-primary">Quay lại danh sách</a>
            </div>
        </div>
    @endif
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

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Set current conversation data
    ChatApp.currentThreadId = '{{ $threadId }}';
    ChatApp.currentPartnerId = {{ $partner ? $partner->id : 'null' }};

    let searchTimeout;
    let currentPage = 1;
    let isLoading = false;
    let hasMoreMessages = true;

    // Load initial messages
    if (ChatApp.currentThreadId && ChatApp.currentPartnerId) {
        loadMessages();

        // Mark messages as read
        markAsRead();

        // Auto refresh messages every 5 seconds
        setInterval(function() {
            loadNewMessages();
        }, 5000);
    }

    function loadMessages(page = 1) {
        if (isLoading) return;

        isLoading = true;

        $.get('/api/chat/messages', {
            thread_id: ChatApp.currentThreadId,
            page: page
        })
        .done(function(response) {
            if (page === 1) {
                $('#chatMessages').empty();
            }

            if (response.messages.length > 0) {
                let messagesHtml = '';
                let currentDate = '';

                response.messages.forEach(function(message) {
                    // Add date separator if needed
                    if (message.formatted_date !== currentDate) {
                        messagesHtml += `<div class="message-date">${message.formatted_date}</div>`;
                        currentDate = message.formatted_date;
                    }

                    // Determine message type
                    const messageType = message.uid_from == {{ $user->id }} ? 'sent' : 'received';

                    // Create message HTML
                    if (message.msg_type === 'file') {
                        messagesHtml += createFileMessage(message, messageType);
                    } else {
                        messagesHtml += createTextMessage(message, messageType);
                    }
                });

                if (page === 1) {
                    $('#chatMessages').html(messagesHtml);
                    ChatApp.scrollToBottom();
                } else {
                    $('#chatMessages').prepend(messagesHtml);
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

    function loadNewMessages() {
        // Load only new messages (implement based on last message timestamp)
        // This is a simplified version - in production, you'd track the last message ID
        $.get('/api/chat/messages', {
            thread_id: ChatApp.currentThreadId,
            page: 1
        })
        .done(function(response) {
            // Check if there are new messages and append them
            // This is simplified - you'd need to compare with existing messages
        });
    }

    function createTextMessage(message, type) {
        return `
            <div class="message ${type}">
                ${type === 'received' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                <div class="message-content">${escapeHtml(message.content)}</div>
                <div class="message-time">${message.formatted_time}</div>
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

        if (isImage) {
            return `
                <div class="message ${type}">
                    ${type === 'received' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                    <div class="message-content">
                        <img src="${fileInfo.file_url}" style="max-width: 200px; border-radius: 8px;" alt="${message.content}">
                        <div style="font-size: 12px; margin-top: 4px;">${message.content}</div>
                    </div>
                    <div class="message-time">${message.formatted_time}</div>
                    ${type === 'sent' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                </div>
            `;
        } else {
            return `
                <div class="message ${type}">
                    ${type === 'received' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                    <div class="message-content">
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
                    <div class="message-time">${message.formatted_time}</div>
                    ${type === 'sent' ? `<img src="${message.sender_avatar}" class="message-avatar" alt="">` : ''}
                </div>
            `;
        }
    }

    function markAsRead() {
        $.post('/api/chat/mark-read', {
            thread_id: ChatApp.currentThreadId
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

    // Search functionality (same as index page)
    $('#searchConversations').on('input', function() {
        const query = $(this).val().trim();

        if (query.length >= 2) {
            searchUsers(query, '#userSearchResults');
            $('#userSearchResults').show();
        } else {
            $('#userSearchResults').hide();
        }
    });

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

    window.startConversation = function(userId) {
        $.post('/api/chat/start-conversation', { user_id: userId })
            .done(function(response) {
                if (response.success) {
                    window.location.href = response.redirect_url;
                }
            })
            .fail(function() {
                alert('Không thể bắt đầu cuộc trò chuyện');
            });
    };

    window.openConversation = function(threadId) {
        window.location.href = `/chat/conversation/${threadId}`;
    };

    window.toggleChatInfo = function() {
        // Implement chat info panel
        alert('Tính năng thông tin chat sẽ được phát triển');
    };

    window.clearChat = function() {
        if (confirm('Bạn có chắc muốn xóa toàn bộ tin nhắn?')) {
            // Implement clear chat functionality
            alert('Tính năng xóa chat sẽ được phát triển');
        }
    };

    // Scroll to load more messages
    $('#chatMessages').on('scroll', function() {
        if ($(this).scrollTop() === 0 && hasMoreMessages && !isLoading) {
            currentPage++;
            loadMessages(currentPage);
        }
    });
});
</script>
@endpush
