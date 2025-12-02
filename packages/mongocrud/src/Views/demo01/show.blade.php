@extends('layouts.adm')

@section('title', 'View Record - MongoDB CRUD')

@section('content')
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">View Record</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="/admin">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('mongocrud.dashboard') }}">MongoDB CRUD</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('mongocrud.demo01.index') }}">Demo01 Records</a></li>
                        <li class="breadcrumb-item active">View</li>
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
                                <i class="fas fa-eye mr-2"></i>
                                Record Details
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('mongocrud.demo01.edit', $record->_id) }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit mr-1"></i>
                                    Edit
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Name:</strong></label>
                                        <p class="form-control-static">{{ $record->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Email:</strong></label>
                                        <p class="form-control-static">{{ $record->email }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Phone:</strong></label>
                                        <p class="form-control-static">{{ $record->phone ?? 'N/A' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Age:</strong></label>
                                        <p class="form-control-static">
                                            @if($record->age)
                                                <span class="badge badge-secondary">{{ $record->age }} years</span>
                                            @else
                                                N/A
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($record->address)
                            <div class="form-group">
                                <label><strong>Address:</strong></label>
                                <p class="form-control-static">{{ $record->address }}</p>
                            </div>
                            @endif
                            
                            @if($record->description)
                            <div class="form-group">
                                <label><strong>Description:</strong></label>
                                <p class="form-control-static">{{ $record->description }}</p>
                            </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Status:</strong></label>
                                        <p class="form-control-static">
                                            @if($record->status)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-danger">Inactive</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Tags:</strong></label>
                                        <p class="form-control-static">
                                            @if($record->tags && count($record->tags) > 0)
                                                @foreach($record->tags as $tag)
                                                    <span class="badge badge-info mr-1">{{ $tag }}</span>
                                                @endforeach
                                            @else
                                                No tags
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            @if($record->metadata)
                            <div class="form-group">
                                <label><strong>Metadata:</strong></label>
                                <pre class="bg-light p-3 rounded">{{ json_encode($record->metadata, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                            @endif
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Created:</strong></label>
                                        <p class="form-control-static">{{ $record->created_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Updated:</strong></label>
                                        <p class="form-control-static">{{ $record->updated_at->format('d/m/Y H:i:s') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label><strong>ID:</strong></label>
                                <p class="form-control-static"><code>{{ $record->_id }}</code></p>
                            </div>
                            
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <a href="{{ route('mongocrud.demo01.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left mr-1"></i>
                                        Back to List
                                    </a>
                                </div>
                                <div class="col-md-6 text-right">
                                    <a href="{{ route('mongocrud.demo01.edit', $record->_id) }}" class="btn btn-warning mr-2">
                                        <i class="fas fa-edit mr-1"></i>
                                        Edit Record
                                    </a>
                                    <button onclick="deleteRecord('{{ $record->_id }}', '{{ $record->name }}')" class="btn btn-danger">
                                        <i class="fas fa-trash mr-1"></i>
                                        Delete Record
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
        }
    }
};

// Utility functions
window.MongoCrudUtils = {
    showAlert(message, type = 'success') {
        toastr[type](message);
    }
};

// Delete record function
async function deleteRecord(id, name) {
    if (!confirm(`Are you sure you want to delete "${name}"?`)) {
        return;
    }
    
    try {
        const response = await MongoCrudAPI.demo01.delete(id);
        
        if (response.success) {
            MongoCrudUtils.showAlert('Record deleted successfully');
            setTimeout(() => {
                window.location.href = '{{ route("mongocrud.demo01.index") }}';
            }, 1000);
        } else {
            MongoCrudUtils.showAlert(response.message, 'error');
        }
    } catch (error) {
        MongoCrudUtils.showAlert('Error deleting record', 'error');
    }
}
</script>
@endpush 