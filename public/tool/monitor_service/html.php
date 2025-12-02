
<!DOCTYPE html>
<html>
<head>
    <title>Monitor Service Dashboard</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 10px;
            background: #f5f5f5;
        }
        .container { max-width: 1200px; margin: 0 auto; }
        .card {
            background: white;
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header { text-align: center; color: #333; }
        .status-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .status-card { text-align: center; padding: 15px; }
        .status-card h3 { margin: 0 0 10px 0; color: #666; }
        .status-card .number { font-size: 2em; font-weight: bold; color: #2196F3; }
        .monitor-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .monitor-table th, .monitor-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .monitor-table th { background: #f8f9fa; font-weight: 600; }
        .status-ok { color: #28a745; font-weight: bold; }
        .status-error { color: #dc3545; font-weight: bold; }
        .status-unknown { color: #6c757d; }
        .enabled { color: #28a745; }
        .disabled { color: #dc3545; }
        .refresh-btn, .shutdown-btn {
            padding: 10px 20px;
            margin: 5px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .refresh-btn { background: #2196F3; color: white; }
        .shutdown-btn { background: #dc3545; color: white; }
        .logs-container { max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 15px; border-radius: 4px; }
        .log-line { font-family: monospace; font-size: 12px; margin-bottom: 2px; }
        .loading { text-align: center; color: #666; }
        .header-actions { text-align: right; margin-bottom: 10px; }

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
        }

        .badge-online {
            background: #d4edda;
            color: #155724;
        }

        .badge-offline {
            background: #f8d7da;
            color: #721c24;
        }

        .badge-warning {
            background: #fff3cd;
            color: #856404;
        }

        .user-filter {
            margin-bottom: 20px;
        }

        .check-type-badge {
            font-size: 0.7rem;
            padding: 2px 6px;
            border-radius: 8px;
            background: #e9ecef;
            color: #495057;
        }

        .auto-refresh-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 200px;
            z-index: 1000;
            border-radius: 50px;
            padding: 15px 20px;
            font-size: 14px;
        }

        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.2s;
        }

        .stats-card:hover {
            transform: translateY(-2px);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
<div class="container">

    <div class="card">
        <div id="status-grid" class="status-grid">
            <div class="loading">Loading...</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <h3>🔍 Filters</h3>
        <div class="row user-filter">
            <div class="col-md-3">
                <select class="form-control" id="status-filter" onchange="filterMonitors()">
                    <option value="">All Status</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-control" id="user-filter" onchange="filterMonitors()">
                    <option value="">All Users</option>
                    @for($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}">User {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="search-filter" placeholder="Search monitors..." onkeyup="filterMonitors()">
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary btn-block" onclick="clearFilters()">
                    <i class="fas fa-times"></i> Clear
                </button>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>📋 Monitor Items</h2>
        <div id="monitors-table">
            <div class="loading">Loading...</div>
        </div>
    </div>

    <div class="card">
        <h2>📝 Recent Logs</h2>
        <div id="logs-container" class="logs-container">
            <div class="loading">Loading...</div>
        </div>
    </div>


</div>

<!-- Auto-refresh toggle (starts automatically) -->
<button class="btn btn-danger auto-refresh-btn" id="auto-refresh-btn" onclick="toggleAutoRefresh()">
    <i class="fas fa-pause"></i> Stop Auto
</button>

<script>
    let autoRefreshInterval = null;
    let autoRefreshEnabled = false;
    let currentPage = 0;
    let currentLimit = 50;
    let currentFilters = {
        status: '',
        user_id: '',
        search: ''
    };

    // API Configuration - Direct PHP API
    const API_CONFIG = {
        BASE_URL: 'api.php', // Parameter-based API
        ENDPOINTS: {
            STATUS: '?cmd=status',
            MONITORS: '?cmd=monitors',
            LOGS: '?cmd=logs',
            STATISTICS: '?cmd=statistics'
        }
    };

    // Initialize page
    document.addEventListener('DOMContentLoaded', function() {
        loadStatistics();
        loadMonitors();
        loadLogs();

        // Start auto-refresh immediately
        startAutoRefresh();

        console.log('Monitor Dashboard initialized with auto-refresh enabled');
    });

    // Start auto-refresh function
    function startAutoRefresh() {
        const btn = document.getElementById('auto-refresh-btn');

        autoRefreshInterval = setInterval(() => {
            refreshData();
        }, 10000); // Refresh every 10 seconds

        autoRefreshEnabled = true;
        btn.innerHTML = '<i class="fas fa-pause"></i> Stop Auto';
        btn.className = 'btn btn-danger auto-refresh-btn';
    }

    async function fetchData(endpoint) {
        const response = await fetch(`${API_CONFIG.BASE_URL}${endpoint}`);
        return await response.json();
    }

    async function refreshData() {
        try {
            // Load status
            const status = await fetchData('?cmd=statistics');
            if (status.status === 'success') {
                const data = status.data;
                document.getElementById('status-grid').innerHTML = `
                    <div class="status-card">
                        <h3>Total Monitors</h3>
                        <div class="number">${data.total_monitors || 0}</div>
                    </div>
                    <div class="status-card">
                        <h3>Online</h3>
                        <div class="number" style="color: #28a745;">${data.online_count || 0}</div>
                    </div>
                    <div class="status-card">
                        <h3>Offline</h3>
                        <div class="number" style="color: #dc3545;">${data.offline_count || 0}</div>
                    </div>
                    <div class="status-card">
                        <h3>Warning</h3>
                        <div class="number" style="color: #ffc107;">${data.warning_count || 0}</div>
                    </div>
                `;

                // Update service status
                // document.getElementById('api-status').textContent = 'Connected';
                // document.getElementById('api-status').className = 'badge badge-success';
                // document.getElementById('active-monitors').textContent = data.total_monitors || 0;
                // document.getElementById('last-updated').textContent = data.last_updated || '-';
            }

            // Load monitors
            const monitors = await fetchData('?cmd=monitors&limit=20');
            if (monitors.status === 'success') {
                renderMonitorsTable(monitors.data.monitors);
            }

            // Load logs
            const logs = await fetchData('?cmd=logs&limit=10');
            if (logs.status === 'success') {
                renderLogs(logs.data.logs);
            }

            console.log('Data refreshed successfully');
        } catch (error) {
            console.error('Error refreshing data:', error);
            // document.getElementById('api-status').textContent = 'Error';
            // document.getElementById('api-status').className = 'badge badge-danger';
        }
    }

    // Load statistics
    async function loadStatistics() {
        try {
            const result = await fetchData('?cmd=statistics');
            if (result.status === 'success') {
                const data = result.data;
                document.getElementById('status-grid').innerHTML = `
                    <div class="status-card">
                        <h3>Total Monitors</h3>
                        <div class="number">${data.total_monitors || 0}</div>
                    </div>
                    <div class="status-card">
                        <h3>Online</h3>
                        <div class="number" style="color: #28a745;">${data.online_count || 0}</div>
                    </div>
                    <div class="status-card">
                        <h3>Offline</h3>
                        <div class="number" style="color: #dc3545;">${data.offline_count || 0}</div>
                    </div>
                    <div class="status-card">
                        <h3>Warning</h3>
                        <div class="number" style="color: #ffc107;">${data.warning_count || 0}</div>
                    </div>
                `;

                // Update service status
                // document.getElementById('api-status').textContent = 'Connected';
                // document.getElementById('api-status').className = 'badge badge-success';
                // document.getElementById('active-monitors').textContent = data.total_monitors || 0;
                // document.getElementById('last-updated').textContent = data.last_updated || '-';
            }
        } catch (error) {
            console.error('Error loading statistics:', error);
            // document.getElementById('api-status').textContent = 'Error';
            // document.getElementById('api-status').className = 'badge badge-danger';
        }
    }

    // Load monitors
    async function loadMonitors(page = 0) {
        try {
            const params = new URLSearchParams({
                limit: 20,
                offset: page * 20,
                ...currentFilters
            });

            Object.keys(currentFilters).forEach(key => {
                if (!currentFilters[key]) {
                    params.delete(key);
                }
            });

            const result = await fetchData('?cmd=monitors&' + params);
            if (result.status === 'success') {
                renderMonitorsTable(result.data.monitors);
            }
        } catch (error) {
            console.error('Error loading monitors:', error);
            document.getElementById('monitors-table').innerHTML = `
                <div class="text-center text-danger py-4">
                    <i class="fas fa-exclamation-triangle"></i> Error loading monitors: ${error.message}
                </div>
            `;
        }
    }

    // Render monitors table
    function renderMonitorsTable(monitors) {
        const container = document.getElementById('monitors-table');

        if (!monitors || monitors.length === 0) {
            container.innerHTML = `
                <div class="text-center text-muted py-4">
                    No monitors found
                </div>
            `;
            return;
        }

        let tableHTML = `
            <table class="monitor-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Enabled</th>
                        <th>Success</th>
                        <th>Failed</th>
                        <th>Last Check</th>
                    </tr>
                </thead>
                <tbody>
        `;

        monitors.forEach(monitor => {
            const statusClass = monitor.last_check_status === 'online' ? 'status-ok' :
                monitor.last_check_status === 'offline' ? 'status-error' : 'status-unknown';
            const statusText = monitor.last_check_status === 'online' ? '✅ Online' :
                monitor.last_check_status === 'offline' ? '❌ Offline' : '⚪ Unknown';

            const enabledClass = monitor.enable ? 'enabled' : 'disabled';
            const enabledText = monitor.enable ? '✅ Yes' : '❌ No';

            tableHTML += `
                <tr>
                    <td>${monitor.id}</td>
                    <td><strong>${monitor.name || 'Unnamed Service'}</strong></td>
                    <td><span class="check-type-badge">${monitor.type || 'unknown'}</span></td>
                    <td class="${statusClass}">${statusText}</td>
                    <td class="${enabledClass}">${enabledText}</td>
                    <td style="color: #28a745; font-weight: bold;">${monitor.count_online || 0}</td>
                    <td style="color: #dc3545; font-weight: bold;">${monitor.count_offline || 0}</td>
                    <td>${monitor.last_check_time || 'Never'}</td>
                 </tr>
            `;
        });

        tableHTML += `</tbody></table>`;
        container.innerHTML = tableHTML;
    }

    // Load logs
    async function loadLogs() {
        try {
            const result = await fetchData('?cmd=logs&limit=15');
            if (result.status === 'success') {
                renderLogs(result.data.logs);
            }
        } catch (error) {
            console.error('Error loading logs:', error);
            document.getElementById('logs-container').innerHTML = `
                <div class="text-center text-danger py-3">
                    <i class="fas fa-exclamation-triangle"></i> Error loading logs: ${error.message}
                </div>
            `;
        }
    }

    // Render logs
    function renderLogs(logs) {
        const container = document.getElementById('logs-container');

        if (!logs || logs.length === 0) {
            container.innerHTML = `
                <div class="text-muted text-center py-3">
                    No logs available
                </div>
            `;
            return;
        }

        container.innerHTML = logs.map(log => `
            <div class="log-line">[${log.timestamp || 'N/A'}] ${log.message || 'Empty log entry'}</div>
        `).join('');
    }

    // Filter monitors
    function filterMonitors() {
        currentFilters.status = document.getElementById('status-filter').value;
        currentFilters.user_id = document.getElementById('user-filter').value;
        currentFilters.search = document.getElementById('search-filter').value;
        loadMonitors(0);
    }

    // Clear filters
    function clearFilters() {
        document.getElementById('status-filter').value = '';
        document.getElementById('user-filter').value = '';
        document.getElementById('search-filter').value = '';
        currentFilters = { status: '', user_id: '', search: '' };
        loadMonitors(0);
    }

    // Toggle auto-refresh
    function toggleAutoRefresh() {
        const btn = document.getElementById('auto-refresh-btn');

        if (autoRefreshEnabled) {
            clearInterval(autoRefreshInterval);
            autoRefreshInterval = null;
            autoRefreshEnabled = false;
            btn.innerHTML = '<i class="fas fa-play"></i> Start Auto';
            btn.className = 'btn btn-success auto-refresh-btn';
        } else {
            autoRefreshInterval = setInterval(() => {
                refreshData();
            }, 10000); // 10 seconds

            autoRefreshEnabled = true;
            btn.innerHTML = '<i class="fas fa-pause"></i> Stop Auto';
            btn.className = 'btn btn-danger auto-refresh-btn';
        }
    }

    // Remove the old auto-refresh timer since we start it in DOMContentLoaded
    // setInterval(refreshData, 30000);

    // Initial load handled in DOMContentLoaded
    // refreshData();
</script>
</body>
</html>
