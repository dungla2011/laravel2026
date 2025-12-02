@extends('testmongo1.layout')

@section('title', 'View TestMongo1')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-eye"></i> TestMongo1 Record Details
                </h5>
                <div class="btn-group" role="group">
                    <a href="{{ route('testmongo1.edit', $testMongo1->_id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <button type="button" 
                            class="btn btn-danger btn-sm"
                            onclick="confirmDelete('{{ route('testmongo1.destroy', $testMongo1->_id) }}', 'Delete {{ $testMongo1->name }}?')">
                        <i class="fas fa-trash"></i> Delete
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Profile Image -->
                    <div class="col-md-4 text-center mb-4">
                        @if($testMongo1->image)
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $testMongo1->image) }}" 
                                     alt="{{ $testMongo1->name }}'s Image" 
                                     class="img-fluid rounded shadow"
                                     style="max-height: 300px; cursor: pointer;"
                                     data-bs-toggle="modal" 
                                     data-bs-target="#imageModal">
                                <div class="mt-2">
                                    <small class="text-muted">Click to enlarge</small>
                                </div>
                            </div>
                            
                            <!-- Image Modal -->
                            <div class="modal fade" id="imageModal" tabindex="-1">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">{{ $testMongo1->name }}'s Profile Image</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img src="{{ asset('storage/' . $testMongo1->image) }}" 
                                                 alt="{{ $testMongo1->name }}'s Image" 
                                                 class="img-fluid">
                                        </div>
                                        <div class="modal-footer">
                                            <a href="{{ asset('storage/' . $testMongo1->image) }}" 
                                               target="_blank" 
                                               class="btn btn-primary">
                                                <i class="fas fa-external-link-alt"></i> Open in New Tab
                                            </a>
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="bg-light rounded d-flex align-items-center justify-content-center shadow" 
                                 style="height: 200px; width: 200px; margin: 0 auto;">
                                <i class="fas fa-user fa-4x text-muted"></i>
                            </div>
                            <div class="mt-2">
                                <small class="text-muted">No image uploaded</small>
                            </div>
                        @endif
                    </div>

                    <!-- Record Details -->
                    <div class="col-md-8">
                        <div class="row">
                            <!-- Name -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold text-muted">
                                    <i class="fas fa-user"></i> Full Name
                                </label>
                                <div class="form-control-plaintext border rounded p-2 bg-light">
                                    <strong>{{ $testMongo1->name }}</strong>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold text-muted">
                                    <i class="fas fa-envelope"></i> Email Address
                                </label>
                                <div class="form-control-plaintext border rounded p-2 bg-light">
                                    <a href="mailto:{{ $testMongo1->email }}" class="text-decoration-none">
                                        {{ $testMongo1->email }}
                                    </a>
                                    <button class="btn btn-sm btn-outline-secondary ms-2" 
                                            onclick="copyToClipboard('{{ $testMongo1->email }}')"
                                            title="Copy email">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Phone -->
                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold text-muted">
                                    <i class="fas fa-phone"></i> Phone Number
                                </label>
                                <div class="form-control-plaintext border rounded p-2 bg-light">
                                    @if($testMongo1->phone)
                                        <a href="tel:{{ $testMongo1->phone }}" class="text-decoration-none">
                                            {{ $testMongo1->phone }}
                                        </a>
                                        <button class="btn btn-sm btn-outline-secondary ms-2" 
                                                onclick="copyToClipboard('{{ $testMongo1->phone }}')"
                                                title="Copy phone">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @else
                                        <span class="text-muted">Not provided</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Record Metadata -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-header">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-info-circle"></i> Record Information
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-2">
                                        <strong>Record ID:</strong><br>
                                        <code>{{ $testMongo1->_id }}</code>
                                        <button class="btn btn-sm btn-outline-secondary ms-1" 
                                                onclick="copyToClipboard('{{ $testMongo1->_id }}')"
                                                title="Copy ID">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <strong>Created Date:</strong><br>
                                        @if($testMongo1->created_at)
                                            <span title="{{ $testMongo1->created_at->format('Y-m-d H:i:s') }}">
                                                {{ $testMongo1->created_at->format('d/m/Y H:i') }}
                                            </span>
                                            <br><small class="text-muted">{{ $testMongo1->created_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">Not available</span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <strong>Last Updated:</strong><br>
                                        @if($testMongo1->updated_at)
                                            <span title="{{ $testMongo1->updated_at->format('Y-m-d H:i:s') }}">
                                                {{ $testMongo1->updated_at->format('d/m/Y H:i') }}
                                            </span>
                                            <br><small class="text-muted">{{ $testMongo1->updated_at->diffForHumans() }}</small>
                                        @else
                                            <span class="text-muted">Not available</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('testmongo1.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                            <div class="btn-group" role="group">
                                <a href="{{ route('testmongo1.edit', $testMongo1->_id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Edit Record
                                </a>
                                <button type="button" 
                                        class="btn btn-danger"
                                        onclick="confirmDelete('{{ route('testmongo1.destroy', $testMongo1->_id) }}', 'Delete {{ $testMongo1->name }}?')">
                                    <i class="fas fa-trash"></i> Delete Record
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card border-info">
                            <div class="card-header bg-info text-white">
                                <h6 class="card-title mb-0">
                                    <i class="fas fa-bolt"></i> Quick Actions
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="btn-group-vertical d-grid gap-2 d-md-block">
                                    @if($testMongo1->email)
                                        <a href="mailto:{{ $testMongo1->email }}" class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-envelope"></i> Send Email
                                        </a>
                                    @endif
                                    @if($testMongo1->phone)
                                        <a href="tel:{{ $testMongo1->phone }}" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-phone"></i> Call Phone
                                        </a>
                                    @endif
                                    <button class="btn btn-outline-info btn-sm" onclick="printRecord()">
                                        <i class="fas fa-print"></i> Print Record
                                    </button>
                                    <button class="btn btn-outline-secondary btn-sm" onclick="exportRecord()">
                                        <i class="fas fa-download"></i> Export Data
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Text copied to clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        }).catch(function(err) {
            // Fallback for older browsers
            const textArea = document.createElement('textarea');
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Text copied to clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        });
    }

    // Print record function
    function printRecord() {
        const printContent = `
            <div style="font-family: Arial, sans-serif; padding: 20px;">
                <h2>TestMongo1 Record Details</h2>
                <hr>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Name:</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $testMongo1->name }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Email:</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $testMongo1->email }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Phone:</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $testMongo1->phone ?? 'Not provided' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Created:</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $testMongo1->created_at ? $testMongo1->created_at->format('d/m/Y H:i:s') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd; font-weight: bold;">Record ID:</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $testMongo1->_id }}</td>
                    </tr>
                </table>
                <p style="margin-top: 20px; font-size: 12px; color: #666;">
                    Printed on: ${new Date().toLocaleString()}
                </p>
            </div>
        `;
        
        const printWindow = window.open('', '_blank');
        printWindow.document.write(printContent);
        printWindow.document.close();
        printWindow.print();
    }

    // Export record function
    function exportRecord() {
        const data = {
            id: '{{ $testMongo1->_id }}',
            name: '{{ $testMongo1->name }}',
            email: '{{ $testMongo1->email }}',
            phone: '{{ $testMongo1->phone ?? "" }}',
            created_at: '{{ $testMongo1->created_at ? $testMongo1->created_at->format("Y-m-d H:i:s") : "" }}',
            updated_at: '{{ $testMongo1->updated_at ? $testMongo1->updated_at->format("Y-m-d H:i:s") : "" }}'
        };

        const jsonData = JSON.stringify(data, null, 2);
        const blob = new Blob([jsonData], { type: 'application/json' });
        const url = URL.createObjectURL(blob);
        
        const a = document.createElement('a');
        a.href = url;
        a.download = `testmongo1_${data.id}.json`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);

        Swal.fire({
            icon: 'success',
            title: 'Exported!',
            text: 'Record data has been exported as JSON file',
            timer: 2000,
            showConfirmButton: false
        });
    }
</script>
@endsection 