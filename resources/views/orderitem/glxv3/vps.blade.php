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


    <div class="container mt-5">

        <div class="pricing-container1">


        <?php
$keyAndName = config('vps_config.specs');
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
    .price_hour1, .price_month1{
        color: orange;
        font-weight: bold;
        margin: 1px;
    }
    .price_month1 {
        color: gray
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
    .accordion-button {
        color: orange;
        border: 1px solid #eee;
    }
    .card {
        cursor: pointer; transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    }
</style>

<div class="pricing-header mt-3 mb-3 text-orange qqqq1111" data-code-pos='ppp17600530813801'>
    <?php


    $ui = \App\Models\BlockUi::showEditButtonStatic('vps_vip');
//                            $ui->showEditButton();

    $str =  $ui->getExtra();
    $mm = explode("\n", $str);
    $mm = array_values(array_filter($mm));
//                            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                            print_r($mm);
//                            echo "</pre>";

    ?>
    <div style="white-space: normal;">
        <!-- <i class="fas fa-rocket" style="font-size: 2.2rem; color: orange; animation: pulse 2s infinite;"></i> -->
        <span class="badge" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 8px 16px; font-size: 0.9rem; border-radius: 20px;">
            <i class="fas fa-tachometer-alt" style="margin-right: 5px;"></i>
            {{$mm[0]}}
        </span>
        <h2 class="mb-0" style="text-shadow: 5px 2px 4px rgba(0,0,0,0.2); font-weight: 800; font-size: 2.5rem; color: orange;">
            {{$mm[1]}}
        </h2>
        <span class="badge" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 8px 16px; font-size: 0.9rem; border-radius: 20px;">
            <i class="fas fa-crown" style="margin-right: 5px;"></i> {{$mm[2]}}
        </span>
        <!-- <i class="fas fa-bolt" style="font-size: 2.2rem; color: orange; animation: pulse 2s infinite;"></i> -->
    </div>
</div>

<!-- Features Table -->
<div class="container mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-1 shadow" style="box-shadow: 0 5px 9px rgba(0, 0, 0, 0.15) !important;">

                <div class="card-body">
                    <div class="row g-4">



                        <!-- Feature 1: Backup -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #e7f3ff; border-radius: 8px;">
                                        <i class="fas fa-shield-alt" style="font-size: 1.5rem; color: #667eea;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2" style="color: #333; font-weight: 600;">
                                        <i class="fas fa-check-circle text-success"></i> {{ $mm[3]  }}
                                    </h6>
                                    <p class="mb-0 text-muted small" style="font-size: 0.9rem;">
                                        {{ $mm[4]  }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 2: HA -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #f0e7ff; border-radius: 8px;">
                                        <i class="fas fa-network-wired" style="font-size: 1.5rem; color: #764ba2;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2" style="color: #333; font-weight: 600;">
                                        <i class="fas fa-check-circle text-success"></i>  {{ $mm[5]  }}
                                    </h6>
                                    <p class="mb-0 text-muted small" style="font-size: 0.9rem;">
                                        {{ $mm[6]  }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Feature 3: Pay Per Minute -->
                        <div class="col-md-4">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0">
                                    <div class="d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #fff4e7; border-radius: 8px;">
                                        <i class="fas fa-dollar-sign" style="font-size: 1.5rem; color: #ff9800;"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-2" style="color: #333; font-weight: 600;">
                                        <i class="fas fa-check-circle text-success"></i>
                                        {{ $mm[7]  }}
                                    </h6>
                                    <p class="mb-0 text-muted small" style="font-size: 0.9rem;">
                                        {{ $mm[8]  }}
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
            @php
                // Lấy tất cả VPS plans từ bảng vps_plans
                $vpsPlans = \App\Models\VpsPlan::where('status', 1)
                    ->get();
            @endphp

            @forelse($vpsPlans as $plan)
                @php
                    // Lấy giá trị specs từ các trường của VpsPlan
                    $specValues = [
                        'n_cpu_core' => $plan->cpu,
                        'n_ram_gb' => $plan->ram_gb,
                        'n_gb_disk' => $plan->disk_gb,
                        'n_network_mbit' => 200,  // Mặc định network share
                        'n_network_dedicated_mbit' => $plan->network_mbit,
                        'n_ip_address' => $plan->number_ip_address,
                    ];




                    // Tính giá lần đầu dựa vào giá trị specs từ plan
                    $initialPriceVND = \App\Models\Product_Meta::calculateVpsPrice(
                        $plan->cpu,
                        $plan->ram_gb,
                        $plan->disk_gb,
                        200,  // network share default
                        $plan->network_mbit,
                        $plan->number_ip_address,
                        json_decode($plan->price_config)
                    ); // Returns VND

                    $initialPriceFormattedHour = number_format(round($initialPriceVND / 30 / 24), 0, '.', '');
                    $initialPriceFormattedMonth = number_format($initialPriceVND, 0, ',', '.');
                @endphp

                <div class="col-md-6 col-lg-3 mb-8">
                    <div class="card h-100 shadow-sm border-0 hover-shadow transition-all card-item-price" style="cursor: pointer; transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;">
                        <!-- Card Header -->
                        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <h2 class="mb-0 text-center text-orange">{{ $plan->name }}</h2>
                        </div>

                        <!-- Card Body -->
                        <div class="card-body">
                            <!-- Danh sách Specs có thể chỉnh sửa với +/- buttons -->
                            <div class="specs" data-plan-id="{{ $plan->id }}">
                                @foreach($keyAndName as $attrName => $attrConfig)
                                    @php
                                        $defaultValue = $specValues[$attrName] ?? $attrConfig['min'];
                                        $isDisabled = $attrConfig['disable_change'] ?? false;

                                        $isHide  = $attrConfig['is_hide'] ?? false;
                                        if($isHide)
                                            continue;

                                    @endphp
                                    <div class="spec-item d-flex justify-content-between align-items-center py-2 border-bottom" data-spec-name="{{ $attrName }}">
                                        <span class="spec-label font-weight-500">{!!  $attrConfig['desc'] !!}</span>
                                        <div class="d-flex align-items-center gap-2">
                                            <input type="number" class="spec-value form-control form-control-sm" style="width: 70px; text-align: center; font-weight: bold; color: orange;"
                                                onkeyup="debounceSpecValueChange(this)"
                                                min="{{ $attrConfig['min'] }}"
                                                max="{{ $attrConfig['max'] }}"
                                                step="{{ $attrConfig['step'] }}"
                                                data-rounding-step="{{ $attrConfig['rounding'] ?? $attrConfig['step'] }}"
                                                value="{{ $defaultValue }}"
                                                {{ $isDisabled ? 'readonly' : '' }} />
                                            <button class="btn btn-sm btn-outline-secondary" onclick="decreaseSpec(this)" {{ $isDisabled ? 'disabled' : '' }}>−</button>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="increaseSpec(this)" {{ $isDisabled ? 'disabled' : '' }}>+</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Giá -->
                            <div class="price-section mt-3 pt-3 text-center" data-code-pos='ppp17736337301231'>
                                <p class="mb-0">
                                    <span class="text-muted small">Giá:</span>
                                    <span class="price-display text-danger font-weight-bold" style="font-size: 1.3em;" data-plan-id="{{ $plan->id }}">
                                        <p class="price_hour1">
                                            <span>
                                        {{ $initialPriceFormattedHour }}
                                                </span>
                                                đ / Giờ
                                        </p>
                                        <p class="price_month1">
                                        <span>{{ $initialPriceFormattedMonth }}</span> đ / Tháng
                                        </p>
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Select OS -->
                        <div class="px-3 py-3 border-top">
                            <label class="form-label small mb-2">
                                <i class="fas fa-penguin"></i> Chọn hệ điều hành (tùy chọn):
                            </label>
                            <select style="text-align: center" class="select_vps_package form-select form-select-sm select-os" data-plan-id="{{ $plan->id }}">
                                <option value="">-- Chọn Hệ điều hành --</option>
                                @foreach(\App\Models\VpsOsVersion::where('is_active', 1)->get() as $os)
                                    <option value="{{ $os->id }}">{{ $os->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-light border-0" data-code-pos='ppp17727260707091'>
                            <button id="" class="select_vps_package btn btn-primary w-100 select-vps-btn" data-plan-id="{{ $plan->id }}">
                                <i class="fas fa-shopping-cart"></i> Chọn gói này
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Hiện không có gói VPS nào
                    </div>
                </div>
            @endforelse



        </div>
    </div>
</div>

<!-- VPS Information Section -->
<div class="container mt-1 mb-5">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow">
                <div class="card-body p-5">
                    <h4 class="mb-4" style="color: #333; font-weight: 700;">
                        <i class="fas fa-info-circle" style="color: #667eea;"></i> VPS là gì?
                    </h4>

                    <p class="lead mb-3">
                        <strong>VPS (Virtual Private Server)</strong> là một máy chủ riêng ảo được cấp phát từ một máy chủ vật lý.
                        Mỗi VPS có tài nguyên riêng biệt (CPU, RAM, Disk) và hoàn toàn độc lập với các VPS khác trên cùng máy chủ vật lý.
                    </p>

                    <p class="mb-3">
                        VPS mang lại sự cân bằng hoàn hảo giữa chi phí và hiệu năng so với các giải pháp khác:
                    </p>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Rẻ hơn Dedicated Server</strong> - Chi phí thấp nhưng vẫn có tài nguyên riêng
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Mạnh hơn Shared Hosting</strong> - Đủ công suất để chạy website, ứng dụng có lưu lượng cao
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Toàn quyền quản trị</strong> - Root/Administrator access, cài đặt phần mềm tùy ý
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle text-success"></i> <strong>Bảo mật cao</strong> - Dữ liệu hoàn toàn riêng, không bị người dùng khác truy cập
                        </li>
                    </ul>

                    <div class="alert alert-info border-0 mb-4" style="background: #e7f3ff; border-left: 4px solid #667eea;">
                        <i class="fas fa-list"></i> <strong>Hệ điều hành có sẵn:</strong><br>
                        <span class="mt-2 d-block" style="font-size: 0.9rem;">
                            <strong>Linux:</strong> Ubuntu, CentOS, AlmaLinux, Debian, Rocky Linux<br>
                            <strong>Windows:</strong> Windows 10 + 11, Windows Server 2016, 2019, 2022, 2025
                        </span>
                    </div>

                    <p class="mb-3" style="font-weight: 500;">
                        <i class="fas fa-rocket" style="color: #667eea;"></i> <strong>Ứng dụng của VPS:</strong>
                    </p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> Chạy <strong>24/7</strong> cho mọi nhu cầu
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> Chạy <strong>website, web server</strong>, ứng dụng web
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> Cài đặt <strong>phần mềm, tool</strong> như n8n, automation tools
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Thay thế máy PC</strong> để làm việc văn phòng, development
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-arrow-right" style="color: #667eea;"></i> <strong>Database server, mail server</strong>, hoặc bất kỳ dịch vụ nào
                        </li>
                    </ul>

                    <p class="mb-3" style="font-weight: 500;">
                        <i class="fas fa-trophy" style="color: #f5576c;"></i> <strong>Lợi thế VPS so với máy chủ vật lý:</strong>
                    </p>
                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Cấu hình linh hoạt</strong> - Bắt đầu với cấu hình tối thiểu để chi phí tốt nhất, sau đó thêm/bớt RAM, CPU, Disk bất cứ lúc nào
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Tính phí tương ứng</strong> - Chỉ trả phí cho những gì bạn sử dụng, không phí cố định
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Không cần đầu tư lớn</strong> - Máy chủ vật lý yêu cầu đầu tư ban đầu lớn và không thể nhanh chóng tăng giảm tài nguyên
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Không cần bảo dưỡng</strong> - Không phải lo về nguồn điện 24/7, điều hoà, bảo dưỡng hạ tầng
                        </li>
                        <li class="list-group-item ps-0 border-0">
                            <i class="fas fa-check-circle" style="color: #f5576c;"></i> <strong>Tiết kiệm chi phí nhân sự</strong> - Không cần đội ngũ quản trị server tốn kém, chúng tôi quản lý hệ thống
                        </li>
                    </ul>

                    <div class="alert alert-info border-0" style="padding: 10px 15px!important; background: #e7f3ff; border-left: 4px solid #667eea;">
                        <i class="fas fa-building"></i> <strong>Vị trí Datacenter:</strong><br>
                        <span class="mt-2 d-block">
                            Các VPS của chúng tôi được đặt tại các nhà mạng lớn
                            <strong>(FPT, Viettel, VNPT, ...)</strong>
                            đảm bảo an toàn vật lý theo <strong>tiêu chuẩn quốc tế</strong>,
                            với hạ tầng mạng ổn định và tốc độ cao.
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
            <div class="card border-0 shadow">
                <div class="card-body p-5">
                    <h4 class="mb-4" style="color: #333; font-weight: 700;">
                        <i class="fas fa-question-circle" style="color: #667eea;"></i> Các câu hỏi thường gặp
                    </h4>

                    <div class="accordion" id="faqAccordion">
                        <!-- FAQ 1: Web Console là gì-->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-terminal" style="color: #667eea; margin-right: 10px;"></i>
                                    Web Console là gì?
                                </button>
                            </div>
                            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    <strong>Web Console</strong> là công cụ truy cập VPS qua quản trị VPS trên trang web này,
                                    trong phần <a href="/member" target="_blank" style="color: #667eea; font-weight: 500;">
                                    Thành viên / Member <i class="fas fa-external-link-alt" style="font-size: 0.7rem;"></i></a>.
                                    <br>
                                    Web Console cho phép bạn <strong>truy cập VPS qua web</strong>, ngay cả khi gặp các sự cố:
                                    <ul class="mt-2 mb-0" style="font-size: 0.9rem;">
                                        <li>VPS lỗi mạng (không kết nối internet)</li>
                                        <li>Lỗi phần mềm hoặc cấu hình</li>
                                        <li>Không truy cập được SSH (Linux)</li>
                                        <li>Không truy cập được Remote Desktop (Windows)</li>
                                    </ul>
                                    Bạn vẫn có thể <strong>điều khiển máy và khắc phục sự cố</strong> thông qua Web Console!
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 2: Địa chỉ IP, Remote Desktop, SSH -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-laptop" style="color: #764ba2; margin-right: 10px;"></i>
                                    Tôi có thể sử dụng Remote Desktop, SSH hay không?
                                </button>
                            </div>
                            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Hoàn toàn có thể! Mỗi VPS sẽ được cấp <strong>địa chỉ IP riêng</strong>.
                                    Bạn có thể truy cập VPS qua SSH (Linux) hoặc Remote Desktop (Windows) bằng IP đó.
                                    Cấp số IP phụ thuộc vào gói VPS bạn chọn (có thể 1 IP hoặc nhiều IP).
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 3: Cài đặt phần mềm -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-cube" style="color: #ff9800; margin-right: 10px;"></i>
                                    Tôi có thể cài đặt các phần mềm trên VPS không?
                                </button>
                            </div>
                            <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Có, bạn cũng có <strong>toàn quyền quản trị (Root/Admin access)</strong> đối với VPS.
                                    Điều này có nghĩa là bạn có thể cài đặt bất kỳ phần mềm, ứng dụng nào bạn cần
                                    (web server, database, tools khác, ...).
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 4: Hiệu năng phụ thuộc vào -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-gauge-high" style="color: #28a745; margin-right: 10px;"></i>
                                    Hiệu năng VPS phụ thuộc vào những yếu tố nào?
                                </button>
                            </div>
                            <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Hiệu năng VPS chủ yếu phụ thuộc vào:
                                    <ul class="mt-2 mb-0" style="font-size: 0.9rem;">
                                        <li><strong>CPU (xử lý)</strong>: Đáp ứng tốc độ xử lý yêu cầu</li>
                                        <li><strong>RAM (bộ nhớ)</strong>: Khả năng chạy đa nhiệm, lưu cache</li>
                                        <li><strong>Disk (ổ cứng)</strong>: Dung lượng lưu trữ dữ liệu</li>
                                        <li><strong>Network (mạng)</strong>: Tốc độ truyền dữ liệu</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 5: Truy cập từ bất cứ đâu -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq5" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-globe" style="color: #2196f3; margin-right: 10px;"></i>
                                    Tôi có thể truy cập VPS từ bất cứ đâu không?
                                </button>
                            </div>
                            <div id="faq5" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Vâng! Miễn là bạn có <strong>kết nối Internet</strong>, bạn có thể truy cập VPS từ bất cứ
                                    nơi nào trên thế giới (nhà, văn phòng, máy bay, quán cà phê, ...).
                                    Chỉ cần có địa chỉ IP và thông tin truy cập, bạn sẽ kết nối được.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 6: Hỗ trợ 24/7 -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq6" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-headset" style="color: #f5576c; margin-right: 10px;"></i>
                                    Có hỗ trợ khi gặp vấn đề không?
                                </button>
                            </div>
                            <div id="faq6" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Tuyệt đối có! Đội ngũ <strong>Galaxy Cloud hỗ trợ 24/7</strong> sẵn sàng giúp bạn
                                    giải quyết bất kỳ vấn đề nào. Bạn có thể liên hệ qua email, chat, hoặc điện thoại
                                    bất cứ lúc nào trong ngày để nhận trợ giúp từ các kỹ sư chuyên nghiệp.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 7: VPS được tạo như thế nào -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq7" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-cogs" style="color: #667eea; margin-right: 10px;"></i>
                                    VPS này được tạo như thế nào?
                                </button>
                            </div>
                            <div id="faq7" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Chúng tôi <strong>tạo sẵn các VPS</strong> cho bạn theo <strong>mẫu cơ bản</strong> và
                                    <strong>thay đổi cấu hình vật lý</strong> theo yêu cầu cụ thể của bạn (CPU, RAM, Disk, ...).
                                    <br>
                                    Sau khi bạn khởi tạo một VPS mới, chúng tôi sẽ gửi cho bạn:
                                    <ul class="mt-2 mb-0" style="font-size: 0.9rem;">
                                        <li><strong>Địa chỉ IP</strong> để kết nối VPS</li>
                                        <li><strong>Tên tài khoản (User)</strong> để truy cập</li>
                                        <li><strong>Mật khẩu (Password)</strong> đăng nhập</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 8: Hệ điều hành Windows, Linux có sẵn không -->
                        <div class="accordion-item border-0 mb-2">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq8" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-windows" style="color: #0078d4; margin-right: 10px;"></i>
                                    Các hệ điều hành Windows, Linux có sẵn không?
                                </button>
                            </div>
                            <div id="faq8" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Có! Chúng tôi có sẵn <strong>hầu hết các bản Windows và Linux</strong> phổ biến:
                                    <ul class="mt-2 mb-2" style="font-size: 0.9rem;">
                                        <li><strong>Linux:</strong> Ubuntu, CentOS, Debian, AlmaLinux, Rocky Linux, ...</li>
                                        <li><strong>Windows:</strong> Windows 10 + 11, Windows Server 2016, 2019, 2022, 2025 ...</li>
                                    </ul>
                                    <strong>Về bản quyền:</strong> Bạn có thể sử dụng <strong>license của riêng bạn</strong> hoặc
                                    mua thêm license từ chúng tôi nếu cần thiết. Chúng tôi sẽ hỗ trợ bạn trong quá trình cài đặt.
                                </div>
                            </div>
                        </div>

                        <!-- FAQ 9: Có thể truy cập VPS từ Mobile không -->
                        <div class="accordion-item border-0">
                            <div class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq9" style="font-size: 0.95rem; font-weight: 500;">
                                    <i class="fas fa-mobile-alt" style="color: #764ba2; margin-right: 10px;"></i>
                                    Có thể quản trị VPS từ Mobile không?
                                </button>
                            </div>
                            <div id="faq9" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                                <div class="accordion-body" style="font-size: 0.9rem; color: #666;">
                                    Hoàn toàn có thể! <strong>Vps chúng tôi tương thích để App mobile cũng có thể truy cập</strong> rất dễ dàng:
                                    <ul class="mt-2 mb-0" style="font-size: 0.9rem;">
                                        <li><strong>Remote Desktop App</strong> - truy cập VPS Windows từ iPhone/Android</li>
                                        <li><strong>SSH Client App</strong> - truy cập VPS Linux từ di động</li>
                                        <li><strong>Web Console</strong> - truy cập qua browser trên di động</li>
                                    </ul>
                                    Bạn có thể quản lý, kiểm soát VPS ở bất cứ nơi nào, ngay từ chiếc điện thoại của bạn!
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

    .spec-item {
        font-size: 0.95rem;
    }

    .spec-label {
        color: #555;
        font-size: 0.8rem;
    }

    /* Ẩn spinner của number input */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    .btn-outline-secondary {
        min-width: 32px;
        padding: 4px 8px;
        font-size: 0.9rem;
        font-weight: bold;
        transition: all 0.2s ease;
    }

    .btn-outline-secondary:hover {
        background-color: #6c757d;
        color: white;
        transform: scale(1.1);
    }

    .btn-primary {
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: scale(1.02);
    }
</style>

<script>
    const API_ENDPOINT = '/_site/hosting_site/price-vps.php';
    const debounceTimers = {}; // Lưu timer cho từng input

    function debounceSpecValueChange(inputElement) {
        // Clear timer cũ nếu có
        const inputId = inputElement.name || inputElement.id || inputElement.dataset.specId;
        if (debounceTimers[inputId]) {
            clearTimeout(debounceTimers[inputId]);
        }

        // Set timer mới - chạy sau 500ms
        debounceTimers[inputId] = setTimeout(() => {
            onSpecValueChange(inputElement);
        }, 500);
    }

    function getSpecsFromCard(card) {
        const specsContainer = card.querySelector('.specs');
        const specs = {};
        const specItems = specsContainer.querySelectorAll('.spec-item');

        specItems.forEach(item => {
            const specName = item.getAttribute('data-spec-name');
            const inputElement = item.querySelector('.spec-value');
            let value = parseInt(inputElement.value) || 0;

            // Validate from min/max attributes (handle 0 as valid min)
            const minAttr = inputElement.getAttribute('min');
            const maxAttr = inputElement.getAttribute('max');
            const min = minAttr !== null ? parseInt(minAttr) : 1;
            const max = maxAttr !== null ? parseInt(maxAttr) : Infinity;

            if (value < min) value = min;
            if (value > max) value = max;

            // Áp dụng rounding dựa vào rounding-step
            const roundingStep = parseInt(inputElement.getAttribute('data-rounding-step')) || 1;
            value = Math.ceil(value / roundingStep) * roundingStep;

            // Map attribute names to API parameter names
            if (specName === 'n_cpu_core') specs.n_cpu_core = value;
            else if (specName === 'n_ram_gb') specs.n_ram_gb = value;
            else if (specName === 'n_gb_disk') specs.n_gb_disk = value;
            else if (specName === 'n_network_mbit') specs.n_network_mbit = value;
            else if (specName === 'n_network_dedicated_mbit') specs.n_network_dedicated_mbit = value;
            else if (specName === 'n_ip_address') specs.n_ip_address = value;
        });

        return specs;
    }

    function recalculatePrice(button) {
        const card = button.closest('.card');
        const priceDisplay = card.querySelector('.price_month1 span');
        const priceDisplayHour = card.querySelector('.price_hour1 span');
        const specs = getSpecsFromCard(card);

        // Hiển thị trạng thái loading
        priceDisplay.innerHTML = '<i class="fas fa-spinner fa-spin"></i>...';

        // Tạo query string
        const params = new URLSearchParams(specs);

        // Thêm plan_id nếu có
        const selectBtn = card.querySelector('.select-vps-btn');
        if (selectBtn && selectBtn.getAttribute('data-plan-id')) {
            params.append('plan_id', selectBtn.getAttribute('data-plan-id'));
        }

        fetch(`${API_ENDPOINT}?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    priceDisplay.textContent = data.data.total_price_formatted;
                    priceDisplayHour.textContent = Math.round(parseInt(data.data.total_price) /24/30);
                } else {
                    priceDisplay.textContent = 'Lỗi tính giá';
                    console.error('API Error:', data.message);
                }
            })
            .catch(error => {
                priceDisplay.textContent = 'Lỗi kết nối';
                console.error('Fetch Error:', error);
            });
    }

    function increaseSpec(button) {
        const inputElement = button.parentElement.querySelector('.spec-value');
        const step = parseInt(inputElement.getAttribute('step')) || 1;
        const max = parseInt(inputElement.getAttribute('max')) || Infinity;
        const roundingStep = parseInt(inputElement.getAttribute('data-rounding-step')) || step;

        let currentValue = parseInt(inputElement.value) || 0;
        currentValue += step;

        // Không vượt quá max
        if (currentValue > max) {
            currentValue = max;
        }

        // Áp dụng rounding dựa vào rounding-step
        currentValue = Math.ceil(currentValue / roundingStep) * roundingStep;

        inputElement.value = currentValue;
        recalculatePrice(button);
    }

    function decreaseSpec(button) {
        const inputElement = button.parentElement.querySelector('.spec-value');
        const step = parseInt(inputElement.getAttribute('step')) || 1;
        const minAttr = inputElement.getAttribute('min');
        const min = minAttr !== null ? parseInt(minAttr) : 1;
        const roundingStep = parseInt(inputElement.getAttribute('data-rounding-step')) || step;

        let currentValue = parseInt(inputElement.value) || 0;
        currentValue -= step;

        // Không thấp hơn min
        if (currentValue < min) {
            currentValue = min;
        }

        // Áp dụng rounding dựa vào rounding-step
        currentValue = Math.ceil(currentValue / roundingStep) * roundingStep;

        inputElement.value = currentValue;
        recalculatePrice(button);
    }

    function onSpecValueChange(inputElement) {
        const minAttr = inputElement.getAttribute('min');
        const maxAttr = inputElement.getAttribute('max');
        const min = minAttr !== null ? parseInt(minAttr) : 1;
        const max = maxAttr !== null ? parseInt(maxAttr) : Infinity;
        const roundingStep = parseInt(inputElement.getAttribute('data-rounding-step')) || 1;
        let value = parseInt(inputElement.value) || 0;

        // Validate value
        if (value < min) {
            value = min;
        } else if (value > max) {
            value = max;
        }

        // Áp dụng rounding dựa vào rounding-step
        value = Math.ceil(value / roundingStep) * roundingStep;

        inputElement.value = value;

        // Tính lại giá
        recalculatePrice(inputElement);
    }

    // Không gọi API lần đầu - giá đã được tính bằng PHP ở phía server
    // API chỉ gọi khi user thay đổi specs (via +/- button hoặc input)

    // Handle "Chọn gói này" button click - GET VPS data (shareable URL)
    document.querySelectorAll('.select-vps-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();

            const card = this.closest('.card');
            const osSelect = card.querySelector('.select-os');

            // Kiểm tra xem OS đã được chọn chưa
            if (!osSelect || osSelect.value === '') {
                alert('Vui lòng chọn hệ điều hành trước khi tiếp tục!');
                osSelect.focus(); // Focus vào select để user biết
                return;
            }

            const specs = getSpecsFromCard(card);

            // Build query string from specs
            const params = new URLSearchParams();
            params.append('post', 'vps');

            // Add all specs
            Object.keys(specs).forEach(key => {
                params.append(key, specs[key]);
            });

            // Add plan ID
            const planId = this.getAttribute('data-plan-id');
            if (planId) {
                params.append('plan_id', planId);
            }

            // Add selected OS (init_os)
            if (osSelect && osSelect.value) {
                params.append('init_os', osSelect.value);
            }

            // Redirect with GET parameters (shareable URL)
            window.location.href = '/our-services?' + params.toString();
        });
    });
</script>

        </div>
    </div>

    {{--    @include("orderitem.glxv3.css-js")--}}

@endsection
