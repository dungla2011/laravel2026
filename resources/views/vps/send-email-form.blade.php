<?php
$uid = getCurrentUserId();
?>
@extends("layouts.member")
@section('header')
    @include('parts.header-all')
@endsection


@section('title')
    {{
    \App\Models\SiteMng::getTitle()
    }}
@endsection

@section('meta-description')
    <?php
    \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section("css")
@endsection

@section('content')
<div class="container mt-4">
    <br>
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg mt-5">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-envelope mr-2"></i> Send Billing Report Email
                    </h5>
                </div>

                <div class="card-body p-4">
                    <div class="alert alert-info mb-4" role="alert">
                        <strong>📧 Email will be sent to:</strong> <code>{{ $user->email }}</code>
                    </div>

                    <!-- Email Edit Form -->
                    <form method="POST" action="" class="needs-validation">
                        @csrf

                        <!-- Hidden filter params -->
                        @foreach($filters as $key => $value)
                            @if($value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endif
                        @endforeach

                        <!-- Email Subject / Title -->
                        <div class="form-group mb-4">
                            <label for="title" class="form-label fw-bold">
                                📌 Email Subject
                            </label>
                            <input
                                type="text"
                                class="form-control form-control-lg"
                                id="title"
                                name="title"
                                value="{{ $title }}"
                                placeholder="Enter email subject"
                                required
                            >
                            <small class="form-text text-muted d-block mt-2">
                                The subject line of the email
                            </small>
                        </div>

                        <!-- Email Content / Body -->
                        <div class="form-group mb-4">
                            <label for="content" class="form-label fw-bold">
                                📝 Email Content
                            </label>
                            <textarea
                                class="form-control"
                                id="content"
                                name="content"
                                rows="15"
                                placeholder="Enter email body content"
                                style="font-family: 'Courier New', monospace; font-size: 14px;"
                            >{{ $content }}</textarea>
                            <small class="form-text text-muted d-block mt-2">
                                The main body of the email. Excel report will be attached automatically.
                            </small>
                        </div>

                        <!-- Character count -->
                        <div class="mb-4">
                            <small class="text-muted">
                                Content length: <span id="charCount">0</span> characters
                            </small>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2 justify-content-between">
                            <div>
                                <button data-code-pos='ppp17731411333491' type="submit" class="btn btn-success btn-lg" onclick="document.getElementById('debugMode').value = 0;">
                                    <i class="fas fa-paper-plane mr-2"></i> Send Email
                                </button>
                                <button data-code-pos='ppp17731411360351' type="submit" class="btn btn-warning btn-lg ml-2" onclick="document.getElementById('debugMode').value = 1;">
                                    <i class="fas fa-bug mr-2"></i> Send Email Debug
                                </button>
                                <a data-code-pos='ppp17731411388131' href="{{ $report_url }}" class="btn btn-secondary btn-lg ml-2">
                                    <i class="fas fa-times mr-2"></i> Cancel
                                </a>
                            </div>
                        </div>

                        <!-- Debug mode hidden input -->
                        <input type="hidden" id="debugMode" name="debug" value="0">
                    </form>
                </div>

                <div class="card-footer bg-light">
                    <small class="text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        The Excel file with billing details will be automatically attached to this email.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Update character count
document.getElementById('content').addEventListener('input', function() {
    document.getElementById('charCount').textContent = this.value.length;
});

// Update on page load
document.getElementById('charCount').textContent = document.getElementById('content').value.length;
</script>

<style>
    .card {
        border: none;
        border-radius: 8px;
    }

    .form-label {
        font-size: 16px;
        color: #333;
        margin-bottom: 10px;
    }

    .form-control {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 12px;
        font-size: 15px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #0056b3;
        box-shadow: 0 0 0 0.2rem rgba(0, 86, 179, 0.25);
    }

    .btn-lg {
        padding: 12px 24px;
        font-size: 16px;
        font-weight: 500;
    }

    textarea.form-control {
        resize: vertical;
        min-height: 300px;
    }
</style>
@endsection
