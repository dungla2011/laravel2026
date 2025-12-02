@extends('servicemanager::layouts.app')

@section('title', 'Billing - Service Manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Billing Management</h1>
    <div>
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addFundsModal">
            <i class="fas fa-plus me-1"></i>
            Add Funds
        </button>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats success border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Total Revenue</h5>
                        <span class="h2 font-weight-bold mb-0">{{ number_format($billingStats['total_revenue']) }} VND</span>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon text-success">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-success mr-2">
                        <i class="fas fa-arrow-up"></i> Period revenue
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
                        <h5 class="card-title text-uppercase text-muted mb-0">Pending</h5>
                        <span class="h2 font-weight-bold mb-0">{{ number_format($billingStats['pending_amount']) }} VND</span>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon text-warning">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-warning mr-2">
                        <i class="fas fa-exclamation-triangle"></i> Pending payments
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
                        <span class="h2 font-weight-bold mb-0">{{ number_format($billingStats['overdue_amount']) }} VND</span>
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
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats border-0 shadow-sm">
            <div class="card-body">
                <div class="row">
                    <div class="col">
                        <h5 class="card-title text-uppercase text-muted mb-0">Transactions</h5>
                        <span class="h2 font-weight-bold mb-0">{{ number_format($billingStats['total_transactions']) }}</span>
                    </div>
                    <div class="col-auto">
                        <div class="stats-icon text-primary">
                            <i class="fas fa-receipt"></i>
                        </div>
                    </div>
                </div>
                <p class="mt-3 mb-0 text-muted text-sm">
                    <span class="text-primary mr-2">
                        <i class="fas fa-chart-line"></i> Total count
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Start Date</label>
                <input type="date" class="form-control" name="start_date" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">End Date</label>
                <input type="date" class="form-control" name="end_date" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">All Status</option>
                    <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('servicemanager.billing') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Billing Records Table -->
<div class="card shadow-sm">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Billing Records</h6>
    </div>
    <div class="card-body">
        @if($billingRecords->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Invoice ID</th>
                            <th>Service</th>
                            <th>User ID</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Billing Period</th>
                            <th>Due Date</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($billingRecords as $record)
                        <tr>
                            <td>
                                <strong>#{{ substr($record->_id, -8) }}</strong>
                            </td>
                            <td>
                                @if($record->service_id)
                                    <span class="text-primary">Service {{ substr($record->service_id, -8) }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $record->user_id }}</td>
                            <td>
                                <strong>{{ number_format($record->amount) }} VND</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $record->status === 'paid' ? 'success' : ($record->status === 'pending' ? 'warning' : ($record->status === 'overdue' ? 'danger' : 'secondary')) }}">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td>
                                @if($record->billing_period_start && $record->billing_period_end)
                                    {{ $record->billing_period_start->format('d/m') }} - {{ $record->billing_period_end->format('d/m/Y') }}
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                @if($record->due_date)
                                    {{ $record->due_date->format('d/m/Y') }}
                                    @if($record->due_date->isPast() && $record->status !== 'paid')
                                        <i class="fas fa-exclamation-triangle text-danger ms-1"></i>
                                    @endif
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>{{ $record->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary" onclick="viewBilling('{{ $record->_id }}')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    @if($record->status === 'pending' || $record->status === 'overdue')
                                        <button class="btn btn-outline-success" onclick="markAsPaid('{{ $record->_id }}')">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    @endif
                                    @if($record->status !== 'paid')
                                        <button class="btn btn-outline-danger" onclick="cancelBilling('{{ $record->_id }}')">
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
                {{ $billingRecords->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No billing records found</h5>
                <p class="text-muted">Billing records will appear here when services are created.</p>
            </div>
        @endif
    </div>
</div>

<!-- Add Funds Modal -->
<div class="modal fade" id="addFundsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Funds</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Use the API endpoint to add funds:</p>
                <code>POST /api/service-manager/billing/add-funds</code>
                <br><br>
                <p><strong>Example payload:</strong></p>
                <pre class="bg-light p-3 rounded"><code>{
  "amount": 1000000,
  "description": "Account top-up",
  "payment_method": "bank_transfer"
}</code></pre>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="/api/service-manager/test" target="_blank" class="btn btn-primary">Test API</a>
            </div>
        </div>
    </div>
</div>

<!-- Billing Details Modal -->
<div class="modal fade" id="billingDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Billing Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="billingDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function viewBilling(id) {
    // Load billing details
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6>Billing Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Invoice ID:</strong></td><td>#${id.substr(-8)}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge bg-info">Loading...</span></td></tr>
                    <tr><td><strong>Amount:</strong></td><td>Loading...</td></tr>
                    <tr><td><strong>Created:</strong></td><td>Loading...</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Service Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Service ID:</strong></td><td>Loading...</td></tr>
                    <tr><td><strong>User ID:</strong></td><td>Loading...</td></tr>
                    <tr><td><strong>Billing Period:</strong></td><td>Loading...</td></tr>
                </table>
            </div>
        </div>
        <div class="mt-3">
            <h6>Description</h6>
            <p>Loading billing details...</p>
        </div>
    `;
    document.getElementById('billingDetailsContent').innerHTML = content;
    new bootstrap.Modal(document.getElementById('billingDetailsModal')).show();
}

function markAsPaid(id) {
    if (confirm('Mark this billing record as paid?')) {
        fetch(`/api/service-manager/billing/pay/${id}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Billing record marked as paid');
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error marking as paid');
        });
    }
}

function cancelBilling(id) {
    if (confirm('Cancel this billing record? This action cannot be undone.')) {
        // Implement cancel billing functionality
        alert('Cancel billing: ' + id + '\n\nThis feature needs to be implemented in the API.');
    }
}
</script>
@endpush 