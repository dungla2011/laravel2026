@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    Lỗi - Nạp Tiền
@endsection

@section("css")
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .error-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 80vh;
        padding: 60px 0;
    }
    .content-card {
        background: white;
        border-radius: 20px;
        padding: 40px;
        box-shadow: 0 15px 40px rgba(0,0,0,0.3);
    }
    .icon-large {
        font-size: 5rem;
        margin-bottom: 20px;
        color: #dc3545;
    }
    .alert-custom {
        border-radius: 15px;
        padding: 20px;
        font-size: 1.1rem;
    }
    .btn-primary-custom {
        display: inline-block;
        padding: 15px 40px;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
        margin: 10px;
    }
    .btn-primary-custom:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .debug-info {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        font-size: 0.85rem;
        margin-top: 20px;
    }
</style>
@endsection

@section("content")
<div class="error-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="content-card text-center">
                    <div class="icon-large">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h2 class="mb-4">Có Lỗi Xảy Ra</h2>
                    <div class="alert alert-danger alert-custom">
                        <i class="fas fa-exclamation-circle"></i> 
                        {{ $message }}
                    </div>
                    
                    <div class="mt-4">
                        <a href="/deposit" class="btn-primary-custom">
                            <i class="fas fa-arrow-left"></i> Thử Lại
                        </a>
                        <a href="/" class="btn-primary-custom">
                            <i class="fas fa-home"></i> Trang Chủ
                        </a>
                    </div>
                    
                    @if(function_exists('isDebugIp') && isDebugIp() && isset($trace))
                    <div class="debug-info text-start">
                        <strong>Stack Trace:</strong>
                        <pre>{{ $trace }}</pre>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
