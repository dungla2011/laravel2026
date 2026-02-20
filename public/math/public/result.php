<?php 
$pageTitle = 'MathMiSu - Kết Quả';
$showNavbar = true;
$requireAuth = true;

$submissionId = $_GET['submissionId'] ?? null;
if (!$submissionId) {
    header('Location: /dashboard.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<style>
    .container {
        max-width: 900px;
        margin: 30px auto;
        padding: 0 20px;
    }

    .score-card {
        background: white;
        border-radius: 12px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .score-circle {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 48px;
        font-weight: 700;
        color: white;
    }

    .score-excellent .score-circle {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .score-good .score-circle {
        background: linear-gradient(135deg, #4caf50 0%, #45a049 100%);
    }

    .score-poor .score-circle {
        background: linear-gradient(135deg, #f44336 0%, #da190b 100%);
    }

    .score-message {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .score-details {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-top: 30px;
        padding-top: 30px;
        border-top: 2px solid #f0f0f0;
    }

    .detail-item {
        text-align: center;
    }

    .detail-label {
        color: #999;
        font-size: 14px;
        margin-bottom: 5px;
    }

    .detail-value {
        font-size: 24px;
        font-weight: 600;
        color: #667eea;
    }

    .answers-section {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 30px;
    }

    .answers-title {
        font-size: 22px;
        font-weight: 600;
        margin-bottom: 20px;
        color: #333;
    }

    .answer-item {
        border-left: 4px solid #e0e0e0;
        padding: 15px;
        margin-bottom: 15px;
        background: #f9f9f9;
        border-radius: 4px;
    }

    .answer-item.correct {
        border-left-color: #4caf50;
        background: #f1f8f6;
    }

    .answer-item.wrong {
        border-left-color: #f44336;
        background: #fef1f0;
    }

    .answer-question {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
        color: #333;
    }

    .answer-details {
        display: flex;
        gap: 20px;
        font-size: 14px;
        color: #666;
    }

    .answer-correct {
        color: #4caf50;
        font-weight: 600;
    }

    .answer-wrong {
        color: #f44336;
        font-weight: 600;
    }

    .actions {
        text-align: center;
        padding: 20px;
    }

    .btn-action {
        padding: 12px 30px;
        margin: 0 10px;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }

    .btn-secondary {
        background: white;
        color: #667eea;
        border: 2px solid #667eea;
    }

    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .loading {
        text-align: center;
        padding: 60px;
        color: #999;
    }
</style>

<div class="container">
    <div id="scoreCard" class="score-card">
        <div class="loading">Đang tải kết quả...</div>
    </div>

    <div class="answers-section">
        <h3 class="answers-title">📋 Chi Tiết Từng Câu</h3>
        <div id="answersDetails">
            <div class="loading">Đang tải...</div>
        </div>
    </div>

    <div class="actions">
        <button class="btn-action btn-primary" onclick="window.location.href='/dashboard.php'">
            🏠 Về Trang Chủ
        </button>
        <button class="btn-action btn-secondary" onclick="retryExercise()">
            🔄 Làm Lại Bài Này
        </button>
    </div>
</div>

<script>
    const submissionId = <?= $submissionId ?>;
    const token = localStorage.getItem('token');
    let currentExerciseId = null;

    async function loadResults() {
        try {
            const response = await fetch(`/api/submissions/${submissionId}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!response.ok) throw new Error('Failed to load results');

            const data = await response.json();
            currentExerciseId = data.submission.exercise_id;

            // Display score
            const score = data.submission.score || 0;
            const total = data.submission.total_questions || 0;
            const correct = data.answers.filter(a => a.isCorrect).length;
            const wrong = total - correct;

            let scoreClass = 'score-excellent';
            let message = '🎉 Xuất Sắc!';
            
            if (score < 50) {
                scoreClass = 'score-poor';
                message = '💪 Cố Gắng Hơn!';
            } else if (score < 70) {
                scoreClass = 'score-poor';
                message = '📈 Cần Cải Thiện!';
            } else if (score < 85) {
                scoreClass = 'score-good';
                message = '👍 Khá Tốt!';
            }

            document.getElementById('scoreCard').innerHTML = `
                <div class="${scoreClass}">
                    <div class="score-circle">${score}%</div>
                    <div class="score-message">${message}</div>
                    <div class="score-details">
                        <div class="detail-item">
                            <div class="detail-label">Tổng Câu</div>
                            <div class="detail-value">${total}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">✅ Đúng</div>
                            <div class="detail-value" style="color: #4caf50;">${correct}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">❌ Sai</div>
                            <div class="detail-value" style="color: #f44336;">${wrong}</div>
                        </div>
                    </div>
                </div>
            `;

            // Display answers
            let answersHtml = data.answers.map((a, idx) => `
                <div class="answer-item ${a.isCorrect ? 'correct' : 'wrong'}">
                    <div class="answer-question">
                        Câu ${idx + 1}: ${a.num1} ${a.operation} ${a.num2} = ?
                    </div>
                    <div class="answer-details">
                        <span>Đáp án của bạn: <strong class="${a.isCorrect ? 'answer-correct' : 'answer-wrong'}">${a.userAnswer || 'Không trả lời'}</strong></span>
                        ${!a.isCorrect ? `<span> <strong class="answer-correct"></strong></span>` : ''}
                        <span>${a.isCorrect ? '✅' : '❌'}</span>
                    </div>
                </div>
            `).join('');

            document.getElementById('answersDetails').innerHTML = answersHtml;

        } catch (error) {
            console.error('Error loading results:', error);
            alert('Lỗi tải kết quả!');
            window.location.href = '/dashboard.php';
        }
    }

    function retryExercise() {
        if (currentExerciseId) {
            window.location.href = `/quiz.php?exerciseId=${currentExerciseId}`;
        }
    }

    // Auto-load
    loadResults();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
