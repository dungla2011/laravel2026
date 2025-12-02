@extends('layouts.adm')

@section('title', 'Create Record - MongoDB CRUD')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Create New Record</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('mongocrud.dashboard') }}">MongoDB CRUD</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('mongocrud.demo01.index') }}">Demo01 Records</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-plus mr-2"></i>
                                Create New Demo01 Record
                            </h3>
                        </div>
                        <div class="card-body">
                            <form id="createForm">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name *</label>
                                            <input type="text" class="form-control" name="name" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Email *</label>
                                            <input type="email" class="form-control" name="email" required>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Phone</label>
                                            <input type="text" class="form-control" name="phone" maxlength="20">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Age</label>
                                            <input type="number" class="form-control" name="age" min="0" max="150">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Address</label>
                                    <textarea class="form-control" name="address" rows="2" maxlength="500"></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label>Description</label>
                                    <textarea class="form-control" name="description" rows="3" maxlength="1000"></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Status</label>
                                            <select class="form-control" name="status">
                                                <option value="1" selected>Active</option>
                                                <option value="0">Inactive</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Tags</label>
                                            <input type="text" class="form-control" name="tags" 
                                                   placeholder="Enter tags separated by commas">
                                            <small class="form-text text-muted">Example: developer, php, laravel</small>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>Metadata (JSON)</label>
                                    <textarea class="form-control" name="metadata" rows="4" 
                                              placeholder='{"department": "IT", "position": "Developer"}'></textarea>
                                    <small class="form-text text-muted">Enter valid JSON object or leave empty</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <a href="{{ route('mongocrud.demo01.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left mr-1"></i>
                                            Back to List
                                        </a>
                                    </div>
                                    <div class="col-md-6 text-right">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save mr-1"></i>
                                            Create Record
                                        </button>
                                    </div>
                                </div>
                            </form>
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
        async create(data) {
            return await MongoCrudAPI.request('POST', '/demo01', data);
        }
    }
};

// Utility functions
window.MongoCrudUtils = {
    showAlert(message, type = 'success') {
        toastr[type](message);
    }
};

document.getElementById('createForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = {};
    
    // Convert form data to object
    for (let [key, value] of formData.entries()) {
        if (key === 'tags' && value) {
            // Convert comma-separated tags to array
            data[key] = value.split(',').map(tag => tag.trim()).filter(tag => tag);
        } else if (key === 'metadata' && value) {
            // Parse JSON metadata
            try {
                data[key] = JSON.parse(value);
            } catch (error) {
                MongoCrudUtils.showAlert('Invalid JSON in metadata field', 'error');
                return;
            }
        } else if (key === 'status') {
            data[key] = value === '1';
        } else if (key === 'age' && value) {
            data[key] = parseInt(value);
        } else if (value) {
            data[key] = value;
        }
    }
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Creating...';
    submitBtn.disabled = true;
    
    try {
        const response = await MongoCrudAPI.demo01.create(data);
        
        if (response.success) {
            MongoCrudUtils.showAlert('Record created successfully!');
            setTimeout(() => {
                window.location.href = '{{ route("mongocrud.demo01.index") }}';
            }, 1000);
        } else {
            MongoCrudUtils.showAlert(response.message, 'error');
            if (response.errors) {
                console.error('Validation errors:', response.errors);
            }
        }
    } catch (error) {
        console.error('Error:', error);
        MongoCrudUtils.showAlert('Error creating record', 'error');
    } finally {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Auto-format JSON in metadata field
document.querySelector('textarea[name="metadata"]').addEventListener('blur', function() {
    if (this.value.trim()) {
        try {
            const parsed = JSON.parse(this.value);
            this.value = JSON.stringify(parsed, null, 2);
            this.classList.remove('is-invalid');
        } catch (error) {
            this.classList.add('is-invalid');
        }
    }
});
</script>
@endpush 