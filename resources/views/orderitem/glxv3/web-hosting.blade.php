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
        'icon' => 'fas fa-server',
        'name' => 'NVMe Hosting - 01',
        'storage' => '6 GB',
        'cpu' => '2 Core',
        'ram' => '2 GB',
        'processor' => 'Intel Xeon Gold',
        'bandwidth' => 'Unlimited',
        'databases' => 'Unlimited',
        'price' => 70000,
        'period' => 'tháng',
        'link' => '#nvme-01',
        'badge_type' => 'badge-info'
    ],
    [
        'icon' => 'fas fa-hdd',
        'name' => 'NVMe Hosting - 02',
        'storage' => '10 GB',
        'cpu' => '2 Core',
        'ram' => '3 GB',
        'processor' => 'Intel Xeon Gold',
        'bandwidth' => 'Unlimited',
        'databases' => 'Unlimited',
        'price' => 120000,
        'period' => 'tháng',
        'link' => '#nvme-02',
        'badge_type' => 'badge-info'
    ],
    [
        'icon' => 'fas fa-database',
        'name' => 'NVMe Hosting - 03',
        'storage' => '15 GB',
        'cpu' => '2 Core',
        'ram' => '4 GB',
        'processor' => 'Intel Xeon Gold',
        'bandwidth' => 'Unlimited',
        'databases' => 'Unlimited',
        'price' => 170000,
        'period' => 'tháng',
        'link' => '#nvme-03',
        'badge_type' => 'badge-warning'
    ],
    [
        'icon' => 'fas fa-layer-group',
        'name' => 'NVMe Hosting - 04',
        'storage' => '35 GB',
        'cpu' => '4 Core',
        'ram' => '4 GB',
        'processor' => 'Intel Xeon Platinum',
        'bandwidth' => 'Unlimited',
        'databases' => 'Unlimited',
        'price' => 500000,
        'period' => 'tháng',
        'link' => '#nvme-04',
        'badge_type' => 'badge-success',
        'popular' => true
    ]
];

$packages_turbo = [
    [
        'icon' => 'fas fa-bolt',
        'name' => 'NVMe Turbo - 01',
        'storage' => '6 GB',
        'cpu' => '2 Core',
        'ram' => '2 GB',
        'processor' => 'Intel Xeon Gold',
        'bandwidth' => 'Unlimited',
        'inode' => 'Unlimited',
        'price' => 80000,
        'period' => 'tháng',
        'link' => '#turbo-01',
        'badge_type' => 'badge-info'
    ],
    [
        'icon' => 'fas fa-zap',
        'name' => 'NVMe Turbo Business - 100GB',
        'storage' => '100 GB',
        'cpu' => '10 Core',
        'ram' => '12 GB',
        'processor' => 'Intel Xeon Gold',
        'bandwidth' => 'Unlimited',
        'inode' => 'Unlimited',
        'price' => 1500000,
        'period' => 'tháng',
        'link' => '#turbo-business',
        'badge_type' => 'badge-danger'
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
    .hosting-badge {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 8px 16px;
        font-size: 0.9rem;
        border-radius: 20px;
    }
    .hosting-badge-secondary {
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
<div class="pricing-header mt-3 mb-3 text-orange" data-code-pos='web-hosting-header'>
    <div style="white-space: normal;">
        <span class="badge hosting-badge">
            <i class="fas fa-globe" style="margin-right: 5px;"></i> Tốc Độ Cao
        </span>
        <h2 class="text-orange mb-0" style="text-shadow: 5px 2px 4px rgba(0,0,0,0.2); font-weight: 800; font-size: 2.5rem;">
            WEB HOSTING
        </h2>
        <span class="badge hosting-badge hosting-badge-secondary">
            <i class="fas fa-leaf" style="margin-right: 5px;"></i> NVMe SSD
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
                        <!-- Feature 1: NVMe SSD Storage -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-hdd" style="font-size: 2rem; color: #667eea;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 style="color: #333;">NVMe SSD Storage</h5>
                                    <p style="color: #666; font-size: 0.9rem;">Lưu trữ tốc độ cao với công nghệ NVMe U.2 mới nhất</p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 2: Unlimited Bandwidth -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-wifi" style="font-size: 2rem; color: #764ba2;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 style="color: #333;">Bandwidth Unlimited</h5>
                                    <p style="color: #666; font-size: 0.9rem;">Lưu lượng truy cập không giới hạn, khả năng mở rộng tự do</p>
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
                                    <p style="color: #666; font-size: 0.9rem;">Hỗ trợ kỹ thuật chuyên nghiệp 24/7, sẵn sàng giúp đỡ ngay lập tức</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Section 1: Standard NVMe Hosting Plans -->
<div style="font-size: 100%">
    <div class="container p-3">
        <h4 class="mb-4" style="color: #333; font-weight: 700; border-bottom: 3px solid #667eea; padding-bottom: 10px;">
            <i class="fas fa-layer-group" style="color: #667eea;"></i> Gói NVMe Hosting Standard
        </h4>
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
                        <div class="specs">
                            <!-- Processor -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-microchip" style="margin-right: 5px;"></i>Processor
                                </span>
                                <span class="badge" style="background-color: #667eea; color: white; font-size: 0.75rem;">{{ $pkg['processor'] }}</span>
                            </div>

                            <!-- Storage -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-database" style="margin-right: 5px;"></i>Storage
                                </span>
                                <span class="badge" style="background-color: #764ba2; color: white;">{{ $pkg['storage'] }}</span>
                            </div>

                            <!-- CPU -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-processor" style="margin-right: 5px;"></i>CPU
                                </span>
                                <span class="badge" style="background-color: #ff9800; color: white;">{{ $pkg['cpu'] }}</span>
                            </div>

                            <!-- RAM -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-memory" style="margin-right: 5px;"></i>RAM
                                </span>
                                <span class="badge" style="background-color: #e74c3c; color: white;">{{ $pkg['ram'] }}</span>
                            </div>

                            <!-- Bandwidth -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-wifi" style="margin-right: 5px;"></i>Bandwidth
                                </span>
                                <span class="badge" style="background-color: #27ae60; color: white;">{{ $pkg['bandwidth'] }}</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="price-section mt-3 pt-3 text-center">
                            <p class="mb-0">
                                <span class="text-muted small">Giá:</span>
                                <span class="price-display text-danger font-weight-bold" style="font-size: 1.3em;">
                                    {{ number_format((int)$pkg['price']) }} VNĐ
                                </span>
                                <span class="text-muted small d-block" style="font-size: 0.85rem;">{{ $pkg['period'] }}</span>
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

<!-- Section 2: NVMe Turbo Plans -->
<div style="font-size: 100%; margin-top: 40px;">
    <div class="container p-3">
        <h4 class="mb-4" style="color: #333; font-weight: 700; border-bottom: 3px solid #ff9800; padding-bottom: 10px;">
            <i class="fas fa-bolt" style="color: #ff9800;"></i> Gói NVMe Turbo (Không Giới Hạn Inode)
        </h4>
        <div class="row g-4">
            @foreach($packages_turbo as $index => $pkg)
            <div class="col-md-6 col-lg-6 mb-8">
                <div class="card h-100 shadow-sm border-0 hover-shadow transition-all"
                     style="cursor: pointer; transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">

                    <!-- Card Header -->
                    <div class="card-header bg-gradient text-white"
                         style="background: linear-gradient(135deg, #ff9800 0%, #f39c12 100%);">
                        <h2 class="mb-0 text-center" style="color: white;">
                            <i class="{{ $pkg['icon'] }}" style="margin-right: 8px;"></i>{{ $pkg['name'] }}
                        </h2>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="specs">
                            <!-- Processor -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-microchip" style="margin-right: 5px;"></i>Processor
                                </span>
                                <span class="badge" style="background-color: #667eea; color: white; font-size: 0.75rem;">{{ $pkg['processor'] }}</span>
                            </div>

                            <!-- Storage -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-database" style="margin-right: 5px;"></i>Storage
                                </span>
                                <span class="badge" style="background-color: #764ba2; color: white;">{{ $pkg['storage'] }}</span>
                            </div>

                            <!-- CPU -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-processor" style="margin-right: 5px;"></i>CPU
                                </span>
                                <span class="badge" style="background-color: #ff9800; color: white;">{{ $pkg['cpu'] }}</span>
                            </div>

                            <!-- RAM -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-memory" style="margin-right: 5px;"></i>RAM
                                </span>
                                <span class="badge" style="background-color: #e74c3c; color: white;">{{ $pkg['ram'] }}</span>
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="price-section mt-3 pt-3 text-center">
                            <p class="mb-0">
                                <span class="text-muted small">Giá:</span>
                                <span class="price-display text-danger font-weight-bold" style="font-size: 1.3em;">
                                    {{ number_format((int)$pkg['price']) }} VNĐ
                                </span>
                                <span class="text-muted small d-block" style="font-size: 0.85rem;">{{ $pkg['period'] }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-light border-0">
                        <a href="{{ $pkg['link'] }}" class="btn btn-warning w-100">
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

                    <p class="mb-4 text-muted">Nâng cấp và mở rộng dịch vụ web hosting của bạn:</p>

                    <div class="row g-3">
                        <!-- Service 1: SSL Certificate -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-1 hover-shadow" style="border-color: #667eea; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-lock" style="font-size: 2rem; color: #667eea; margin-bottom: 10px;"></i>
                                    <h6 style="font-weight: 600; margin-bottom: 8px;">SSL Certificate</h6>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Bảo mật trang web với SSL/TLS encryption</p>
                                    <p style="font-weight: bold; color: #667eea; font-size: 1.1rem;">100,000 VNĐ/năm</p>
                                </div>
                            </div>
                        </div>

                        <!-- Service 2: Daily Backup -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-1 hover-shadow" style="border-color: #764ba2; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-shield-alt" style="font-size: 2rem; color: #764ba2; margin-bottom: 10px;"></i>
                                    <h6 style="font-weight: 600; margin-bottom: 8px;">Daily Backup</h6>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Sao lưu dữ liệu hàng ngày, bảo vệ tối ưu</p>
                                    <p style="font-weight: bold; color: #764ba2; font-size: 1.1rem;">50,000 VNĐ/tháng</p>
                                </div>
                            </div>
                        </div>

                        <!-- Service 3: Domain Registration -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-1 hover-shadow" style="border-color: #ff9800; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-globe" style="font-size: 2rem; color: #ff9800; margin-bottom: 10px;"></i>
                                    <h6 style="font-weight: 600; margin-bottom: 8px;">Domain Registration</h6>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Đăng ký domain .com, .vn, v.v...</p>
                                    <p style="font-weight: bold; color: #ff9800; font-size: 1.1rem;">90,000 VNĐ/năm</p>
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
                        <i class="fas fa-info-circle" style="color: #667eea;"></i> Thông Tin Web Hosting
                    </h4>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <h6 style="color: #333; font-weight: 600; margin-bottom: 12px;">
                                <i class="fas fa-check-circle" style="color: #27ae60; margin-right: 8px;"></i>Ứng Dụng
                            </h6>
                            <ul style="color: #666; font-size: 0.95rem; line-height: 1.8;">
                                <li>Website corporate, blog cá nhân</li>
                                <li>Thương mại điện tử, online store</li>
                                <li>Ứng dụng web PHP, Python, Node.js</li>
                                <li>Quản lý nội dung WordPress, Joomla</li>
                                <li>API server, RESTful web services</li>
                            </ul>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h6 style="color: #333; font-weight: 600; margin-bottom: 12px;">
                                <i class="fas fa-star" style="color: #ff9800; margin-right: 8px;"></i>Đặc Điểm
                            </h6>
                            <ul style="color: #666; font-size: 0.95rem; line-height: 1.8;">
                                <li>NVMe SSD tốc độ cao - 6x nhanh hơn HDD</li>
                                <li>CPU mạnh từ Intel Xeon Gold/Platinum</li>
                                <li>Bandwidth không giới hạn</li>
                                <li>Database MySQL/PostgreSQL unlimited</li>
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
                                    <i class="fas fa-cogs" style="margin-right: 5px;"></i>Control Panel
                                </p>
                                <p style="color: #666; font-size: 0.9rem;">cPanel/Plesk, PHP/MySQL Manager, SSH Access</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #764ba2;">
                                <p style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    <i class="fas fa-code" style="margin-right: 5px;"></i>Server Scripting
                                </p>
                                <p style="color: #666; font-size: 0.9rem;">PHP, Python, Node.js, Ruby on Rails</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #ff9800;">
                                <p style="font-weight: 600; color: #333; margin-bottom: 8px;">
                                    <i class="fas fa-headset" style="margin-right: 5px;"></i>Support
                                </p>
                                <p style="color: #666; font-size: 0.9rem;">Technical Support, Migration Service</p>
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
                                    NVMe SSD có nhanh hơn HDD thông thường bao nhiêu?
                                </button>
                            </h2>
                            <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    NVMe SSD nhanh gấp 6-10 lần so với HDD thông thường. Tốc độ đọc ghi lên đến 3000-7000 MB/s, giúp website tải nhanh hơn, cải thiện SEO và trải nghiệm người dùng.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                    Có hỗ trợ WordPress/PHP/MySQL không?
                                </button>
                            </h2>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Có, đầy đủ hỗ trợ PHP 7.4, 8.0, 8.1, 8.2 và MySQL 5.7, 8.0. Cài đặt WordPress, Joomla, Drupal, Magento chỉ cần vài click. Có cPanel/Plesk để quản lý dễ dàng.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                    Có thể nâng cấp hoặc hạ cấp gói không?
                                </button>
                            </h2>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Có, bạn có thể nâng cấp hoặc hạ cấp gói bất cứ lúc nào. Phí nâng cấp sẽ được tính theo tỷ lệ, không có phí hủy hoặc hạ cấp.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                    Có tự động backup không?
                                </button>
                            </h2>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Gói cơ bản không bao gồm backup. Để có backup hàng ngày tự động, bạn có thể thêm dịch vụ Daily Backup với giá 50,000 VNĐ/tháng hoặc tự backup qua SSH/FTP.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5">
                                    Có giới hạn website bao nhiêu?
                                </button>
                            </h2>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Không giới hạn số lượng website. Bạn có thể host bao nhiêu domain tuỳ thích miễn là không vượt quá dung lượng storage và tài nguyên CPU/RAM của gói.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6">
                                    Có miễn phí migration từ host cũ không?
                                </button>
                            </h2>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.95rem; color: #666;">
                                    Có, chúng tôi cung cấp dịch vụ migration miễn phí. Team kỹ thuật sẽ giúp bạn transfer website, database, email từ host cũ mà không gây downtime.
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
                    <h3 class="mb-2" style="font-weight: 700;">Sẵn sàng để host website của bạn?</h3>
                    <p class="mb-4" style="font-size: 1.05rem;">Chọn gói web hosting phù hợp với nhu cầu kinh doanh của bạn</p>
                    <a href="#packages" class="btn btn-light btn-lg">
                        <i class="fas fa-rocket"></i> Xem Gói Hosting
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
