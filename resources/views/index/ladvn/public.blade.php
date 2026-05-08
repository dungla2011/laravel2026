@extends(getLayoutNameMultiReturnDefaultIfNull())


@section('css')

    <style>
        .row1 {
            border-bottom: 2px solid darkorange;
        }

        .heading1 {
            background-color: darkorange;
            color: white;
            display: inline-block;
            font-weight: bold;
            padding: 7px 30px 7px 15px;
            font-size: 20px;
            text-transform: uppercase;
        }
        .heading1 a {
            color: white;
        }
        .cls1 li::after {
            content: '-';
            color: transparent;
        }
    </style>

@endsection
@section('title')
    <?php
//    echo \App\Models\SiteMng::getTitle();
    ?>
    VTim

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

    <header class="py-5 py-lg-6">
      <div class="container py-lg-5">
        <div class="row align-items-center g-4 g-lg-5">
          <div class="col-lg-7">
            <div class="brand-pill fade-up">
              <i class="bi bi-stars"></i> AI Assistant + Automation Platform
            </div>
            <h1 class="hero-title mt-3 fade-up delay-1">
              Biến website thành <span>AI Chatbot</span> làm việc 24/7.
            </h1>
            <p class="soft mt-3 fs-5 fade-up delay-2">
              Tự động trả lời khách hàng, chốt lead, đồng bộ CRM và kích hoạt workflow chỉ trong vài phút.
              Tất cả được vận hành bởi một dashboard trực quan.
            </p>
            <div class="d-flex flex-wrap gap-2 mt-4 fade-up delay-3">
              <a class="btn btn-main btn-lg" href="#contact">Bắt đầu ngay</a>
              <a class="btn btn-outline-light btn-lg" href="#features">Xem demo</a>
            </div>
          </div>

          <div class="col-lg-5 hero-visual">
            <div class="floating-card p-4 fade-up delay-2">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Live Automation</h5>
                <span class="badge text-bg-success">Online</span>
              </div>
              <div class="small soft">Chatbot đang xử lý hội thoại</div>
              <div class="mt-3 p-3 rounded bg-dark bg-opacity-50 border border-info-subtle">
                <div class="fw-semibold">Khách hàng:</div>
                <div class="soft">"Bên bạn có tích hợp chatbot cho fanpage không?"</div>
              </div>
              <div class="mt-2 p-3 rounded" style="background: rgba(0, 209, 178, 0.12); border: 1px solid rgba(0, 209, 178, 0.35);">
                <div class="fw-semibold text-info">AI Bot:</div>
                <div class="soft">"Có, chatbot kết nối Website, Facebook, Zalo và tự đẩy thông tin vào CRM theo tag."</div>
              </div>
              <div class="row text-center mt-4 g-3">
                <div class="col-4">
                  <div class="counter">97%</div>
                  <div class="small soft">Tự động hóa</div>
                </div>
                <div class="col-4">
                  <div class="counter">2.4s</div>
                  <div class="small soft">Tốc độ phản hồi</div>
                </div>
                <div class="col-4">
                  <div class="counter">24/7</div>
                  <div class="small soft">Luôn sẵn sàng</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </header>

    <main>
      <section id="features" class="py-5">
        <div class="container">
          <div class="text-center mb-4 mb-lg-5">
            <h2 class="fw-bold">Nền tảng cho AI, Chatbot và Automation</h2>
            <p class="soft mb-0">Tối ưu từ khâu tiếp cận khách hàng đến vận hành nội bộ.</p>
          </div>

          <div class="row g-4">
            <div class="col-md-6 col-lg-4">
              <article class="feature-card p-4 h-100">
                <div class="icon-badge mb-3"><i class="bi bi-chat-dots"></i></div>
                <h5>AI Chatbot đa kênh</h5>
                <p class="soft mb-0">Một luồng hội thoại thống nhất cho Website, Messenger, Zalo OA và Email.</p>
              </article>
            </div>

            <div class="col-md-6 col-lg-4">
              <article class="feature-card p-4 h-100">
                <div class="icon-badge mb-3"><i class="bi bi-diagram-3"></i></div>
                <h5>Workflow Automation</h5>
                <p class="soft mb-0">Kích hoạt task tự động theo điều kiện: phân loại lead, gửi báo giá, tạo ticket.</p>
              </article>
            </div>

            <div class="col-md-6 col-lg-4">
              <article class="feature-card p-4 h-100">
                <div class="icon-badge mb-3"><i class="bi bi-bar-chart-line"></i></div>
                <h5>Phân tích thời gian thực</h5>
                <p class="soft mb-0">Theo dõi tỉ lệ chuyển đổi, intent khách hàng và hiệu suất bot theo từng chiến dịch.</p>
              </article>
            </div>
          </div>
        </div>
      </section>

      <section id="workflow" class="py-5">
        <div class="container">
          <div class="row g-4 align-items-start">
            <div class="col-lg-6">
              <div class="floating-card p-4 p-lg-5">
                <h3 class="fw-bold mb-4">Quy trình triển khai trong 3 bước</h3>
                <div class="step mb-4" data-step="1">
                  <h6 class="mb-1">Kết nối dữ liệu</h6>
                  <p class="soft mb-0">Import tài liệu sản phẩm, FAQ, chính sách để bot học nhanh theo ngữ cảnh doanh nghiệp.</p>
                </div>
                <div class="step mb-4" data-step="2">
                  <h6 class="mb-1">Thiết kế hội thoại</h6>
                  <p class="soft mb-0">Xây kịch bản chăm sóc khách hàng, upsell và handoff tự động cho nhân viên khi cần.</p>
                </div>
                <div class="step" data-step="3">
                  <h6 class="mb-1">Kích hoạt automation</h6>
                  <p class="soft mb-0">Thiết lập trigger, hành động và đo lường để liên tục tối ưu tỷ lệ chuyển đổi.</p>
                </div>
              </div>
            </div>

            <div class="col-lg-6" id="about">
              <div class="floating-card p-4 p-lg-5 h-100">
                <div class="soft text-uppercase small">Về chúng tôi</div>
                <h3 class="fw-bold mt-1">Đội ngũ triển khai AI thực chiến</h3>
                <p class="soft mt-3">
                  AIFlow là đơn vị dịch vụ tập trung vào tư vấn, thiết kế và triển khai hệ thống AI Chatbot
                  và Automation cho doanh nghiệp tại Việt Nam.
                </p>
                <p class="soft mb-4">
                  Chúng tôi kết hợp công nghệ LLM, tư duy vận hành và kinh nghiệm ngành để xây nền tảng
                  tự động hóa hiệu quả, dễ mở rộng và dễ đo lường.
                </p>
                <div class="row g-3">
                  <div class="col-sm-6">
                    <div class="p-3 rounded border border-info-subtle h-100" style="background: rgba(45, 226, 255, 0.08);">
                      <div class="fw-semibold">200+ dự án</div>
                      <div class="small soft">Đã triển khai chatbot & workflow</div>
                    </div>
                  </div>
                  <div class="col-sm-6">
                    <div class="p-3 rounded border border-success-subtle h-100" style="background: rgba(0, 209, 178, 0.1);">
                      <div class="fw-semibold">15+ lĩnh vực</div>
                      <div class="small soft">Thương mại, giáo dục, bất động sản...</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <section id="contact" class="py-5">
        <div class="container">
          <div class="floating-card p-4 p-lg-5 text-center">
            <h2 class="fw-bold">Sẵn sàng tự động hóa doanh nghiệp của bạn?</h2>
            <p class="soft mx-auto mt-3" style="max-width: 720px;">
              Đặt lịch demo miễn phí 30 phút để nhận blueprint AI Chatbot và Automation phù hợp ngành của bạn.
            </p>
            <div class="d-flex flex-column flex-sm-row gap-2 justify-content-center mt-4">
              <a href="#" class="btn btn-main btn-lg">Đặt lịch ngay</a>
              <a href="#" class="btn btn-outline-light btn-lg">Nhận tài liệu mẫu</a>
            </div>
          </div>
        </div>
      </section>
    </main>


@endsection
