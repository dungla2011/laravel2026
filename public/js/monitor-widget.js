/**
 * Monitor Widget - Reusable monitoring graph component
 * Usage: 
 *   <div id="uptime-widget" data-monitor-id="1" data-period="24h"></div>
 *   <script src="/js/monitor-widget.js"></script>
 *   <script>
 *     new MonitorWidget('uptime-widget', {
 *       type: 'uptime',
 *       monitorId: 1,
 *       period: '24h',
 *       autoRefresh: 60000 // 60 seconds
 *     });
 *   </script>
 */

class MonitorWidget {
    constructor(elementId, options = {}) {
        this.elementId = elementId;
        this.element = document.getElementById(elementId);
        
        if (!this.element) {
            console.error(`Element #${elementId} not found`);
            return;
        }
        
        // Configuration
        this.config = {
            type: options.type || this.element.dataset.type || 'uptime',
            monitorId: options.monitorId || this.element.dataset.monitorId,
            metricType: options.metricType || this.element.dataset.metricType,
            period: options.period || this.element.dataset.period || '24h',
            height: options.height || this.element.dataset.height || '300px',
            autoRefresh: options.autoRefresh || parseInt(this.element.dataset.autoRefresh) || 0,
            showLegend: options.showLegend !== undefined ? options.showLegend : true,
            showStats: options.showStats !== undefined ? options.showStats : true,
            apiBase: options.apiBase || '/api/monitor-graph',
        };
        
        // State
        this.chart = null;
        this.refreshTimer = null;
        this.loading = false;
        
        // Initialize
        this.init();
    }
    
    async init() {
        this.createContainer();
        await this.loadData();
        
        if (this.config.autoRefresh > 0) {
            this.startAutoRefresh();
        }
    }
    
    createContainer() {
        this.element.classList.add('monitor-widget');
        this.element.innerHTML = `
            <div class="monitor-widget-header">
                <h3 class="monitor-widget-title">${this.getTitle()}</h3>
                <div class="monitor-widget-controls">
                    <select class="period-selector" onchange="this.widget.changePeriod(this.value)">
                        <option value="1h" ${this.config.period === '1h' ? 'selected' : ''}>Last Hour</option>
                        <option value="24h" ${this.config.period === '24h' ? 'selected' : ''}>Last 24 Hours</option>
                        <option value="7d" ${this.config.period === '7d' ? 'selected' : ''}>Last 7 Days</option>
                        <option value="30d" ${this.config.period === '30d' ? 'selected' : ''}>Last 30 Days</option>
                        <option value="90d" ${this.config.period === '90d' ? 'selected' : ''}>Last 90 Days</option>
                    </select>
                    <button class="refresh-btn" onclick="this.widget.refresh()">
                        <span class="refresh-icon">↻</span>
                    </button>
                </div>
            </div>
            ${this.config.showStats ? '<div class="monitor-widget-stats"></div>' : ''}
            <div class="monitor-widget-chart">
                <canvas id="${this.elementId}-canvas"></canvas>
            </div>
            <div class="monitor-widget-loading">
                <div class="spinner"></div>
                <p>Loading data...</p>
            </div>
            <div class="monitor-widget-error" style="display: none;">
                <p class="error-message"></p>
                <button onclick="this.widget.refresh()">Retry</button>
            </div>
        `;
        
        // Store reference for inline handlers
        this.element.querySelectorAll('.period-selector, .refresh-btn').forEach(el => {
            el.widget = this;
        });
        
        this.element.querySelector('.monitor-widget-error button').widget = this;
    }
    
    getTitle() {
        const titles = {
            'uptime': 'Uptime Status',
            'response-time': 'Response Time',
            'system-metrics': `System Metrics: ${this.config.metricType || 'N/A'}`
        };
        return titles[this.config.type] || 'Monitor Graph';
    }
    
    async loadData() {
        if (this.loading) return;
        
        this.loading = true;
        this.showLoading();
        this.hideError();
        
        try {
            const url = this.buildApiUrl();
            const response = await fetch(url);
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const data = await response.json();
            
            if (!data.success) {
                throw new Error(data.error || 'Unknown error');
            }
            
            this.renderChart(data);
            this.renderStats(data.stats);
            this.hideLoading();
            
        } catch (error) {
            console.error('Failed to load monitor data:', error);
            this.showError(error.message);
        } finally {
            this.loading = false;
        }
    }
    
    buildApiUrl() {
        let url = `${this.config.apiBase}/${this.config.type}?period=${this.config.period}`;
        
        if (this.config.monitorId) {
            url += `&monitor_id=${this.config.monitorId}`;
        }
        
        if (this.config.metricType) {
            url += `&metric_type=${this.config.metricType}`;
        }
        
        return url;
    }
    
    renderChart(data) {
        const canvas = this.element.querySelector(`#${this.elementId}-canvas`);
        const ctx = canvas.getContext('2d');
        
        // Destroy existing chart
        if (this.chart) {
            this.chart.destroy();
        }
        
        // Create new chart based on type
        const chartConfig = this.getChartConfig(data);
        this.chart = new Chart(ctx, chartConfig);
    }
    
    getChartConfig(data) {
        switch (this.config.type) {
            case 'uptime':
                return this.getUptimeChartConfig(data);
            case 'response-time':
                return this.getResponseTimeChartConfig(data);
            case 'system-metrics':
                return this.getSystemMetricsChartConfig(data);
            default:
                throw new Error(`Unknown chart type: ${this.config.type}`);
        }
    }
    
    getUptimeChartConfig(data) {
        const { labels, status, uptime_percentage } = data.chart_data;
        
        return {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Status (Up/Down)',
                        data: status,
                        backgroundColor: status.map(s => s === 1 ? 'rgba(75, 192, 192, 0.2)' : 'rgba(255, 99, 132, 0.2)'),
                        borderColor: status.map(s => s === 1 ? 'rgba(75, 192, 192, 1)' : 'rgba(255, 99, 132, 1)'),
                        borderWidth: 2,
                        fill: true,
                        stepped: true,
                        yAxisID: 'y-status',
                    },
                    {
                        label: 'Uptime %',
                        data: uptime_percentage,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        yAxisID: 'y-percentage',
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: this.config.showLegend,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) label += ': ';
                                
                                if (context.datasetIndex === 0) {
                                    label += context.parsed.y === 1 ? 'UP ✓' : 'DOWN ✗';
                                } else {
                                    label += context.parsed.y.toFixed(2) + '%';
                                }
                                
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    'y-status': {
                        type: 'linear',
                        position: 'left',
                        min: 0,
                        max: 1,
                        ticks: {
                            stepSize: 1,
                            callback: function(value) {
                                return value === 1 ? 'UP' : 'DOWN';
                            }
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                    'y-percentage': {
                        type: 'linear',
                        position: 'right',
                        min: 0,
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        };
    }
    
    getResponseTimeChartConfig(data) {
        const { labels, avg, min, max } = data.chart_data;
        
        return {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Average Response Time',
                        data: avg,
                        borderColor: 'rgba(54, 162, 235, 1)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        fill: true,
                    },
                    {
                        label: 'Min',
                        data: min,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        borderWidth: 1,
                        borderDash: [5, 5],
                        fill: false,
                    },
                    {
                        label: 'Max',
                        data: max,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 1,
                        borderDash: [5, 5],
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: this.config.showLegend,
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2) + ' ms';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' ms';
                            }
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        };
    }
    
    getSystemMetricsChartConfig(data) {
        const { labels, avg, min, max } = data.chart_data;
        
        return {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Average',
                        data: avg,
                        borderColor: 'rgba(153, 102, 255, 1)',
                        backgroundColor: 'rgba(153, 102, 255, 0.1)',
                        borderWidth: 2,
                        fill: true,
                    },
                    {
                        label: 'Min',
                        data: min,
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1,
                        borderDash: [5, 5],
                        fill: false,
                    },
                    {
                        label: 'Max',
                        data: max,
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 1,
                        borderDash: [5, 5],
                        fill: false,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        display: this.config.showLegend,
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                    },
                    x: {
                        ticks: {
                            maxRotation: 45,
                            minRotation: 0
                        }
                    }
                }
            }
        };
    }
    
    renderStats(stats) {
        if (!this.config.showStats) return;
        
        const statsContainer = this.element.querySelector('.monitor-widget-stats');
        if (!statsContainer || !stats) return;
        
        let html = '<div class="stats-grid">';
        
        for (const [key, value] of Object.entries(stats)) {
            const label = this.formatStatLabel(key);
            const formattedValue = this.formatStatValue(key, value);
            
            html += `
                <div class="stat-item">
                    <div class="stat-label">${label}</div>
                    <div class="stat-value">${formattedValue}</div>
                </div>
            `;
        }
        
        html += '</div>';
        statsContainer.innerHTML = html;
    }
    
    formatStatLabel(key) {
        return key
            .replace(/_/g, ' ')
            .replace(/\b\w/g, l => l.toUpperCase());
    }
    
    formatStatValue(key, value) {
        if (key.includes('percentage')) {
            return value.toFixed(2) + '%';
        }
        if (key.includes('time')) {
            return value.toFixed(2) + ' ms';
        }
        if (typeof value === 'number') {
            return value.toLocaleString();
        }
        return value;
    }
    
    showLoading() {
        this.element.querySelector('.monitor-widget-loading').style.display = 'flex';
        this.element.querySelector('.monitor-widget-chart').style.opacity = '0.3';
    }
    
    hideLoading() {
        this.element.querySelector('.monitor-widget-loading').style.display = 'none';
        this.element.querySelector('.monitor-widget-chart').style.opacity = '1';
    }
    
    showError(message) {
        const errorDiv = this.element.querySelector('.monitor-widget-error');
        errorDiv.querySelector('.error-message').textContent = message;
        errorDiv.style.display = 'block';
        this.element.querySelector('.monitor-widget-chart').style.display = 'none';
    }
    
    hideError() {
        this.element.querySelector('.monitor-widget-error').style.display = 'none';
        this.element.querySelector('.monitor-widget-chart').style.display = 'block';
    }
    
    changePeriod(period) {
        this.config.period = period;
        this.refresh();
    }
    
    refresh() {
        this.loadData();
    }
    
    startAutoRefresh() {
        this.stopAutoRefresh();
        this.refreshTimer = setInterval(() => {
            this.refresh();
        }, this.config.autoRefresh);
    }
    
    stopAutoRefresh() {
        if (this.refreshTimer) {
            clearInterval(this.refreshTimer);
            this.refreshTimer = null;
        }
    }
    
    destroy() {
        this.stopAutoRefresh();
        if (this.chart) {
            this.chart.destroy();
        }
        this.element.innerHTML = '';
    }
}

// Auto-initialize widgets with data-auto-init attribute
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-auto-init="monitor-widget"]').forEach(element => {
        new MonitorWidget(element.id);
    });
});
