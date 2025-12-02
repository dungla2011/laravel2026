<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=1024, initial-scale=1.0">
    <title>HZM Plastic - Sơ đồ Nhà Xưởng Sản Xuất</title>
    <!-- Thêm thư viện Mermaid.js từ CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mermaid/10.6.1/mermaid.min.js"></script>
    <!-- Thêm thư viện panzoom để hỗ trợ zoom -->
    <script src='https://unpkg.com/panzoom@9.4.0/dist/panzoom.min.js'></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            overflow: hidden; /* Ngăn cuộn trang khi ở chế độ fullscreen */
        }
        .container {
            width: 100%;
            height: 100vh;
            background-color: white;
            display: flex;
            flex-direction: column;
        }
        header {
            padding: 10px 20px;
            background-color: #333;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .controls {
            display: flex;
            gap: 10px;
        }
        button {
            padding: 8px 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .content {
            flex: 1;
            display: flex;
            overflow: hidden;
        }
        .diagram-container {
            flex: 1;
            padding: 20px;
            overflow: hidden;
            position: relative;
        }
        #diagram-wrapper {
            width: 100%;
            height: 100%;
            overflow: hidden;
        }
        .mermaid {
            transform-origin: 0 0;
        }
        .info-panel {
            width: 300px;
            padding: 20px;
            background-color: #f0f0f0;
            overflow-y: auto;
            transition: transform 0.3s ease;
        }
        .hide-info .info-panel {
            transform: translateX(100%);
        }
        .zoom-info {
            position: absolute;
            bottom: 10px;
            left: 10px;
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 14px;
        }
        /* Chỉnh lại màu sắc cho đồ thị Mermaid */
        .fullscreen {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 9999;
            background-color: white;
        }
    </style>
</head>
<body>
<div class="container" id="main-container">
    <header>
        <h1>Sơ đồ Nhà Xưởng Sản Xuất - HZM PLASTIC</h1>
        <div class="controls">
            <button id="zoom-in">Phóng to (+)</button>
            <button id="zoom-out">Thu nhỏ (-)</button>
            <button id="reset-zoom">Đặt lại (R)</button>
            <button id="toggle-info">Ẩn/Hiện thông tin</button>
            <button id="fullscreen-btn">Toàn màn hình</button>
        </div>
    </header>

    <div class="content">
        <div class="diagram-container">
            <div id="diagram-wrapper">
                <!-- Sơ đồ Mermaid -->
                <div class="mermaid" id="mermaid-diagram">
                    flowchart TD
                    subgraph "Nhà Xưởng Sản Xuất"
                    direction LR
                    subgraph "Khu Vực Nhập Liệu"
                    A[Kho Nguyên Liệu] --> B[Kiểm Tra Chất Lượng]
                    B --> C[Phân Loại Nguyên Liệu]
                    end

                    subgraph "Khu Vực Sản Xuất"
                    D[Máy Cắt] --> E[Máy Dập]
                    E --> F[Dây Chuyền Lắp Ráp]
                    F --> G[Kiểm Tra Sản Phẩm]
                    end

                    subgraph "Khu Vực Đóng Gói"
                    H[Đóng Gói] --> I[Dán Nhãn]
                    I --> J[Đóng Thùng]
                    end

                    subgraph "Kho Thành Phẩm"
                    K[Lưu Kho] --> L[Chuẩn Bị Xuất Hàng]
                    end

                    C --> D
                    G --> H
                    J --> K

                    subgraph "Văn Phòng Điều Hành"
                    M[Phòng Quản Lý]
                    N[Phòng Kỹ Thuật]
                    O[Phòng Họp]
                    end

                    subgraph "Khu Vực Phụ Trợ"
                    P[Phòng Nghỉ Công Nhân]
                    Q[Căn Tin]
                    R[Bãi Đậu Xe]
                    end
                    end
                </div>
            </div>
            <div class="zoom-info">Zoom: 100%</div>
        </div>

        <div class="info-panel">
            <h2>Chú thích</h2>
            <ul>
                <li><strong>Khu Vực Nhập Liệu</strong>: Nơi tiếp nhận, kiểm tra và phân loại nguyên liệu</li>
                <li><strong>Khu Vực Sản Xuất</strong>: Các máy cắt, máy dập, dây chuyền lắp ráp và kiểm tra sản phẩm</li>
                <li><strong>Khu Vực Đóng Gói</strong>: Nơi đóng gói, dán nhãn và đóng thùng sản phẩm</li>
                <li><strong>Kho Thành Phẩm</strong>: Khu vực lưu trữ và chuẩn bị xuất hàng</li>
                <li><strong>Văn Phòng Điều Hành</strong>: Bao gồm phòng quản lý, phòng kỹ thuật và phòng họp</li>
                <li><strong>Khu Vực Phụ Trợ</strong>: Có phòng nghỉ công nhân, căn tin và bãi đậu xe</li>
            </ul>

            <h2>Hướng dẫn sử dụng</h2>
            <ul>
                <li><strong>Phóng to/Thu nhỏ</strong>: Sử dụng nút +/- hoặc cuộn chuột</li>
                <li><strong>Di chuyển</strong>: Nhấn và kéo chuột</li>
                <li><strong>Đặt lại</strong>: Nhấn nút R để đưa về kích thước ban đầu</li>
                <li><strong>Toàn màn hình</strong>: Nhấn nút "Toàn màn hình" để mở rộng</li>
            </ul>
        </div>
    </div>
</div>

<script>
    // Khởi tạo Mermaid
    mermaid.initialize({
        startOnLoad: true,
        theme: 'default',
        flowchart: {
            useMaxWidth: false,
            htmlLabels: true,
            curve: 'basis'
        }
    });

    // Đợi Mermaid render xong
    document.addEventListener('DOMContentLoaded', function() {
        // Đợi một chút để Mermaid hoàn thành render
        setTimeout(function() {
            // Lấy các phần tử
            const diagramWrapper = document.getElementById('diagram-wrapper');
            const mermaidSvg = diagramWrapper.querySelector('svg');
            const zoomInfo = document.querySelector('.zoom-info');
            const container = document.getElementById('main-container');
            const fullscreenBtn = document.getElementById('fullscreen-btn');
            const toggleInfoBtn = document.getElementById('toggle-info');
            const contentDiv = document.querySelector('.content');

            // Thiết lập PanZoom
            if (mermaidSvg) {
                // Đảm bảo SVG đủ lớn
                mermaidSvg.style.width = '100%';
                mermaidSvg.style.height = '100%';
                mermaidSvg.style.minWidth = '800px';
                mermaidSvg.style.minHeight = '600px';

                // Khởi tạo PanZoom
                const panzoomInstance = panzoom(mermaidSvg, {
                    maxZoom: 20,
                    minZoom: 0.1,
                    zoomSpeed: 0.1,
                    bounds: true,
                    boundsPadding: 0.1
                });

                // Theo dõi mức zoom
                panzoomInstance.on('zoom', function(e) {
                    const zoom = Math.round(e.getTransform().scale * 100);
                    zoomInfo.textContent = `Zoom: ${zoom}%`;
                });

                // Nút phóng to
                document.getElementById('zoom-in').addEventListener('click', function() {
                    panzoomInstance.zoomIn();
                });

                // Nút thu nhỏ
                document.getElementById('zoom-out').addEventListener('click', function() {
                    panzoomInstance.zoomOut();
                });

                // Nút đặt lại
                document.getElementById('reset-zoom').addEventListener('click', function() {
                    panzoomInstance.moveTo(0, 0);
                    panzoomInstance.zoomAbs(0, 0, 1);
                });

                // Phím tắt
                document.addEventListener('keydown', function(e) {
                    if (e.key === '+' || e.key === '=') {
                        panzoomInstance.zoomIn();
                    } else if (e.key === '-') {
                        panzoomInstance.zoomOut();
                    } else if (e.key === 'r' || e.key === 'R') {
                        panzoomInstance.moveTo(0, 0);
                        panzoomInstance.zoomAbs(0, 0, 1);
                    }
                });
            }

            // Chức năng toàn màn hình
            fullscreenBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    if (container.requestFullscreen) {
                        container.requestFullscreen();
                    } else if (container.mozRequestFullScreen) {
                        container.mozRequestFullScreen();
                    } else if (container.webkitRequestFullscreen) {
                        container.webkitRequestFullscreen();
                    } else if (container.msRequestFullscreen) {
                        container.msRequestFullscreen();
                    }
                    fullscreenBtn.textContent = "Thoát toàn màn hình";
                } else {
                    if (document.exitFullscreen) {
                        document.exitFullscreen();
                    } else if (document.mozCancelFullScreen) {
                        document.mozCancelFullScreen();
                    } else if (document.webkitExitFullscreen) {
                        document.webkitExitFullscreen();
                    } else if (document.msExitFullscreen) {
                        document.msExitFullscreen();
                    }
                    fullscreenBtn.textContent = "Toàn màn hình";
                }
            });

            // Theo dõi sự thay đổi trạng thái toàn màn hình
            document.addEventListener('fullscreenchange', updateFullscreenButton);
            document.addEventListener('webkitfullscreenchange', updateFullscreenButton);
            document.addEventListener('mozfullscreenchange', updateFullscreenButton);
            document.addEventListener('MSFullscreenChange', updateFullscreenButton);

            function updateFullscreenButton() {
                if (document.fullscreenElement) {
                    fullscreenBtn.textContent = "Thoát toàn màn hình";
                } else {
                    fullscreenBtn.textContent = "Toàn màn hình";
                }
            }

            // Ẩn/Hiện bảng thông tin
            toggleInfoBtn.addEventListener('click', function() {
                contentDiv.classList.toggle('hide-info');
            });
        }, 1000); // Đợi 1 giây để đảm bảo Mermaid đã render xong
    });
</script>
</body>
</html>
