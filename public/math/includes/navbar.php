<?php if (isset($showNavbar) && $showNavbar): ?>
<header style="
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    <?= isset($stickyHeader) && $stickyHeader ? 'position: sticky; top: 0; z-index: 100;' : '' ?>
">
    <h1 style="font-size: 28px; cursor: pointer;" onclick="window.location.href='/math/?act=dashboard'">
        🧮 MathMiSu
    </h1>

    <?php if (isset($headerRight)): ?>
        <?= $headerRight ?>
    <?php else: ?>
        <div class="user-info" style="display: flex; gap: 15px; align-items: center;">
            <span class="username" id="usernameDisplay" style="font-size: 14px;"></span>

            <button class="btn-admin" onclick="window.location.href='<?= url('?act=admin') ?>'" style="display: none;" id="adminBtn">
                👨‍💼 Admin
            </button>

            <button class="btn-logout" onclick="logout()">
                🚪 Đăng Xuất
            </button>
        </div>
    <?php endif; ?>
</header>

<script>
function logout() {
    localStorage.removeItem('token');
    localStorage.removeItem('username');
    localStorage.removeItem('userId');
    window.location.href = '<?= url('?act=login') ?>';
}

// Display username
if (!window.navbarInitialized) {
    window.navbarInitialized = true;
    var username = localStorage.getItem('username');
    if (username) {
        var usernameEl = document.getElementById('usernameDisplay');
        if (usernameEl) {
            usernameEl.textContent = `👤 ${username}`;
        }
    }

    // Check if admin (for demo - you can implement properly)
    var isAdmin = username === 'admin';
    if (isAdmin) {
        const adminBtn = document.getElementById('adminBtn');
        if (adminBtn) adminBtn.style.display = 'inline-block';
    }
}
</script>
<?php endif; ?>
