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

@section('content')
<div class="container mt-5">
    <br>
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-danger shadow-lg">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-circle mr-2"></i> Error Sending Email
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger mb-4" role="alert">
                        <i class="fas fa-times mr-2"></i>
                        <strong>Failed!</strong> {{ $message }}
                    </div>

                    <div class="info-box">
                        <p class="mb-2">
                            <strong>Email Address:</strong> <code>{{ $user->email }}</code>
                        </p>
                        <p class="mb-2">
                            <strong>Attempted At:</strong> <code>{{ now()->format('Y-m-d H:i:s') }}</code>
                        </p>
                        <p class="mb-0">
                            <strong>User:</strong> <code>{{ $user->name ?? $user->id }}</code>
                        </p>
                    </div>

                    <div class="alert alert-info mt-4" role="alert">
                        <strong>Troubleshooting:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Check your email address is correct</li>
                            <li>Verify your email account settings</li>
                            <li>Try again later if there's a temporary server issue</li>
                            <li>Contact support if the problem persists</li>
                        </ul>
                    </div>

                    <hr>

                    <div class="mt-4">
                        <a href="{{ $report_url }}" class="btn btn-primary mr-2">
                            <i class="fas fa-arrow-left mr-2"></i> Back to Report
                        </a>
                        <a href="{{ route('vps.billing.report') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home mr-2"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .info-box {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #dc3545;
    }
    
    code {
        background-color: #e9ecef;
        padding: 3px 6px;
        border-radius: 3px;
        color: #495057;
    }
</style>
@endsection
