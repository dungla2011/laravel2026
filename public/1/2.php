<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trò Tôm Cua Cá</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            padding: 30px;
            max-width: 800px;
            width: 100%;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .items-selection {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
        }

        .item-btn {
            padding: 20px;
            border: 3px solid #ddd;
            background: white;
            border-radius: 10px;
            cursor: pointer;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .item-btn:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }

        .item-btn.selected {
            background: #667eea;
            color: white;
            border-color: #667eea;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .item-emoji {
            font-size: 32px;
        }

        .selected-items {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .selected-items h2 {
            color: #333;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table td {
            border: 2px solid #ddd;
            padding: 15px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            background: white;
        }

        .items-table .empty-cell {
            color: #ccc;
            font-style: italic;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            justify-content: center;
        }

        button {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-roll {
            background: #667eea;
            color: white;
        }

        .btn-roll:hover {
            background: #5568d3;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-roll:disabled {
            background: #ccc;
            cursor: not-allowed;
        }

        .btn-clear {
            background: #ff6b6b;
            color: white;
        }

        .btn-clear:hover {
            background: #ee5a5a;
        }

        .btn-reset {
            background: #ffa502;
            color: white;
        }

        .btn-reset:hover {
            background: #ff8c00;
        }

        .statistics {
            background: #f0f4ff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
        }

        .statistics h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 18px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        .stat-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .stat-value {
            color: #333;
            font-size: 24px;
            font-weight: bold;
        }

        .probability {
            background: white;
            padding: 15px;
            border-radius: 8px;
            border: 2px solid #ffa502;
        }

        .probability-title {
            color: #333;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .probability-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
            align-items: center;
        }

        .probability-item:last-child {
            border-bottom: none;
        }

        .probability-name {
            font-weight: bold;
            color: #333;
        }

        .probability-percent {
            background: linear-gradient(90deg, #ffa502, #ff8c00);
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
        }

        .highlight-max {
            background: #fff3cd !important;
            font-weight: bold;
        }

        .roll-result {
            background: #e7f5ff;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #339af0;
            margin-top: 20px;
            display: none;
        }

        .roll-result.show {
            display: block;
            animation: slideIn 0.3s ease-in-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .roll-result-title {
            color: #1971c2;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .roll-result-items {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }

        .roll-item {
            background: white;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            border: 2px solid #339af0;
        }

        .roll-item-emoji {
            font-size: 28px;
            margin-bottom: 5px;
        }

        .roll-item-name {
            color: #333;
            font-weight: bold;
        }

        .total-rolls {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🎲 Trò Tôm Cua Cá 🎲</h1>

        <div class="items-selection">
            <button class="item-btn" data-item="tôm">
                <span class="item-emoji">🦐</span>
                Tôm
            </button>
            <button class="item-btn" data-item="cua">
                <span class="item-emoji">🦀</span>
                Cua
            </button>
            <button class="item-btn" data-item="cá">
                <span class="item-emoji">🐟</span>
                Cá
            </button>
            <button class="item-btn" data-item="bầu">
                <span class="item-emoji">🎃</span>
                Bầu
            </button>
            <button class="item-btn" data-item="gà">
                <span class="item-emoji">🐔</span>
                Gà
            </button>
            <button class="item-btn" data-item="bọ cạp">
                <span class="item-emoji">🦂</span>
                Bọ Cạp
            </button>
        </div>

        <div class="selected-items">
            <h2>📋 Lựa chọn của bạn (chọn 3 cái):</h2>
            <table class="items-table">
                <tr>
                    <td id="item-1" class="empty-cell">-</td>
                    <td id="item-2" class="empty-cell">-</td>
                    <td id="item-3" class="empty-cell">-</td>
                </tr>
            </table>
            <div class="total-rolls">
                Đã chọn: <strong id="selected-count">0</strong>/3
            </div>
        </div>

        <div class="action-buttons">
            <button class="btn-roll" id="btn-roll" disabled>🎯 Quay Số</button>
            <button class="btn-clear" id="btn-clear">🗑️ Xóa Chọn</button>
            <button class="btn-reset" id="btn-reset">🔄 Reset Thống Kê</button>
        </div>

        <div class="roll-result" id="roll-result">
            <div class="roll-result-title">🎰 Kết Quả Quay Số:</div>
            <div class="roll-result-items" id="roll-result-items"></div>
        </div>

        <div class="statistics">
            <h2>📊 Thống Kê & Xác Suất</h2>

            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-label">Tổng Lần Quay</div>
                    <div class="stat-value" id="total-rolls">0</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Lần Trúng</div>
                    <div class="stat-value" id="wins">0</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Tỷ Lệ Thắng</div>
                    <div class="stat-value" id="win-rate">0%</div>
                </div>
                <div class="stat-item">
                    <div class="stat-label">Lần Thua</div>
                    <div class="stat-value" id="losses">0</div>
                </div>
            </div>

            <div class="probability">
                <div class="probability-title">🎯 Xác Suất Xuất Hiện (Cao Nhất → Thấp Nhất):</div>
                <div id="probability-list"></div>
            </div>
        </div>
    </div>

    <script>
        // Data structure
        const items = ['tôm', 'cua', 'cá', 'bầu', 'gà', 'bọ cạp'];
        const emojis = {
            'tôm': '🦐',
            'cua': '🦀',
            'cá': '🐟',
            'bầu': '🎃',
            'gà': '🐔',
            'bọ cạp': '🦂'
        };

        let selected = [];
        let stats = {
            totalRolls: 0,
            wins: 0,
            losses: 0,
            counts: {
                'tôm': 0,
                'cua': 0,
                'cá': 0,
                'bầu': 0,
                'gà': 0,
                'bọ cạp': 0
            }
        };

        // Load stats from localStorage
        function loadStats() {
            const saved = localStorage.getItem('tcStats');
            if (saved) {
                stats = JSON.parse(saved);
            }
        }

        // Save stats to localStorage
        function saveStats() {
            localStorage.setItem('tcStats', JSON.stringify(stats));
        }

        // Initialize on page load
        loadStats();
        updateUI();

        // Item selection
        document.querySelectorAll('.item-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const item = this.dataset.item;
                
                if (selected.includes(item)) {
                    selected = selected.filter(i => i !== item);
                    this.classList.remove('selected');
                } else {
                    if (selected.length < 3) {
                        selected.push(item);
                        this.classList.add('selected');
                    }
                }
                
                updateSelectedDisplay();
                updateRollButton();
            });
        });

        // Update selected items display
        function updateSelectedDisplay() {
            for (let i = 0; i < 3; i++) {
                const cell = document.getElementById(`item-${i + 1}`);
                if (i < selected.length) {
                    cell.textContent = selected[i];
                    cell.classList.remove('empty-cell');
                } else {
                    cell.textContent = '-';
                    cell.classList.add('empty-cell');
                }
            }
            document.getElementById('selected-count').textContent = selected.length;
        }

        // Update roll button state
        function updateRollButton() {
            document.getElementById('btn-roll').disabled = selected.length !== 3;
        }

        // Roll
        document.getElementById('btn-roll').addEventListener('click', function() {
            const rolled = [];
            for (let i = 0; i < 3; i++) {
                rolled.push(items[Math.floor(Math.random() * items.length)]);
            }

            // Update stats
            stats.totalRolls++;
            rolled.forEach(item => {
                stats.counts[item]++;
            });

            // Check if won
            const won = selected.every(item => rolled.includes(item));
            if (won) {
                stats.wins++;
            } else {
                stats.losses++;
            }

            saveStats();
            displayRollResult(rolled, won);
            updateUI();
        });

        // Display roll result
        function displayRollResult(rolled, won) {
            const resultDiv = document.getElementById('roll-result');
            const resultItems = document.getElementById('roll-result-items');
            
            resultItems.innerHTML = rolled.map(item => `
                <div class="roll-item">
                    <div class="roll-item-emoji">${emojis[item]}</div>
                    <div class="roll-item-name">${item}</div>
                </div>
            `).join('');

            resultDiv.classList.add('show');
            setTimeout(() => {
                resultDiv.classList.remove('show');
            }, 5000);
        }

        // Clear selection
        document.getElementById('btn-clear').addEventListener('click', function() {
            selected = [];
            document.querySelectorAll('.item-btn').forEach(btn => btn.classList.remove('selected'));
            updateSelectedDisplay();
            updateRollButton();
        });

        // Reset stats
        document.getElementById('btn-reset').addEventListener('click', function() {
            if (confirm('Bạn chắc chắn muốn xóa tất cả thống kê?')) {
                stats = {
                    totalRolls: 0,
                    wins: 0,
                    losses: 0,
                    counts: {
                        'tôm': 0,
                        'cua': 0,
                        'cá': 0,
                        'bầu': 0,
                        'gà': 0,
                        'bọ cạp': 0
                    }
                };
                saveStats();
                updateUI();
            }
        });

        // Update all UI
        function updateUI() {
            // Update stats display
            document.getElementById('total-rolls').textContent = stats.totalRolls;
            document.getElementById('wins').textContent = stats.wins;
            document.getElementById('losses').textContent = stats.totalRolls - stats.wins;
            
            const winRate = stats.totalRolls > 0 
                ? Math.round((stats.wins / stats.totalRolls) * 100) 
                : 0;
            document.getElementById('win-rate').textContent = winRate + '%';

            // Update probability
            updateProbability();
        }

        // Update probability
        function updateProbability() {
            const sorted = items.map(item => ({
                name: item,
                count: stats.counts[item],
                emoji: emojis[item]
            })).sort((a, b) => a.count - b.count);

            const probList = document.getElementById('probability-list');
            
            let maxCount = Math.max(...sorted.map(s => s.count));
            if (maxCount === 0) maxCount = 1;

            probList.innerHTML = sorted.map((item, index) => {
                const percent = Math.round((item.count / maxCount) * 100);
                const isMax = index === 0 && item.count === sorted[0].count;
                return `
                    <div class="probability-item ${isMax ? 'highlight-max' : ''}">
                        <span class="probability-name">${item.emoji} ${item.name}</span>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <span style="color: #666; font-size: 14px;">${item.count} lần</span>
                            <span class="probability-percent">${percent}%</span>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Real-time update when window is focused
        window.addEventListener('focus', function() {
            updateUI();
        });
    </script>
</body>
</html>
