<?php

/**
 * Laravel Routes cho Zalo Proxy API
 *
 * File: routes/api.php
 *
 * Thêm các routes này vào file routes/api.php của Laravel project
 */

use App\Http\ControllerApi\ZaloProxyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Zalo Proxy API Routes
|--------------------------------------------------------------------------
|
| Tất cả routes có prefix /api/zalo
| Ví dụ: /api/zalo/accounts/abc/qr-login
|
*/

Route::prefix('zalo2')->group(function () {

    // ============================================
    // PUBLIC ROUTES (không cần auth Laravel)
    // ============================================

    /**
     * Lấy ảnh QR code (public)
     * GET /api/zalo/qr/{name}
     */
    Route::get('qr/{name}', [ZaloProxyController::class, 'getQrImage'])
        ->name('zalo.qr.image');


    // ============================================
    // PROTECTED ROUTES (cần auth Laravel - tùy chọn)
    // ============================================

    // Nếu muốn bảo vệ bằng Laravel auth, bọc trong middleware:
    // Route::middleware(['auth:sanctum'])->group(function () {

    /**
     * Quản lý tài khoản
     */

    // Lấy danh sách tất cả tài khoản
    // GET /api/zalo/accounts
    Route::get('accounts', [ZaloProxyController::class, 'getAllAccounts'])
        ->name('zalo.accounts.list');

    // Tạo tài khoản mới
    // POST /api/zalo/accounts
    Route::post('accounts', [ZaloProxyController::class, 'createAccount'])
        ->name('zalo.accounts.create');

    // Lấy thông tin chi tiết 1 tài khoản
    // GET /api/zalo/accounts/{name}
    Route::get('accounts/{name}', [ZaloProxyController::class, 'getAccountDetail'])
        ->name('zalo.accounts.detail');

    // Xóa tài khoản
    // DELETE /api/zalo/accounts/{name}
    Route::delete('accounts/{name}', [ZaloProxyController::class, 'deleteAccount'])
        ->name('zalo.accounts.delete');


    /**
     * Đăng nhập / Đăng xuất
     */

    // Tạo QR code đăng nhập
    // POST /api/zalo/accounts/{name}/qr-login
    Route::get('accounts/{name}/get-match-messages', [ZaloProxyController::class, 'getMatchMessages'])
        ->name('zalo.accounts.get-match-messages');

    // Tạo QR code đăng nhập
    // POST /api/zalo/accounts/{name}/qr-login
    Route::post('accounts/{name}/qr-login', [ZaloProxyController::class, 'createQrLogin'])
        ->name('zalo.accounts.qr-login');

    // Kiểm tra trạng thái đăng nhập
    // GET /api/zalo/accounts/{name}/login-status
    Route::get('accounts/{name}/login-status', [ZaloProxyController::class, 'getLoginStatus'])
        ->name('zalo.accounts.login-status');

    // Đăng xuất tài khoản
    // POST /api/zalo/accounts/{name}/qr-logout
    Route::post('accounts/{name}/qr-logout', [ZaloProxyController::class, 'logout'])
        ->name('zalo.accounts.logout');

    // Kiểm tra credentials còn hợp lệ
    // POST /api/zalo/accounts/{name}/validate-credentials
    Route::post('accounts/{name}/validate-credentials', [ZaloProxyController::class, 'validateCredentials'])
        ->name('zalo.accounts.validate');


    /**
     * Lắng nghe tin nhắn
     */

    // Bắt đầu lắng nghe
    // POST /api/zalo/accounts/{name}/start-listening
    Route::post('accounts/{name}/start-listening', [ZaloProxyController::class, 'startListening'])
        ->name('zalo.accounts.start-listening');

    // Dừng lắng nghe
    // POST /api/zalo/accounts/{name}/stop-listening
    Route::post('accounts/{name}/stop-listening', [ZaloProxyController::class, 'stopListening'])
        ->name('zalo.accounts.stop-listening');


    /**
     * Thông tin user
     */

    // Lấy thông tin user
    // GET /api/zalo/accounts/{name}/user-info
    Route::get('accounts/{name}/user-info', [ZaloProxyController::class, 'getUserInfo'])
        ->name('zalo.accounts.user-info');


    /**
     * Helper workflows
     */

    // Full login workflow (QR + polling)
    // POST /api/zalo/accounts/{name}/full-login
    Route::post('accounts/{name}/full-login', [ZaloProxyController::class, 'fullLoginWorkflow'])
        ->name('zalo.accounts.full-login');

    // }); // End middleware group
});


/*
|--------------------------------------------------------------------------
| Route List Summary
|--------------------------------------------------------------------------
|
| PUBLIC:
|   GET    /api/zalo/qr/{name}                                  - Lấy ảnh QR
|
| ACCOUNTS:
|   GET    /api/zalo/accounts                                   - Danh sách accounts
|   POST   /api/zalo/accounts                                   - Tạo account mới
|   GET    /api/zalo/accounts/{name}                           - Chi tiết account
|   DELETE /api/zalo/accounts/{name}                           - Xóa account
|
| LOGIN/LOGOUT:
|   POST   /api/zalo/accounts/{name}/qr-login                  - Tạo QR login
|   GET    /api/zalo/accounts/{name}/login-status              - Trạng thái login
|   POST   /api/zalo/accounts/{name}/qr-logout                 - Logout
|   POST   /api/zalo/accounts/{name}/validate-credentials      - Kiểm tra credentials
|
| LISTENER:
|   POST   /api/zalo/accounts/{name}/start-listening           - Bắt đầu lắng nghe
|   POST   /api/zalo/accounts/{name}/stop-listening            - Dừng lắng nghe
|
| USER INFO:
|   GET    /api/zalo/accounts/{name}/user-info                 - Thông tin user
|
| HELPERS:
|   POST   /api/zalo/accounts/{name}/full-login                - Full workflow
|
*/
