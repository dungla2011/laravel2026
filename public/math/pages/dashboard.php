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

    /* Weekly Calendar */
    .weekly-calendar {
        background: white;
        border-radius: 12px;
        padding: 25px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .weekly-calendar h3 {
        margin-bottom: 20px;
        color: #333;
        font-size: 18px;
    }

    .week-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
    }

    .day-column {
        text-align: center;
        border-radius: 8px;
        padding: 10px;
        background: #f9f9f9;
        min-height: 120px;
    }

    .day-header {
        font-weight: 600;
        color: #667eea;
        padding-bottom: 8px;
        border-bottom: 2px solid #e0e0e0;
        margin-bottom: 10px;
        font-size: 13px;
    }

    .day-date {
        font-size: 12px;
        color: #999;
        margin-bottom: 8px;
    }

    .day-submissions {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .submission-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 10px;
        cursor: pointer;
        transition: all 0.2s;
        text-align: left;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .submission-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.4);
    }

    .submission-badge.excellent {
        background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
    }

    .submission-badge.good {
        background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%);
    }

    .submission-badge.poor {
        background: linear-gradient(135deg, #f44336 0%, #da190b 100%);
    }

    .no-submissions {
        color: #ccc;
        font-size: 11px;
        margin-top: 10px;
    }
</style>

<div class="container">
    <!-- Weekly Calendar -->
    <div class="weekly-calendar">
        <h3>📅 Lịch Sử Tuần Này</h3>
        <div class="week-grid" id="weeklyCalendar">
            <div class="loading">Đang tải...</div>
        </div>
    </div>

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
            const response = await fetch('<?= url('api/exercises.php') ?>', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (response.status === 401) {
                // Token invalid or expired - redirect to login
                localStorage.clear();
                alert('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.');
                window.location.href = '<?= url('?act=login') ?>';
                return;
            }

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
            const response = await fetch(`<?= url('api/exercise_stats.php') ?>?id=${exerciseId}`, {
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
            const response = await fetch('<?= url('api/user_history.php') ?>', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!response.ok) throw new Error('Failed to load history');

            const data = await response.json();

            // Filter submissions with at least 2 answered questions
            const filteredData = data.filter(item => item.answered_count && item.answered_count >= 2);

            if (filteredData.length === 0) {
                document.getElementById('historyContent').innerHTML = '<div class="no-data">Chưa có lịch sử làm bài</div>';
                return;
            }

            let html = '<table class="history-table"><thead><tr><th>Bài Tập</th><th>Thời Gian</th><th>Thời Lượng</th><th>Điểm</th><th>Hành Động</th></tr></thead><tbody>';

            filteredData.forEach(item => {
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
        window.location.href = `<?= url() ?>?act=quiz&exerciseId=${exerciseId}`;
    }

    function viewSubmission(submissionId) {
        window.location.href = `<?= url() ?>?act=result&submissionId=${submissionId}`;
    }

    // Load weekly calendar
    async function loadWeeklyCalendar() {
        const token = localStorage.getItem('token');
        
        try {
            const response = await fetch('<?= url('api/user_history.php') ?>', {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!response.ok) return;

            const data = await response.json();
            
            // Get last 7 days
            const days = [];
            const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
            
            for (let i = 6; i >= 0; i--) {
                const date = new Date();
                date.setDate(date.getDate() - i);
                days.push({
                    date: date,
                    dateStr: date.toISOString().split('T')[0],
                    dayName: dayNames[date.getDay()],
                    submissions: []
                });
            }
            
            // Group submissions by date (only show submissions with at least 2 answered questions)
            data.forEach(item => {
                if (!item.start_time) return;
                if (!item.answered_count || item.answered_count < 2) return; // Skip if less than 2 answers
                const itemDate = item.start_time.split(' ')[0];
                const day = days.find(d => d.dateStr === itemDate);
                if (day) {
                    day.submissions.push(item);
                }
            });
            
            // Render calendar
            let html = '';
            days.forEach(day => {
                const dateDisplay = day.date.getDate() + '/' + (day.date.getMonth() + 1);
                
                html += `
                    <div class="day-column">
                        <div class="day-header">${day.dayName}</div>
                        <div class="day-date">${dateDisplay}</div>
                        <div class="day-submissions">
                `;
                
                if (day.submissions.length === 0) {
                    html += '<div class="no-submissions">-</div>';
                } else {
                    day.submissions.forEach(sub => {
                        let badgeClass = 'excellent';
                        if (sub.score < 70) badgeClass = 'poor';
                        else if (sub.score < 85) badgeClass = 'good';
                        
                        // Truncate name if too long
                        let displayName = sub.name.length > 15 ? sub.name.substring(0, 15) + '...' : sub.name;
                        
                        html += `
                            <div class="submission-badge ${badgeClass}" onclick="viewSubmission(${sub.id})" title="${sub.name} - ${sub.score}%">
                                ${displayName} (${sub.score}%)
                            </div>
                        `;
                    });
                }
                
                html += `
                        </div>
                    </div>
                `;
            });
            
            document.getElementById('weeklyCalendar').innerHTML = html;
            
        } catch (error) {
            console.error('Error loading weekly calendar:', error);
        }
    }

    // Auto-load on page ready
    loadExercises();
    loadWeeklyCalendar();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
