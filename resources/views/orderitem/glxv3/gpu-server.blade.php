<?php
$uid = getCurrentUserId();
$siteId = \App\Models\SiteMng::getSiteId();
setLogFile("/var/glx/weblog/baokim_$siteId.log");
$params = request()->all();
$domain = \LadLib\Common\UrlHelper1::getDomainHostName();

?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

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


    <?php
$packages = [
    [
        'icon' => 'fas fa-microchip',
        'name' => 'GPU-2 x P40',
        'gpus' => '2x Nvidia Tesla P40 24G',
        'cpu' => '2x Intel Xeon 2667v4',
        'ram' => '128 GB',
        'storage' => '2TB SSD NVME',
        'cuda_cores' => '2 x 3840',
        'price' => 9000000,
        'period' => 'tháng',
        'link' => '#p40',
        'badge_type' => 'badge-info'
    ],
    [
        'icon' => 'fas fa-cube',
        'name' => 'GPU-2 x 3090',
        'gpus' => '2x Nvidia RTX 3090 24G',
        'cpu' => '2x Intel Xeon 2667v4',
        'ram' => '128 GB',
        'storage' => '2TB SSD NVME',
        'cuda_cores' => '2 x 10496',
        'price' => 20000000,
        'period' => 'tháng',
        'link' => '#3090-2',
        'badge_type' => 'badge-success',
        'popular' => true
    ],
    [
        'icon' => 'fas fa-cubes',
        'name' => 'GPU-4 x 3090',
        'gpus' => '4x Nvidia RTX 3090 24G',
        'cpu' => '2x Intel Xeon 2667v4',
        'ram' => '128 GB',
        'storage' => '2TB SSD NVME',
        'cuda_cores' => '4 x 10496',
        'price' => 35000000,
        'period' => 'tháng',
        'link' => '#3090-4',
        'badge_type' => 'badge-info'
    ],
    [
        'icon' => 'fas fa-fan',
        'name' => 'GPU-2 x 4090',
        'gpus' => '2x Nvidia RTX 4090 24GB',
        'cpu' => '2x Intel Xeon Gold 6133',
        'ram' => '256 GB',
        'storage' => '4TB SSD NVME',
        'cuda_cores' => 'Tensor Core',
        'price' => 35000000,
        'period' => 'tháng',
        'link' => '#4090-2',
        'badge_type' => 'badge-warning'
    ]
];
?>

<style>
    .pricing-header {
        overflow-x: auto;
    }
    .pricing-header > div {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    .pricing-header h2 {
        order: 2;
    }
    .pricing-header .badge:first-of-type {
        order: 1;
    }
    .pricing-header .badge:last-of-type {
        order: 3;
    }
    @media (max-width: 768px) {
        .pricing-header > div {
            flex-direction: column;
        }
        .pricing-header h2 {
            order: -1;
            font-size: 1.8rem !important;
            width: 100%;
            text-align: center;
            margin-bottom: 10px;
        }
        .pricing-header .badge {
            font-size: 0.75rem !important;
            padding: 6px 12px !important;
        }
    }
    .gpu-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 8px 16px;
        font-size: 0.9rem;
        border-radius: 20px;
    }
    .gpu-badge-secondary {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    .hover-shadow {
        transition: all 0.3s ease;
    }
    .hover-shadow:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2) !important;
        transform: translateY(-8px);
    }
</style>

<!-- Header with Badges -->
<div class="pricing-header mt-3 mb-3 text-orange" data-code-pos='gpu-server-header'>
    <div style="white-space: normal;">
        <span class="badge gpu-badge">
            <i class="fas fa-rocket" style="margin-right: 5px;"></i> Hiệu Suất Cao
        </span>
        <h2 class="text-orange mb-0" style="text-shadow: 5px 2px 4px rgba(0,0,0,0.2); font-weight: 800; font-size: 2.5rem;">
            GPU SERVER
        </h2>
        <span class="badge gpu-badge gpu-badge-secondary">
            <i class="fas fa-lightning-bolt" style="margin-right: 5px;"></i> Tối Ưu AI/ML
        </span>
    </div>
</div>

<!-- Features Table -->
<div class="container mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-1 shadow" style="box-shadow: 0 5px 9px rgba(0, 0, 0, 0.15) !important;">
                <div class="card-body">
                    <div class="row g-4">
                        <!-- Feature 1: High CUDA Performance -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-microchip" style="font-size: 2rem; color: #667eea;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 style="color: #333;">CUDA Performance</h5>
                                    <p style="color: #666; font-size: 0.9rem;">Nhiều CUDA cores cho tính toán song song siêu nhanh</p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 2: AI/ML Optimization -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-brain" style="font-size: 2rem; color: #764ba2;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 style="color: #333;">AI & Machine Learning</h5>
                                    <p style="color: #666; font-size: 0.9rem;">Tối ưu cho huấn luyện models, inference, và data processing</p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 3: 24/7 Support -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-headset" style="font-size: 2rem; color: #ff9800;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 style="color: #333;">24/7 Support</h5>
                                    <p style="color: #666; font-size: 0.9rem;">Hỗ trợ kỹ thuật chuyên nghiệp, sẵn sàng giúp bất cứ lúc nào</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- GPU Packages -->
<div style="font-size: 100%">
    <div class="container p-3">
        <div class="row g-4">
            @foreach($packages as $index => $pkg)
            <div class="col-md-6 col-lg-3 mb-8">
                <div class="card h-100 shadow-sm border-0 hover-shadow transition-all"
                     style="cursor: pointer; transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important; position: relative;">

                    @if($pkg['popular'] ?? false)
                    <span class="badge bg-success" style="position: absolute; top: 10px; right: 10px; z-index: 10;">
                        <i class="fas fa-star"></i> POPULAR
                    </span>
                    @endif

                    <!-- Card Header -->
                    <div class="card-header bg-gradient text-white"
                         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h2 class="mb-0 text-center" style="color: white;">
                            <i class="{{ $pkg['icon'] }}" style="margin-right: 8px;"></i>{{ $pkg['name'] }}
                        </h2>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="specs" data-code-pos='ppp17742211005741'>
                            <!-- GPUs -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    GPU
                                </span>
                                <span class="badge" style="background-color: #667eea; color: white; font-size: 0.6rem; word-break: break-word;">{{ $pkg['gpus'] }}</span>
                            </div>

                            <!-- CPU -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom" data-code-pos='ppp17742211057891'>
                                <span class="spec-label font-weight-500">
                                    CPU
                                </span>
                                <span class="badge" style="background-color: #764ba2; color: white; font-size: 0.6rem;">{{ $pkg['cpu'] }}</span>
                            </div>

                            <!-- RAM -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-memory" style="margin-right: 5px;"></i>RAM
                                </span>
                                <span class="badge" style="background-color: #ff9800; color: white;">{{ $pkg['ram'] }}</span>
                            </div>

                            <!-- Storage -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-database" style="margin-right: 5px;"></i>Storage
                                </span>
                                <span class="badge" style="background-color: #e74c3c; color: white;">{{ $pkg['storage'] }}</span>
                            </div>

                            <!-- CUDA Cores -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-bolt" style="margin-right: 5px;"></i>CUDA
                                </span>
                                <span class="badge" style="background-color: #27ae60; color: white;">{{ $pkg['cuda_cores'] }}</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="price-section mt-3 pt-3 text-center">
                            <p class="mb-0">
                                <span class="text-muted small">Giá:</span>
                                <span class="price-display text-danger font-weight-bold" style="font-size: 1.3em;">
                                    {{ number_format((int)$pkg['price']) }} VNĐ
                                </span>
                                <span class="text-muted small d-block" style="font-size: 0.85rem;">{{ $pkg['period'] }} (15% giảm 12 tháng)</span>
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-light border-0">
                        <a href="{{ $pkg['link'] }}" class="btn btn-primary w-100">
                            <i class="fas fa-shopping-cart"></i> Đăng ký
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

<!-- Additional Services Section -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                <div class="card-body p-5">
                    <h4 class="mb-4" style="color: #333; font-weight: 700;">
                        <i class="fas fa-plus-circle" style="color: #ff9800;"></i> Dịch Vụ Bổ Sung
                    </h4>

                    <p class="mb-4 text-muted">Mở rộng khả năng GPU Server với các dịch vụ bổ sung:</p>

                    <div class="row g-3">
                        <!-- Service 1: Extra GPU -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-1 hover-shadow" style="border-color: #667eea; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-cubes" style="font-size: 2rem; color: #667eea; margin-bottom: 10px;"></i>
                                    <h6 style="font-weight: 600; margin-bottom: 8px;">GPU Bổ Sung</h6>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Thêm GPU card để tăng hiệu suất</p>
                                    <p style="font-weight: bold; color: #667eea; font-size: 1.1rem;">Liên hệ</p>
                                </div>
                            </div>
                        </div>

                        <!-- Service 2: Extra RAM -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-1 hover-shadow" style="border-color: #764ba2; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-memory" style="font-size: 2rem; color: #764ba2; margin-bottom: 10px;"></i>
                                    <h6 style="font-weight: 600; margin-bottom: 8px;">RAM Upgrade</h6>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Nâng cấp RAM để xử lý dữ liệu lớn</p>
                                    <p style="font-weight: bold; color: #764ba2; font-size: 1.1rem;">Liên hệ</p>
                                </div>
                            </div>
                        </div>

                        <!-- Service 3: CUDA Optimization -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-1 hover-shadow" style="border-color: #ff9800; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-cogs" style="font-size: 2rem; color: #ff9800; margin-bottom: 10px;"></i>
                                    <h6 style="font-weight: 600; margin-bottom: 8px;">CUDA Optimization</h6>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Tối ưu hóa software cho GPU</p>
                                    <p style="font-weight: bold; color: #ff9800; font-size: 1.1rem;">Liên hệ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Information Section -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                <div class="card-body p-5">
                    <h4 class="mb-4" style="color: #333; font-weight: 700;">
                        <i class="fas fa-info-circle" style="color: #667eea;"></i> Thông Tin GPU Server
                    </h4>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 style="color: #333; font-weight: 600; margin-bottom: 12px;">
                                <i class="fas fa-check-circle" style="color: #27ae60; margin-right: 8px;"></i>Ứng Dụng
                            </h6>
                            <ul style="color: #666; font-size: 0.95rem; line-height: 1.8;">
                                <li>Huấn luyện AI Models (Deep Learning, Machine Learning)</li>
                                <li>Xử lý Big Data & Data Analytics</li>
                                <li>Video/Image Processing & Rendering</li>
                                <li>Scientific Computing & Simulation</li>
                                <li>Blockchain & Cryptocurrency Mining</li>
                            </ul>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h6 style="color: #333; font-weight: 600; margin-bottom: 12px;">
                                <i class="fas fa-star" style="color: #ff9800; margin-right: 8px;"></i>Đặc Điểm
                            </h6>
                            <ul style="color: #666; font-size: 0.95rem; line-height: 1.8;">
                                <li>GPU Nvidia chính hãng, hiệu suất cao</li>
                                <li>Hỗ trợ CUDA, cuDNN, TensorFlow, PyTorch</li>
                                <li>Bandwidth cao cho tốc độ truyền dữ liệu nhanh</li>
                                <li>NVLink support (trên các model cao cấp)</li>
                                <li>Uptime 99.9%, SLA guarantee</li>
                            </ul>
                        </div>
                    </div>

                    <hr style="margin: 20px 0;">

                    <h6 style="color: #333; font-weight: 600; margin-bottom: 12px;">
                        <i class="fas fa-lightbulb" style="color: #f39c12; margin-right: 8px;"></i>Công Nghệ & Hỗ Trợ
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #667eea;">
                                <p style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    <i class="fas fa-cogs" style="margin-right: 5px;"></i>Pre-installed Software
                                </p>
                                <p style="color: #666; font-size: 0.9rem;">CUDA Toolkit, cuDNN, Docker, Jupyter Hub</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #764ba2;">
                                <p style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    <i class="fas fa-lock" style="margin-right: 5px;"></i>Security
                                </p>
                                <p style="color: #666; font-size: 0.9rem;">Firewall, DDoS Protection, Private Network</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #ff9800;">
                                <p style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    <i class="fas fa-headset" style="margin-right: 5px;"></i>Support
                                </p>
                                <p style="color: #666; font-size: 0.9rem;">Technical Support, Setup & Configuration</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- FAQ Section -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                <div class="card-body p-5">
                    <h4 class="mb-4" style="color: #333; font-weight: 700;">
                        <i class="fas fa-question-circle" style="color: #764ba2;"></i> Câu Hỏi Thường Gặp
                    </h4>

                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ 1 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                    GPU nào tốt nhất cho Deep Learning?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Nvidia RTX 4090 và RTX 3090 là lựa chọn tốt cho Deep Learning với CUDA cores cao. A100 80GB/40GB là lựa chọn enterprise cho production. Lựa chọn phụ thuộc vào model size và budget của bạn.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Có hỗ trợ multi-GPU training không?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Có, tất cả GPU Server support multi-GPU training. Chúng tôi cung cấp các package có 2, 4, 8 GPU cards với NVLink support trên các model cao cấp để tăng bandwidth liên GPU.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Framework nào được hỗ trợ?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Tất cả framework chính đều được hỗ trợ: TensorFlow, PyTorch, MXNet, Caffe, MXCAI, v.v. Chúng tôi cung cấp Docker containers pre-configured cho dễ setup và deployment.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Có contract tối thiểu không?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Không có contract tối thiểu. Bạn có thể thuê theo tháng hoặc được giảm giá 15% nếu thanh toán 12 tháng trước. Linh hoạt cho phép tạm ngừng và phục hồi bất cứ lúc nào.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Có hỗ trợ migration từ on-premise không?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Có, team kỹ thuật của chúng tôi giúp migrate data, setup environment, và optimize configuration từ on-premise hoặc cloud provider khác. Hỗ trợ miễn phí cho khách hàng.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    Có giảm giá cho dài hạn không?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Có! Chúng tôi cung cấp 15% giảm giá khi bạn thanh toán 12 tháng trước. Liên hệ với team sales cho thương lượng về volume lớn hoặc commitment dài hạn.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="container mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                <div class="card-body text-center p-5">
                    <h3 class="mb-2" style="font-weight: 700;">Sẵn sàng để accelerate AI/ML projects?</h3>
                    <p class="mb-4" style="font-size: 1.05rem;">Chọn GPU Server phù hợp với nhu cầu của bạn</p>
                    <a href="#packages" class="btn btn-light btn-lg">
                        <i class="fas fa-rocket"></i> Xem Gói GPU
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</div>

{{--    @include("orderitem.glxv3.css-js")--}}

@endsection
