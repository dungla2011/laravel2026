<?php 
$pageTitle = 'MathMiSu - Làm Bài Tập';
$showNavbar = true;
$stickyHeader = true;
$requireAuth = true;

// Get exercise ID from URL
$exerciseId = $_GET['exerciseId'] ?? null;
if (!$exerciseId) {
    header('Location: ' . url('?act=dashboard'));
    exit;
}

// Custom header right content (timer)
ob_start();
?>
<div class="timer" id="timer">00:00</div>
<?php
$headerRight = ob_get_clean();

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<style>
    .container {
        max-width: 900px;
        margin: 30px auto;
        padding: 30px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    .exercise-title {
        font-size: 24px;
        color: #333;
        margin-bottom: 10px;
    }

    .exercise-info {
        color: #666;
        margin-bottom: 30px;
        font-size: 14px;
    }

    .progress-bar {
        background: #e0e0e0;
        height: 8px;
        border-radius: 4px;
        margin-bottom: 30px;
        overflow: hidden;
    }

    .progress-fill {
        background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
        height: 100%;
        transition: width 0.3s;
    }

    .questions-container {
        display: flex;
        flex-direction: column;
        gap: 15px;
        margin-bottom: 30px;
    }

    .question-box {
        background: #f9f9f9;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 20px;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .question-box:hover {
        border-color: #FF8C00;
        box-shadow: 0 3px 10px rgba(255, 140, 0, 0.2);
    }

    .question-box.focused {
        border-color: #FF8C00;
        border-width: 3px;
        box-shadow: 0 4px 15px rgba(255, 140, 0, 0.3);
    }

    .question-box.answered {
        border-color: #4caf50;
        background: #f1f8f6;
    }

    .question-box.error {
        border-color: #f44336;
        background: #fef1f0;
    }

    .question-number {
        font-size: 14px;
        color: #999;
        font-weight: 600;
        min-width: 60px;
        flex-shrink: 0;
    }

    .question-text {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        flex: 1;
    }

    .question-text span {
        font-size: 24px;
        margin: 0 5px;
        color: #667eea;
    }

    .answer-input {
        width: 120px;
        padding: 12px;
        border: 2px solid #e0e0e0;
        border-radius: 6px;
        font-size: 18px;
        text-align: center;
        transition: all 0.3s;
        flex-shrink: 0;
    }

    .answer-input:focus {
        outline: none;
        border-color: #FF8C00;
        box-shadow: 0 0 0 3px rgba(255, 140, 0, 0.1);
    }

    .submit-section {
        text-align: center;
        padding: 30px 0;
        border-top: 2px solid #f0f0f0;
    }

    .btn-submit {
        padding: 15px 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
    }

    .loading {
        text-align: center;
        padding: 60px;
        color: #999;
    }
</style>

<div class="container">
    <div id="exerciseInfo"></div>
    
    <div class="progress-bar">
        <div class="progress-fill" id="progressFill" style="width: 0%"></div>
    </div>

    <div class="questions-container" id="questionsContainer">
        <div class="loading">Đang tải bài tập...</div>
    </div>

    <div class="submit-section">
        <button class="btn-submit" onclick="submitQuiz()">📝 Nộp Bài</button>
    </div>
</div>

<script>
    var exerciseId = <?= $exerciseId ?>;
    var token = localStorage.getItem('token');
    var submissionId = null;
    var questions = [];
    var startTime = Date.now();
    var timerInterval;

    // Timer
    function startTimer() {
        timerInterval = setInterval(() => {
            const elapsed = Math.floor((Date.now() - startTime) / 1000);
            const minutes = Math.floor(elapsed / 60);
            const seconds = elapsed % 60;
            document.getElementById('timer').textContent = 
                `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }, 1000);
    }

    async function loadExercise() {
        try {
            const response = await fetch(`<?= url('api/exercise_detail.php') ?>?id=${exerciseId}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (!response.ok) throw new Error('Failed to load exercise');

            const data = await response.json();
            questions = data.questions;

            // Display exercise info
            document.getElementById('exerciseInfo').innerHTML = `
                <h2 class="exercise-title">${data.exercise.name}</h2>
                <div class="exercise-info">📊 ${questions.length} câu hỏi</div>
            `;

            // Render questions
            renderQuestions();

            // Create submission
            await createSubmission();

            // Start timer
            startTimer();

        } catch (error) {
            console.error('Error:', error);
            alert('Lỗi tải bài tập!');
            window.location.href = '<?= url('?act=dashboard') ?>';
        }
    }

    function renderQuestions() {
        const container = document.getElementById('questionsContainer');
        container.innerHTML = questions.map((q, idx) => `
            <div class="question-box" id="box-${q.id}">
                <div class="question-number">Câu ${idx + 1}:</div>
                <div class="question-text">
                    <span>${q.num1}</span>
                    <span>${q.operation}</span>
                    <span>${q.num2}</span>
                    <span>=</span>
                    <input type="number" 
                           class="answer-input" 
                           id="input-${q.id}"
                           data-question-id="${q.id}"
                           placeholder="?"
                           onfocus="highlightBox(${q.id}); this.select();"
                           onblur="unhighlightBox(${q.id})"
                           oninput="handleInput(${q.id})"
                           onkeypress="handleEnter(event, ${idx})"
                           onkeydown="handleArrowKeys(event, ${idx})"
                           style="display: inline-block; margin-left: 10px;">
                </div>
            </div>
        `).join('');

        // Focus first input
        setTimeout(() => {
            document.querySelector('.answer-input')?.focus();
        }, 100);
    }

    function highlightBox(questionId) {
        document.getElementById(`box-${questionId}`).classList.add('focused');
    }

    function unhighlightBox(questionId) {
        document.getElementById(`box-${questionId}`).classList.remove('focused');
    }

    function handleInput(questionId) {
        const input = document.getElementById(`input-${questionId}`);
        const box = document.getElementById(`box-${questionId}`);
        
        if (input.value) {
            box.classList.add('answered');
            saveAnswer(input);
            updateProgress();
        } else {
            box.classList.remove('answered');
        }
    }

    function handleEnter(event, currentIndex) {
        if (event.key === 'Enter') {
            const inputs = document.querySelectorAll('.answer-input');
            if (currentIndex < inputs.length - 1) {
                inputs[currentIndex + 1].focus();
            } else {
                submitQuiz();
            }
        }
    }

    function handleArrowKeys(event, currentIndex) {
        const inputs = document.querySelectorAll('.answer-input');
        
        if (event.key === 'ArrowUp') {
            event.preventDefault();
            if (currentIndex > 0) {
                inputs[currentIndex - 1].focus();
            }
        } else if (event.key === 'ArrowDown') {
            event.preventDefault();
            if (currentIndex < inputs.length - 1) {
                inputs[currentIndex + 1].focus();
            }
        }
    }

    async function createSubmission() {
        try {
            const response = await fetch('<?= url('api/submission_start.php') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({ exerciseId })
            });

            const data = await response.json();
            submissionId = data.submissionId;
        } catch (error) {
            console.error('Error creating submission:', error);
        }
    }

    async function saveAnswer(input) {
        const questionId = input.getAttribute('data-question-id');
        const answer = input.value.trim();
        
        if (!answer || !submissionId) return;
        
        try {
            const response = await fetch('<?= url('api/submission_answer.php') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    submission_id: submissionId,
                    question_id: questionId,
                    user_answer: answer
                })
            });
            
            // Save without showing correct/wrong to prevent cheating
            if (response.ok) {
                const data = await response.json();
                // Answer saved successfully
            }
        } catch (error) {
            console.error('Error saving answer:', error);
        }
    }

    function updateProgress() {
        const answered = document.querySelectorAll('.answer-input').length;
        const total = questions.length;
        const filled = Array.from(document.querySelectorAll('.answer-input')).filter(i => i.value).length;
        const progress = (filled / total) * 100;
        document.getElementById('progressFill').style.width = `${progress}%`;
    }

    async function submitQuiz() {
        if (!confirm('Bạn chắc chắn muốn nộp bài không?')) return;

        try {
            await fetch('<?= url('api/submission_finish.php') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${token}`
                },
                body: JSON.stringify({
                    submission_id: submissionId
                })
            });
            
            clearInterval(timerInterval);
            window.location.href = `<?= url() ?>?act=result&submissionId=${submissionId}`;
        } catch (error) {
            console.error('Error submitting:', error);
            window.location.href = `/result.php?submissionId=${submissionId}`;
        }
    }

    // Auto-load
    loadExercise();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
