// State
let currentProblems = [];
let currentLevel = 10;
let currentOperation = 'mixed';
let currentCount = 5;
let totalCorrect = 0;
let totalWrong = 0;

// DOM Elements
const problemsList = document.getElementById('problemsList');
const newProblemBtn = document.getElementById('newProblemBtn');
const submitAllBtn = document.getElementById('submitAllBtn');
const resultSummary = document.getElementById('resultSummary');
const correctCountEl = document.getElementById('correctCount');
const wrongCountEl = document.getElementById('wrongCount');

// Level buttons
const levelButtons = document.querySelectorAll('.level-btn');
levelButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        levelButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentLevel = parseInt(btn.dataset.level);
        loadProblems();
    });
});

// Operation buttons
const opButtons = document.querySelectorAll('.op-btn');
opButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        opButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentOperation = btn.dataset.op;
        loadProblems();
    });
});

// Count buttons
const countButtons = document.querySelectorAll('.count-btn');
countButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        countButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentCount = parseInt(btn.dataset.count);
        loadProblems();
    });
});

// Load multiple problems from API
async function loadProblems() {
    try {
        const response = await fetch(`http://localhost:3000/api/problems?max=${currentLevel}&operation=${currentOperation}&count=${currentCount}`);
        const data = await response.json();
        
        currentProblems = data.problems;
        renderProblems();
        resultSummary.style.display = 'none';
        
    } catch (error) {
        console.error('Error loading problems:', error);
        problemsList.innerHTML = '<div style="color: red; text-align: center; padding: 20px;">❌ Lỗi kết nối API. Vui lòng khởi động server!</div>';
    }
}

// Render all problems
function renderProblems() {
    problemsList.innerHTML = '';
    
    currentProblems.forEach((problem, index) => {
        const problemItem = document.createElement('div');
        problemItem.className = 'problem-item';
        problemItem.innerHTML = `
            <div class="problem-number">${problem.id}</div>
            <div class="problem-question">
                <span class="num">${problem.num1}</span>
                <span class="op">${problem.operation}</span>
                <span class="num">${problem.num2}</span>
                <span>=</span>
            </div>
            <input type="number" 
                   class="problem-input" 
                   data-id="${problem.id}"
                   placeholder="?"
                   ${index === 0 ? 'autofocus' : ''}>
            <div class="problem-result" data-result="${problem.id}"></div>
        `;
        
        problemsList.appendChild(problemItem);
        
        // Enter key to move to next input
        const input = problemItem.querySelector('.problem-input');
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                const allInputs = document.querySelectorAll('.problem-input');
                const currentIndex = Array.from(allInputs).indexOf(input);
                if (currentIndex < allInputs.length - 1) {
                    allInputs[currentIndex + 1].focus();
                } else {
                    // Last input, check all
                    checkAllAnswers();
                }
            }
        });
    });
}

// Check all answers
async function checkAllAnswers() {
    const inputs = document.querySelectorAll('.problem-input');
    const answers = [];
    
    // Collect all answers
    currentProblems.forEach((problem, index) => {
        const input = inputs[index];
        answers.push({
            id: problem.id,
            num1: problem.num1,
            num2: problem.num2,
            operation: problem.operation,
            userAnswer: input.value
        });
    });
    
    try {
        const response = await fetch('http://localhost:3000/api/check-batch', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ answers })
        });
        
        const result = await response.json();
        displayResults(result);
        
    } catch (error) {
        console.error('Error checking answers:', error);
        alert('❌ Lỗi kết nối API');
    }
}

// Display results
function displayResults(result) {
    // Update each problem item
    result.results.forEach((item) => {
        const problemItem = document.querySelector(`input[data-id="${item.id}"]`).closest('.problem-item');
        const resultDiv = problemItem.querySelector('.problem-result');
        
        if (item.correct) {
            problemItem.classList.add('correct');
            problemItem.classList.remove('wrong');
            resultDiv.innerHTML = '✅';
            resultDiv.className = 'problem-result correct';
        } else {
            problemItem.classList.add('wrong');
            problemItem.classList.remove('correct');
            resultDiv.innerHTML = `❌<span class="correct-answer">(${item.correctAnswer})</span>`;
            resultDiv.className = 'problem-result wrong';
        }
        
        // Disable input
        problemItem.querySelector('.problem-input').disabled = true;
    });
    
    // Update global score
    totalCorrect += result.summary.correct;
    totalWrong += result.summary.wrong;
    correctCountEl.textContent = totalCorrect;
    wrongCountEl.textContent = totalWrong;
    
    // Show summary
    resultSummary.style.display = 'block';
    resultSummary.innerHTML = `
        <h2>${getResultEmoji(result.summary.percentage)} ${getResultMessage(result.summary.percentage)}</h2>
        <div class="result-stats">
            <div class="result-stat">
                <span class="result-stat-value">${result.summary.correct}/${result.summary.total}</span>
                <span class="result-stat-label">Đúng</span>
            </div>
            <div class="result-stat">
                <span class="result-stat-value">${result.summary.percentage}%</span>
                <span class="result-stat-label">Điểm số</span>
            </div>
        </div>
    `;
    
    // Scroll to summary
    resultSummary.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

// Get result emoji based on percentage
function getResultEmoji(percentage) {
    if (percentage === 100) return '🏆';
    if (percentage >= 80) return '🌟';
    if (percentage >= 60) return '😊';
    if (percentage >= 40) return '💪';
    return '📚';
}

// Get result message based on percentage
function getResultMessage(percentage) {
    if (percentage === 100) return 'Hoàn hảo! Xuất sắc!';
    if (percentage >= 80) return 'Giỏi lắm!';
    if (percentage >= 60) return 'Khá tốt!';
    if (percentage >= 40) return 'Cố gắng lên!';
    return 'Cần luyện tập thêm nhé!';
}

// Event Listeners
newProblemBtn.addEventListener('click', loadProblems);
submitAllBtn.addEventListener('click', checkAllAnswers);

// Load first set of problems on page load
loadProblems();
