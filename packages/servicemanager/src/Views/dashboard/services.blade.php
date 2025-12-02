@extends('servicemanager::layouts.app')

@section('title', 'Services - Service Manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Services Management</h1>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createServiceModal">
            <i class="fas fa-plus me-1"></i>
            Create Service
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card card-stats border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="stats-icon text-primary mb-2">
                    <i class="fas fa-server"></i>
                </div>
                <h3 class="mb-0">{{ number_format($serviceStats['total']) }}</h3>
                <p class="text-muted mb-0">Total Services</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats success border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="stats-icon text-success mb-2">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="mb-0">{{ number_format($serviceStats['active']) }}</h3>
                <p class="text-muted mb-0">Active</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats warning border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="stats-icon text-warning mb-2">
                    <i class="fas fa-pause-circle"></i>
                </div>
                <h3 class="mb-0">{{ number_format($serviceStats['suspended']) }}</h3>
                <p class="text-muted mb-0">Suspended</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card card-stats danger border-0 shadow-sm">
            <div class="card-body text-center">
                <div class="stats-icon text-danger mb-2">
                    <i class="fas fa-times-circle"></i>
                </div>
                <h3 class="mb-0">{{ number_format($serviceStats['terminated']) }}</h3>
                <p class="text-muted mb-0">Terminated</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search services...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="suspended" {{ request('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('servicemanager.services') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Services Table -->
<div class="card shadow-sm">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Services List</h6>
    </div>
    <div class="card-body">
        @if($services->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Service Name</th>
                            <th>Plan</th>
                            <th>User ID</th>
                            <th>Status</th>
                            <th>Resources</th>
                            <th>Monthly Cost</th>
                            <th>Next Billing</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($services as $service)
                        <tr>
                            <td>
                                <strong>{{ $service->name }}</strong>
                                @if($service->description)
                                    <br><small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($service->plan)
                                    <span class="badge bg-info">{{ $service->plan->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $service->user_id }}</td>
                            <td>
                                <span class="badge bg-{{ $service->status === 'active' ? 'success' : ($service->status === 'suspended' ? 'warning' : ($service->status === 'terminated' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($service->status) }}
                                </span>
                            </td>
                            <td>
                                @if($service->current_resources)
                                    <small>
                                        @foreach($service->current_resources as $type => $value)
                                            <div>{{ ucfirst($type) }}: {{ $value }}</div>
                                        @endforeach
                                    </small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($service->plan)
                                    {{ number_format($service->plan->calculatePrice('month', $service->current_resources)) }} VND
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($service->next_billing_date)
                                    {{ $service->next_billing_date->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $service->created_at->format('d/m/Y') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary" onclick="viewService('{{ $service->_id }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($service->status === 'active')
                                        <button class="btn btn-outline-warning" onclick="suspendService('{{ $service->_id }}')">
                                            <i class="fas fa-pause"></i>
                                        </button>
                                    @elseif($service->status === 'suspended')
                                        <button class="btn btn-outline-success" onclick="reactivateService('{{ $service->_id }}')">
                                            <i class="fas fa-play"></i>
                                        </button>
                                    @endif
                                    @if($service->status !== 'terminated')
                                        <button class="btn btn-outline-danger" onclick="terminateService('{{ $service->_id }}')">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $services->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-server fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No services found</h5>
                <p class="text-muted">Create your first service to get started.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                    <i class="fas fa-plus me-1"></i>
                    Create Service
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Create Service Modal -->
<div class="modal fade" id="createServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Service</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Use the API endpoint to create services:</p>
                <code>POST /api/service-manager/services</code>
                <br><br>
                <p>Or use the dashboard to manage existing services.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="/api/service-manager/test" target="_blank" class="btn btn-primary">Test API</a>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewService(id) {
    // Implement view service details
    alert('View service: ' + id);
}

function suspendService(id) {
    if (confirm('Are you sure you want to suspend this service?')) {
        // Implement suspend service
        alert('Suspend service: ' + id);
    }
}

function reactivateService(id) {
    if (confirm('Are you sure you want to reactivate this service?')) {
        // Implement reactivate service
        alert('Reactivate service: ' + id);
    }
}

function terminateService(id) {
    if (confirm('Are you sure you want to terminate this service? This action cannot be undone.')) {
        // Implement terminate service
        alert('Terminate service: ' + id);
    }
}
</script>
@endpush 