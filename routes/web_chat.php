<?php

use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

// Chat Routes - Yêu cầu authentication
Route::middleware(['auth'])->group(function () {
    
    // Giao diện chat chính
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    
    // Hiển thị conversation cụ thể
    Route::get('/chat/conversation/{thread_id}', [ChatController::class, 'showConversation'])->name('chat.conversation');
    
    // API endpoints cho chat
    Route::prefix('api/chat')->group(function () {
        
        // Lấy tin nhắn của conversation
        Route::get('/messages', [ChatController::class, 'getMessages'])->name('api.chat.messages');
        
        // Gửi tin nhắn Zalo với callback saveDbCallback
        Route::post('/send', function(\Illuminate\Http\Request $request) {
            // Convert từ chat parameters sang Zalo parameters
            $request->merge([
                'uid' => $request->get('to_user_id'),
                'message' => $request->get('content'),
                'channel_name' => $request->get('channel_name', 'event1')
            ]);

            $controller = new ChatController();
            return $controller->sendMessage($request, function($data) {
                return app(ChatController::class)->saveDbCallback($data);
            });
        })->name('api.chat.send');
        
        // Tìm kiếm users
        Route::get('/search-users', [ChatController::class, 'searchUsers'])->name('api.chat.search-users');
        
        // Bắt đầu conversation mới
        Route::post('/start-conversation', [ChatController::class, 'startConversation'])->name('api.chat.start-conversation');
        
        // Đánh dấu đã đọc
        Route::post('/mark-read', [ChatController::class, 'markAsRead'])->name('api.chat.mark-read');
        
        // Upload file
        Route::post('/upload', [ChatController::class, 'uploadFile'])->name('api.chat.upload');
        
    });
    
}); 