<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thần Số Học Pythagoras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/thansohoc/style.css?v=<?php echo filemtime('/var/www/html/public/assets/thansohoc/style.css'); ?>">
</head>
<body>
<div class="container-fluid px-4 py-3" style="max-width: 1400px;">
    <header class="text-center mb-3">
        <h1 class="h4 fw-bold mb-0" style="color: #667eea;">
            <a href="/" style="text-decoration: none; color: inherit;">🔮 Thần Số Học Pythagoras</a>
        </h1>
    </header>

    <!-- Form nhập liệu -->
    <div class="card shadow-lg mb-3">
        <div class="card-body p-3">
            <form id="numerologyForm">
                <div class="row g-2 align-items-center">
                    <div class="col-md-5">
                        <label for="fullName" class="form-label fw-bold">Họ và Tên Đầy Đủ:</label>
                        <div style="position: relative;">
                            <input type="text" class="form-control" id="fullName" placeholder="VD: Nguyễn Văn An" value="" required autocomplete="off">
                            <div id="historyDropdown" class="dropdown-menu" style="width: 100%; max-height: 300px; overflow-y: auto; display: none;">
                                <!-- Dropdown sẽ được render bởi JS -->
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Ngày Sinh:</label>
                        <!-- Desktop: date input -->
                        <input type="date" class="form-control d-none d-md-block" id="birthDate" value="2001-01-19" required>
                        <!-- Mobile: 3 dropdowns -->
                        <div class="d-md-none">
                            <div class="row g-1">
                                <div class="col-4">
                                    <select class="form-select form-select-sm" id="birthYear" required>
                                        <option value="">Năm</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select class="form-select form-select-sm" id="birthMonth" required>
                                        <option value="">Tháng</option>
                                    </select>
                                </div>
                                <div class="col-4">
                                    <select class="form-select form-select-sm" id="birthDay" required>
                                        <option value="">Ngày</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary w-100" style="margin-top: 31px;">
                            Tính Toán
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Kết quả phân tích - Layout giống bảng lịch sử -->
    <div id="results" class="results d-none">
        <div class="card shadow-lg mb-4">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0 text-center">📊 Kết Quả Phân Tích</h2>
            </div>
            <div class="card-body">
                <!-- Desktop: Table view -->
                <div class="result-table-view">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" id="resultTable">
                            <thead class="table-light">
                            <tr id="resultTableHeader">
                                <!-- Header sẽ được render bởi JS -->
                            </tr>
                            </thead>
                            <tbody id="resultTableBody">
                            <!-- Body sẽ được render bởi JS -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Mobile: Card view -->
                <div class="result-card-view">
                    <div id="resultCard">
                        <!-- Card sẽ được render bởi JS -->
                    </div>
                </div>

                <!-- Chi tiết số chủ đạo -->
                <div class="card mt-4">
                    <div class="card-header bg-success text-white">
                        <h3 class="mb-0" id="detailHeader">📖 Chi Tiết</h3>
                    </div>
                    <div class="card-body">
                        <div id="detailInfo"></div>
                    </div>
                </div>

                <!-- Hành Trình Cuộc Đời -->
                <div class="card mt-4" id="lifeJourneySection" style="display:none;">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0">🛤️ Hành Trình Cuộc Đời</h3>
                    </div>
                    <div class="card-body">
                        <!-- Giai đoạn phát triển -->
                        <div class="mb-4">
                            <h5 class="text-primary">📅 Giai Đoạn Phát Triển</h5>
                            <div id="lifeStagesContent"></div>
                        </div>

                        <!-- Đỉnh cao -->
                        <div class="mb-4">
                            <h5 class="text-warning">⛰️ Đỉnh Cao</h5>
                            <div id="pinnaclesContent"></div>
                        </div>

                        <!-- Thử thách -->
                        <div>
                            <h5 class="text-danger">💪 Thử Thách</h5>
                            <div id="challengesContent"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Công thức tính toán -->
    <div class="card shadow-lg mb-4 d-none">
        <div class="card-header bg-success text-white">
            <h3 class="mb-0">📐 Công Thức Tính Toán</h3>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="formula-box">
                        <h6 class="text-primary">🌟 Số Đường Đời (Life Path Number)</h6>
                        <p class="mb-1"><small>Tách theo cụm: ngày, tháng, năm. Rút gọn từng phần (giữ Master 11, 22, 33), rồi cộng tổng.</small></p>
                        <p class="mb-0"><strong>VD:</strong> 03/11/1979 → 3 + 11 + 8 = 22</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="formula-box">
                        <h6 class="text-info">💫 Số Linh Hồn (Soul Number)</h6>
                        <p class="mb-1"><small>Tổng giá trị các nguyên âm trong họ tên đầy đủ. Rút gọn về 1-9 hoặc Master (11, 22, 33).</small></p>
                        <p class="mb-0"><strong>VD:</strong> NGUYEN VAN AN → U(3) + E(5) + A(1) + A(1) = 10 → 1</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="formula-box">
                        <h6 class="text-success">🎭 Số Tương Tác (Personality Number)</h6>
                        <p class="mb-1"><small>Tổng giá trị các phụ âm trong họ tên đầy đủ. Rút gọn về 1-9 hoặc Master (11, 22, 33).</small></p>
                        <p class="mb-0"><strong>VD:</strong> NGUYEN VAN AN → N(5) + G(7) + Y(7) + ... = tổng → rút gọn</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="formula-box">
                        <h6 class="text-warning">🌈 Số Trưởng Thành (Maturity Number)</h6>
                        <p class="mb-1"><small>Tổng giá trị TẤT CẢ chữ cái trong họ tên đầy đủ. Rút gọn về 1-9 hoặc Master (11, 22, 33).</small></p>
                        <p class="mb-0"><strong>VD:</strong> Tổng tất cả chữ cái → rút gọn</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="formula-box">
                        <h6 class="text-secondary">😊 Số Thái Độ (Attitude Number)</h6>
                        <p class="mb-1"><small>Tổng ngày + tháng sinh. Rút gọn về 1 chữ số (1-9), KHÔNG giữ Master Number.</small></p>
                        <p class="mb-0"><strong>VD:</strong> 03/11 → 3 + 11 = 14 → 5</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="formula-box">
                        <h6 class="text-dark">📅 Số Ngày Sinh (Birth Day Number)</h6>
                        <p class="mb-1"><small>Chính là ngày sinh trong tháng (1-31).</small></p>
                        <p class="mb-0"><strong>VD:</strong> 03/11/1979 → Số 3</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="formula-box">
                        <h6 class="text-danger">⚖️ Số Cân Bằng (Balance Number)</h6>
                        <p class="mb-1"><small>Số lượng chữ cái trong tên (first name).</small></p>
                        <p class="mb-0"><strong>VD:</strong> Nguyễn Văn <strong>An</strong> → 2 chữ cái</p>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="formula-box">
                        <h6 class="text-muted">❌ Số Thiếu Vắng (Missing Numbers)</h6>
                        <p class="mb-1"><small>Các số từ 1-9 không xuất hiện trong họ tên đầy đủ.</small></p>
                        <p class="mb-0"><strong>Ý nghĩa:</strong> Các khía cạnh cần phát triển trong cuộc đời</p>
                    </div>
                </div>
            </div>

            <div class="alert alert-light mt-3 mb-0">
                <strong>📋 Bảng chữ cái Pythagoras:</strong><br>
                <code>
                    1: A, J, S | 2: B, K, T | 3: C, L, U | 4: D, M, V | 5: E, N, W | 6: F, O, X | 7: G, P, Y | 8: H, Q, Z | 9: I, R
                </code><br>
                <small class="text-muted">Tiếng Việt có dấu sẽ tương ứng với chữ gốc (VD: Á, À, Ả, Ã, Ạ = A = 1)</small>
            </div>
        </div>
    </div>

    <!-- Lịch sử ngay trên trang chủ -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
            <h3 class="mb-0">📜 Lịch Sử </h3>
            <div>
                <button class="btn btn-sm btn-success" onclick="exportToCSV()">
                    📥 Tải CSV
                </button>

                <!-- <button class="btn btn-sm btn-info" onclick="exportToJSON()">
                    📄 Tải JSON
                </button> -->

                <button id="toggleHints" class="btn btn-sm btn-light" onclick="toggleHints()">
                    📋 Hiện Chi Tiết
                </button>
            </div>
        </div>
        <div class="card-body">
            <div id="homeHistoryTable" class="table-responsive" style="max-height: 450px; overflow-y: auto;">
                <p class="text-center text-muted py-4">Chưa có lịch sử tra cứu</p>
            </div>
        </div>
    </div>

    <!-- Bảng ý nghĩa các con số -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h3 class="mb-0">📊 Ý Nghĩa Các Con Số Theo Từng Chỉ Số</h3>
        </div>
        <div class="card-body">
            <div class="accordion" id="meaningAccordion">
                <!-- Đường Đời / Life Path -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseLifePath">
                            🌟 Đường Đời - Sứ Mệnh Cốt Lõi
                        </button>
                    </h2>
                    <div id="collapseLifePath" class="accordion-collapse collapse" data-bs-parent="#meaningAccordion">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-warning">
                                    <tr>
                                        <th style="width: 80px;">Số</th>
                                        <th>Ý Nghĩa</th>
                                    </tr>
                                    </thead>
                                    <tbody id="lifePath-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sứ Mệnh / Expression -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseExpression">
                            ✨ Sứ Mệnh - Tài Năng Bẩm Sinh
                        </button>
                    </h2>
                    <div id="collapseExpression" class="accordion-collapse collapse" data-bs-parent="#meaningAccordion">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-warning">
                                    <tr>
                                        <th style="width: 80px;">Số</th>
                                        <th>Ý Nghĩa</th>
                                    </tr>
                                    </thead>
                                    <tbody id="expression-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Nội Tâm / Soul -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSoul">
                            💫 Nội Tâm / Linh Hồn - Động Lực Bên Trong
                        </button>
                    </h2>
                    <div id="collapseSoul" class="accordion-collapse collapse" data-bs-parent="#meaningAccordion">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-info">
                                    <tr>
                                        <th style="width: 80px;">Số</th>
                                        <th>Ý Nghĩa</th>
                                    </tr>
                                    </thead>
                                    <tbody id="soul-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tương Tác / Personality -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePersonality">
                            🎭 Tương Tác - Ấn Tượng Bên Ngoài
                        </button>
                    </h2>
                    <div id="collapsePersonality" class="accordion-collapse collapse" data-bs-parent="#meaningAccordion">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-primary">
                                    <tr>
                                        <th style="width: 80px;">Số</th>
                                        <th>Ý Nghĩa</th>
                                    </tr>
                                    </thead>
                                    <tbody id="personality-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Trưởng Thành / Maturity -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMaturity">
                            🌈 Trưởng Thành - Mục Tiêu Sau 40 Tuổi
                        </button>
                    </h2>
                    <div id="collapseMaturity" class="accordion-collapse collapse" data-bs-parent="#meaningAccordion">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-warning">
                                    <tr>
                                        <th style="width: 80px;">Số</th>
                                        <th>Ý Nghĩa</th>
                                    </tr>
                                    </thead>
                                    <tbody id="maturity-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Thái Độ / Attitude -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAttitude">
                            😊 Thái Độ - Cách Nhìn Cuộc Sống
                        </button>
                    </h2>
                    <div id="collapseAttitude" class="accordion-collapse collapse" data-bs-parent="#meaningAccordion">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-primary">
                                    <tr>
                                        <th style="width: 80px;">Số</th>
                                        <th>Ý Nghĩa</th>
                                    </tr>
                                    </thead>
                                    <tbody id="attitude-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cân Bằng / Balance -->
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBalance">
                            ⚖️ Cân Bằng - Cách Giải Quyết Vấn Đề
                        </button>
                    </h2>
                    <div id="collapseBalance" class="accordion-collapse collapse" data-bs-parent="#meaningAccordion">
                        <div class="accordion-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-info">
                                    <tr>
                                        <th style="width: 80px;">Số</th>
                                        <th>Ý Nghĩa</th>
                                    </tr>
                                    </thead>
                                    <tbody id="balance-table"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mô tả cách tính toán -->
    <div class="card shadow-lg mb-4">
        <div class="card-header bg-info text-white">
            <h3 class="mb-0">📖 Cách Tính Toán</h3>
        </div>
        <div class="card-body">
            <div class="mb-4">
                <h5>🌟 Đường Đời (Life Path)</h5>
                <p><strong>Công thức:</strong> Cộng tất cả các chữ số trong ngày sinh (dd/mm/yyyy), rút gọn về 1-9 hoặc Master Number (11, 22, 33).<br>
                    <strong>Ví dụ:</strong> 19/01/2001 → 1+9+0+1+2+0+0+1 = 14 → 1+4 = 5</p>
            </div>

            <div class="mb-4">
                <h5>✨ Sứ Mệnh (Expression/Destiny)</h5>
                <p><strong>Công thức:</strong> Tổng giá trị TẤT CẢ chữ cái trong họ tên đầy đủ, rút gọn về 1-9 hoặc Master Number (11, 22, 33).<br>
                    <strong>Ví dụ:</strong> NGUYEN VAN AN → Tổng tất cả chữ cái → rút gọn</p>
            </div>

            <div class="mb-4">
                <h5>🎯 Trưởng Thành (Maturity)</h5>
                <p><strong>Công thức:</strong> Đường Đời + Sứ Mệnh, rút gọn về 1-9 hoặc Master Number (11, 22, 33).<br>
                    <strong>Ví dụ:</strong> Đường Đời (5) + Sứ Mệnh (7) = 12 → 1+2 = 3</p>
            </div>

            <div class="mb-4">
                <h5>🎭 Tương Tác (Personality)</h5>
                <p><strong>Công thức:</strong> Tổng giá trị các PHỤ ÂM trong họ tên đầy đủ (Y là nguyên âm khi đứng giữa 2 phụ âm), rút gọn về 1-9 hoặc Master Number (11, 22, 33).<br>
                    <strong>Ví dụ:</strong> NGUYEN VAN AN → N(5) + G(7) + Y(7) + N(5) + V(4) + N(5) = 33</p>
            </div>

            <div class="mb-4">
                <h5>😊 Thái Độ (Attitude)</h5>
                <p><strong>Công thức:</strong> Ngày sinh + Tháng sinh, rút gọn về 1-9 (KHÔNG giữ Master Number).<br>
                    <strong>Ví dụ:</strong> 19/01 → 1+9+0+1 = 11 → 1+1 = 2</p>
            </div>

            <div class="mb-4">
                <h5>💫 Nội Tâm (Soul Urge)</h5>
                <p><strong>Công thức:</strong> Tổng giá trị các NGUYÊN ÂM trong họ tên đầy đủ (Y là phụ âm khi đứng giữa 2 phụ âm), rút gọn về 1-9 hoặc Master Number (11, 22, 33).<br>
                    <strong>Ví dụ:</strong> NGUYEN VAN AN → U(3) + E(5) + A(1) + A(1) = 10 → 1+0 = 1</p>
            </div>

            <div class="mb-4">
                <h5>📊 Các Chỉ Số Khác</h5>
                <p><strong>🔹 Nội Cảm (Inner Self):</strong> Chữ cái đầu của tên (first name)</p>
                <p><strong>🔹 Năng Lực (Approach):</strong> Tổng giá trị các chữ cái trong TÊN (first name only)</p>
                <p><strong>🔹 Trí Tuệ (Intelligence):</strong> Tổng giá trị các chữ cái trong HỌ (last name only)</p>
                <p><strong>🔹 Cân Bằng (Balance):</strong> Chữ cái đầu tiên của HỌ + TÊN + TÊN ĐỆM</p>
                <p><strong>🔹 Thiếu Vắng (Missing Numbers):</strong> Các số từ 1-9 không xuất hiện trong họ tên đầy đủ</p>
            </div>

            <div class="alert alert-info mb-0">
                <p class="mb-0"><strong>📋 Bảng chữ cái Pythagoras:</strong><br>
                    <code>1: A,J,S | 2: B,K,T | 3: C,L,U | 4: D,M,V | 5: E,N,W | 6: F,O,X | 7: G,P,Y | 8: H,Q,Z | 9: I,R</code><br>
                    <small class="text-muted">Tiếng Việt có dấu tương ứng với chữ gốc (VD: Á,À,Ả,Ã,Ạ = A = 1)</small></p>
            </div>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="/assets/thansohoc/numerology-calculator-v2.js?v=<?php echo filemtime('/var/www/html/public/assets/thansohoc/style.css'); ?>"></script>
<script src="/assets/thansohoc/script.js?v=<?php echo filemtime('/var/www/html/public/assets/thansohoc/style.css'); ?>"></script>
</body>
</html>
