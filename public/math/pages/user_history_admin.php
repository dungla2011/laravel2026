<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch sử làm bài - Math Baby</title>
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

        .container {
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

        .back-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .back-btn:hover {
            background: #5568d3;
            transform: translateY(-2px);
        }

        .content {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eee;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.2s;
        }

        .btn-danger {
            background: #f44336;
            color: white;
        }

        .btn-danger:hover {
            background: #da190b;
        }

        .btn-danger:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .selected-info {
            color: #667eea;
            font-weight: 600;
            font-size: 14px;
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

        .checkbox-col {
            width: 40px;
            text-align: center;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .error {
            text-align: center;
            padding: 40px;
            color: #f44336;
        }

        .empty {
            text-align: center;
            padding: 40px;
            color: #999;
        }

        .score-excellent {
            color: #4caf50;
            font-weight: 600;
        }

        .score-good {
            color: #ff9800;
            font-weight: 600;
        }

        .score-poor {
            color: #f44336;
            font-weight: 600;
        }

        .detail-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }

        .detail-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Lịch sử làm bài - <span id="userName">...</span></h1>
            <a href="<?= url() ?>?act=admin" class="back-btn">← Quay lại Admin</a>
        </div>

        <div class="content">
            <div class="actions">
                <button class="btn btn-danger" onclick="deleteSelected()" id="deleteBtn" disabled>
                    Xóa đã chọn (<span id="selectedCount">0</span>)
                </button>
                <span class="selected-info" id="selectedInfo"></span>
            </div>

            <div id="tableContent">
                <div class="loading">Đang tải...</div>
            </div>
        </div>
    </div>

    <script>
        const BASE_URL = '<?= url() ?>';
        const API_URL = '<?= url('api') ?>';
        
        const urlParams = new URLSearchParams(window.location.search);
        const userId = urlParams.get('user_id');
        const userName = urlParams.get('username') || 'User';

        document.getElementById('userName').textContent = userName;

        if (!userId) {
            document.getElementById('tableContent').innerHTML = '<div class="error">Thiếu thông tin user_id</div>';
        } else {
            loadHistory();
        }

        async function loadHistory() {
            const token = localStorage.getItem('token');
            
            if (!token) {
                window.location.href = BASE_URL + '?act=login';
                return;
            }

            try {
                const response = await fetch(API_URL + '/admin/user_submissions.php?user_id=' + userId, {
                    headers: { 'Authorization': `Bearer ${token}` }
                });

                if (!response.ok) {
                    throw new Error('Failed to load');
                }

                const data = await response.json();
                
                // Filter submissions with at least 2 answered questions
                const filteredData = data.filter(sub => sub.answered_count && sub.answered_count >= 2);
                
                if (filteredData.length === 0) {
                    document.getElementById('tableContent').innerHTML = '<div class="empty">Chưa có lịch sử làm bài</div>';
                    return;
                }
                
                let html = '<table><thead><tr>';
                html += '<th class="checkbox-col"><input type="checkbox" id="selectAll" onchange="toggleSelectAll(this)"></th>';
                html += '<th>ID</th><th>Bài tập</th><th>Loại</th><th>Thời gian bắt đầu</th><th>Thời gian kết thúc</th><th>Điểm</th><th>Số câu</th><th>Chi tiết</th>';
                html += '</tr></thead><tbody>';
                
                filteredData.forEach(sub => {
                    const startTime = new Date(sub.start_time).toLocaleString('vi-VN');
                    const endTime = sub.end_time ? new Date(sub.end_time).toLocaleString('vi-VN') : '-';
                    
                    let scoreClass = 'score-excellent';
                    if (sub.score < 70) scoreClass = 'score-poor';
                    else if (sub.score < 85) scoreClass = 'score-good';
                    
                    const score = sub.score != null ? `<span class="${scoreClass}">${sub.score}%</span>` : '-';
                    
                    html += `<tr>
                        <td class="checkbox-col"><input type="checkbox" class="submission-checkbox" value="${sub.id}" onchange="updateDeleteButton()"></td>
                        <td>${sub.id}</td>
                        <td>${sub.exercise_name}</td>
                        <td>${sub.exercise_type}</td>
                        <td>${startTime}</td>
                        <td>${endTime}</td>
                        <td>${score}</td>
                        <td>${sub.total_questions}</td>
                        <td><a href="${BASE_URL}?act=result&submissionId=${sub.id}" class="detail-link" target="_blank">Xem</a></td>
                    </tr>`;
                });
                
                html += '</tbody></table>';
                document.getElementById('tableContent').innerHTML = html;
                
            } catch (error) {
                console.error('Error loading history:', error);
                document.getElementById('tableContent').innerHTML = '<div class="error">Lỗi tải dữ liệu: ' + error.message + '</div>';
            }
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
            
            if (count > 0) {
                document.getElementById('selectedInfo').textContent = `Đã chọn ${count} bài làm`;
            } else {
                document.getElementById('selectedInfo').textContent = '';
            }
            
            // Update select all checkbox
            const allCheckboxes = document.querySelectorAll('.submission-checkbox');
            const selectAllCheckbox = document.getElementById('selectAll');
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allCheckboxes.length > 0 && count === allCheckboxes.length;
            }
        }

        async function deleteSelected() {
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
                    loadHistory(); // Reload data
                } else {
                    const error = await response.json();
                    alert('Lỗi: ' + (error.error || 'Không thể xóa'));
                }
            } catch (error) {
                alert('Lỗi: ' + error.message);
            }
        }
    </script>
</body>
</html>
