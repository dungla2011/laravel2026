<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Camera giao thông, nhận dạng biển số xe, vi phạm giao thông</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .taxi-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 2px solid #e9ecef;
            cursor: pointer;
        }
        .taxi-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            border-color: #007bff;
        }
        .price-tag {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            font-weight: bold;
            font-size: 1.2em;
        }
        .booking-form {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px;
            color: white;
        }
        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        .btn-custom {
            background: #007bff;
            border: none;
            font-weight: bold;
            color: white;
        }
        .btn-custom:hover {
            background: #0056b3;
            transform: translateY(-2px);
            color: white;
        }
        .nav-tabs .nav-link {
            border: none;
            color: #6c757d;
            font-size: 1.1rem;
            transition: all 0.3s ease;
            position: relative;
        }
        .nav-tabs .nav-link.active {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border-radius: 0;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.4);
            transform: translateY(-2px);
        }
        .nav-tabs .nav-link:hover {
            border: none;
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
        }
        .night-option {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            position: relative;
            overflow: hidden;
        }
        .night-option::before {
            content: '✨🌙';
            position: absolute;
            top: 8px;
            right: 10px;
            font-size: 1rem;
            opacity: 0.8;
            animation: twinkle 2s infinite;
        }
        @keyframes twinkle {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 0.4; }
        }
        .night-option .fa-moon {
            color: #f8f9fa !important;
            text-shadow: 0 0 10px rgba(248, 249, 250, 0.5);
        }
        .day-option .fa-sun {
            color: #ffc107 !important;
            text-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
        }
        .nav-pills .nav-link {
            border-radius: 25px;
            margin: 0 5px;
            transition: all 0.3s ease;
            background: rgba(108, 117, 125, 0.1);
            color: #6c757d;
            font-weight: 600;
        }
        .nav-pills .nav-link.active {
            background: #007bff;
            color: white;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            transform: translateY(-2px);
        }
        .nav-pills .nav-link:hover {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
            transform: translateY(-1px);
        }
        .night-option .price-tag {
            background: #6c757d;
        }
        .night-option .btn-custom {
            background: #007bff;
        }
        .night-option .btn-custom:hover {
            background: #0056b3;
        }
        .day-option {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: #333;
        }
        @media (max-width: 768px) {
            .nav-tabs .nav-link {
                font-size: 0.9rem;
                padding: 0.75rem 0.5rem;
            }
            .taxi-card {
                margin-bottom: 1rem;
            }
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
            background: linear-gradient(45deg, #007bff, #28a745);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .nav-link {
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .nav-link:hover {
            color: #007bff !important;
            transform: translateY(-1px);
        }
        .hover-primary:hover {
            color: #007bff !important;
            transform: scale(1.2);
            transition: all 0.3s ease;
        }
        .price-container {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        .original-price {
            text-decoration: line-through;
            color: #dc3545;
            font-size: 0.9rem;
        }
        .discounted-price {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.1rem;
        }
        .discount-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-weight: bold;
        }
        .floating-btn {
            position: fixed;
            left: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: all 0.3s ease;
            text-decoration: none;
            animation: pulse 2s infinite;
        }
        .floating-call-btn {
            bottom: 30px;
            background: linear-gradient(45deg, #28a745, #20c997);
            box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4);
        }
        .floating-call-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(40, 167, 69, 0.6);
            background: linear-gradient(45deg, #20c997, #17a2b8);
        }
        .floating-zalo-btn {
            bottom: 100px;
            background: linear-gradient(45deg, #0068ff, #0091ff);
            box-shadow: 0 4px 20px rgba(0, 104, 255, 0.4);
        }
        .floating-zalo-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 25px rgba(0, 104, 255, 0.6);
            background: linear-gradient(45deg, #0091ff, #00a8ff);
        }
        .floating-btn i {
            color: white;
            font-size: 1.5rem;
        }
        @keyframes pulse {
            0% { box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4), 0 0 0 0 rgba(40, 167, 69, 0.7); }
            70% { box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4), 0 0 0 10px rgba(40, 167, 69, 0); }
            100% { box-shadow: 0 4px 20px rgba(40, 167, 69, 0.4), 0 0 0 0 rgba(40, 167, 69, 0); }
        }
        @keyframes pulseZalo {
            0% { box-shadow: 0 4px 20px rgba(0, 104, 255, 0.4), 0 0 0 0 rgba(0, 104, 255, 0.7); }
            70% { box-shadow: 0 4px 20px rgba(0, 104, 255, 0.4), 0 0 0 10px rgba(0, 104, 255, 0); }
            100% { box-shadow: 0 4px 20px rgba(0, 104, 255, 0.4), 0 0 0 0 rgba(0, 104, 255, 0); }
        }
        .floating-zalo-btn {
            animation: pulseZalo 2s infinite;
        }
        @media (max-width: 768px) {
            .floating-btn {
                left: 20px;
                width: 55px;
                height: 55px;
            }
            .floating-call-btn {
                bottom: 20px;
            }
            .floating-zalo-btn {
                bottom: 85px;
            }
            .floating-btn i {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body class="bg-light">
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
    <div class="container">
        <a class="navbar-brand" href="#home">
            <i class="fas fa-taxi me-2"></i>AI-CAMERA
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#home">
                        <i class="fas fa-home me-1"></i>Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#about">
                        <i class="fas fa-info-circle me-1"></i>Giới thiệu
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" onclick="openPricingModal()">
                        <i class="fas fa-tags me-1"></i>Bảng giá
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#terms">
                        <i class="fas fa-file-contract me-1"></i>DEMO
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-outline-primary ms-2 px-3" href="#booking">
                        <i class="fas fa-phone me-1"></i>Liên hệ
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container py-5" id="home">

    CAMERA GIA0 THONG

</div>
</body>
</html>
