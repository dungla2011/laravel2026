    <script>
        // Auth check
        if (!window.authChecked) {
            window.authChecked = true;
            var token = localStorage.getItem('token');
            <?php if (isset($requireAuth) && $requireAuth): ?>
            if (!token) {
                window.location.href = '<?= url('?act=login') ?>';
            }
            <?php endif; ?>
        }
    </script>
</body>
</html>
