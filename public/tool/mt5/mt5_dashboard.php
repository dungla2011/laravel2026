<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Monitoring Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            color: #333;
            padding: 20px;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .header h1 {
            color: #1e3c72;
            font-size: 28px;
        }

        .summary-bar {
            display: flex;
            gap: 30px;
            padding: 15px 20px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
        }

        .summary-item {
            color: white;
        }

        .summary-label {
            font-size: 12px;
            opacity: 0.9;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 24px;
            font-weight: bold;
        }

        .summary-value.positive {
            color: #4ade80;
        }

        .summary-value.negative {
            color: #f87171;
        }

        .status-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .status-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        .status-dot.online {
            background: #4caf50;
        }

        .status-dot.offline {
            background: #f44336;
            animation: none;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .accounts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 20px;
        }

        .account-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .account-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        .account-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }

        .account-number {
            font-size: 24px;
            font-weight: bold;
            color: #1e3c72;
        }

        .account-status {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 15px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
        }

        .account-status.running {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .account-status.paused {
            background: #fff3e0;
            color: #e65100;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }

        .info-item {
            padding: 8px;
            background: #f5f5f5;
            border-radius: 5px;
        }

        .info-label {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 15px;
            font-weight: bold;
            color: #333;
        }

        .profit-positive {
            color: #4caf50;
        }

        .profit-negative {
            color: #f44336;
        }

        .settings-row {
            display: flex;
            justify-content: space-between;
            padding: 8px;
            background: #f9f9f9;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .settings-label {
            font-size: 13px;
            color: #666;
        }

        .settings-value {
            font-weight: bold;
            color: #333;
        }

        .orders-section {
            margin-top: 15px;
        }

        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #1e3c72;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .order-count {
            background: #1e3c72;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 12px;
        }

        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 13px;
        }

        .orders-table th {
            background: #e3f2fd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
            color: #1976d2;
        }

        .orders-table td {
            padding: 8px;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-type-buy {
            color: #2196f3;
            font-weight: bold;
        }

        .order-type-sell {
            color: #ff5722;
            font-weight: bold;
        }

        .last-update {
            text-align: center;
            color: #666;
            font-size: 12px;
            margin-top: 10px;
            padding: 8px;
            background: #f5f5f5;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .last-update.flash {
            animation: flashRed 0.6s;
        }

        @keyframes flashRed {
            0%, 100% { background-color: #f5f5f5; }
            50% { background-color: #ffcdd2; }
        }

        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }

        .loading {
            text-align: center;
            color: white;
            font-size: 18px;
            padding: 40px;
        }

        .no-data {
            text-align: center;
            color: #666;
            padding: 40px;
            background: white;
            border-radius: 10px;
        }

        @media (max-width: 768px) {
            .accounts-grid {
                grid-template-columns: 1fr;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="header-top">
            <h1>🤖 Monitoring Dashboard</h1>
            <div class="status-indicator">
                <div class="status-dot" id="statusDot"></div>
                <span id="statusText">Connecting...</span>
            </div>
        </div>
        <div class="summary-bar" id="summaryBar">
            <div class="summary-item">
                <div class="summary-label">Real Accounts</div>
                <div class="summary-value" id="realCount">0</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Total Accounts</div>
                <div class="summary-value" id="totalCount">0</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Today Real P/L</div>
                <div class="summary-value" id="totalProfit">$0.00</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Open P/L</div>
                <div class="summary-value" id="totalOpenProfit">$0.00</div>
            </div>
        </div>
    </div>

    <div id="content">
        <div class="loading">⏳ Loading data...</div>
    </div>
</div>

<script>
    const API_URL = 'mt5_api.php';
    const UPDATE_INTERVAL = 10000;
    let updateTimer;

    async function fetchData() {
        try {
            const response = await fetch(API_URL);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            if (data.status === 'success') {
                updateUI(data);
                updateStatus(true);
            } else {
                throw new Error(data.message || 'Unknown error');
            }
        } catch (error) {
            console.error('Fetch error:', error);
            showError(error.message);
            updateStatus(false);
        }
    }

    function updateUI(data) {
        const content = document.getElementById('content');
        if (!data.accounts || data.accounts.length === 0) {
            content.innerHTML = '<div class="no-data">📭 No accounts found. Waiting for data from MT5 bots...</div>';
            return;
        }

        // Update summary bar
        document.getElementById('realCount').textContent = data.realAccountsCount || 0;
        document.getElementById('totalCount').textContent = data.accountCount || 0;
        
        const totalProfit = data.totalRealProfit || 0;
        const profitElement = document.getElementById('totalProfit');
        profitElement.textContent = (totalProfit >= 0 ? '+' : '') + '$' + formatNumber(totalProfit);
        profitElement.className = 'summary-value ' + (totalProfit >= 0 ? 'positive' : 'negative');
        
        const totalOpenProfit = data.totalOpenProfit || 0;
        const openProfitElement = document.getElementById('totalOpenProfit');
        openProfitElement.textContent = (totalOpenProfit >= 0 ? '+' : '') + '$' + formatNumber(totalOpenProfit);
        openProfitElement.className = 'summary-value ' + (totalOpenProfit >= 0 ? 'positive' : 'negative');

        let html = '<div class="accounts-grid">';
        data.accounts.forEach(account => { html += createAccountCard(account); });
        html += '</div>';
        content.innerHTML = html;
        data.accounts.forEach(account => {
            const updateElement = document.getElementById(`lastUpdate-${account.account}`);
            if (updateElement) {
                updateElement.classList.add('flash');
                setTimeout(() => updateElement.classList.remove('flash'), 600);
            }
        });
    }

    function createAccountCard(account) {
        const status = account.status || {};
        const settings = status.settings || {};
        const profit = status.profit || {};
        const price = status.price || {};
        const accountType = status.accountType || 'Unknown';
        const isRunning = settings.status === 'RUNNING';
        const statusClass = isRunning ? 'running' : 'paused';
        const statusText = isRunning ? '⏹ Stop' : '⏸ PAUSED';
        const todayProfit = profit.todayProfit || 0;
        const openProfit = profit.open || 0;
        const profitClass = todayProfit >= 0 ? 'profit-positive' : 'profit-negative';
        const openProfitClass = openProfit >= 0 ? 'profit-positive' : 'profit-negative';
        const profitSign = todayProfit >= 0 ? '+' : '';
        const openProfitSign = openProfit >= 0 ? '+' : '';

        // Check if offline and stale (>3600s)
        const secondsSinceUpdate = account.secondsSinceUpdate || 0;
        const isOfflineStale = !account.isOnline && secondsSinceUpdate > 3600;
        const cardStyle = isOfflineStale ? 'border: 3px solid #f44336; background: #ffebee;' : '';
        const headerStyle = isOfflineStale ? 'color: #f44336;' : '';

        // Account type badge
        const typeColor = accountType === 'Real' ? '#2e7d32' : '#666';
        const typeBadge = `<span style="background: ${typeColor}; color: white; padding: 3px 8px; border-radius: 4px; font-size: 11px; margin-left: 10px;">${accountType}</span>`;

        let html = `<div class="account-card" style="${cardStyle}">
                <div class="account-header" style="${headerStyle}">
                    <div class="account-number">Account #${account.account}${typeBadge}</div>
                    <div class="account-status ${statusClass}">${statusText}</div>
                </div>
                <div class="info-grid">
                    <div class="info-item"><div class="info-label">Today P/L</div><div class="info-value ${profitClass}">${profitSign}$${formatNumber(todayProfit)}</div></div>
                    <div class="info-item"><div class="info-label">Balance</div><div class="info-value">$${formatNumber(status.balance || 0)}</div></div>
                    <div class="info-item"><div class="info-label">Equity (Open P/L)</div><div class="info-value">$${formatNumber(status.equity || 0)} <span class="${openProfitClass}">(${openProfitSign}$${formatNumber(openProfit)})</span></div></div>
                    <div class="info-item"><div class="info-label">Open Orders</div><div class="info-value">${account.openOrders ? account.openOrders.count : 0} / ${settings.MaxB || 0}</div></div>
                </div>
                <div class="settings-row"><span class="settings-label">Settings:</span><span class="settings-value">${status.config_input || 'N/A'} | Price: ${formatNumber(price.bid || 0, 2)}/${formatNumber(price.ask || 0, 2)}</span></div>`;

        if (account.openOrders && account.openOrders.orders && account.openOrders.orders.length > 0) {

            // Sort by openTime descending (newest first) and limit to 5
            const sortedOrders = [...account.openOrders.orders].sort((a, b) => {
                const timeA = a.openTime || '';
                const timeB = b.openTime || '';
                return timeB.localeCompare(timeA);
            }).slice(0, 5);

            const displayCount = sortedOrders.length;
            const totalCount = account.openOrders.count;
            const countText = totalCount > 5 ? `${displayCount}/${totalCount}` : displayCount;

            html += `<div class="orders-section"><div class="section-title">Open Orders<span class="order-count">${countText}</span></div><table class="orders-table"><thead><tr><th>Type</th><th>Price</th><th>Lot</th><th>Profit</th><th>Open Time</th></tr></thead><tbody>`;
            sortedOrders.forEach(o => {
                const tc = o.type === 'BUY' ? 'order-type-buy' : 'order-type-sell';
                const pc = (o.currentProfit || 0) >= 0 ? 'profit-positive' : 'profit-negative';
                html += `<tr><td class="${tc}">${o.type}</td><td>${formatNumber(o.price || 0, 2)}</td><td>${formatNumber(o.volume || 0, 2)}</td><td class="${pc}">$${formatNumber(o.currentProfit || 0)}</td><td style="font-size: 11px; color: #888;">${o.openTime || 'N/A'}</td></tr>`;
            });
            html += `</tbody></table></div>`;
        }

        if (account.recentClosedOrders && account.recentClosedOrders.orders && account.recentClosedOrders.orders.length > 0) {
            html += `<div class="orders-section"><div class="section-title">Recent Closed Orders<span class="order-count">${account.recentClosedOrders.orders.length}</span></div><table class="orders-table"><thead><tr><th>Type</th><th>Price</th><th>Lot</th><th>Profit</th><th>Close Time</th></tr></thead><tbody>`;
            account.recentClosedOrders.orders.forEach(o => {
                const tc = o.type === 'BUY' ? 'order-type-buy' : 'order-type-sell';
                const pc = (o.profit || 0) >= 0 ? 'profit-positive' : 'profit-negative';
                html += `<tr><td class="${tc}">${o.type}</td><td>${formatNumber(o.price || 0, 2)}</td><td>${formatNumber(o.volume || 0, 2)}</td><td class="${pc}">$${formatNumber(o.profit || 0)}</td><td style="font-size: 11px; color: #888;">${o.closeTime || 'N/A'}</td></tr>`;
            });
            html += `</tbody></table></div>`;
        }

        const onlineStatus = account.isOnline ? '🟢 Online' : '🔴 Offline';
        html += `<div class="last-update" id="lastUpdate-${account.account}">${onlineStatus} | Last update: ${account.lastUpdate || 'Never'} (${account.secondsSinceUpdate || 0}s ago)</div></div>`;
        return html;
    }

    function updateStatus(isOnline) {
        document.getElementById('statusDot').className = 'status-dot ' + (isOnline ? 'online' : 'offline');
        document.getElementById('statusText').textContent = isOnline ? 'Connected' : 'Connection Error';
    }

    function showError(message) {
        document.getElementById('content').innerHTML = `<div class="error-message"><h2>⚠️ Error</h2><p>${message}</p><p style="margin-top: 10px; font-size: 14px;">Retrying in ${UPDATE_INTERVAL/1000} seconds...</p></div>`;
    }

    function formatNumber(num, decimals = 2) {
        return parseFloat(num).toFixed(decimals).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    function startAutoUpdate() { fetchData(); updateTimer = setInterval(fetchData, UPDATE_INTERVAL); }
    function stopAutoUpdate() { if (updateTimer) clearInterval(updateTimer); }

    document.addEventListener('visibilitychange', () => document.hidden ? stopAutoUpdate() : startAutoUpdate());
    window.addEventListener('load', startAutoUpdate);
    window.addEventListener('beforeunload', stopAutoUpdate);
</script>
</body>
</html>
