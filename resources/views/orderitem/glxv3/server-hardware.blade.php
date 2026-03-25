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

<div class="pricing-header mt-3 mb-3 text-orange" data-code-pos='ppp17600530813801'>
    <div style="white-space: normal;">
        <!-- <i class="fas fa-server" style="font-size: 2.5rem; color: #ff6b6b; animation: pulse 2s infinite;"></i> -->
        <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 8px 16px; font-size: 0.9rem; border-radius: 20px;">
            <i class="fas fa-tachometer-alt" style="margin-right: 5px;"></i> Hiệu năng cao
        </span>
        <h2 class="text-orange mb-0" style="text-shadow: 5px 2px 4px rgba(0,0,0,0.2); font-weight: 800; font-size: 2.5rem;">
            DEDICATED SERVER
        </h2>
        <span class="badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 8px 16px; font-size: 0.9rem; border-radius: 20px;">
            <i class="fas fa-crown" style="margin-right: 5px;"></i> Enterprise Grade
        </span>
        <!-- <i class="fas fa-microchip" style="font-size: 2.5rem; color: #ffd93d; animation: pulse 2s infinite;"></i> -->
    </div>
</div>

<!-- Features Table -->
<div class="container mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-1 shadow" style="box-shadow: 0 5px 9px rgba(0, 0, 0, 0.15) !important;">

                <div class="card-body">
                    <div class="row g-4">
                        <!-- Feature 1: Raw Power -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #e7f3ff; border-radius: 8px;">
                                        <i class="fas fa-bolt" style="font-size: 1.5rem; color: #667eea;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2" style="color: #333; font-weight: 600;">
                                        <i class="fas fa-check-circle text-success"></i> Hạ tầng mạnh mẽ
                                    </h6>
                                    <p class="mb-0 text-muted small" style="font-size: 0.9rem;">
                                        Toàn bộ tài nguyên phần cứng chuyên dụng cho ứng dụng của bạn.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 2: Reliability -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #f0e7ff; border-radius: 8px;">
                                        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #764ba2;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2" style="color: #333; font-weight: 600;">
                                        <i class="fas fa-check-circle text-success"></i> Độ Tin Cậy Cao
                                    </h6>
                                    <p class="mb-0 text-muted small" style="font-size: 0.9rem;">
                                        Phần cứng enterprise, bảo hành 24/7 và hỗ trợ kỹ thuật tức thì.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 3: Customization -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #fff4e7; border-radius: 8px;">
                                        <i class="fas fa-cogs" style="font-size: 1.5rem; color: #ff9800;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2" style="color: #333; font-weight: 600;">
                                        <i class="fas fa-check-circle text-success"></i> Tùy Chỉnh Linh Hoạt
                                    </h6>
                                    <p class="mb-0 text-muted small" style="font-size: 0.9rem;">
                                        Cấu hình phần cứng theo yêu cầu cụ thể của bạn.
                                    </p>
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
    .card-item-price{
        box-shadow: 0 0 0 .05rem rgba(8, 60, 130, .06), 0 0 1.25rem rgba(30, 34, 40, .04);
        border: 0;
    }
    .card-body {
        padding: 1rem 1rem;
    }

    @keyframes bounce {
        0%, 100% {
            transform: translateY(0);
        }
        50% {
            transform: translateY(-10px);
        }
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.1);
        }
    }

</style>

<div style="font-size: 100%">
    <div class="container p-3">
        <div class="row g-4">
            <!-- Server 1: Dell R730 -->
            <div class="col-md-6 col-lg-4 mb-8">
                <div class="card h-100 shadow-sm border-0 hover-shadow transition-all card-item-price" style="cursor: pointer; transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                    <!-- Card Header -->
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h2 class="mb-0 text-center text-orange">Dell R730</h2>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="specs">
                            <!-- Specs Info -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-microchip" style="margin-right: 5px;"></i>CPU</span>
                                <span class="badge badge-info" style="color: #333;">Intel Xeon 2680v4 - 28c/56t</span>
                            </div>
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-memory" style="margin-right: 5px;"></i>RAM</span>
                                <span class="badge badge-info" style="color: #333;">64GB</span>
                            </div>
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-hdd" style="margin-right: 5px;"></i>SSD</span>
                                <span class="badge badge-info" style="color: #333;">960GB</span>
                            </div>
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-wifi" style="margin-right: 5px;"></i>Network</span>
                                <span class="badge badge-info" style="color: #333;">200Mb Share</span>
                            </div>
                        </div>

                        <!-- Giá -->
                        <div class="price-section mt-3 pt-3 text-center">
                            <p class="mb-0">
                                <span class="text-muted small">Giá:</span>
                                <span class="price-display text-danger font-weight-bold" style="font-size: 1.3em;">
                                    3.700.000đ
                                </span>
                                <span class="text-muted small d-block" style="font-size: 0.85rem;">/tháng</span>
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-light border-0">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-envelope"></i> Liên Hệ
                        </button>
                    </div>
                </div>
            </div>

            <!-- Server 2: Dell FC430 -->
            <div class="col-md-6 col-lg-4 mb-8">
                <div class="card h-100 shadow-sm border-0 hover-shadow transition-all card-item-price" style="cursor: pointer; transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                    <!-- Card Header -->
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                        <h2 class="mb-0 text-center text-orange">Dell FC430</h2>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="specs">
                            <!-- Specs Info -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-microchip" style="margin-right: 5px;"></i>CPU</span>
                                <span class="badge badge-info" style="color: #333;">1x Intel Xeon 2680v4 - 28c/56t</span>
                            </div>
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-memory" style="margin-right: 5px;"></i>RAM</span>
                                <span class="badge badge-info" style="color: #333;">64GB</span>
                            </div>
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-hdd" style="margin-right: 5px;"></i>SSD</span>
                                <span class="badge badge-info" style="color: #333;">960GB</span>
                            </div>
                            <div class="spec-item d-flex justify-content-between align-items-center py-2">
                                <span class="spec-label font-weight-500"><i class="fas fa-wifi" style="margin-right: 5px;"></i>Network</span>
                                <span class="badge badge-info" style="color: #333;">200Mb Share</span>
                            </div>
                        </div>

                        <!-- Giá -->
                        <div class="price-section mt-3 pt-3 text-center">
                            <p class="mb-0">
                                <span class="text-muted small">Giá:</span>
                                <span class="price-display text-danger font-weight-bold" style="font-size: 1.3em;">
                                    3.300.000đ
                                </span>
                                <span class="text-muted small d-block" style="font-size: 0.85rem;">/tháng</span>
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-light border-0">
                        <button class="btn btn-primary w-100">
                            <i class="fas fa-envelope"></i> Liên Hệ
                        </button>
                    </div>
                </div>
            </div>

            <!-- Server 3: Dell FC640 -->
            <div class="col-md-6 col-lg-4 mb-8">
                <div class="card h-100 shadow-sm border-0 hover-shadow transition-all card-item-price" style="cursor: pointer; transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important; border-top: 3px solid #ff9800;">
                    <!-- Popular Badge -->
                    <div style="position: absolute; top: -12px; right: 15px; background: #ff9800; color: white; padding: 4px 12px; border-radius: 12px; font-size: 0.7rem; font-weight: 600;">
                        RECOMMENDED
                    </div>

                    <!-- Card Header -->
                    <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #f5576c 0%, #ff9800 100%);">
                        <h2 class="mb-0 text-center text-orange">Dell FC640</h2>
                    </div>

                    <!-- Card Body -->
                    <div class="card-body">
                        <div class="specs">
                            <!-- Specs Info -->
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-microchip" style="margin-right: 5px;"></i>CPU</span>
                                <span class="badge badge-warning" style="color: #333;">1x Intel Gold 6133 - 40c/80t</span>
                            </div>

                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-memory" style="margin-right: 5px;"></i>RAM</span>
                                <span class="badge badge-warning" style="color: #333;">64GB</span>
                            </div>
                            <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom">
                                <span class="spec-label font-weight-500"><i class="fas fa-hdd" style="margin-right: 5px;"></i>SSD</span>
                                <span class="badge badge-warning" style="color: #333;">960GB</span>
                            </div>
                            <div class="spec-item d-flex justify-content-between align-items-center py-2">
                                <span class="spec-label font-weight-500"><i class="fas fa-wifi" style="margin-right: 5px;"></i>Network</span>
                                <span class="badge badge-warning" style="color: #333;">200Mb Share</span>
                            </div>
                        </div>

                        <!-- Giá -->
                        <div class="price-section mt-3 pt-3 text-center">
                            <p class="mb-0">
                                <span class="text-muted small">Giá:</span>
                                <span class="price-display text-danger font-weight-bold" style="font-size: 1.3em;">
                                    3.800.000đ
                                </span>
                                <span class="text-muted small d-block" style="font-size: 0.85rem;">/tháng</span>
                            </p>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer bg-light border-0">
                        <button class="btn btn-warning w-100" style="background: #ff9800; border-color: #ff9800; color: white;">
                            <i class="fas fa-crown"></i> Khuyên Dùng - Liên Hệ
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Additional Services Section -->
<div class="container mt-0 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                <div class="card-body p-5">
                    <h4 class="mb-4" style="color: #333; font-weight: 700;">
                        <i class="fas fa-plus-circle" style="color: #ff9800;"></i> Dịch Vụ Bổ Sung & Nâng Cấp
                    </h4>

                    <p class="mb-4 text-muted">Bạn có thể dễ dàng nâng cấp tài nguyên server của mình bất cứ lúc nào:</p>

                    <div class="row g-3">
                        <!-- Service 1: CPU -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border-1 hover-shadow" style="border-color: #667eea; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <div style="font-size: 2.5rem; color: #667eea; margin-bottom: 15px;">
                                        <i class="fas fa-microchip"></i>
                                    </div>
                                    <h6 class="mb-2" style="color: #333; font-weight: 700;">Thêm CPU</h6>
                                    <p class="text-muted small mb-3">(01 CPU)</p>
                                    <p style="font-size: 1.4rem; color: #f5576c; font-weight: 700;">
                                        200K
                                    </p>
                                    <small class="text-muted">/tháng</small>
                                </div>
                            </div>
                        </div>

                        <!-- Service 2: RAM -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border-1 hover-shadow" style="border-color: #764ba2; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <div style="font-size: 2.5rem; color: #764ba2; margin-bottom: 15px;">
                                        <i class="fas fa-memory"></i>
                                    </div>
                                    <h6 class="mb-2" style="color: #333; font-weight: 700;">Thêm RAM</h6>
                                    <p class="text-muted small mb-3">(32GB)</p>
                                    <p style="font-size: 1.4rem; color: #f5576c; font-weight: 700;">
                                        350K
                                    </p>
                                    <small class="text-muted">/tháng</small>
                                </div>
                            </div>
                        </div>

                        <!-- Service 3: SSD -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border-1 hover-shadow" style="border-color: #ff9800; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <div style="font-size: 2.5rem; color: #ff9800; margin-bottom: 15px;">
                                        <i class="fas fa-hdd"></i>
                                    </div>
                                    <h6 class="mb-2" style="color: #333; font-weight: 700;">Thêm SSD</h6>
                                    <p class="text-muted small mb-3">(960GB)</p>
                                    <p style="font-size: 1.4rem; color: #f5576c; font-weight: 700;">
                                        500K
                                    </p>
                                    <small class="text-muted">/tháng</small>
                                </div>
                            </div>
                        </div>

                        <!-- Service 4: Network -->
                        <div class="col-md-6 col-lg-3">
                            <div class="card border-1 hover-shadow" style="border-color: #2196f3; transition: all 0.3s ease; cursor: pointer;">
                                <div class="card-body text-center p-4">
                                    <div style="font-size: 2.5rem; color: #2196f3; margin-bottom: 15px;">
                                        <i class="fas fa-wifi"></i>
                                    </div>
                                    <h6 class="mb-2" style="color: #333; font-weight: 700;">Thêm Network</h6>
                                    <p class="text-muted small mb-3">(100Mbit)</p>
                                    <p style="font-size: 1.4rem; color: #f5576c; font-weight: 700;">
                                        1.000K
                                    </p>
                                    <small class="text-muted">/tháng</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- <div class="alert alert-info border-0 mt-4" style="background: #e7f3ff; border-left: 4px solid #667eea;">
                        <i class="fas fa-exclamation-circle"></i> <strong>Lưu ý:</strong> Các dịch vụ bổ sung có thể được kích hoạt/hủy bất cứ lúc nào.
                        Chi phí sẽ được tính theo cách tính lại invoice hàng tháng. Liên hệ team support để được hỗ trợ.
                    </div> -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Server Hardware Information Section -->
<div class="container mt-5 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow" style="box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                <div class="card-body p-5">
                    <h4 class="mb-4" style="color: #333; font-weight: 700;">
                        <i class="fas fa-info-circle" style="color: #667eea;"></i> Máy Chủ Phần Cứng là gì?
                    </h4>

                    <p class="lead mb-3">
                        <strong>Dedicated Server (Máy Chủ Chuyên Dụng)</strong> là một máy chủ vật lý được cung cấp hoàn toàn cho một công ty hoặc cá nhân.
                        Bạn có quyền sử dụng độc quyền tất cả tài nguyên phần cứng của máy chủ đó.
                    </p>

                    <p class="mb-3">
                        Dedicated Server cung cấp hiệu năng tối ưu cho các ứng dụng yêu cầu cao:
                    </p>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Hiệu năng tối đa</strong> - Không chia sẻ tài nguyên với ai khác
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Độ tin cậy cao</strong> - Phần cứng enterprise-grade, pháp lý đầy đủ
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Toàn quyền quản trị</strong> - Root/Administrator access hoàn toàn
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Bảo mật cấp enterprise</strong> - Tách biệt hoàn toàn, bảo vệ dữ liệu tối đa
                        </li>
                    </ul>

                    <p class="mb-3" style="font-weight: 500;">
                        <i class="fas fa-rocket" style="color: #667eea;"></i> <strong>Ứng dụng của Dedicated Server:</strong>
                    </p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Website lớn, ứng dụng high traffic</strong> - Hàng triệu request/ngày
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Database server</strong> - Cơ sở dữ liệu lớn, xử lý phức tạp
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Mail server, streaming server</strong> - Dịch vụ nặng
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Gaming server, giải pháp enterprise</strong> - Yêu cầu độ tin cậy cao
                        </li>
                    </ul>

                    <p class="mb-3" style="font-weight: 500;">
                        <i class="fas fa-trophy" style="color: #f5576c;"></i> <strong>Lợi thế Dedicated Server so với VPS:</strong>
                    </p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Hiệu năng không giới hạn</strong> - Toàn bộ phần cứng dành cho bạn
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Không bị ảnh hưởng bởi người khác</strong> - Không có sự cạnh tranh tài nguyên
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Dễ dàng scale up</strong> - Nâng cấp phần cứng trực tiếp trên máy
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Hỗ trợ chuyên sâu</strong> - Đội kỹ thuật chuyên sâu 24/7
                        </li>
                    </ul>

                    <div class="alert alert-info border-0" style="background: #e7f3ff; border-left: 4px solid #667eea;">
                        <i class="fas fa-building"></i> <strong>Vị trí Datacenter:</strong><br>
                        <span class="mt-2 d-block">
                            Các máy chủ của chúng tôi được đặt tại các nhà mạng lớn
                            <strong>(FPT, Viettel, VNPT, ...)</strong>
                            đảm bảo an toàn vật lý theo <strong>tiêu chuẩn quốc tế</strong>,
                            với hạ tầng mạng ổn định, điều hoà nhiệt độ và nguồn điện dự phòng.
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
                        <i class="fas fa-question-circle" style="color: #667eea;"></i> Các câu hỏi thường gặp
                    </h4>

                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ 1: Dedicated Server vs VPS -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-compare" style="color: #667eea; margin-right: 10px;"></i>
                                    Sự khác biệt giữa Dedicated Server và VPS?
                                </button>
                            </div>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    <strong>Dedicated Server</strong> - Toàn bộ máy chủ vật lý cho bạn, không chia sẻ tài nguyên, hiệu năng tối ưu.
                                    <br><br>
                                    <strong>VPS</strong> - Chia sẻ máy chủ vật lý với các user khác, nhưng tài nguyên riêng, chi phí thấp hơn.
                                    <br><br>
                                    Chọn <strong>Dedicated Server</strong> nếu bạn cần hiệu năng cao, chọn <strong>VPS</strong> nếu cần tiết kiệm chi phí.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2: Hỗ trợ quản lý -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-wrench" style="color: #764ba2; margin-right: 10px;"></i>
                                    Có dịch vụ quản lý được không?
                                </button>
                            </div>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Có! Chúng tôi cung cấp cả <strong>Unmanaged Server</strong> (bạn tự quản lý) và <strong>Managed Server</strong> (chúng tôi quản lý).
                                    Với Managed Server, chúng tôi sẽ cài đặt OS, update bảo mật, giám sát hiệu năng và hỗ trợ 24/7.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3: Tài nguyên của Dedicated Server -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-microchip" style="color: #ff9800; margin-right: 10px;"></i>
                                    Các loại cấu hình Dedicated Server có sẵn không?
                                </button>
                            </div>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Có, chúng tôi cung cấp nhiều cấu hình từ cơ bản đến cao cấp:
                                    <ul class="mt-2 mb-0" style="font-size: 0.9rem;">
                                        <li><strong>Intel/AMD CPU</strong> - 4 core đến 64+ core</li>
                                        <li><strong>RAM</strong> - Từ 16GB đến 512GB+</li>
                                        <li><strong>Storage</strong> - SSD, HDD, NVMe</li>
                                        <li><strong>Network</strong> - 1Gbps, 10Gbps+</li>
                                    </ul>
                                    Liên hệ chúng tôi để tìm cấu hình phù hợp với yêu cầu của bạn.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4: Contract và Pricing -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-calendar" style="color: #28a745; margin-right: 10px;"></i>
                                    Thời hạn hợp đồng và giá cả?
                                </button>
                            </div>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Dedicated Server thường tính theo tháng hoặc năm.
                                    Giá cả phụ thuộc vào cấu hình phần cứng (CPU, RAM, Storage, Bandwidth).
                                    Hợp đồng thường là 12 tháng hoặc 24 tháng, có thể liên hệ để thương lượng.
                                    <br><br>
                                    <strong>Liên hệ chúng tôi để nhận báo giá tốt nhất!</strong>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5: Migration từ server khác -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-arrow-right-arrow-left" style="color: #2196f3; margin-right: 10px;"></i>
                                    Có thể migrate từ server khác không?
                                </button>
                            </div>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Có! Chúng tôi cung cấp dịch vụ migration miễn phí từ server cũ.
                                    Đội kỹ thuật sẽ:
                                    <ul class="mt-2 mb-0" style="font-size: 0.9rem;">
                                        <li>Backup dữ liệu của bạn</li>
                                        <li>Cài đặt OS và cấu hình trên server mới</li>
                                        <li>Migrate dữ liệu, ứng dụng, database</li>
                                        <li>Cấu hình DNS, SSL, optimization</li>
                                        <li>Testing và xác nhận hoạt động</li>
                                    </ul>
                                    Quá trình migration được thực hiện với downtime tối thiểu.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6: Bảo mật và Backup -->
                        <div class="accordion-item border-0">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-lock" style="color: #f5576c; margin-right: 10px;"></i>
                                    Bảo mật và Backup như thế nào?
                                </button>
                            </div>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Chúng tôi cung cấp các dịch vụ bảo mật toàn diện:
                                    <ul class="mt-2" style="font-size: 0.9rem;">
                                        <li><strong>Firewall DDoS</strong> - Bảo vệ khỏi tấn công DDoS</li>
                                        <li><strong>Automatic Backup</strong> - Backup hàng ngày/tuần</li>
                                        <li><strong>SSL Certificate</strong> - HTTPS bảo mật</li>
                                        <li><strong>Monitoring 24/7</strong> - Giám sát, cảnh báo</li>
                                        <li><strong>Redundancy</strong> - Dự phòng, failover</li>
                                    </ul>
                                    Tất cả đều được quản lý bởi đội kỹ thuật chuyên sâu.
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
