@extends('layouts.adm')

@section('title', 'Demo01 Records - MongoDB CRUD')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Demo01 Records</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('mongocrud.dashboard') }}">MongoDB CRUD</a></li>
                        <li class="breadcrumb-item active">Demo01 Records</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <!-- Filters and Search -->
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-filter mr-1"></i>
                        Filters & Search
                    </h3>
                </div>
                <div class="card-body">
                    <form method="GET" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" class="form-control" name="search" value="{{ request('search') }}" 
                                       placeholder="Search by name...">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Min Age</label>
                                <input type="number" class="form-control" name="min_age" value="{{ request('min_age') }}" 
                                       placeholder="18" min="0" max="150">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Max Age</label>
                                <input type="number" class="form-control" name="max_age" value="{{ request('max_age') }}" 
                                       placeholder="65" min="0" max="150">
                            </div>
                        </div>
                        
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Per Page</label>
                                <select class="form-control" name="per_page">
                                    <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ request('per_page', 20) == 20 ? 'selected' : '' }}>20</option>
                                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div class="d-block">
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    @if(request()->hasAny(['search', 'status', 'min_age', 'max_age']))
                        <div class="mt-3">
                            <a href="{{ route('mongocrud.demo01.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-times mr-1"></i>
                                Clear Filters
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions Bar -->
            <div class="row mb-3">
                <div class="col-sm-6">
                    <h5>
                        Records 
                        @if($records instanceof \Illuminate\Pagination\LengthAwarePaginator)
                            <span class="badge badge-primary">{{ number_format($records->total()) }}</span>
                        @endif
                    </h5>
                </div>
                <div class="col-sm-6 text-right">
                    <button class="btn btn-danger btn-sm mr-1" onclick="bulkAction('delete')" id="bulkDeleteBtn" style="display: none;">
                        <i class="fas fa-trash mr-1"></i>
                        Delete Selected
                    </button>
                    <button class="btn btn-success btn-sm mr-1" onclick="bulkAction('activate')" id="bulkActivateBtn" style="display: none;">
                        <i class="fas fa-check mr-1"></i>
                        Activate Selected
                    </button>
                    <button class="btn btn-warning btn-sm mr-1" onclick="bulkAction('deactivate')" id="bulkDeactivateBtn" style="display: none;">
                        <i class="fas fa-times mr-1"></i>
                        Deactivate Selected
                    </button>
                    <a href="{{ route('mongocrud.demo01.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus mr-1"></i>
                        Create New
                    </a>
                </div>
            </div>

            <!-- Records Table -->
            <div class="card">
                <div class="card-body">
                    @if($records instanceof \Illuminate\Pagination\LengthAwarePaginator && $records->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="40">
                                            <input type="checkbox" id="selectAll" class="form-check-input">
                                        </th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'name', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                                               class="text-decoration-none">
                                                Name
                                                @if(request('sort_by') === 'name')
                                                    <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'age', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                                               class="text-decoration-none">
                                                Age
                                                @if(request('sort_by') === 'age')
                                                    <i class="fas fa-sort-{{ request('sort_order') === 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th>Status</th>
                                        <th>Tags</th>
                                        <th>
                                            <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'created_at', 'sort_order' => request('sort_order') === 'asc' ? 'desc' : 'asc']) }}" 
                                               class="text-decoration-none">
                                                Created
                                                @if(request('sort_by', 'created_at') === 'created_at')
                                                    <i class="fas fa-sort-{{ request('sort_order', 'desc') === 'asc' ? 'up' : 'down' }}"></i>
                                                @endif
                                            </a>
                                        </th>
                                        <th width="120">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($records as $record)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="form-check-input record-checkbox" value="{{ $record->_id }}">
                                        </td>
                                        <td>
                                            <strong>{{ $record->name }}</strong>
                                            @if($record->description)
                                                <br><small class="text-muted">{{ Str::limit($record->description, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>{{ $record->email }}</td>
                                        <td>{{ $record->phone ?? '-' }}</td>
                                        <td>
                                            @if($record->age)
                                                <span class="badge badge-secondary">{{ $record->age }}</span>
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
                                                    <span class="badge badge-light" title="{{ implode(', ', array_slice($record->tags, 2)) }}">
                                                        +{{ count($record->tags) - 2 }}
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                {{ $record->created_at->format('d/m/Y') }}<br>
                                                {{ $record->created_at->format('H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <a href="{{ route('mongocrud.demo01.show', $record->_id) }}" 
                                                   class="btn btn-info" title="View">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('mongocrud.demo01.edit', $record->_id) }}" 
                                                   class="btn btn-warning" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="deleteRecord('{{ $record->_id }}', '{{ $record->name }}')" 
                                                        class="btn btn-danger" title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $records->appends(request()->query())->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">No records found</h5>
                            @if(request()->hasAny(['search', 'status', 'min_age', 'max_age']))
                                <p class="text-muted">Try adjusting your search criteria.</p>
                                <a href="{{ route('mongocrud.demo01.index') }}" class="btn btn-secondary mr-2">
                                    <i class="fas fa-times mr-1"></i>
                                    Clear Filters
                                </a>
                            @else
                                <p class="text-muted">Start by creating your first record.</p>
                            @endif
                            <a href="{{ route('mongocrud.demo01.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus mr-1"></i>
                                Create Record
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

@push('js')
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

    demo01: {
        async delete(id) {
            return await MongoCrudAPI.request('DELETE', `/demo01/${id}`);
        },

        async bulk(action, ids) {
            return await MongoCrudAPI.request('POST', '/demo01/bulk', { action, ids });
        }
    }
};

// Utility functions
window.MongoCrudUtils = {
    showAlert(message, type = 'success') {
        toastr[type](message);
    }
};

// Select all checkbox functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.record-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    toggleBulkActions();
});

// Individual checkbox change
document.addEventListener('change', function(e) {
    if (e.target.classList.contains('record-checkbox')) {
        toggleBulkActions();
        
        // Update select all checkbox
        const checkboxes = document.querySelectorAll('.record-checkbox');
        const checkedBoxes = document.querySelectorAll('.record-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        if (checkedBoxes.length === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (checkedBoxes.length === checkboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }
});

// Toggle bulk action buttons
function toggleBulkActions() {
    const checkedBoxes = document.querySelectorAll('.record-checkbox:checked');
    const bulkButtons = ['bulkDeleteBtn', 'bulkActivateBtn', 'bulkDeactivateBtn'];
    
    bulkButtons.forEach(btnId => {
        const btn = document.getElementById(btnId);
        if (checkedBoxes.length > 0) {
            btn.style.display = 'inline-block';
        } else {
            btn.style.display = 'none';
        }
    });
}

// Bulk actions
async function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.record-checkbox:checked');
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (ids.length === 0) {
        MongoCrudUtils.showAlert('Please select at least one record', 'warning');
        return;
    }
    
    const actionText = action === 'delete' ? 'delete' : (action === 'activate' ? 'activate' : 'deactivate');
    
    if (!confirm(`Are you sure you want to ${actionText} ${ids.length} record(s)?`)) {
        return;
    }
    
    try {
        const response = await MongoCrudAPI.demo01.bulk(action, ids);
        
        if (response.success) {
            MongoCrudUtils.showAlert(response.message);
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            MongoCrudUtils.showAlert(response.message, 'error');
        }
    } catch (error) {
        MongoCrudUtils.showAlert('Error performing bulk action', 'error');
    }
}

// Delete single record
async function deleteRecord(id, name) {
    if (!confirm(`Are you sure you want to delete "${name}"?`)) {
        return;
    }
    
    try {
        const response = await MongoCrudAPI.demo01.delete(id);
        
        if (response.success) {
            MongoCrudUtils.showAlert('Record deleted successfully');
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } else {
            MongoCrudUtils.showAlert(response.message, 'error');
        }
    } catch (error) {
        MongoCrudUtils.showAlert('Error deleting record', 'error');
    }
}

// Auto-submit form on per_page change
document.querySelector('select[name="per_page"]').addEventListener('change', function() {
    this.form.submit();
});
</script>
@endpush 