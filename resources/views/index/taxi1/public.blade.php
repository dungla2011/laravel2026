@extends(getLayoutNameMultiReturnDefaultIfNull())


@section('title')
    <?php
    echo \App\Models\SiteMng::getTitle();
    ?>

@endsection

@section('meta-description')<?php
                            echo \App\Models\SiteMng::getDesc()
                            ?>
@endsection

@section('meta-keywords')<?php
                         echo \App\Models\SiteMng::getKeyword()
                         ?>
@endsection

@section('content')

<!-- Main Content -->
<div class="container py-5" id="home">
    <!-- Header -->
    <div class="text-center mb-5" id="booking">
        <h1 class="display-4 fw-bold text-primary">
            <i class="fas fa-taxi me-3"></i>Taxi Hà Nội - Nội Bài
        </h1>
        <p class="lead text-muted">Chọn loại xe và khung giờ phù hợp với bạn
            <br>
            Giá chọn gói cho hành trình</p>


    </div>

    <!-- Taxi Options Tabs -->
    <div class="card shadow-lg">
        <div class="card-header p-0">
            <ul class="nav nav-tabs nav-fill" id="directionTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active fw-bold py-3" id="hanoi-tab" data-bs-toggle="tab" data-bs-target="#hanoi-to-noibai" type="button" role="tab">
                        <i class="fas fa-plane-departure me-2"></i>
                        Hà Nội → Nội Bài
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link fw-bold py-3" id="noibai-tab" data-bs-toggle="tab" data-bs-target="#noibai-to-hanoi" type="button" role="tab">
                        <i class="fas fa-plane-arrival me-2"></i>
                        Nội Bài → Hà Nội
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="directionTabsContent">
                <!-- Tab 1: Hà Nội -> Nội Bài -->
                <div class="tab-pane fade show active" id="hanoi-to-noibai" role="tabpanel">
                    <!-- Sub-tabs cho loại xe -->
                    <div class="card">
                        <div class="card-header p-0">
                            <ul class="nav nav-pills nav-fill" id="hanoi-car-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="hanoi-5-tab" data-bs-toggle="pill" data-bs-target="#hanoi-5-seats" type="button" role="tab">
                                        <i class="fas fa-car me-2"></i>5 chỗ
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="hanoi-7-tab" data-bs-toggle="pill" data-bs-target="#hanoi-7-seats" type="button" role="tab">
                                        <i class="fas fa-car-side me-2"></i>7 chỗ
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-3">
                            <div class="tab-content" id="hanoi-car-content">
                                <!-- 5 chỗ -->
                                <div class="tab-pane fade show active" id="hanoi-5-seats" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card taxi-card h-100 day-option position-relative" onclick="selectOption(1, 'Hà Nội - Nội Bài', '5 chỗ', 'Giờ ngày (06:30 - 22:00)', 150)">
                                                <div class="discount-badge">-50k</div>
                                                <div class="card-body text-center p-3">
                                                    <div class="price-tag rounded-pill px-3 py-2 mb-3 d-inline-block">
                                                        <div class="price-container">
                                                            <div class="original-price">200k VNĐ</div>
                                                            <h5 class="mb-0 discounted-price">150k VNĐ</h5>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <i class="fas fa-sun fs-1 mb-2"></i>
                                                        <div class="fw-bold">Giờ ngày (06:30 - 22:00)</div>
                                                    </div>
                                                    <button class="btn btn-custom mt-3 w-100 fw-bold" onclick="event.stopPropagation(); showContactOptions('Hà Nội - Nội Bài', '5 chỗ', 'Giờ ngày (06:30 - 22:00)', 150)">
                                                        <i class="fas fa-phone me-2"></i>Gọi ngay
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card taxi-card h-100 night-option position-relative" onclick="selectOption(2, 'Hà Nội - Nội Bài', '5 chỗ', 'Giờ đêm (22:00 - 06:30)', 200)">
                                                <div class="discount-badge">-50k</div>
                                                <div class="card-body text-center p-3">
                                                    <div class="price-tag rounded-pill px-3 py-2 mb-3 d-inline-block">
                                                        <div class="price-container">
                                                            <div class="original-price">250k VNĐ</div>
                                                            <h5 class="mb-0 discounted-price">200k VNĐ</h5>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <i class="fas fa-moon fs-1 mb-2"></i>
                                                        <div class="fw-bold">Giờ đêm (22:00 - 06:30)</div>
                                                    </div>
                                                    <button class="btn btn-custom mt-3 w-100 fw-bold" onclick="event.stopPropagation(); showContactOptions('Hà Nội - Nội Bài', '5 chỗ', 'Giờ đêm (22:00 - 06:30)', 200)">
                                                        <i class="fas fa-phone me-2"></i>Gọi ngay
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- 7 chỗ -->
                                <div class="tab-pane fade" id="hanoi-7-seats" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card taxi-card h-100 day-option position-relative" onclick="selectOption(3, 'Hà Nội - Nội Bài', '7 chỗ', 'Giờ ngày (06:30 - 22:00)', 200)">
                                                <div class="discount-badge">-50k</div>
                                                <div class="card-body text-center p-3">
                                                    <div class="price-tag rounded-pill px-3 py-2 mb-3 d-inline-block">
                                                        <div class="price-container">
                                                            <div class="original-price">250k VNĐ</div>
                                                            <h5 class="mb-0 discounted-price">200k VNĐ</h5>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <i class="fas fa-sun fs-1 mb-2"></i>
                                                        <div class="fw-bold">Giờ ngày (06:30 - 22:00)</div>
                                                    </div>
                                                    <button class="btn btn-custom mt-3 w-100 fw-bold" onclick="event.stopPropagation(); showContactOptions('Hà Nội - Nội Bài', '7 chỗ', 'Giờ ngày (06:30 - 22:00)', 200)">
                                                        <i class="fas fa-phone me-2"></i>Gọi ngay
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card taxi-card h-100 night-option position-relative" onclick="selectOption(4, 'Hà Nội - Nội Bài', '7 chỗ', 'Giờ đêm (22:00 - 06:30)', 250)">
                                                <div class="discount-badge">-50k</div>
                                                <div class="card-body text-center p-3">
                                                    <div class="price-tag rounded-pill px-3 py-2 mb-3 d-inline-block">
                                                        <div class="price-container">
                                                            <div class="original-price">300k VNĐ</div>
                                                            <h5 class="mb-0 discounted-price">250k VNĐ</h5>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <i class="fas fa-moon fs-1 mb-2"></i>
                                                        <div class="fw-bold">Giờ đêm (22:00 - 06:30)</div>
                                                    </div>
                                                    <button class="btn btn-custom mt-3 w-100 fw-bold" onclick="event.stopPropagation(); showContactOptions('Hà Nội - Nội Bài', '7 chỗ', 'Giờ đêm (22:00 - 06:30)', 250)">
                                                        <i class="fas fa-phone me-2"></i>Gọi ngay
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Nội Bài -> Hà Nội -->
                <div class="tab-pane fade" id="noibai-to-hanoi" role="tabpanel">
                    <!-- Sub-tabs cho loại xe -->
                    <div class="card">
                        <div class="card-header p-0">
                            <ul class="nav nav-pills nav-fill" id="noibai-car-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="noibai-5-tab" data-bs-toggle="pill" data-bs-target="#noibai-5-seats" type="button" role="tab">
                                        <i class="fas fa-car me-2"></i>5 chỗ
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="noibai-7-tab" data-bs-toggle="pill" data-bs-target="#noibai-7-seats" type="button" role="tab">
                                        <i class="fas fa-car-side me-2"></i>7 chỗ
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-3">
                            <div class="tab-content" id="noibai-car-content">
                                <!-- 5 chỗ -->
                                <div class="tab-pane fade show active" id="noibai-5-seats" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card taxi-card h-100 day-option position-relative" onclick="selectOption(5, 'Nội Bài - Hà Nội', '5 chỗ', 'Giờ ngày (06:30 - 22:00)', 200)">
                                                <div class="discount-badge">-50k</div>
                                                <div class="card-body text-center p-3">
                                                    <div class="price-tag rounded-pill px-3 py-2 mb-3 d-inline-block">
                                                        <div class="price-container">
                                                            <div class="original-price">250k VNĐ</div>
                                                            <h5 class="mb-0 discounted-price">200k VNĐ</h5>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <i class="fas fa-sun fs-1 mb-2"></i>
                                                        <div class="fw-bold">Giờ ngày (06:30 - 22:00)</div>
                                                    </div>
                                                    <button class="btn btn-custom mt-3 w-100 fw-bold" onclick="event.stopPropagation(); showContactOptions('Nội Bài - Hà Nội', '5 chỗ', 'Giờ ngày (06:30 - 22:00)', 200)">
                                                        <i class="fas fa-phone me-2"></i>Gọi ngay
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card taxi-card h-100 night-option position-relative" onclick="selectOption(6, 'Nội Bài - Hà Nội', '5 chỗ', 'Giờ đêm (22:00 - 06:30)', 250)">
                                                <div class="discount-badge">-50k</div>
                                                <div class="card-body text-center p-3">
                                                    <div class="price-tag rounded-pill px-3 py-2 mb-3 d-inline-block">
                                                        <div class="price-container">
                                                            <div class="original-price">300k VNĐ</div>
                                                            <h5 class="mb-0 discounted-price">250k VNĐ</h5>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <i class="fas fa-moon fs-1 mb-2"></i>
                                                        <div class="fw-bold">Giờ đêm (22:00 - 06:30)</div>
                                                    </div>
                                                    <button class="btn btn-custom mt-3 w-100 fw-bold" onclick="event.stopPropagation(); showContactOptions('Nội Bài - Hà Nội', '5 chỗ', 'Giờ đêm (22:00 - 06:30)', 250)">
                                                        <i class="fas fa-phone me-2"></i>Gọi ngay
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- 7 chỗ -->
                                <div class="tab-pane fade" id="noibai-7-seats" role="tabpanel">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card taxi-card h-100 day-option position-relative" onclick="selectOption(7, 'Nội Bài - Hà Nội', '7 chỗ', 'Giờ ngày (06:30 - 22:00)', 250)">
                                                <div class="discount-badge">-50k</div>
                                                <div class="card-body text-center p-3">
                                                    <div class="price-tag rounded-pill px-3 py-2 mb-3 d-inline-block">
                                                        <div class="price-container">
                                                            <div class="original-price">300k VNĐ</div>
                                                            <h5 class="mb-0 discounted-price">250k VNĐ</h5>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <i class="fas fa-sun fs-1 mb-2"></i>
                                                        <div class="fw-bold">Giờ ngày (06:30 - 22:00)</div>
                                                    </div>
                                                    <button class="btn btn-custom mt-3 w-100 fw-bold" onclick="event.stopPropagation(); showContactOptions('Nội Bài - Hà Nội', '7 chỗ', 'Giờ ngày (06:30 - 22:00)', 250)">
                                                        <i class="fas fa-phone me-2"></i>Gọi ngay
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card taxi-card h-100 night-option position-relative" onclick="selectOption(8, 'Nội Bài - Hà Nội', '7 chỗ', 'Giờ đêm (22:00 - 06:30)', 300)">
                                                <div class="discount-badge">-50k</div>
                                                <div class="card-body text-center p-3">
                                                    <div class="price-tag rounded-pill px-3 py-2 mb-3 d-inline-block">
                                                        <div class="price-container">
                                                            <div class="original-price">350k VNĐ</div>
                                                            <h5 class="mb-0 discounted-price">300k VNĐ</h5>
                                                        </div>
                                                    </div>
                                                    <div class="text-center">
                                                        <i class="fas fa-moon fs-1 mb-2"></i>
                                                        <div class="fw-bold">Giờ đêm (22:00 - 06:30)</div>
                                                    </div>
                                                    <button class="btn btn-custom mt-3 w-100 fw-bold" onclick="event.stopPropagation(); showContactOptions('Nội Bài - Hà Nội', '7 chỗ', 'Giờ đêm (22:00 - 06:30)', 300)">
                                                        <i class="fas fa-phone me-2"></i>Gọi ngay
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Booking Form -->
    <div id="bookingSection" class="d-none mt-5" style="display: none">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card booking-form shadow-lg">
                    <div class="card-header text-center py-4">
                        <h3 class="mb-0">
                            <i class="fas fa-clipboard-list me-3"></i>Thông tin đặt xe
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <!-- Selected Option Display -->
                        <div class="alert alert-light mb-4">
                            <div class="row">
                                <div class="col-md-8">
                                    <h5 id="selectedOptionText" class="text-dark mb-0"></h5>
                                </div>
                                <div class="col-md-4 text-end">
                                    <div id="selectedPriceContainer">
                                        <h4 id="selectedPrice" class="text-success mb-0"></h4>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form id="bookingForm">
                            <input type="hidden" id="selectedOptionId" name="option_id">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="customerName" class="form-label">
                                        <i class="fas fa-user me-2"></i>Tên khách hàng
                                    </label>
                                    <input type="text" class="form-control" id="customerName" name="customer_name" placeholder="Nhập tên của bạn">
                                </div>

                                <div class="col-md-6">
                                    <label for="phoneNumber" class="form-label">
                                        <i class="fas fa-phone me-2"></i>Số điện thoại <span class="text-danger">*</span>
                                    </label>
                                    <input type="tel" class="form-control" id="phoneNumber" name="phone_number"
                                           placeholder="0xxxxxxxxx" required>
                                    <div class="invalid-feedback">
                                        Vui lòng nhập số điện thoại hợp lệ (VD: 0987654321)
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label for="pickupDate" class="form-label">
                                        <i class="fas fa-calendar me-2"></i>Ngày đón
                                    </label>
                                    <input type="date" class="form-control" id="pickupDate" name="pickup_date" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="pickupTime" class="form-label">
                                        <i class="fas fa-clock me-2"></i>Giờ đón
                                    </label>
                                    <input type="time" class="form-control" id="pickupTime" name="pickup_time" required>
                                </div>

                                <div class="col-12">
                                    <label for="pickupLocation" class="form-label" id="locationLabel">
                                        <i class="fas fa-map-marker-alt me-2"></i>Điểm đón
                                    </label>
                                    <div class="row g-2">
                                        <div class="col-8">
                                            <input type="text" class="form-control" id="pickupLocation" name="pickup_location"
                                                   placeholder="Nhập địa chỉ điểm đón..." required>
                                        </div>
                                        <div class="col-4">
                                            <button type="button" class="btn btn-outline-light w-100" onclick="openMap()">
                                                <i class="fas fa-map me-2"></i>Chọn trên bản đồ
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12">
                                    <label for="notes" class="form-label">
                                        <i class="fas fa-sticky-note me-2"></i>Ghi chú
                                    </label>
                                    <textarea class="form-control" id="notes" name="notes" rows="3"
                                              placeholder="Ghi chú thêm về chuyến đi (địa chỉ cụ thể, yêu cầu đặc biệt...)"></textarea>
                                </div>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                                <button type="button" class="btn btn-outline-light me-md-2" onclick="cancelBooking()">
                                    <i class="fas fa-times me-2"></i>Hủy
                                </button>
                                <button type="submit" class="btn btn-warning btn-lg">
                                    <i class="fas fa-paper-plane me-2"></i>Đặt xe ngay
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Driver Carousel Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">
                <i class="fas fa-users me-3"></i>Đội Ngũ Lái Xe Chuyên Nghiệp
            </h2>
            <p class="lead">Những tài xế giàu kinh nghiệm, thân thiện và đáng tin cậy</p>
        </div>

        <div id="driverCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="row g-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center bg-white text-dark h-100">
                                <div class="card-body p-4">
                                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(45deg, #28a745, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white fs-2"></i>
                                    </div>
                                    <h5 class="card-title">Anh Minh</h5>
                                    <p class="text-muted">5 năm kinh nghiệm</p>
                                    <div class="mb-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-2">5/5</span>
                                    </div>
                                    <small class="text-muted">"Lái xe an toàn, chu đáo"</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center bg-white text-dark h-100">
                                <div class="card-body p-4">
                                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(45deg, #007bff, #6610f2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white fs-2"></i>
                                    </div>
                                    <h5 class="card-title">Anh Tuấn</h5>
                                    <p class="text-muted">8 năm kinh nghiệm</p>
                                    <div class="mb-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-2">5/5</span>
                                    </div>
                                    <small class="text-muted">"Thân thiện, đúng giờ"</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center bg-white text-dark h-100">
                                <div class="card-body p-4">
                                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(45deg, #dc3545, #fd7e14); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white fs-2"></i>
                                    </div>
                                    <h5 class="card-title">Anh Hùng</h5>
                                    <p class="text-muted">6 năm kinh nghiệm</p>
                                    <div class="mb-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-2">5/5</span>
                                    </div>
                                    <small class="text-muted">"Lái xe êm ái, an toàn"</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center bg-white text-dark h-100">
                                <div class="card-body p-4">
                                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(45deg, #6f42c1, #e83e8c); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white fs-2"></i>
                                    </div>
                                    <h5 class="card-title">Anh Đức</h5>
                                    <p class="text-muted">7 năm kinh nghiệm</p>
                                    <div class="mb-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-2">5/5</span>
                                    </div>
                                    <small class="text-muted">"Nhiệt tình, hỗ trợ tốt"</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="row g-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center bg-white text-dark h-100">
                                <div class="card-body p-4">
                                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(45deg, #20c997, #17a2b8); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white fs-2"></i>
                                    </div>
                                    <h5 class="card-title">Anh Nam</h5>
                                    <p class="text-muted">4 năm kinh nghiệm</p>
                                    <div class="mb-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-2">5/5</span>
                                    </div>
                                    <small class="text-muted">"Lịch sự, chu đáo"</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center bg-white text-dark h-100">
                                <div class="card-body p-4">
                                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(45deg, #ffc107, #fd7e14); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white fs-2"></i>
                                    </div>
                                    <h5 class="card-title">Anh Vinh</h5>
                                    <p class="text-muted">9 năm kinh nghiệm</p>
                                    <div class="mb-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-2">5/5</span>
                                    </div>
                                    <small class="text-muted">"Giàu kinh nghiệm, tin cậy"</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center bg-white text-dark h-100">
                                <div class="card-body p-4">
                                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(45deg, #198754, #20c997); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white fs-2"></i>
                                    </div>
                                    <h5 class="card-title">Anh Thắng</h5>
                                    <p class="text-muted">6 năm kinh nghiệm</p>
                                    <div class="mb-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-2">4.8/5</span>
                                    </div>
                                    <small class="text-muted">"Nhanh nhẹn, vui vẻ"</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card text-center bg-white text-dark h-100">
                                <div class="card-body p-4">
                                    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; background: linear-gradient(45deg, #0d6efd, #6610f2); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user text-white fs-2"></i>
                                    </div>
                                    <h5 class="card-title">Anh Long</h5>
                                    <p class="text-muted">5 năm kinh nghiệm</p>
                                    <div class="mb-2">
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <i class="fas fa-star text-warning"></i>
                                        <span class="ms-2">4.7/5</span>
                                    </div>
                                    <small class="text-muted">"Tận tình, nhiệt huyết"</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carousel Controls -->
            <button class="carousel-control-prev" type="button" data-bs-target="#driverCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#driverCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>

            <!-- Carousel Indicators -->
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#driverCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#driverCarousel" data-bs-slide-to="1"></button>
            </div>
        </div>
    </div>
</section>


@endsection
