<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'MongoDB CRUD Manager')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .stats-card-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stats-card-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        .stats-card-info {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
            transform: translateY(-2px);
        }
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.1);
        }
        .badge-status-active {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .badge-status-inactive {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0">
                <div class="sidebar p-3">
                    <div class="text-center mb-4">
                        <h4 class="text-white">
                            <i class="fas fa-database me-2"></i>
                            MongoDB CRUD
                        </h4>
                        <small class="text-white-50">Management System</small>
                    </div>

                    <nav class="nav flex-column">
                        <a class="nav-link {{ request()->routeIs('mongocrud.dashboard*') ? 'active' : '' }}"
                           href="{{ route('mongocrud.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>
                            Dashboard
                        </a>

                        <a class="nav-link {{ request()->routeIs('mongocrud.demo01*') ? 'active' : '' }}"
                           href="{{ route('mongocrud.demo01.index') }}">
                            <i class="fas fa-users me-2"></i>
                            Demo01 Records
                        </a>

                        <hr class="text-white-50">

                        <a class="nav-link" href="/api/mongo-crud/test" target="_blank">
                            <i class="fas fa-code me-2"></i>
                            API Test
                        </a>

                        <a class="nav-link" href="/api/mongo-crud/demo01" target="_blank">
                            <i class="fas fa-database me-2"></i>
                            API Data
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content p-4">
                    <!-- Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h2 class="mb-0">@yield('page-title', 'Dashboard')</h2>
                            <small class="text-muted">@yield('page-description', 'MongoDB CRUD Management')</small>
                        </div>
                        <div>
                            <span class="badge bg-success">
                                <i class="fas fa-circle me-1"></i>
                                Connected
                            </span>
                        </div>
                    </div>

                    <!-- Alerts -->
                    @if(isset($error))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Error:</strong> {{ $error }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Content -->
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Common JavaScript -->
    <script>
        // CSRF Token setup for AJAX
        window.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Common API functions
        window.MongoCrudAPI = {
            baseUrl: '/api/mongo-crud',

            async request(method, endpoint, data = null) {
                const options = {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.csrfToken
                    }
                };

                if (data) {
                    options.body = JSON.stringify(data);
                }

                try {
                    const response = await fetch(this.baseUrl + endpoint, options);
                    return await response.json();
                } catch (error) {
                    console.error('API Error:', error);
                    throw error;
                }
            },

            // Demo01 API methods
            demo01: {
                async getAll(params = {}) {
                    const queryString = new URLSearchParams(params).toString();
                    return await MongoCrudAPI.request('GET', `/demo01?${queryString}`);
                },

                async getById(id) {
                    return await MongoCrudAPI.request('GET', `/demo01/${id}`);
                },

                async create(data) {
                    return await MongoCrudAPI.request('POST', '/demo01', data);
                },

                async update(id, data) {
                    return await MongoCrudAPI.request('PUT', `/demo01/${id}`, data);
                },

                async delete(id) {
                    return await MongoCrudAPI.request('DELETE', `/demo01/${id}`);
                },

                async getStats() {
                    return await MongoCrudAPI.request('GET', '/demo01/stats/overview');
                },

                async bulk(action, ids) {
                    return await MongoCrudAPI.request('POST', '/demo01/bulk', { action, ids });
                }
            }
        };

        // Common utility functions
        window.MongoCrudUtils = {
            showAlert(message, type = 'success') {
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                alertDiv.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                `;

                const container = document.querySelector('.main-content');
                container.insertBefore(alertDiv, container.firstChild);

                // Auto remove after 5 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 5000);
            },

            formatDate(dateString) {
                return new Date(dateString).toLocaleDateString('vi-VN', {
                    year: 'numeric',
                    month: '2-digit',
                    day: '2-digit',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            },

            formatNumber(number) {
                return new Intl.NumberFormat('vi-VN').format(number);
            }
        };
    </script>

    @stack('scripts')
</body>
</html>
