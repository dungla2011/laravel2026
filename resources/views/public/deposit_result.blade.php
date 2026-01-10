@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
    Kết Quả Thanh Toán - Nạp Tiền
@endsection

@section("css")
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    .result-container {
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
    }
    .alert-custom {
        border-radius: 15px;
        padding: 15px 20px;
        font-size: 1.1rem;
    }
    .info-box {
        background: #f8f9fa;
        border-radius: 15px;
        padding: 20px;
        margin: 25px 0;
    }
    .amount-display {
        font-size: 2.5rem;
        font-weight: bold;
        color: #667eea;
        margin: 30px 0;
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
    }
</style>
@endsection

@section("content")
<div class="result-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="content-card text-center">
                    @if($result['success'])
                        @php
                            $data = $result['data'];
                            $orderID = $data['order_id'];
                            $total_amount = $data['amount'];
                        @endphp
                        
                        @if($result['duplicate'] ?? false)
                            <div class="icon-large">
                                <i class="fas fa-info-circle text-warning"></i>
                            </div>
                            <h2 class="mb-4">Giao Dịch Đã Xử Lý</h2>
                            <div class="alert alert-warning alert-custom">
                                <i class="fas fa-exclamation-triangle"></i> 
                                Giao dịch này đã được xử lý trước đó
                            </div>
                        @else
                            <div class="icon-large">
                                <i class="fas fa-check-circle text-success"></i>
                            </div>
                            <h2 class="mb-4">Nạp Tiền Thành Công!</h2>
                            <div class="alert alert-success alert-custom">
                                <i class="fas fa-check"></i> 
                                Giao dịch của bạn đã được xử lý thành công
                            </div>
                        @endif
                        
                        <div class="info-box">
                            <div class="row">
                                <div class="col-md-6 text-start">
                                    <strong><i class="fas fa-receipt"></i> Mã đơn hàng:</strong>
                                </div>
                                <div class="col-md-6 text-end">
                                    {{ $orderID }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="amount-display">
                            <i class="fas fa-coins"></i> 
                            {{ number_format($total_amount, 0, ',', '.') }} VNĐ
                        </div>
                        
                        <div class="mt-4">
                            <a href="/" class="btn-primary-custom">
                                <i class="fas fa-home"></i> Trở Lại Trang Chủ
                            </a>
                        </div>
                        
                    @else
                        <div class="icon-large">
                            <i class="fas fa-times-circle text-danger"></i>
                        </div>
                        <h2 class="mb-4">Có Lỗi Xảy Ra</h2>
                        <div class="alert alert-danger alert-custom">
                            <i class="fas fa-exclamation-circle"></i> 
                            {{ $result['message'] }}
                        </div>
                        <div class="mt-4">
                            <a href="/deposit" class="btn-primary-custom">
                                <i class="fas fa-arrow-left"></i> Thử Lại
                            </a>
                        </div>
                    @endif
                    
                    @if(function_exists('isDebugIp') && isDebugIp())
                    <div class="debug-info mt-4 text-start">
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
