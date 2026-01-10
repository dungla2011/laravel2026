@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    Xác Nhận Thanh Toán - Nạp Tiền
@endsection

@section("css")
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .confirm-container {
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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
    .info-box {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 30px;
        margin: 25px 0;
    }
    .amount-display {
        font-size: 3rem;
        font-weight: bold;
        color: #667eea;
        margin: 20px 0;
    }
    .btn-primary-custom {
        display: inline-block;
        padding: 18px 50px;
        font-size: 1.3rem;
        font-weight: bold;
        border-radius: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .btn-primary-custom:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        color: white;
    }
    .alert-custom {
        border-radius: 15px;
        padding: 15px 20px;
        font-size: 1rem;
    }
    .debug-info {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        font-size: 0.85rem;
    }
</style>
@endsection

@section("content")
<div class="confirm-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="content-card text-center my-5">

                    <h2 class="mb-4">Xác Nhận Nạp Tiền</h2>

                    <div class="info-box">
                        <p class="mb-2"><strong>Bạn đang nạp:</strong></p>
                        <div class="amount-display">
                            {{ $total_amountV }} VNĐ
                        </div>
                        <p class="text-muted mb-0">vào tài khoản của bạn</p>
                    </div>

                    @if(!auth()->id())
                        <div class="alert alert-warning alert-custom">
                            <i class="fas fa-exclamation-triangle"></i>
                            Bạn cần <a href="/login" class="alert-link">Đăng nhập</a> để nạp tiền
                        </div>
                    @else
                        <div class="mt-4">
                            <a href="{{ $linkBK }}" class="btn-primary btn-lg">
                                <i class="fas fa-credit-card"></i> Tiếp Tục Thanh Toán
                            </a>
                        </div>
                        <p class="text-muted mt-3">
                            <small><i class="fas fa-shield-alt"></i> Thanh toán an toàn qua BaoKim</small>
                        </p>
                    @endif

                    @if(function_exists('isDebugIp') && isDebugIp())
                    <div class="debug-info mt-4 text-start" style="display: none">
                        <strong>Debug Info:</strong>
                        <pre>{{ print_r($params, true) }}</pre>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
