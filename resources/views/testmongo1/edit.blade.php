@extends('testmongo1.layout')

@section('title', 'Edit TestMongo1')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-edit"></i> Edit TestMongo1 Record
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('testmongo1.update', $testMongo1->_id) }}" method="POST" enctype="multipart/form-data" id="editForm">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Name Field -->
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-user"></i> Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $testMongo1->name) }}" 
                                   placeholder="Enter full name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Email Field -->
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i> Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $testMongo1->email) }}" 
                                   placeholder="Enter email address"
                                   required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Phone Field -->
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i> Phone
                            </label>
                            <input type="tel" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $testMongo1->phone) }}" 
                                   placeholder="Enter phone number">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Image Field -->
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">
                                <i class="fas fa-image"></i> Profile Image
                            </label>
                            <input type="file" 
                                   class="form-control @error('image') is-invalid @enderror" 
                                   id="image" 
                                   name="image" 
                                   accept="image/*">
                            <div class="form-text">
                                Accepted formats: JPEG, PNG, JPG, GIF. Max size: 2MB
                                @if($testMongo1->image)
                                    <br><small class="text-info">Leave empty to keep current image</small>
                                @endif
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Current Image Display -->
                    @if($testMongo1->image)
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label">Current Image:</label>
                                <div class="border rounded p-3 text-center bg-light">
                                    <img src="{{ asset('storage/' . $testMongo1->image) }}" 
                                         alt="Current Image" 
                                         class="img-fluid rounded" 
                                         style="max-height: 200px;"
                                         id="currentImage">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-outline-danger" id="removeCurrentImage">
                                            <i class="fas fa-trash"></i> Remove Current Image
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- New Image Preview -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div id="imagePreview" class="d-none">
                                <label class="form-label">New Image Preview:</label>
                                <div class="border rounded p-3 text-center">
                                    <img id="previewImg" src="" alt="Preview" class="img-fluid" style="max-height: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Record Info -->
                    <div class="row">
                        <div class="col-12 mb-3">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> 
                                <strong>Record Information:</strong><br>
                                <small>
                                    Created: {{ $testMongo1->created_at ? $testMongo1->created_at->format('d/m/Y H:i:s') : 'N/A' }}<br>
                                    Last Updated: {{ $testMongo1->updated_at ? $testMongo1->updated_at->format('d/m/Y H:i:s') : 'N/A' }}<br>
                                    ID: {{ $testMongo1->_id }}
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field to track image removal -->
                    <input type="hidden" id="removeImage" name="remove_image" value="0">

                    <!-- Form Actions -->
                    <div class="row">
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('testmongo1.index') }}" class="btn btn-secondary me-2">
                                        <i class="fas fa-arrow-left"></i> Back to List
                                    </a>
                                    <a href="{{ route('testmongo1.show', $testMongo1->_id) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i> View Record
                                    </a>
                                </div>
                                <div>
                                    <button type="reset" class="btn btn-outline-secondary me-2">
                                        <i class="fas fa-undo"></i> Reset
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> Update Record
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Image preview functionality
        $('#image').change(function() {
            const file = this.files[0];
            if (file) {
                // Validate file size (2MB = 2 * 1024 * 1024 bytes)
                if (file.size > 2 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File too large',
                        text: 'Please select an image smaller than 2MB.'
                    });
                    $(this).val('');
                    $('#imagePreview').addClass('d-none');
                    return;
                }

                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid file type',
                        text: 'Please select a valid image file (JPEG, PNG, JPG, GIF).'
                    });
                    $(this).val('');
                    $('#imagePreview').addClass('d-none');
                    return;
                }

                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#previewImg').attr('src', e.target.result);
                    $('#imagePreview').removeClass('d-none');
                };
                reader.readAsDataURL(file);
            } else {
                $('#imagePreview').addClass('d-none');
            }
        });

        // Remove current image functionality
        $('#removeCurrentImage').click(function() {
            Swal.fire({
                title: 'Remove Current Image?',
                text: "This will remove the current image when you save the record.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#currentImage').parent().parent().addClass('d-none');
                    $('#removeImage').val('1');
                    Swal.fire(
                        'Marked for Removal!',
                        'The current image will be removed when you save.',
                        'success'
                    );
                }
            });
        });

        // Form validation
        $('#editForm').submit(function(e) {
            let isValid = true;
            const requiredFields = ['name', 'email'];
            
            requiredFields.forEach(function(field) {
                const value = $(`#${field}`).val().trim();
                if (!value) {
                    $(`#${field}`).addClass('is-invalid');
                    isValid = false;
                } else {
                    $(`#${field}`).removeClass('is-invalid');
                }
            });

            // Email validation
            const email = $('#email').val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                $('#email').addClass('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Validation Error',
                    text: 'Please fill in all required fields correctly.'
                });
            }
        });

        // Real-time validation
        $('#name, #email').on('input', function() {
            const value = $(this).val().trim();
            if (value) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        });

        // Email format validation
        $('#email').on('blur', function() {
            const email = $(this).val().trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (email && !emailRegex.test(email)) {
                $(this).removeClass('is-valid').addClass('is-invalid');
            } else if (email) {
                $(this).removeClass('is-invalid').addClass('is-valid');
            }
        });

        // Phone number formatting (optional)
        $('#phone').on('input', function() {
            let value = $(this).val().replace(/\D/g, ''); // Remove non-digits
            if (value.length > 0) {
                // Format as needed (example: xxx-xxx-xxxx)
                if (value.length <= 3) {
                    value = value;
                } else if (value.length <= 6) {
                    value = value.slice(0, 3) + '-' + value.slice(3);
                } else {
                    value = value.slice(0, 3) + '-' + value.slice(3, 6) + '-' + value.slice(6, 10);
                }
            }
            $(this).val(value);
        });

        // Reset form
        $('button[type="reset"]').click(function() {
            $('#imagePreview').addClass('d-none');
            $('#removeImage').val('0');
            $('.form-control').removeClass('is-valid is-invalid');
            
            // Show current image again if it was hidden
            @if($testMongo1->image)
                $('#currentImage').parent().parent().removeClass('d-none');
            @endif
        });

        // Auto-save draft functionality (optional)
        let autoSaveTimer;
        $('#name, #email, #phone').on('input', function() {
            clearTimeout(autoSaveTimer);
            autoSaveTimer = setTimeout(function() {
                // You can implement auto-save to localStorage here
                console.log('Auto-saving draft...');
            }, 2000);
        });
    });
</script>
@endsection 