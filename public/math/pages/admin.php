<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Math Baby</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            border-radius: 12px;
            padding: 20px 30px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            color: #667eea;
            font-size: 24px;
        }

        .logout-btn {
            background: #f44336;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }

        .logout-btn:hover {
            background: #da190b;
            transform: translateY(-2px);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-value {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
        }

        .section {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f5f5f5;
            font-weight: 600;
            color: #333;
        }

        tr:hover {
            background: #f9f9f9;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            transition: all 0.2s;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        .btn-success {
            background: #4caf50;
            color: white;
        }

        .btn-success:hover {
            background: #45a049;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            max-width: 500px;
            margin: 100px auto;
            padding: 30px;
            border-radius: 12px;
        }

        .modal-content h3 {
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #666;
            font-size: 14px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
        }

        .clickable-row {
            cursor: pointer;
        }

        .clickable-row:hover {
            background: #f0f0f0 !important;
        }

        .checkbox-col {
            width: 40px;
            text-align: center;
        }

        .delete-actions {
            margin-top: 15px;
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .selected-count {
            color: #667eea;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="header">
            <h1>🎯 Admin Panel</h1>
            <button class="logout-btn" onclick="logout()">Đăng xuất</button>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Tổng người dùng</h3>
                <div class="stat-value" id="totalUsers">-</div>
            </div>
            <div class="stat-card">
                <h3>Tổng bài tập</h3>
                <div class="stat-value" id="totalExercises">-</div>
            </div>
            <div class="stat-card">
                <h3>Tổng lượt làm</h3>
                <div class="stat-value" id="totalSubmissions">-</div>
            </div>
            <div class="stat-card">
                <h3>Điểm TB</h3>
                <div class="stat-value" id="avgScore">-</div>
            </div>
        </div>

        <div class="section">
            <h2>📚 Quản lý bài tập</h2>
            <button class="btn btn-success" onclick="showAddExerciseModal()">+ Thêm bài tập</button>
            <div id="exercisesTable">
                <div class="loading">Đang tải...</div>
            </div>
        </div>

        <div class="section">
            <h2>👥 Người dùng</h2>
            <div id="usersTable">
                <div class="loading">Đang tải...</div>
            </div>
        </div>
    </div>

    <!-- Modal Add Exercise -->
    <div id="addExerciseModal" class="modal">
        <div class="modal-content">
            <h3>Thêm bài tập mới</h3>
            <form id="addExerciseForm">
                <div class="form-group">
                    <label>Tên bài tập</label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea name="description" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label>Loại</label>
                    <select name="type">
                        <option value="cong">Phép cộng</option>
                        <option value="tru">Phép trừ</option>
                        <option value="nhan">Phép nhân</option>
                        <option value="chia">Phép chia</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Độ khó (1-5)</label>
                    <input type="number" name="difficulty" min="1" max="5" value="1">
                </div>
                <div class="form-group">
                    <label>Số câu hỏi</label>
                    <input type="number" name="question_count" min="5" max="50" value="10">
                </div>
                <div class="form-actions">
                    <button type="button" class="btn" onclick="closeModal()">Hủy</button>
                    <button type="submit" class="btn btn-success">Tạo bài tập</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const BASE_URL = '<?= url() ?>';
        const API_URL = '<?= url('api') ?>';

        function logout() {
            localStorage.removeItem('token');
            window.location.href = BASE_URL + '?act=login';
        }

        async function loadStats() {
            const token = localStorage.getItem('token');
            
            try {
                // Load exercises
                const exercisesRes = await fetch(API_URL + '/exercises.php', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const exercisesData = await exercisesRes.json();
                const exercises = Object.values(exercisesData).flat();
                document.getElementById('totalExercises').textContent = exercises.length;

                // Load user history to count submissions
                const historyRes = await fetch(API_URL + '/user_history.php', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });
                const history = await historyRes.json();
                document.getElementById('totalSubmissions').textContent = history.length;
                
                // Calculate average score
                const scores = history.filter(h => h.score != null).map(h => h.score);
                const avgScore = scores.length > 0 ? Math.round(scores.reduce((a, b) => a + b, 0) / scores.length) : 0;
                document.getElementById('avgScore').textContent = avgScore + '%';
                
            } catch (error) {
                console.error('Error loading stats:', error);
            }
        }

        async function loadUsers() {
            const token = localStorage.getItem('token');
            
            try {
                const response = await fetch(API_URL + '/admin/users.php', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (!response.ok) {
                    throw new Error('Failed to load');
                }

                const data = await response.json();
                
                let html = '<table><thead><tr>';
                html += '<th>ID</th><th>Username</th><th>Ngày tạo</th><th>Lượt làm</th><th>Điểm TB</th>';
                html += '</tr></thead><tbody>';
                
                data.forEach(user => {
                    const createdDate = new Date(user.created_at).toLocaleDateString('vi-VN');
                    const avgScore = user.avg_score ? Math.round(user.avg_score) + '%' : '-';
                    
                    html += `<tr class="clickable-row" onclick="viewUserHistory(${user.id}, '${user.username}')">
                        <td>${user.id}</td>
                        <td>${user.username}</td>
                        <td>${createdDate}</td>
                        <td>${user.total_submissions}</td>
                        <td>${avgScore}</td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                document.getElementById('usersTable').innerHTML = html;
                
                // Update total users stat
                document.getElementById('totalUsers').textContent = data.length;
                
            } catch (error) {
                console.error('Error loading users:', error);
                document.getElementById('usersTable').innerHTML = '<p style="color: red;">Lỗi tải dữ liệu</p>';
            }
        }

        async function loadExercises() {
            const token = localStorage.getItem('token');
            
            try {
                const response = await fetch(API_URL + '/exercises.php', {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (!response.ok) {
                    throw new Error('Failed to load');
                }

                const groupedData = await response.json();
                const data = Object.values(groupedData).flat();
                
                let html = '<table><thead><tr>';
                html += '<th>ID</th><th>Tên bài</th><th>Loại</th><th>Độ khó</th><th>Số câu</th><th>Thao tác</th>';
                html += '</tr></thead><tbody>';
                
                data.forEach(ex => {
                    html += `<tr>
                        <td>${ex.id}</td>
                        <td>${ex.name}</td>
                        <td>${ex.type}</td>
                        <td>${ex.difficulty}</td>
                        <td>${ex.question_count}</td>
                        <td>
                            <button class="btn btn-primary" onclick="editExercise(${ex.id})">Sửa</button>
                            <button class="btn btn-danger" onclick="deleteExercise(${ex.id})">Xóa</button>
                        </td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                document.getElementById('exercisesTable').innerHTML = html;
                
            } catch (error) {
                document.getElementById('exercisesTable').innerHTML = '<p style="color: red;">Lỗi tải dữ liệu</p>';
            }
        }

        function showAddExerciseModal() {
            document.getElementById('addExerciseModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('addExerciseModal').style.display = 'none';
            document.getElementById('addExerciseForm').reset();
        }

        function viewUserHistory(userId, username) {
            window.location.href = `${BASE_URL}?act=user_history_admin&user_id=${userId}&username=${encodeURIComponent(username)}`;
        }

        function closeUserHistoryModal() {
            document.getElementById('userHistoryModal').style.display = 'none';
        }

        function toggleSelectAll(checkbox) {
            const checkboxes = document.querySelectorAll('.submission-checkbox');
            checkboxes.forEach(cb => cb.checked = checkbox.checked);
            updateDeleteButton();
        }

        function updateDeleteButton() {
            const checkboxes = document.querySelectorAll('.submission-checkbox:checked');
            const count = checkboxes.length;
            document.getElementById('selectedCount').textContent = count;
            document.getElementById('deleteBtn').disabled = count === 0;
            
            // Update select all checkbox state
            const allCheckboxes = document.querySelectorAll('.submission-checkbox');
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
            }
        }

        async function deleteSelectedSubmissions() {
            const checkboxes = document.querySelectorAll('.submission-checkbox:checked');
            const ids = Array.from(checkboxes).map(cb => parseInt(cb.value));
            
            if (ids.length === 0) return;
            
            if (!confirm(`Bạn có chắc muốn xóa ${ids.length} lượt làm bài?`)) return;
            
            const token = localStorage.getItem('token');
            
            try {
                const response = await fetch(API_URL + '/admin/delete_submissions.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ submission_ids: ids })
                });

                if (response.ok) {
                    alert(`Đã xóa ${ids.length} lượt làm bài!`);
                    // Reload current user history
                    const userId = document.getElementById('userHistoryTitle').textContent.includes('-') ? 
                        parseInt(document.querySelector('.clickable-row')?.onclick?.toString().match(/\d+/)?.[0]) : null;
                    
                    closeUserHistoryModal();
                    loadStats();
                    loadUsers();
                } else {
                    const error = await response.json();
                    alert('Lỗi: ' + (error.error || 'Không thể xóa'));
                }
            } catch (error) {
                alert('Lỗi: ' + error.message);
            }
        }

        document.getElementById('addExerciseForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const token = localStorage.getItem('token');
            
            const formData = new FormData(e.target);
            const data = Object.fromEntries(formData);
            
            try {
                const response = await fetch(API_URL + '/admin/create_exercise.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (response.ok) {
                    alert('Tạo bài tập thành công!');
                    closeModal();
                    loadExercises();
                    loadStats();
                } else {
                    alert('Lỗi tạo bài tập');
                }
            } catch (error) {
                alert('Lỗi: ' + error.message);
            }
        });

        function editExercise(id) {
            alert('Chức năng đang phát triển');
        }

        async function deleteExercise(id) {
            if (!confirm('Bạn có chắc muốn xóa bài tập này?')) return;
            
            const token = localStorage.getItem('token');
            
            try {
                const response = await fetch(API_URL + '/admin/delete_exercise.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id })
                });

                if (response.ok) {
                    alert('Xóa thành công!');
                    loadExercises();
                    loadStats();
                } else {
                    alert('Lỗi xóa bài tập');
                }
            } catch (error) {
                alert('Lỗi: ' + error.message);
            }
        }

        // Check token
        const token = localStorage.getItem('token');
        if (!token) {
            window.location.href = BASE_URL + '?act=login';
        }

        // Load data
        loadStats();
        loadExercises();
        loadUsers();
    </script>
</body>
</html>
