<?php 
$pageTitle = 'MathMiSu - Bảng Điều Khiển';
$showNavbar = true;
$requireAuth = true;
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<style>
    .container {
        max-width: 1200px;
        margin: 30px auto;
        padding: 0 20px;
    }

    .tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
    }

    .tab-button {
        padding: 12px 20px;
        background: white;
        border: 2px solid #ddd;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
        font-size: 16px;
    }

    .tab-button.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-color: #667eea;
    }

    .tab-button:hover {
        border-color: #667eea;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .exercise-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }

    .exercise-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s;
    }

    .exercise-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }

    .exercise-card h3 {
        color: #333;
        margin-bottom: 10px;
        font-size: 18px;
    }

    .exercise-description {
        color: #666;
        font-size: 13px;
        margin-bottom: 15px;
        min-height: 40px;
    }

    .exercise-info {
        color: #999;
        font-size: 12px;
        margin-bottom: 10px;
    }

    .exercise-stats {
        margin: 15px 0;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 6px;
        font-size: 13px;
    }

    .stat-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }

    .btn-start {
        width: 100%;
        padding: 10px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-start:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }

    .loading {
        text-align: center;
        padding: 40px;
        color: #999;
        font-size: 14px;
    }

    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: #999;
        font-size: 16px;
    }

    .history-table {
        width: 100%;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .history-table th {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px;
        text-align: left;
        font-weight: 600;
    }

    .history-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .history-table tr:last-child td {
        border-bottom: none;
    }

    .history-table tr:hover {
        background: #f9f9f9;
    }

    .score-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 13px;
    }

    .score-excellent {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .score-good {
        background: #fff3e0;
        color: #ef6c00;
    }

    .score-poor {
        background: #ffebee;
        color: #c62828;
    }
</style>

<div class="container">
    <div class="tabs">
        <button class="tab-button active" onclick="switchTab('exercises')">📚 Bài Tập</button>
        <button class="tab-button" onclick="switchTab('history')">📊 Lịch Sử</button>
    </div>

    <!-- Exercises Tab -->
    <div id="exercisesTab" class="tab-content active">
        <div style="margin-bottom: 30px;">
            <h3 style="margin-bottom: 15px; color: #667eea;">✏️ Phép Cộng</h3>
            <div id="congExercises" class="exercise-grid">
                <div class="loading">Đang tải...</div>
            </div>
        </div>

        <div>
            <h3 style="margin-bottom: 15px; color: #f093fb;">✂️ Phép Trừ</h3>
            <div id="truExercises" class="exercise-grid">
                <div class="loading">Đang tải...</div>
            </div>
        </div>
    </div>

    <!-- History Tab -->
    <div id="historyTab" class="tab-content">
        <h2 style="margin-bottom: 20px;">Lịch sử làm bài của bạn</h2>
        <div id="historyContent">
            <div class="loading">Đang tải lịch sử...</div>
        </div>
    </div>
</div>

<script>
    function switchTab(tab) {
        document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
        document.querySelectorAll('.tab-button').forEach(el => el.classList.remove('active'));
        
        document.getElementById(tab === 'exercises' ? 'exercisesTab' : 'historyTab').classList.add('active');
        event.target.classList.add('active');

        if (tab === 'history') {
            loadHistory();
        }
    }

    async function loadExercises() {
        const token = localStorage.getItem('token');
        
        try {
            const response = await fetch('/api/exercises', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!response.ok) throw new Error('Failed to load exercises');

            const data = await response.json();

            // Phép cộng
            let congHtml = '';
            for (const ex of data.cong) {
                const stats = await getExerciseStats(ex.id);
                congHtml += `
                    <div class="exercise-card">
                        <h3>${ex.name}</h3>
                        <div class="exercise-description">${ex.description || ''}</div>
                        <div class="exercise-info">📊 ${ex.question_count} câu hỏi</div>
                        <div class="exercise-stats">
                            <div class="stat-row">Số lần làm: <strong>${stats.totalAttempts}</strong></div>
                            <div class="stat-row">Điểm cao nhất: <strong>${stats.bestScore}%</strong></div>
                        </div>
                        <button class="btn-start" onclick="startExercise(${ex.id})">Bắt Đầu Làm Bài</button>
                    </div>
                `;
            }
            document.getElementById('congExercises').innerHTML = congHtml || '<div class="no-data">Chưa có bài tập</div>';

            // Phép trừ
            let truHtml = '';
            for (const ex of data.tru) {
                const stats = await getExerciseStats(ex.id);
                truHtml += `
                    <div class="exercise-card">
                        <h3>${ex.name}</h3>
                        <div class="exercise-description">${ex.description || ''}</div>
                        <div class="exercise-info">📊 ${ex.question_count} câu hỏi</div>
                        <div class="exercise-stats">
                            <div class="stat-row">Số lần làm: <strong>${stats.totalAttempts}</strong></div>
                            <div class="stat-row">Điểm cao nhất: <strong>${stats.bestScore}%</strong></div>
                        </div>
                        <button class="btn-start" onclick="startExercise(${ex.id})">Bắt Đầu Làm Bài</button>
                    </div>
                `;
            }
            document.getElementById('truExercises').innerHTML = truHtml || '<div class="no-data">Chưa có bài tập</div>';

        } catch (error) {
            console.error('Error loading exercises:', error);
            document.getElementById('congExercises').innerHTML = '<div class="no-data" style="color: red;">❌ Lỗi tải dữ liệu</div>';
        }
    }

    async function getExerciseStats(exerciseId) {
        const token = localStorage.getItem('token');
        try {
            const response = await fetch(`/api/users/exercise/${exerciseId}/stats`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            
            if (response.ok) {
                return await response.json();
            }
            return { totalAttempts: 0, bestScore: 0 };
        } catch (error) {
            return { totalAttempts: 0, bestScore: 0 };
        }
    }

    async function loadHistory() {
        const token = localStorage.getItem('token');
        
        try {
            const response = await fetch('/api/users/history', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!response.ok) throw new Error('Failed to load history');

            const data = await response.json();

            if (data.length === 0) {
                document.getElementById('historyContent').innerHTML = '<div class="no-data">Chưa có lịch sử làm bài</div>';
                return;
            }

            let html = '<table class="history-table"><thead><tr><th>Bài Tập</th><th>Thời Gian</th><th>Thời Lượng</th><th>Điểm</th><th>Hành Động</th></tr></thead><tbody>';

            data.forEach(item => {
                const startTime = new Date(item.start_time).toLocaleString('vi-VN');
                const minutes = Math.floor(item.duration_seconds / 60);
                const seconds = item.duration_seconds % 60;
                const duration = item.duration_seconds ? `${minutes}:${seconds.toString().padStart(2, '0')}` : 'N/A';
                
                let scoreClass = 'score-excellent';
                if (item.score < 70) scoreClass = 'score-poor';
                else if (item.score < 85) scoreClass = 'score-good';

                html += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${startTime}</td>
                        <td>${duration}</td>
                        <td><span class="score-badge ${scoreClass}">${item.score || 0}%</span></td>
                        <td><button class="btn-start" style="width: auto; padding: 6px 12px; font-size: 12px;" onclick="viewSubmission(${item.id})">Xem</button></td>
                    </tr>
                `;
            });

            html += '</tbody></table>';
            document.getElementById('historyContent').innerHTML = html;

        } catch (error) {
            console.error('Error loading history:', error);
            document.getElementById('historyContent').innerHTML = '<div class="no-data" style="color: red;">❌ Lỗi tải lịch sử</div>';
        }
    }

    function startExercise(exerciseId) {
        window.location.href = `/quiz.php?exerciseId=${exerciseId}`;
    }

    function viewSubmission(submissionId) {
        window.location.href = `/result.php?submissionId=${submissionId}`;
    }

    // Auto-load on page ready
    loadExercises();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
