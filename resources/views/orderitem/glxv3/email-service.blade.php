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
        'icon' => 'fas fa-envelope-open-text',
        'name' => 'MV-SV-01',
        'users' => 2,
        'storage' => '2GB',
        'price' => 108000,
        'period' => '6 tháng',
        'link' => '#sv-01',
        'badge_type' => 'badge-info'
    ],
    [
        'icon' => 'fas fa-envelope',
        'name' => 'MV-SV-02',
        'users' => 10,
        'storage' => '5GB',
        'price' => 270000,
        'period' => '6 tháng',
        'link' => '#sv-02',
        'badge_type' => 'badge-info'
    ],
    [
        'icon' => 'fas fa-inbox',
        'name' => 'MV-SV-03',
        'users' => 20,
        'storage' => '10GB',
        'price' => 540000,
        'period' => '6 tháng',
        'link' => '#sv-03',
        'badge_type' => 'badge-info'
    ],
    [
        'icon' => 'fas fa-mail-bulk',
        'name' => 'MV-SV-04',
        'users' => 50,
        'storage' => '20GB',
        'price' => 960000,
        'period' => '6 tháng',
        'link' => '#sv04',
        'badge_type' => 'badge-warning',
        'popular' => false
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
</style>

<!-- Header with Badges -->
<div class="pricing-header mt-3 mb-3 text-orange" data-code-pos='email-service-header'>
    <div style="white-space: normal;">
        <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 8px 16px; font-size: 0.9rem; border-radius: 20px;">
            <i class="fas fa-envelope" style="margin-right: 5px;"></i> Bảo Mật Cao
        </span>
        <h2 class="text-orange mb-0" style="text-shadow: 5px 2px 4px rgba(0,0,0,0.2); font-weight: 800; font-size: 2.5rem;">
            EMAIL SERVER
        </h2>
        <span class="badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 8px 16px; font-size: 0.9rem; border-radius: 20px;">
            <i class="fas fa-check-circle" style="margin-right: 5px;"></i> Chuyên Nghiệp
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
                        <!-- Feature 1: IMAP/POP -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-envelope" style="font-size: 2rem; color: #667eea;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 style="color: #333;">IMAP/POP Protocol</h5>
                                    <p style="color: #666; font-size: 0.9rem;">Hỗ trợ đầy đủ IMAP và POP3, tương thích mọi email client</p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 2: Antispam -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-shield-alt" style="font-size: 2rem; color: #764ba2;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 style="color: #333;">Antispam & Antivirus</h5>
                                    <p style="color: #666; font-size: 0.9rem;">Công nghệ lọc spam và virus hiện đại, bảo vệ toàn diện</p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 3: Webmail -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-globe" style="font-size: 2rem; color: #ff9800;"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h5 style="color: #333;">Webmail Interface</h5>
                                    <p style="color: #666; font-size: 0.9rem;">Giao diện webmail hiện đại, dễ sử dụng trên mọi thiết bị</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Packages -->
<div style="font-size: 100%">
    <div class="container p-3">
        <div class="row g-4">
            @foreach($packages as $index => $pkg)
            <div class="col-md-6 col-lg-3 mb-8">
                <div class="card h-100 shadow-sm border-0 hover-shadow transition-all"
                     style="cursor: pointer; transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
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
                            <!-- Users -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-users" style="margin-right: 5px;"></i>Users
                                </span>
                                <span class="badge" style="background-color: #667eea; color: white;">{{ $pkg['users'] }}</span>
                            </div>

                            <!-- Storage -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-database" style="margin-right: 5px;"></i>Dung lượng
                                </span>
                                <span class="badge" style="background-color: #764ba2; color: white;">{{ $pkg['storage'] }}</span>
                            </div>

                            <!-- Features -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2">
                                <span class="spec-label font-weight-500">
                                    <i class="fas fa-check-circle" style="margin-right: 5px;"></i>Tính năng
                                </span>
                                <span class="badge" style="background-color: #ff9800; color: white;">Đầy đủ</span>
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

<!-- Additional Services Section -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                <div class="card-body p-5">
                    <h4 class="mb-4" style="color: #333; font-weight: 700;">
                        <i class="fas fa-plus-circle" style="color: #ff9800;"></i> Dịch Vụ Bổ Sung
                    </h4>

                    <p class="mb-4 text-muted">Mở rộng tính năng email server với các dịch vụ bổ sung:</p>

                    <div class="row g-3">
                        <!-- Service 1: Clean IP Relay -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-1 hover-shadow" style="border-color: #667eea; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-network-wired" style="font-size: 2rem; color: #667eea; margin-bottom: 10px;"></i>
                                    <h6 style="font-weight: 600; margin-bottom: 8px;">Clean IP Relay</h6>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">IP sạch để gửi email nội bộ</p>
                                    <p style="font-weight: bold; color: #667eea; font-size: 1.1rem;">100,000 VNĐ/tháng</p>
                                </div>
                            </div>
                        </div>

                        <!-- Service 2: Extra Storage -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-1 hover-shadow" style="border-color: #764ba2; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-hdd" style="font-size: 2rem; color: #764ba2; margin-bottom: 10px;"></i>
                                    <h6 style="font-weight: 600; margin-bottom: 8px;">Thêm Dung Lượng</h6>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Mỗi 10GB storage bổ sung</p>
                                    <p style="font-weight: bold; color: #764ba2; font-size: 1.1rem;">50,000 VNĐ/tháng</p>
                                </div>
                            </div>
                        </div>

                        <!-- Service 3: Extra Users -->
                        <div class="col-md-6 col-lg-4">
                            <div class="card border-1 hover-shadow" style="border-color: #ff9800; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <i class="fas fa-user-plus" style="font-size: 2rem; color: #ff9800; margin-bottom: 10px;"></i>
                                    <h6 style="font-weight: 600; margin-bottom: 8px;">Thêm User</h6>
                                    <p style="font-size: 0.85rem; color: #666; margin-bottom: 10px;">Mỗi user bổ sung</p>
                                    <p style="font-weight: bold; color: #ff9800; font-size: 1.1rem;">20,000 VNĐ/tháng</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Server Information Section -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                <div class="card-body p-5">
                    <h4 class="mb-4" style="color: #333; font-weight: 700;">
                        <i class="fas fa-info-circle" style="color: #667eea;"></i> Email Server là gì?
                    </h4>

                    <p class="lead mb-3">
                        <strong>Email Server</strong> là một máy chủ chuyên dụng để quản lý, lưu trữ và phân phối email cho doanh nghiệp của bạn.
                        Nó cho phép bạn có domain email riêng (@tencongty.vn) với đầy đủ tính năng quản lý.
                    </p>

                    <p class="mb-3">
                        Email Server cung cấp các giải pháp email chuyên nghiệp:
                    </p>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Domain Email Riêng</strong> - Tạo email với domain của công ty bạn
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Bảo Mật Cao</strong> - Encryption, spam filter, virus protection
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Dễ Quản Lý</strong> - Webmail interface thân thiện
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Đa Nền Tảng</strong> - Hỗ trợ IMAP/POP, tương thích mọi client
                        </li>
                    </ul>

                    <p class="mb-3" style="font-weight: 500;">
                        <i class="fas fa-rocket" style="color: #667eea;"></i> <strong>Ứng dụng của Email Server:</strong>
                    </p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Doanh nghiệp nhỏ, startup</strong> - Email team với domain riêng
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Marketing campaigns</strong> - Gửi email từ domain công ty
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Newsletter, thông báo</strong> - Quản lý mailing list
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Giao tiếp nội bộ</strong> - Email communication system
                        </li>
                    </ul>

                    <p class="mb-3" style="font-weight: 500;">
                        <i class="fas fa-trophy" style="color: #f5576c;"></i> <strong>Lợi thế Email Server:</strong>
                    </p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Email chuyên nghiệp</strong> - @company domain thay vì @gmail
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Không giới hạn đúng quy định</strong> - Nâng cấp dung lượng dễ dàng
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Bảo mật doanh nghiệp</strong> - Toàn quyền kiểm soát dữ liệu
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Backup & Recovery</strong> - Hỗ trợ backup định kỳ
                        </li>
                    </ul>

                    <div class="alert alert-info border-0" style="background: #e7f3ff; border-left: 4px solid #667eea;">
                        <i class="fas fa-server"></i> <strong>Hạ Tầng:</strong><br>
                        <span class="mt-2 d-block">
                            Email server của chúng tôi được đặt tại các datacenter lớn
                            <strong>(FPT, Viettel, VNPT, ...)</strong>
                            với uptime 99.9%, backup hàng ngày, và hỗ trợ 24/7.
                        </span>
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
                        <i class="fas fa-question-circle" style="color: #667eea;"></i> Câu Hỏi Thường Gặp
                    </h4>

                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ 1 -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="font-size: 0.95rem; font-weight: 500;">
                                    Tôi có thể sử dụng email server cho bao nhiêu người?
                                </button>
                            </div>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Tùy theo gói bạn chọn. Gói MV-SV-01 hỗ trợ 2 users, MV-SV-02 hỗ trợ 10 users, MV-SV-03 hỗ trợ 20 users, và MV-SV-04 hỗ trợ 50 users. Bạn có thể nâng cấp hoặc thêm user bổ sung bất cứ lúc nào.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2 -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="font-size: 0.95rem; font-weight: 500;">
                                    Có thể tích hợp với client email nào?
                                </button>
                            </div>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Email server hỗ trợ IMAP/POP3, tương thích với mọi email client: Outlook, Thunderbird, Apple Mail, Gmail app, v.v. Bạn cũng có thể truy cập webmail trực tiếp từ trình duyệt.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3 -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="font-size: 0.95rem; font-weight: 500;">
                                    Dữ liệu email có được backup không?
                                </button>
                            </div>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Có. Tất cả email được backup hàng ngày và lưu trữ an toàn. Trong trường hợp mất dữ liệu, chúng tôi có thể khôi phục từ bản backup.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4 -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="font-size: 0.95rem; font-weight: 500;">
                                    Spam filter hoạt động thế nào?
                                </button>
                            </div>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Hệ thống antispam của chúng tôi sử dụng công nghệ học máy để phân loại email spam, virus, phishing. Mỗi user có thể cấu hình whitelist/blacklist theo nhu cầu.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5 -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" style="font-size: 0.95rem; font-weight: 500;">
                                    Hỗ trợ forwarder và alias email không?
                                </button>
                            </div>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Có. Bạn có thể tạo forwarder (chuyển tiếp email) và alias (bí danh email) để quản lý email linh hoạt. Ví dụ: sales@company.vn có thể chuyển đến account khác.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6 -->
                        <div class="accordion-item border-0">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" style="font-size: 0.95rem; font-weight: 500;">
                                    Có thể nâng cấp hoặc hạ cấp bất cứ lúc nào?
                                </button>
                            </div>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Có. Bạn có thể nâng cấp hoặc hạ cấp dịch vụ bất cứ lúc nào. Chi phí sẽ được tính lại theo cách tính pro-rata (các ngày còn lại).
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    }
</style>

    </div>
    </div>

    {{--    @include("orderitem.glxv3.css-js")--}}

@endsection
