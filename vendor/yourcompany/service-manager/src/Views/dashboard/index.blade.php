@extends('servicemanager::layouts.app')

@section('title', 'Dashboard - Service Manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Dashboard</h1>
    <div class="text-muted">
        <i class="fas fa-calendar me-1"></i>
        {{ now()->format('d/m/Y H:i') }}
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Total Services</h5>
                        <span class="h2 font-weight-bold mb-0">{{ number_format($stats['total_services']) }}</span>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon text-primary">
                            <i class="fas fa-server"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2">
                        <i class="fas fa-arrow-up"></i> {{ $stats['active_services'] }}
                    </span>
                    <span class="text-nowrap">Active services</span>
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats success border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Monthly Revenue</h5>
                        <span class="h2 font-weight-bold mb-0">{{ number_format($stats['total_revenue_month']) }} VND</span>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon text-success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2">
                        <i class="fas fa-arrow-up"></i> This month
                    </span>
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats warning border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Pending Billing</h5>
                        <span class="h2 font-weight-bold mb-0">{{ number_format($stats['pending_billing']) }} VND</span>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-warning mr-2">
                        <i class="fas fa-exclamation-triangle"></i> Needs attention
                    </span>
                </p>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats danger border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Overdue</h5>
                        <span class="h2 font-weight-bold mb-0">{{ number_format($stats['overdue_billing']) }} VND</span>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon text-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-danger mr-2">
                        <i class="fas fa-arrow-down"></i> Overdue bills
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-xl-8 mb-4">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Monthly Revenue Trend</h6>
            </div>
            <div class="card-body">
                <canvas id="revenueChart" width="400" height="200"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 mb-4">
        <div class="card shadow-sm">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Stats</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Total Users</span>
                        <strong>{{ number_format($stats['total_users']) }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Service Plans</span>
                        <strong>{{ number_format($stats['total_plans']) }}</strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Active Services</span>
                        <strong>{{ number_format($stats['active_services']) }}</strong>
                    </div>
                </div>
                <hr>
                <div class="text-center">
                    <a href="{{ route('servicemanager.services') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye me-1"></i>
                        View All Services
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-xl-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Services</h6>
                <a href="{{ route('servicemanager.services') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentServices->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Service</th>
                                    <th>Plan</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentServices as $service)
                                <tr>
                                    <td>{{ $service->name }}</td>
                                    <td>{{ $service->plan->name ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-{{ $service->status === 'active' ? 'success' : ($service->status === 'suspended' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($service->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $service->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">No services found</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-xl-6 mb-4">
        <div class="card shadow-sm">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">Recent Billing</h6>
                <a href="{{ route('servicemanager.billing') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body">
                @if($recentBilling->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBilling as $billing)
                                <tr>
                                    <td>{{ number_format($billing->amount) }} VND</td>
                                    <td>
                                        <span class="badge bg-{{ $billing->status === 'paid' ? 'success' : ($billing->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($billing->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $billing->created_at->format('d/m/Y') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted text-center py-3">No billing records found</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Revenue Chart
const ctx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($monthlyRevenue['months']),
        datasets: [{
            label: 'Revenue (VND)',
            data: @json($monthlyRevenue['revenue']),
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.1,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + ' VND';
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Revenue: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' VND';
                    }
                }
            }
        }
    }
});
</script>
@endpush 