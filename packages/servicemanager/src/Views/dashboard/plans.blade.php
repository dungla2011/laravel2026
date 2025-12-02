@extends('servicemanager::layouts.app')

@section('title', 'Service Plans - Service Manager')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Service Plans Management</h1>
    <div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
            <i class="fas fa-plus me-1"></i>
            Create Plan
        </button>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Search</label>
                <input type="text" class="form-control" name="search" value="{{ request('search') }}" placeholder="Search plans...">
            </div>
            <div class="col-md-3">
                <label class="form-label">Category</label>
                <select class="form-select" name="category">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                            {{ ucfirst($category) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-1"></i>
                        Filter
                    </button>
                    <a href="{{ route('servicemanager.plans') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Plans Table -->
<div class="card shadow-sm">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Service Plans List</h6>
    </div>
    <div class="card-body">
        @if($plans->count() > 0)
            <div class="row">
                @foreach($plans as $plan)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-header bg-{{ $plan->status ? 'success' : 'secondary' }} text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">{{ $plan->name }}</h6>
                                <span class="badge bg-light text-dark">{{ ucfirst($plan->category) }}</span>
                            </div>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">{{ Str::limit($plan->description, 100) }}</p>
                            
                            <!-- Resources -->
                            @if($plan->resources)
                                <div class="mb-3">
                                    <h6 class="text-primary">Resources:</h6>
                                    <div class="row text-sm">
                                        @foreach($plan->resources as $type => $value)
                                            <div class="col-6 mb-1">
                                                <strong>{{ ucfirst($type) }}:</strong> {{ $value }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Pricing -->
                            @if($plan->pricing)
                                <div class="mb-3">
                                    <h6 class="text-primary">Pricing:</h6>
                                    @foreach($plan->pricing as $resource => $periods)
                                        <div class="mb-2">
                                            <strong>{{ ucfirst($resource) }}:</strong>
                                            @foreach($periods as $period => $price)
                                                <div class="text-sm">
                                                    {{ ucfirst($period) }}: {{ number_format($price) }} VND
                                                </div>
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Stats -->
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="fas fa-server me-1"></i>
                                    {{ $plan->services_count ?? 0 }} services
                                </small>
                                <small class="text-muted">
                                    {{ $plan->created_at->format('d/m/Y') }}
                                </small>
                            </div>
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="btn-group w-100" role="group">
                                <button class="btn btn-outline-primary btn-sm" onclick="viewPlan('{{ $plan->_id }}')">
                                    <i class="fas fa-eye"></i>
                                    View
                                </button>
                                <button class="btn btn-outline-warning btn-sm" onclick="editPlan('{{ $plan->_id }}')" data-plan-id="{{ $plan->_id }}">
                                    <i class="fas fa-edit"></i>
                                    Edit
                                </button>
                                @if($plan->services_count == 0)
                                    <button class="btn btn-outline-danger btn-sm" onclick="deletePlan('{{ $plan->_id }}')">
                                        <i class="fas fa-trash"></i>
                                        Delete
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $plans->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-box fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">No service plans found</h5>
                <p class="text-muted">Create your first service plan to get started.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPlanModal">
                    <i class="fas fa-plus me-1"></i>
                    Create Plan
                </button>
            </div>
        @endif
    </div>
</div>

<!-- Create Plan Modal -->
<div class="modal fade" id="createPlanModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Service Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createPlanForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Basic Information</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Plan Name *</label>
                                <input type="text" class="form-control" name="name" required placeholder="e.g., VPS Basic">
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea class="form-control" name="description" rows="3" required placeholder="Describe your service plan"></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="vps">VPS</option>
                                    <option value="shared">Shared Hosting</option>
                                    <option value="dedicated">Dedicated Server</option>
                                    <option value="cloud">Cloud Hosting</option>
                                    <option value="storage">Storage</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Default Resources</h6>
                            
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">CPU (cores)</label>
                                    <input type="number" class="form-control" name="resources[cpu]" min="1" value="2">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">RAM (GB)</label>
                                    <input type="number" class="form-control" name="resources[ram]" min="1" value="4">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Disk (GB)</label>
                                    <input type="number" class="form-control" name="resources[disk]" min="1" value="50">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Network (Mbps)</label>
                                    <input type="number" class="form-control" name="resources[network]" min="1" value="100">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">IP Addresses</label>
                                    <input type="number" class="form-control" name="resources[ip]" min="1" value="1">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Databases</label>
                                    <input type="number" class="form-control" name="resources[databases]" min="0" value="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="text-primary mb-3">Pricing Configuration</h6>
                    <p class="text-muted small">Set prices for each resource type per billing period (VND)</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Resource</th>
                                    <th>Per Minute</th>
                                    <th>Per Hour</th>
                                    <th>Per Day</th>
                                    <th>Per Month</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>CPU (per core)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[cpu][minute]" placeholder="10" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[cpu][hour]" placeholder="600" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[cpu][day]" placeholder="14400" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[cpu][month]" placeholder="432000" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>RAM (per GB)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[ram][minute]" placeholder="5" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[ram][hour]" placeholder="300" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[ram][day]" placeholder="7200" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[ram][month]" placeholder="216000" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>Disk (per GB)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[disk][minute]" placeholder="1" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[disk][hour]" placeholder="60" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[disk][day]" placeholder="1440" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[disk][month]" placeholder="43200" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>Network (per Mbps)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[network][minute]" placeholder="0.5" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[network][hour]" placeholder="30" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[network][day]" placeholder="720" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[network][month]" placeholder="21600" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>IP Address (each)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[ip][minute]" placeholder="20" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[ip][hour]" placeholder="1200" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[ip][day]" placeholder="28800" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[ip][month]" placeholder="864000" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>Database (each)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[databases][minute]" placeholder="2" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[databases][hour]" placeholder="120" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[databases][day]" placeholder="2880" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" name="pricing[databases][month]" placeholder="86400" step="0.01"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Pricing Tips:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Để trống nếu không muốn bán resource đó theo thời gian đó</li>
                            <li>Giá theo phút thường rẻ nhất, giá theo tháng có thể có discount</li>
                            <li>Ví dụ: CPU 10 VND/phút = 600 VND/giờ = 14,400 VND/ngày = 432,000 VND/tháng</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        Create Service Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Plan Details Modal -->
<div class="modal fade" id="planDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Plan Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="planDetailsContent">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Plan Modal -->
<div class="modal fade" id="editPlanModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Service Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editPlanForm">
                <input type="hidden" id="editPlanId" name="plan_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Basic Information</h6>
                            
                            <div class="mb-3">
                                <label class="form-label">Plan Name *</label>
                                <input type="text" class="form-control" id="editPlanName" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Description *</label>
                                <textarea class="form-control" id="editPlanDescription" name="description" rows="3" required></textarea>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" id="editPlanCategory" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="vps">VPS</option>
                                    <option value="shared">Shared Hosting</option>
                                    <option value="dedicated">Dedicated Server</option>
                                    <option value="cloud">Cloud Hosting</option>
                                    <option value="storage">Storage</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="editPlanStatus" name="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <h6 class="text-primary mb-3">Default Resources</h6>
                            
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="form-label">CPU (cores)</label>
                                    <input type="number" class="form-control" id="editResourceCpu" name="resources[cpu]" min="1">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">RAM (GB)</label>
                                    <input type="number" class="form-control" id="editResourceRam" name="resources[ram]" min="1">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Disk (GB)</label>
                                    <input type="number" class="form-control" id="editResourceDisk" name="resources[disk]" min="1">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Network (Mbps)</label>
                                    <input type="number" class="form-control" id="editResourceNetwork" name="resources[network]" min="1">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">IP Addresses</label>
                                    <input type="number" class="form-control" id="editResourceIp" name="resources[ip]" min="1">
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="form-label">Databases</label>
                                    <input type="number" class="form-control" id="editResourceDatabases" name="resources[databases]" min="0">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h6 class="text-primary mb-3">Pricing Configuration</h6>
                    <p class="text-muted small">Update prices for each resource type per billing period (VND)</p>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Resource</th>
                                    <th>Per Minute</th>
                                    <th>Per Hour</th>
                                    <th>Per Day</th>
                                    <th>Per Month</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><strong>CPU (per core)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingCpuMinute" name="pricing[cpu][minute]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingCpuHour" name="pricing[cpu][hour]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingCpuDay" name="pricing[cpu][day]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingCpuMonth" name="pricing[cpu][month]" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>RAM (per GB)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingRamMinute" name="pricing[ram][minute]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingRamHour" name="pricing[ram][hour]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingRamDay" name="pricing[ram][day]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingRamMonth" name="pricing[ram][month]" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>Disk (per GB)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingDiskMinute" name="pricing[disk][minute]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingDiskHour" name="pricing[disk][hour]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingDiskDay" name="pricing[disk][day]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingDiskMonth" name="pricing[disk][month]" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>Network (per Mbps)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingNetworkMinute" name="pricing[network][minute]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingNetworkHour" name="pricing[network][hour]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingNetworkDay" name="pricing[network][day]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingNetworkMonth" name="pricing[network][month]" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>IP Address (each)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingIpMinute" name="pricing[ip][minute]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingIpHour" name="pricing[ip][hour]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingIpDay" name="pricing[ip][day]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingIpMonth" name="pricing[ip][month]" step="0.01"></td>
                                </tr>
                                <tr>
                                    <td><strong>Database (each)</strong></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingDatabasesMinute" name="pricing[databases][minute]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingDatabasesHour" name="pricing[databases][hour]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingDatabasesDay" name="pricing[databases][day]" step="0.01"></td>
                                    <td><input type="number" class="form-control form-control-sm" id="editPricingDatabasesMonth" name="pricing[databases][month]" step="0.01"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Changing pricing will affect new services only. Existing services will keep their current pricing until next billing cycle.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>
                        Update Service Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Handle create plan form submission
document.getElementById('createPlanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {};
    
    // Convert FormData to nested object
    for (let [key, value] of formData.entries()) {
        if (key.includes('[') && key.includes(']')) {
            // Handle nested keys like pricing[cpu][minute]
            const keys = key.split(/[\[\]]+/).filter(k => k);
            let current = data;
            
            for (let i = 0; i < keys.length - 1; i++) {
                if (!current[keys[i]]) {
                    current[keys[i]] = {};
                }
                current = current[keys[i]];
            }
            
            // Only add if value is not empty
            if (value && value.trim() !== '') {
                current[keys[keys.length - 1]] = isNaN(value) ? value : parseFloat(value);
            }
        } else {
            data[key] = value;
        }
    }
    
    // Validate required fields
    if (!data.name || !data.description || !data.category) {
        alert('Please fill in all required fields (Name, Description, Category)');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Creating...';
    submitBtn.disabled = true;
    
    // Submit to API
    fetch('/api/service-manager/plans', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Service plan created successfully!');
            location.reload();
        } else {
            alert('Error creating plan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error creating plan. Please try again.');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

// Handle edit plan form submission
document.getElementById('editPlanForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {};
    const planId = document.getElementById('editPlanId').value;
    
    console.log('Form submission - Plan ID:', planId); // Debug log
    
    if (!planId || planId === '') {
        alert('Error: Plan ID is missing. Please try again.');
        return;
    }
    
    // Convert FormData to nested object
    for (let [key, value] of formData.entries()) {
        if (key === 'plan_id') continue; // Skip plan_id
        
        if (key.includes('[') && key.includes(']')) {
            // Handle nested keys like pricing[cpu][minute]
            const keys = key.split(/[\[\]]+/).filter(k => k);
            let current = data;
            
            for (let i = 0; i < keys.length - 1; i++) {
                if (!current[keys[i]]) {
                    current[keys[i]] = {};
                }
                current = current[keys[i]];
            }
            
            // Only add if value is not empty
            if (value && value.trim() !== '') {
                current[keys[keys.length - 1]] = isNaN(value) ? value : parseFloat(value);
            }
        } else {
            if (key === 'status') {
                data[key] = value === '1';
            } else {
                data[key] = value;
            }
        }
    }
    
    console.log('Data to be sent:', data); // Debug log
    
    // Validate required fields
    if (!data.name || !data.description || !data.category) {
        alert('Please fill in all required fields (Name, Description, Category)');
        return;
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
    submitBtn.disabled = true;
    
    const apiUrl = `/api/service-manager/plans/${planId}`;
    console.log('API URL:', apiUrl); // Debug log
    
    // Submit to API
    fetch(apiUrl, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        console.log('Response status:', response.status); // Debug log
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data); // Debug log
        if (data.success) {
            alert('Service plan updated successfully!');
            location.reload();
        } else {
            alert('Error updating plan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating plan. Please try again.');
    })
    .finally(() => {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
});

function viewPlan(id) {
    // Load plan details via API
    fetch(`/api/service-manager/plans/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const plan = data.data;
                const content = `
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Basic Information</h6>
                            <table class="table table-sm">
                                <tr><td><strong>Name:</strong></td><td>${plan.name}</td></tr>
                                <tr><td><strong>Category:</strong></td><td>${plan.category}</td></tr>
                                <tr><td><strong>Status:</strong></td><td>${plan.status ? 'Active' : 'Inactive'}</td></tr>
                                <tr><td><strong>Created:</strong></td><td>${new Date(plan.created_at).toLocaleDateString()}</td></tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Resources</h6>
                            <table class="table table-sm">
                                ${Object.entries(plan.resources || {}).map(([key, value]) => 
                                    `<tr><td><strong>${key}:</strong></td><td>${value}</td></tr>`
                                ).join('')}
                            </table>
                        </div>
                    </div>
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p>${plan.description}</p>
                    </div>
                    <div class="mt-3">
                        <h6>Pricing</h6>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Resource</th>
                                        <th>Per Minute</th>
                                        <th>Per Hour</th>
                                        <th>Per Day</th>
                                        <th>Per Month</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${Object.entries(plan.pricing || {}).map(([resource, periods]) => `
                                        <tr>
                                            <td><strong>${resource}</strong></td>
                                            <td>${periods.minute ? new Intl.NumberFormat('vi-VN').format(periods.minute) + ' VND' : '-'}</td>
                                            <td>${periods.hour ? new Intl.NumberFormat('vi-VN').format(periods.hour) + ' VND' : '-'}</td>
                                            <td>${periods.day ? new Intl.NumberFormat('vi-VN').format(periods.day) + ' VND' : '-'}</td>
                                            <td>${periods.month ? new Intl.NumberFormat('vi-VN').format(periods.month) + ' VND' : '-'}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                document.getElementById('planDetailsContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('planDetailsModal')).show();
            } else {
                alert('Error loading plan details: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading plan details');
        });
}

function editPlan(id) {
    // Ensure we have a valid ID
    if (!id || id === 'undefined' || id === '') {
        // Try to get ID from the clicked button
        const clickedButton = event.target.closest('button[data-plan-id]');
        if (clickedButton) {
            id = clickedButton.getAttribute('data-plan-id');
        }
    }
    
    console.log('Edit plan called with ID:', id); // Debug log
    
    if (!id || id === 'undefined' || id === '') {
        alert('Error: Unable to determine plan ID. Please try again.');
        return;
    }
    
    // Load plan data and populate edit form
    fetch(`/api/service-manager/plans/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const plan = data.data;
                console.log('Plan data loaded:', plan); // Debug log
                
                // Populate basic information - IMPORTANT: Set the ID first
                document.getElementById('editPlanId').value = id; // Use the passed ID parameter
                document.getElementById('editPlanName').value = plan.name || '';
                document.getElementById('editPlanDescription').value = plan.description || '';
                document.getElementById('editPlanCategory').value = plan.category || '';
                document.getElementById('editPlanStatus').value = plan.status ? '1' : '0';
                
                // Populate resources
                const resources = plan.resources || {};
                document.getElementById('editResourceCpu').value = resources.cpu || '';
                document.getElementById('editResourceRam').value = resources.ram || '';
                document.getElementById('editResourceDisk').value = resources.disk || '';
                document.getElementById('editResourceNetwork').value = resources.network || '';
                document.getElementById('editResourceIp').value = resources.ip || '';
                document.getElementById('editResourceDatabases').value = resources.databases || '';
                
                // Populate pricing
                const pricing = plan.pricing || {};
                
                // CPU pricing
                const cpuPricing = pricing.cpu || {};
                document.getElementById('editPricingCpuMinute').value = cpuPricing.minute || '';
                document.getElementById('editPricingCpuHour').value = cpuPricing.hour || '';
                document.getElementById('editPricingCpuDay').value = cpuPricing.day || '';
                document.getElementById('editPricingCpuMonth').value = cpuPricing.month || '';
                
                // RAM pricing
                const ramPricing = pricing.ram || {};
                document.getElementById('editPricingRamMinute').value = ramPricing.minute || '';
                document.getElementById('editPricingRamHour').value = ramPricing.hour || '';
                document.getElementById('editPricingRamDay').value = ramPricing.day || '';
                document.getElementById('editPricingRamMonth').value = ramPricing.month || '';
                
                // Disk pricing
                const diskPricing = pricing.disk || {};
                document.getElementById('editPricingDiskMinute').value = diskPricing.minute || '';
                document.getElementById('editPricingDiskHour').value = diskPricing.hour || '';
                document.getElementById('editPricingDiskDay').value = diskPricing.day || '';
                document.getElementById('editPricingDiskMonth').value = diskPricing.month || '';
                
                // Network pricing
                const networkPricing = pricing.network || {};
                document.getElementById('editPricingNetworkMinute').value = networkPricing.minute || '';
                document.getElementById('editPricingNetworkHour').value = networkPricing.hour || '';
                document.getElementById('editPricingNetworkDay').value = networkPricing.day || '';
                document.getElementById('editPricingNetworkMonth').value = networkPricing.month || '';
                
                // IP pricing
                const ipPricing = pricing.ip || {};
                document.getElementById('editPricingIpMinute').value = ipPricing.minute || '';
                document.getElementById('editPricingIpHour').value = ipPricing.hour || '';
                document.getElementById('editPricingIpDay').value = ipPricing.day || '';
                document.getElementById('editPricingIpMonth').value = ipPricing.month || '';
                
                // Database pricing
                const databasesPricing = pricing.databases || {};
                document.getElementById('editPricingDatabasesMinute').value = databasesPricing.minute || '';
                document.getElementById('editPricingDatabasesHour').value = databasesPricing.hour || '';
                document.getElementById('editPricingDatabasesDay').value = databasesPricing.day || '';
                document.getElementById('editPricingDatabasesMonth').value = databasesPricing.month || '';
                
                // Verify the ID is set correctly
                console.log('Plan ID set to:', document.getElementById('editPlanId').value);
                
                // Show the edit modal
                new bootstrap.Modal(document.getElementById('editPlanModal')).show();
            } else {
                alert('Error loading plan data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading plan data');
        });
}

function deletePlan(id) {
    if (confirm('Are you sure you want to delete this plan? This action cannot be undone.')) {
        // Implement delete plan functionality
        fetch(`/api/service-manager/plans/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Plan deleted successfully');
                location.reload();
            } else {
                alert('Error deleting plan: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting plan');
        });
    }
}

// Auto-calculate pricing based on minute rate
function setupPricingCalculator() {
    const minuteInputs = document.querySelectorAll('input[name*="[minute]"]');
    
    minuteInputs.forEach(input => {
        input.addEventListener('input', function() {
            const resourceType = this.name.match(/pricing\[(\w+)\]\[minute\]/)[1];
            const minuteRate = parseFloat(this.value) || 0;
            
            if (minuteRate > 0) {
                // Auto-fill other periods based on minute rate
                const hourInput = document.querySelector(`input[name="pricing[${resourceType}][hour]"]`);
                const dayInput = document.querySelector(`input[name="pricing[${resourceType}][day]"]`);
                const monthInput = document.querySelector(`input[name="pricing[${resourceType}][month]"]`);
                
                if (hourInput && !hourInput.value) {
                    hourInput.value = (minuteRate * 60).toFixed(2);
                }
                if (dayInput && !dayInput.value) {
                    dayInput.value = (minuteRate * 60 * 24).toFixed(2);
                }
                if (monthInput && !monthInput.value) {
                    monthInput.value = (minuteRate * 60 * 24 * 30).toFixed(2);
                }
            }
        });
    });
}

// Initialize pricing calculator when modal is shown
document.getElementById('createPlanModal').addEventListener('shown.bs.modal', function() {
    setupPricingCalculator();
});
</script>
@endpush 