<?php


if($pr = request("param")){
    $pr = substr($pr,0,1000);
    $IP = request()->ip();
    outputT("/var/glx/weblog/zalo.log","$IP - PRZL2 = $pr");

    die("OKFROMGLX2");
}

?>

    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="ZaloPC Multi: chạy nhiều tài khoản zalo trên PC, không phải bản Web, rất dễ dàng! Zalo PC nhanh chóng tiện lợi hơn web rất nhiều, bạn sẽ không bỏ lỡ một tin nhắn nào,
    và chạy bao nhiêu tài khoản một lúc cũng được" />
    <meta name="author" content="" />
    <title>ZaloPC Multi - Chạy nhiều tài khoản Zalo Trên PC (không phải chạy với Web) </title>

    <link rel="icon" type="image/x-icon" href="//startbootstrap.github.io/startbootstrap-scrolling-nav/assets/favicon.ico" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="//startbootstrap.github.io/startbootstrap-scrolling-nav/css/styles.css" rel="stylesheet" />
</head>
<body id="page-top">
<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <div class="container px-4">
        <a class="navbar-brand" href="#page-top">Galaxy Technical Support</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ms-auto">
                <li>
                    <a class="nav-link" href="/download/ZaloPC-Multi-Setup.zip" style="color: white; text-decoration: none"> Download </a>
                </li>
                <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>
<!-- Header-->
<header class="bg-primary bg-gradient text-white">
    <div class="container px-4 text-center">
        <h1 class="fw-bolder">

            <img src="/images/icon/zalo2.png" alt="" style="max-width: 80px">
            <br> <br>
            Phần mềm chạy nhiều Zalo trên Máy tính
        </h1>
        <p class="lead">
            Chạy nhiều bản ZaloPC không giới hạn, Vô cùng dễ dàng Tiện lợi!
            <br> <br>
            Bạn có nhiều số điện thoại, cần chạy nhiều tài khoản Zalo trên máy tính?
            <br>
            Bạn không muốn bỏ lỡ tin nhắn của Khách hàng?
            <br>
            Bảo mật: phần mềm Tuyệt đối An toàn, nếu là virus thì chắc chắn sẽ bị xoá bởi Window
            <br>
            <br>
            <i>
                <b>
            "Tôi có vài Số Phone cần Dùng zalo, Trước đây tôi rất vất vả, phải mở nhiều Web, nhiều máy tính...
                    <br>
            Tìm hiểu nhiều cách, cài đặt nhiều phần mềm khác nhau, thật là bất tiện...
            <br>
            Cho đến khi tôi phát hiện ra phần mềm Tuyệt vời này! Không thể tin được và nó lại Miễn phí!
                    <br>
                    Doanh số của tôi đã tăng trông thấy" - Anh Ánh - Chủ cửa hàng
                </b>
            </i>
        </p>
{{--        <a class="btn btn-lg btn-light" href="#about">Start scrolling!</a>--}}
    </div>
    <style>
        section {
            padding-top: 3rem;
            padding-bottom: 3rem;
        }
    </style>
</header>
<!-- About section-->
<section id="about">
    <div class="container px-4">
        <div class="row gx-4 justify-content-center">
            <div class="col-lg-8">
                <h2>Hướng dẫn</h2>

                <ul>
                    <li>
                        <a href="/download/ZaloPC-Multi-Setup.zip" style="text-decoration: none">
                            Tải phần mềm ZaloPC-Multi Tại đây
                        </a>
                    </li>
                    <li>Cài đặt, Chạy phần mềm</li>
                    <li>Nhập số điện thoại + Thêm</li>
                    <li>Bấm nút Mở</li>
                    <li>Zalo bật lên, đăng nhập như thông thường. Xong, Thật dễ dàng!</li>
                    <li>Mỗi lúc khởi động, chạy ZaloPC-Multi trên màn hình!</li>
                </ul>
                <img style="max-width: 100%; border: 2px dashed #ccc; padding: 5px" src="/images/zalopc-multi.png" alt="">

            </div>
        </div>
    </div>
</section>
<!-- Services section-->
<section class="bg-light" id="services">
    <div class="container px-4">
        <div class="row gx-4 justify-content-center">
            <div class="col-lg-8">
                <h2>Phần mềm Miễn phí</h2>
                <p class="lead"> Xin liên hệ nếu cần Hỗ trợ</p>
            </div>
        </div>
    </div>
</section>
<!-- Footer-->
<footer class="py-5 bg-dark">
    <div class="container px-4"><p class="m-0 text-center text-white">Copyright &copy; GalaxyCloud 2025</p></div>
</footer>
<!-- Bootstrap core JS-->
<script src="http://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Core theme JS-->
<script src="//startbootstrap.github.io/startbootstrap-scrolling-nav/js/scripts.js"></script>
</body>
</html>
