<?php
$keyAndName = config('vps_config.specs');
?>

<div class="pricing-header mb-3 text-orange" data-code-pos='ppp17600530813801'>
    <h2 class="text-orange" style="text-shadow: 5px 2px 4px rgba(0,0,0,0.2);">CHỌN VPS</h2>
</div>

<style>
    .card-item-price{
        box-shadow: 0 0 0 .05rem rgba(8, 60, 130, .06), 0 0 1.25rem rgba(30, 34, 40, .04);
        border: 0;
    }
    .card-body {
        padding: 1rem 1rem;
    }

</style>

<div style="font-size: 100%">
    <div class="container pt-3">
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
                        $plan->number_ip_address
                    ); // Returns VND

                    $initialPriceFormatted = number_format($initialPriceVND, 0, ',', '.');
                @endphp

                <div class="col-md-6 col-lg-3">
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
                            <div class="price-section mt-3 pt-3 text-center">
                                <p class="mb-0">
                                    <span class="text-muted small">Giá:</span>
                                    <span class="price-display text-danger font-weight-bold" style="font-size: 1.3em;" data-plan-id="{{ $plan->id }}">
                                        {{ $initialPriceFormatted }}đ
                                    </span>
                                </p>
                            </div>
                        </div>

                        <!-- Card Footer -->
                        <div class="card-footer bg-light border-0">
                            <button class="btn btn-primary w-100 select-vps-btn" data-plan-id="{{ $plan->id }}">
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
        const priceDisplay = card.querySelector('.price-display');
        const specs = getSpecsFromCard(card);

        // Hiển thị trạng thái loading
        priceDisplay.innerHTML = '<i class="fas fa-spinner fa-spin"></i>...';

        // Tạo query string
        const params = new URLSearchParams(specs);

        fetch(`${API_ENDPOINT}?${params}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    priceDisplay.textContent = data.data.total_price_formatted;
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

            // Redirect with GET parameters (shareable URL)
            window.location.href = '/our-services?' + params.toString();
        });
    });
</script>
