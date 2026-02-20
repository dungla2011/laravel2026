<?php 
$pageTitle = 'MathMiSu - Đăng Nhập';
$bodyBg = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
include __DIR__ . '/../includes/header.php';
?>

<style>
    body {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 100vh;
    }

    .container {
        background: white;
        padding: 40px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        max-width: 400px;
        width: 100%;
        margin: 20px;
    }

    .header {
        text-align: center;
        margin-bottom: 30px;
    }

    .header h1 {
        color: #667eea;
        font-size: 32px;
        margin-bottom: 10px;
    }

    .header p {
        color: #999;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        color: #333;
        font-weight: 600;
    }

    input {
        width: 100%;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    input:focus {
        outline: none;
        border-color: #667eea;
    }

    .button-group {
        display: flex;
        gap: 10px;
        margin-top: 30px;
    }

    button {
        flex: 1;
        padding: 12px;
        border: none;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-login {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
    }

    .btn-register {
        background: #fff;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-register:hover {
        background: #667eea;
        color: white;
    }

    .message {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
        text-align: center;
        font-size: 14px;
        display: none;
    }

    .message.error {
        background: #ffebee;
        color: #c62828;
        border: 1px solid #ef5350;
    }

    .message.success {
        background: #e8f5e9;
        color: #2e7d32;
        border: 1px solid #66bb6a;
    }
</style>

<div class="container">
    <div class="header">
        <h1>🧮 MathMiSu</h1>
        <p>Luyện tập toán học cho bé</p>
    </div>

    <div id="message" class="message"></div>

    <form id="authForm">
        <div class="form-group">
            <label for="username">Tên đăng nhập</label>
            <input type="text" id="username" name="username" placeholder="Nhập tên đăng nhập" required>
        </div>

        <div class="form-group">
            <label for="password">Mật khẩu</label>
            <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
        </div>

        <div class="button-group">
            <button type="submit" class="btn-login">Đăng Nhập</button>
            <button type="button" class="btn-register" onclick="switchToRegister()">Đăng Ký</button>
        </div>
    </form>
</div>

<script>
    const form = document.getElementById('authForm');
    const messageEl = document.getElementById('message');
    let isRegisterMode = false;

    function showMessage(text, type = 'error') {
        messageEl.textContent = text;
        messageEl.className = `message ${type}`;
        messageEl.style.display = 'block';
        setTimeout(() => {
            messageEl.style.display = 'none';
        }, 3000);
    }

    function switchToRegister() {
        isRegisterMode = !isRegisterMode;
        const btn = event.target;
        const loginBtn = document.querySelector('.btn-login');
        
        if (isRegisterMode) {
            btn.textContent = 'Quay Lại';
            loginBtn.textContent = 'Đăng Ký';
            document.querySelector('.header p').textContent = 'Tạo tài khoản mới';
        } else {
            btn.textContent = 'Đăng Ký';
            loginBtn.textContent = 'Đăng Nhập';
            document.querySelector('.header p').textContent = 'Luyện tập toán học cho bé';
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const username = document.getElementById('username').value;
        const password = document.getElementById('password').value;
        
        const endpoint = isRegisterMode ? '/api/register' : '/api/login';
        
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ username, password })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                if (isRegisterMode) {
                    showMessage('✅ Đăng ký thành công! Đang chuyển đến trang đăng nhập...', 'success');
                    setTimeout(() => {
                        switchToRegister();
                        document.getElementById('password').value = '';
                    }, 1500);
                } else {
                    localStorage.setItem('token', data.token);
                    localStorage.setItem('username', username);
                    localStorage.setItem('userId', data.userId);
                    showMessage('✅ Đăng nhập thành công!', 'success');
                    setTimeout(() => {
                        window.location.href = '/dashboard.php';
                    }, 1000);
                }
            } else {
                showMessage('❌ ' + (data.error || 'Có lỗi xảy ra'));
            }
        } catch (error) {
            showMessage('❌ Không thể kết nối đến server');
            console.error('Error:', error);
        }
    });

    // Check if already logged in
    if (localStorage.getItem('token')) {
        window.location.href = '/dashboard.php';
    }
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
