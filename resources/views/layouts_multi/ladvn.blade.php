
<!doctype html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />

    @yield("css")
    <style>
        :root {
            --bg-1: #071019;
            --bg-2: #0f2434;
            --bg-3: #15384b;
            --accent: #00d1b2;
            --accent-2: #2de2ff;
            --text-main: #f3fbff;
            --text-soft: #b9d5df;
            --card-bg: rgba(255, 255, 255, 0.06);
            --card-border: rgba(255, 255, 255, 0.2);
        }

        body {
            font-family: "Space Grotesk", sans-serif;
            color: var(--text-main);
            min-height: 100vh;
            background:
                radial-gradient(circle at 20% 20%, rgba(45, 226, 255, 0.22), transparent 45%),
                radial-gradient(circle at 78% 18%, rgba(0, 209, 178, 0.2), transparent 38%),
                linear-gradient(150deg, var(--bg-1), var(--bg-2) 45%, var(--bg-3));
        }

        .border-bottom{
            border-bottom: 0px solid #ccc !important;
        }

        .navbar,
        .glass {
            backdrop-filter: blur(10px);
            background: rgba(8, 20, 31, 0.45);
            border: 0px solid rgba(255, 255, 255, 0.1);
        }

        .brand-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            border: 1px solid rgba(45, 226, 255, 0.55);
            background: rgba(45, 226, 255, 0.08);
            color: var(--accent-2);
            font-weight: 600;
        }

        .hero-title {
            font-weight: 700;
            font-size: clamp(2rem, 4vw, 4rem);
            line-height: 1.05;
            letter-spacing: -0.02em;
        }

        .hero-title span {
            color: var(--accent);
        }

        .soft {
            color: var(--text-soft);
        }

        .btn-main {
            --bs-btn-color: #001f1b;
            --bs-btn-bg: var(--accent);
            --bs-btn-border-color: var(--accent);
            --bs-btn-hover-bg: #00b49a;
            --bs-btn-hover-border-color: #00b49a;
            --bs-btn-active-bg: #0ecab0;
            --bs-btn-active-border-color: #0ecab0;
            font-weight: 600;
        }

        .btn-outline-light {
            border-width: 1.5px;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chat-widget {
            position: fixed;
            right: 1.2rem;
            bottom: 1.2rem;
            z-index: 1080;
        }

        .chat-toggle {
            width: 60px;
            height: 60px;
            border: 0;
            border-radius: 50%;
            display: grid;
            place-items: center;
            font-size: 1.45rem;
            color: #03211c;
            background: linear-gradient(135deg, var(--accent), #65f2df);
            box-shadow: 0 12px 30px rgba(0, 209, 178, 0.4);
        }

        .chat-box {
            position: absolute;
            right: 0;
            bottom: 78px;
            width: min(360px, calc(100vw - 1.2rem));
            border-radius: 1rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(6, 16, 25, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            transform: translateY(10px) scale(0.98);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.22s ease, transform 0.22s ease;
        }

        .chat-box.is-open {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: auto;
        }

        .chat-header {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(45, 226, 255, 0.08);
        }

        .chat-close {
            color: #fff;
            border: 0;
            background: transparent;
            font-size: 1.2rem;
            line-height: 1;
        }

        .chat-body {
            padding: 0.95rem;
        }

        .chat-bubble {
            max-width: 90%;
            padding: 0.7rem 0.8rem;
            border-radius: 0.85rem;
            font-size: 0.92rem;
        }

        .chat-bubble.bot {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .chat-bubble.user {
            margin-left: auto;
            background: rgba(0, 209, 178, 0.2);
            border: 1px solid rgba(0, 209, 178, 0.4);
        }

        .chat-input {
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding: 0.75rem;
        }

        .chat-input .form-control {
            border-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            background: rgba(255, 255, 255, 0.07);
        }

        .chat-input .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .floating-card {
            border-radius: 1.2rem;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            box-shadow: 0 16px 45px rgba(0, 0, 0, 0.35);
        }

        .feature-card {
            height: 100%;
            border-radius: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.14);
            transition: transform 0.3s ease, border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-8px);
            border-color: rgba(45, 226, 255, 0.6);
            box-shadow: 0 14px 35px rgba(4, 19, 33, 0.45);
        }

        .icon-badge {
            width: 52px;
            height: 52px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            background: linear-gradient(135deg, rgba(45, 226, 255, 0.2), rgba(0, 209, 178, 0.2));
            border: 1px solid rgba(45, 226, 255, 0.4);
            font-size: 1.4rem;
            color: var(--accent-2);
        }

        .counter {
            font-size: clamp(1.6rem, 2.6vw, 2.2rem);
            font-weight: 700;
        }

        .step {
            position: relative;
            padding-left: 3rem;
        }

        .step::before {
            content: attr(data-step);
            position: absolute;
            left: 0;
            top: 0;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: rgba(0, 209, 178, 0.16);
            border: 1px solid rgba(0, 209, 178, 0.6);
            color: var(--accent);
            font-weight: 700;
        }

        .fade-up {
            opacity: 0;
            transform: translateY(16px);
            animation: fadeUp 0.8s forwards;
        }

        .delay-1 {
            animation-delay: 0.15s;
        }

        .delay-2 {
            animation-delay: 0.3s;
        }

        .delay-3 {
            animation-delay: 0.45s;
        }

        @keyframes fadeUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991px) {
            .hero-visual {
                margin-top: 1.5rem;
            }

            .navbar-collapse {
                margin-top: 0.75rem;
                padding: 0.9rem;
                border-radius: 0.8rem;
                background: rgba(6, 16, 25, 0.88);
                border: 1px solid rgba(255, 255, 255, 0.12);
            }

            .nav-actions {
                margin-top: 0.8rem;
                flex-direction: column;
                align-items: stretch;
            }

            .nav-actions .btn {
                width: 100%;
            }

            .chat-widget {
                right: 0.75rem;
                bottom: 0.9rem;
            }

            .chat-box {
                width: calc(100vw - 1.5rem);
                max-width: 370px;
            }
        }
    </style>
</head>

<body>
<nav class="navbar navbar-expand-lg navbar-dark sticky-top border-bottom border-secondary-subtle">
    <div class="container py-1">
        <a class="navbar-brand text-light fw-semibold" href="#">
            <i class="bi bi-cpu text-info me-2"></i>AIFlow
        </a>
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNav"
            aria-controls="mainNav"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link text-light" href="#features">Tính năng</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="#workflow">Quy trình</a></li>
                <li class="nav-item"><a class="nav-link text-light" href="#about">Giới thiệu</a></li>
            </ul>
            <div class="nav-actions ms-lg-3">
                <a class="btn btn-outline-light" href="/login">Login</a>
            </div>
        </div>
    </div>
</nav>

@yield('content')

<!-- Content -->

<footer class="pt-5 pb-4 soft" style="background: rgba(5, 13, 21, 0.55); border-top: 1px solid rgba(255, 255, 255, 0.1);">
    <div class="container">
        <div class="row g-4 text-start">
            <div class="col-12 col-md-6 col-lg-3">
                <h6 class="text-light fw-semibold mb-3">AIFlow</h6>
                <p class="mb-0">
                    Công ty dịch vụ AI, Chatbot và Automation, đồng hành cùng doanh nghiệp xây hệ thống vận hành thông minh.
                </p>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <h6 class="text-light fw-semibold mb-3">Dịch vụ</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">Tư vấn chiến lược AI</li>
                    <li class="mb-2">Thiết kế AI Chatbot</li>
                    <li class="mb-2">Tự động hóa quy trình</li>
                    <li>Đào tạo vận hành nội bộ</li>
                </ul>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <h6 class="text-light fw-semibold mb-3">Giải pháp</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">Chatbot chăm sóc khách hàng</li>
                    <li class="mb-2">Lead qualification tự động</li>
                    <li class="mb-2">CRM integration workflow</li>
                    <li>Dashboard phân tích hội thoại</li>
                </ul>
            </div>
            <div class="col-12 col-md-6 col-lg-3">
                <h6 class="text-light fw-semibold mb-3">Liên hệ</h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2"><i class="bi bi-geo-alt me-2"></i>Hà Nội, Việt Nam</li>
                    <li class="mb-2"><i class="bi bi-telephone me-2"></i>0901 234 567</li>
                    <li class="mb-2"><i class="bi bi-envelope me-2"></i>hello@aiflow.vn</li>
                    <li><i class="bi bi-globe2 me-2"></i>www.aiflow.vn</li>
                </ul>
            </div>
        </div>
        <hr class="my-4 border-secondary" />
        <small class="d-block text-center">© 2026 AIFlow. Build smart systems, not manual tasks.</small>
    </div>
</footer>

<div class="chat-widget" id="chatWidget">
    <div class="chat-box" id="chatBox" aria-hidden="true">
        <div class="chat-header d-flex align-items-center justify-content-between">
            <div>
                <div class="fw-semibold">AI Support</div>
                <small class="soft">Online 24/7</small>
            </div>
            <button class="chat-close" id="chatClose" aria-label="Đóng chatbox">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="chat-body d-grid gap-2">
            <div class="chat-bubble bot">Xin chào, bạn muốn tư vấn về Chatbot hay Automation?</div>
            <div class="chat-bubble user">Cho mình xem giải pháp cho đội sales.</div>
            <div class="chat-bubble bot">Mình có thể gửi nhanh lộ trình triển khai trong 3 bước cho bạn.</div>
        </div>
        <div class="chat-input">
            <div class="input-group">
                <input type="text" class="form-control" placeholder="Nhập câu hỏi của bạn..." />
                <button class="btn btn-main" type="button">Gửi</button>
            </div>
        </div>
    </div>
    <button class="chat-toggle" id="chatToggle" aria-label="Mở chatbox" aria-expanded="false">
        <i class="bi bi-chat-dots-fill"></i>
    </button>
</div>

<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"
></script>
<script>
    const chatToggle = document.getElementById("chatToggle");
    const chatClose = document.getElementById("chatClose");
    const chatBox = document.getElementById("chatBox");
    const chatWidget = document.getElementById("chatWidget");

    function setChatOpen(isOpen) {
        chatBox.classList.toggle("is-open", isOpen);
        chatToggle.setAttribute("aria-expanded", String(isOpen));
        chatBox.setAttribute("aria-hidden", String(!isOpen));
    }

    chatToggle.addEventListener("click", () => {
        setChatOpen(!chatBox.classList.contains("is-open"));
    });

    chatClose.addEventListener("click", () => setChatOpen(false));

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape") {
            setChatOpen(false);
        }
    });

    document.addEventListener("click", (event) => {
        if (!chatWidget.contains(event.target) && chatBox.classList.contains("is-open")) {
            setChatOpen(false);
        }
    });
</script>
<!-- Code injected by live-server -->
<script>
    // <![CDATA[  <-- For SVG support
    if ('WebSocket' in window) {
        (function () {
            function refreshCSS() {
                var sheets = [].slice.call(document.getElementsByTagName("link"));
                var head = document.getElementsByTagName("head")[0];
                for (var i = 0; i < sheets.length; ++i) {
                    var elem = sheets[i];
                    var parent = elem.parentElement || head;
                    parent.removeChild(elem);
                    var rel = elem.rel;
                    if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
                        var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
                        elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
                    }
                    parent.appendChild(elem);
                }
            }
            var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
            var address = protocol + window.location.host + window.location.pathname + '/ws';
            var socket = new WebSocket(address);
            socket.onmessage = function (msg) {
                if (msg.data == 'reload') window.location.reload();
                else if (msg.data == 'refreshcss') refreshCSS();
            };
            if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
                console.log('Live reload enabled.');
                sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
            }
        })();
    }
    else {
        console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
    }
    // ]]>
</script>
</body>
</html>
