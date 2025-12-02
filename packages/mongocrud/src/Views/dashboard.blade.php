@extends('layouts.adm')

@section('title', 'Dashboard - MongoDB CRUD')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">MongoDB CRUD Dashboard</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                        <li class="breadcrumb-item active">MongoDB CRUD</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Error Alert -->
            @if(isset($error))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> {{ $error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ number_format($stats['total']) }}</h3>
                            <p>Total Records</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <a href="{{ route('mongocrud.demo01.index') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ number_format($stats['active']) }}</h3>
                            <p>Active Records</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <a href="{{ route('mongocrud.demo01.index', ['status' => 'active']) }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ number_format($stats['inactive']) }}</h3>
                            <p>Inactive Records</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <a href="{{ route('mongocrud.demo01.index', ['status' => 'inactive']) }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
                
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3>{{ number_format($stats['recent']) }}</h3>
                            <p>Recent (7 days)</p>
                        </div>
                        <div class="icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <a href="{{ route('mongocrud.demo01.index') }}" class="small-box-footer">
                            More info <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-pie mr-1"></i>
                                Status Distribution
                            </h3>
                        </div>
                        <div class="card-body">
                            <canvas id="statusChart" style="min-height: 250px; height: 250px; max-height: 250px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-chart-bar mr-1"></i>
                                Age Statistics
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <h3 class="text-primary">{{ number_format($stats['avg_age'], 1) }}</h3>
                                <p class="text-muted">Average Age</p>
                            </div>
                            <canvas id="ageChart" style="min-height: 200px; height: 200px; max-height: 200px; max-width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Records -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-history mr-1"></i>
                                Recent Records
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('mongocrud.demo01.index') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye mr-1"></i>
                                    View All
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($recentRecords->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Age</th>
                                                <th>Status</th>
                                                <th>Tags</th>
                                                <th>Created</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($recentRecords as $record)
                                            <tr>
                                                <td><strong>{{ $record->name }}</strong></td>
                                                <td>{{ $record->email }}</td>
                                                <td>
                                                    @if($record->age)
                                                        <span class="badge badge-secondary">{{ $record->age }} years</span>
                                                    @else
                                                        <span class="text-muted">-</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($record->status)
                                                        <span class="badge badge-success">Active</span>
                                                    @else
                                                        <span class="badge badge-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($record->tags && count($record->tags) > 0)
                                                        @foreach(array_slice($record->tags, 0, 2) as $tag)
                                                            <span class="badge badge-info mr-1">{{ $tag }}</span>
                                                        @endforeach
                                                        @if(count($record->tags) > 2)
                                                            <span class="badge badge-light">+{{ count($record->tags) - 2 }}</span>
                                                        @endif
                                                    @else
                                                        <span class="text-muted">No tags</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $record->created_at->format('d/m/Y H:i') }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="{{ route('mongocrud.demo01.show', $record->_id) }}" 
                                                           class="btn btn-info btn-sm">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('mongocrud.demo01.edit', $record->_id) }}" 
                                                           class="btn btn-warning btn-sm">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No records found</h5>
                                    <p class="text-muted">Start by creating your first record.</p>
                                    <a href="{{ route('mongocrud.demo01.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus mr-1"></i>
                                        Create First Record
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt mr-1"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('mongocrud.demo01.create') }}" class="btn btn-primary btn-block">
                                        <i class="fas fa-plus mr-2"></i>
                                        Create Record
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="{{ route('mongocrud.demo01.index') }}" class="btn btn-info btn-block">
                                        <i class="fas fa-list mr-2"></i>
                                        View All Records
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <a href="/api/mongo-crud/demo01/stats/overview" target="_blank" class="btn btn-warning btn-block">
                                        <i class="fas fa-chart-bar mr-2"></i>
                                        API Statistics
                                    </a>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <button onclick="refreshStats()" class="btn btn-success btn-block">
                                        <i class="fas fa-sync mr-2"></i>
                                        Refresh Stats
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
<script>
// Status Distribution Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Active', 'Inactive'],
        datasets: [{
            data: [{{ $stats['active'] }}, {{ $stats['inactive'] }}],
            backgroundColor: [
                'rgba(17, 153, 142, 0.8)',
                'rgba(240, 147, 251, 0.8)'
            ],
            borderColor: [
                'rgba(17, 153, 142, 1)',
                'rgba(240, 147, 251, 1)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

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

    demo01: {
        async getAll(params = {}) {
            const queryString = new URLSearchParams(params).toString();
            return await MongoCrudAPI.request('GET', `/demo01?${queryString}`);
        },

        async getStats() {
            return await MongoCrudAPI.request('GET', '/demo01/stats/overview');
        }
    }
};

// Utility functions
window.MongoCrudUtils = {
    showAlert(message, type = 'success') {
        toastr[type](message);
    }
};

// Age Chart (if we have age data)
@if($stats['avg_age'] > 0)
const ageCtx = document.getElementById('ageChart').getContext('2d');

// Fetch age distribution data
MongoCrudAPI.demo01.getAll().then(response => {
    if (response.success) {
        const records = response.data.data;
        const ageRanges = {
            '0-20': 0,
            '21-30': 0,
            '31-40': 0,
            '41-50': 0,
            '51+': 0
        };
        
        records.forEach(record => {
            if (record.age) {
                if (record.age <= 20) ageRanges['0-20']++;
                else if (record.age <= 30) ageRanges['21-30']++;
                else if (record.age <= 40) ageRanges['31-40']++;
                else if (record.age <= 50) ageRanges['41-50']++;
                else ageRanges['51+']++;
            }
        });
        
        new Chart(ageCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(ageRanges),
                datasets: [{
                    label: 'Number of People',
                    data: Object.values(ageRanges),
                    backgroundColor: 'rgba(102, 126, 234, 0.8)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
});
@endif

// Refresh stats function
async function refreshStats() {
    try {
        const response = await MongoCrudAPI.demo01.getStats();
        if (response.success) {
            MongoCrudUtils.showAlert('Statistics refreshed successfully!');
            // Reload page to update stats
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        }
    } catch (error) {
        MongoCrudUtils.showAlert('Error refreshing statistics', 'error');
    }
}

// Auto-refresh stats every 5 minutes
setInterval(refreshStats, 300000);
</script>
@endpush 