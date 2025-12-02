@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    Triệu phú 24h
@endsection

@section("css")
    <style>

        table.glx01 {
            border: 1px #ccc solid;
            border-collapse: collapse;
            margin: 0px 0px 0px 0px;
        }

        table.glx01 td {
            /*border: 2px #ccc solid;*/
            /*padding: 3px 5px 3px 5px;*/
            font-size: small;
            font-family: courier, monospace;
            background-color: white;
        }

        table.glx01 th {
            /*border: 2px #ccc solid;*/
            /*padding: 5px 8px;*/
            font-size: small;
            font-family: courier, monospace;
            background-color: white;
            text-align: center;
        }

        table.glx01 tr:nth-child(even) {
        }

        table.glx01 tr:nth-child(odd) {
        }

        li {
            margin-left: 5px;
            list-style-type: none;
        }

        .jumbotron{
            padding: 2rem;
        }
        .redbold {
            color: red;
            font-weight: bold;

        }
        .jumbotron{
            text-align: justify;
            padding: 30px 30px;
        }


    </style>

@endsection

@section('content')

    <?php
        if(!isSupperAdminDevCookie()){
            //die('Under construction! <a href="/login"> LOGIN </a>');
        }
    ?>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.css"/>
    <style>
        .blink_me {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }

        .caption1 {
            text-align: center;
            width: 100%;
            position: fixed;
            top: 30%;

        }

        .sub_li {
            font-size: small;
        }

        .bold_sub_li {
            font-weight: bold;
            color: red;
        }


        .caption1 .txt1 {
            font-size: 50px;
            font-style: italic;
            font-weight: bold;
            color: red;
            text-shadow: 2px 0 white, -2px 0 white, 0 2px white, 0 -2px white, 1px 1px white, -1px -1px white, -1px 1px white, 1px -1px white;
        }

        @media only screen and (max-width: 900px) {
            .caption1 .txt1 {
                font-size: 20px;
            }

            .caption1 a {
                font-size: 15px !important;
            }

            .jumbotron{
                padding: 20px;
            }
        }
    </style>

    <!-- Categories Section Begin -->
    <section class="categories">
        <div class="container-fluid" data-code-pos="ppp1682152176552">
            <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper mySwiper2">
                <div class="swiper-wrapper">
                    <div class="swiper-slide" style="">

                        <div class="caption1" style="">

                            <div class="txt1 ">
                                Triệu phú sau một đêm ...
                            </div>


                            <?php
                            if(!getCurrentUserId()){
                            ?>
                            <a class="btn btn-primary blink_me" href="" style="font-">

                                Đăng ký Ngay!

                            </a>
                            <?php
                            }else{
                            ?>
                            <a class="btn btn-primary blink_me1" href="/member/network-marketing/shb" style="">

                                Truy cập kênh của bạn!

                            </a>
                            <?php
                            }
                            ?>
                        </div>
                        <div>

                        </div>
                        <img style="min-height: 200px"
                             src="/images/trieu-phu.png"/>

                    </div>
                    <div class="swiper-slide">
                        <img style="min-height: 200px"
                            src="/images/trieu-phu.png"/>
                    </div>

                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
            </div>
            <div thumbsSlider="" class="swiper mySwiper" style="display: none">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <img src="https://swiperjs.com/demos/images/nature-1.jpg"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="https://swiperjs.com/demos/images/nature-2.jpg"/>
                    </div>
                    <div class="swiper-slide">
                        <img src="https://swiperjs.com/demos/images/nature-3.jpg"/>
                    </div>

                </div>
            </div>

        </div>
    </section>
    <!-- Categories Section End -->


    <br>
    <div class="jumbotron container">
        <div style="text-align: center">
            <h2>Hướng dẫn Chiến Dịch SHB</h2>
        </div>

        <div style="text-align: center" class="mt-2 mb-3">
        Chiến dịch Đăng ký tài khoản SHB được tài trợ bởi Ngân hàng SHB
        </div>

        <ul>
            <li>
                <b>Bước 1:</b>

                Đăng ký tài khoản đơn giản với email
                <?php
                if($cuid = getCurrentUserId()){
                ?>
                <br>
                <i class="sub_li">
                    Bạn đã đăng ký tài khoản : <span class="bold_sub_li"><?php echo getUserEmailCurrent_() ?></span>
                </i>

                <?php
                }else{
                ?>
                <a href="/login">Tại đây</a>
                <?php
                }
                ?>
            </li>
            <li>
                <b>
                    Bước 2:
                </b>
                Ra nhập mạng kinh doanh với
                <b>
                    Mã liên kết
                </b>
                của người giới thiệu
                <?php
                if($pr = \App\Models\NetworkMarketing::getMyParent($cuid)){
                ?>
                <br>
                <i class="sub_li">Bạn đã đăng ký Mã Liên kết : <span
                        class="bold_sub_li"> <?php echo qqgetRandFromId_($pr->user_id)  ?></span></i>
                <?php
                }
                ?>
            </li>
            <li>
                <b>
                    Bước 3:
                </b>
                Tải App của ngân hàng SHB tại đây, đăng ký tài khoản ngân hàng theo Hướng dẫn, nhập Mã Giới
                thiệu trên App SHB là :
                <div style="text-align: center">
                    <br>
                <span class="bold_sub_li">
                1234568585
                </span>
                    <br>
                    <span style="font-size: small">
                    (123 456 85 85)
                        </span>
                    <br>
                    <br>
                    <i class="sub_li">Bạn cần nhập mã giới thiệu này để được tham gia vào mạng                    lưới Kiếm tiền quá dễ của chúng tôi.
                        <br>
                        <a target="_blank"
                           href="https://www.shb.com.vn/wp-content/uploads/2021/09/H%C6%B0%E1%BB%9Bng-d%E1%BA%ABn-nh%E1%BA%ADp-M%C3%A3-gi%E1%BB%9Bi-thi%E1%BB%87u-khi-M%E1%BB%9F-G%C3%B3i-t%C3%A0i-kho%E1%BA%A3n-tr%E1%BB%B1c-tuy%E1%BA%BFn-eKYC.pdf">
                            Xem thêm hướng dẫn hình ảnh tại đây của ngân hàng <b>SHB</b>
                        </a>
                    </i>
                </div>
                <br>


            </li>
            <li>
                <b>
                    Bước 4:
                </b>
                Bạn đã có 30k trong tài khoản, Tuy nhiên SHB yêu cầu phải có giao dịch vào và ra, nên bạn cần chuyển vào tài khoản mới 50k và thêm một bước chuyển ra 50k,  để hoàn tất giao dịch yêu cầu
                <br>
                 Sau đó bạn chụp ảnh màn hình gửi vào Zalo để được xác nhận:
                <span class="bold_sub_li">
                    <a href="https://zalo.me/0904043689" target="_blank">09.04.04.3689 <img src="/template/gp1/images/icon-zalo.svg" style="width: 25px" alt=""></a>
                </span>
            </li>
            <li>
                <?php
                if($pr){
                ?>
                <br>
                <span class="sub_li" style="color: red">
                Bạn đã hoàn tất bước này, chúc mừng bạn!
                    </span>
                <?php
                }
                ?>

            </li>
            <li>
                <b>
                    Bước 5:
                </b>
                Bạn hãy hướng dẫn người khác ra nhập mạng của bạn với Mã liên kết của bạn, để có cơ hội LỚN để nhận được <b> Hàng trăm triệu </b> đồng.
                 <a href="/member/network-marketing/shb">Xem mã liên kết của bạn tại đây!</a>
            </li>

        </ul>
        <br>

        <b>
            Chú ý:
        </b>
        <i>
            <br>
            - Ngân hàng không bắt buộc phải có số dư tài khoản. Sau tất cả các bước trên bạn sẽ có 30K sẵn sàng để nạp thẻ điện thoại
            <br>
            - Nếu không nhập mã giới thiệu 1234568585, nghĩa là bạn cũng như người bạn giới thiệu không tham gia vào hệ thống.
            Đồng nghĩa với việc Bạn mất đi cơ hội lớn được hưởng tiền thưởng có thể lớn gấp hàng trăm, nghìn lần từ hệ thống.
            <br>
            - Chọn người giới thiệu: hãy chọn những người thực sự tin cậy, vì các tài khoản tạo ảo, tạo giả có thể dễ dàng bị phát hiện và loại bỏ bởi ngân hàng, nghĩa là bạn sẽ không nhận được thưởng từ các tài khoản này.
            Thông thường cần giới thiệu khoảng ít nhất 5-10 người để tối ưu doanh thu của bạn!
        </i>

    </div>


    <div class="jumbotron container mt-4">
        <h3 style="text-align: center">Cơ chế phân bổ giải thưởng</h3>

        <br>
        Giải thưởng dựa trên số lượng người trong Cây của bạn, giả sử cây của bạn có 10 người đầu tiên, mỗi người lại giới thiệu cho 10 người khác, cứ như vậy đến câp 5, thì tiền thưởng của bạn sẽ tăng mười lần qua mỗi cấp,
        tổng số sẽ như sau:

        <table class="glx01 mt-3 mb-3 table table-bordered" style="margin: 0 auto">
            <tr>
                <th>Cấp</th>
                <th>Số lượng người</th>
                <th>Tiền thưởng</th>
                <th>Tổng Tiền thưởng</th>
            </tr>
            <?php
            $ttsl = 0;
            $oneBonus = 10000;
            for ($i = 1; $i <= 5; $i++) {
                $sl = pow(10, $i);
                $money = 10000 * $sl;
                $ttsl+=$sl;
                $mn2 = number_format($money);
                $vn = \LadLib\Common\cstring2::toTienVietNamString3($money);
                echo "<tr>";
                echo "<td>Cấp $i</td>";
                echo "<td style='font-family: Courier'> $sl </td>";
                echo "<td> $oneBonus đ</td>";
                echo "<td> $mn2 đ  <br>$vn</td>";

                echo "</tr>";
            }
            $money = $oneBonus * $ttsl;
            $mn2 = number_format($money);
            $vn = \LadLib\Common\cstring2::toTienVietNamString3($money);
            echo "<tr>";
            echo "<td class='redbold'>Tổng số</td>";
            echo "<td class='redbold'style='font-family: Courier'> $ttsl </td>";
            echo "<td class='redbold'> $oneBonus đ</td>";
            echo "<td class='redbold'> $mn2 đ <br> $vn</td>";
            echo "</tr>";


            ?>
        </table>



        Dựa trên mạng lưới phân phối của bạn, bạn có thể có lợi nhuận khổng lồ
        <br>
        Ví dụ bảng trên:
        <br>
        - Bạn giới thiệu được cho 10 người đăng ký (có thể nhiều hơn) tài khoản ngân hàng
        <b>
        SHB
        </b>
        , họ nghiễm nhiên sẽ được nhận 30k do ngân hàng tài trợ ngay vào tài khoản đăng ký
        <br>
        Đây là đại lý Cấp 1 của bạn, bạn sẽ nhận được 5 nghìn/1 người thuộc đại lý cấp 1, nghĩa là bạn thu 100K (10x10)
        <br>
        - Sau đó, mỗi người đó giới thiệu cho 10 người khác, như vậy bạn sẽ có 100 ở cấp 2.
        <br>
        Bạn sẽ thu được 10x100 = 1000k ( 1 triệu) cho 100 người ở cấp 2
        <br>
        ... Cứ như vậy bạn sẽ thu được tận đến đại lý cấp 5, số tiền có thể là 1 tỷ đồng, hoặc hoàn toàn có thể cao
        hơn nếu bất chợt ở cây của bạn có 1 vài người có số thành viên lớn!

        Vì chính Bạn hoặc có những người có thể giới thiệu hàng trăm thành viên tham gia một cách dễ dàng!

        <br>
        - Không những vậy, bất cứ ai cũng có thể thu được đến đại lý Cấp 5 dù người đó thuộc cây bên dưới hay bên trên
        của bạn!


    </div>
    <div class="jumbotron container mt-4">
        <h3 style="text-align: center">
            <i class="fa fa-usd"></i>
            Nhận thưởng</h3>

        <br>
        - Bạn sẽ nhận phần thưởng tiền mặt sau 01 tháng tham gia, do hệ thống cần thời gian đối soát từ ngân hàng với các tài khoản trên cây của bạn!
        <br>
        - Tiền thưởng sẽ được chuyển qua Số tài khoản Ngân hàng của Bạn xác thực cùng Email đăng ký (hoặc số điện thoại)
{{--        <br><br>--}}
{{--        <div style="text-align: center">--}}
{{--            <img src="images/trieu-phu3.png" alt="" style="width: 300px">--}}
{{--        </div>--}}
    </div>


    <div class="jumbotron container mt-4">
        <h3 style="text-align: center">Cơ hội</h3>
        <br>

            Đây là Mô hình Kinh doanh theo mạng rất khoa học, mà cả những người nổi tiếng như BillGate cũng từng ca
            ngợi.
            <br> Đơn giản là vì, thay vì chia sẻ cho các đại lý chuyên nghiệp, thì giờ chia sẻ cho chính bạn, một người
            thông minh để chớp cơ hội
            <br> Riêng với chúng tôi, cách làm này hoàn toàn khác biệt với hầu hết các sản phẩm khác, các sản phẩm khác
            yêu cầu bạn phải nộp tiền, và thường là một số tiền không nhỏ.
            <br>
            Ở đây bạn được tiền, không phải mất một đồng nào (chuyển vào rồi lại chuyển ra), và được đảm bảo bởi Ngân
            hàng Uy tín hàng đầu Việt nam - <b>SHB </b>
            <br>
        <i>
            Đặc biệt: Không chỉ dừng lại ở đây, Khi tham gia, chắc chắn bạn sẽ được ưu tiên cơ hội hợp tác trong các chương trình tiếp theo!
        </i>


        <br><br>
        <div class="text-center">
        <img src="/images/co-hoi.webp" alt="">
        </div>

        <?php
        if(1){
        ?>

        <br>
        <b>
            <i>
                Nhiệm vụ của bạn đơn giản hơn bao giờ hết: giới thiệu mọi người tham gia để họ được tiền, và có cơ
                hội kiếm bội tiền!
                <br>
                Thật Hiếm có cơ hội mà tất cả đều WIN thế này.<br>
                Bí mật: cần nhanh chóng thực hiện để chiếm lĩnh đỉnh cao! <br>
                Chúc bạn thành công rực rỡ!
            </i>
        </b>
        <?php
        }

        ?>

        <br>


    </div>


    <!-- Swiper JS -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@9/swiper-bundle.min.js"></script>

    <!-- Initialize Swiper -->
    <script>
        var swiper = new Swiper(".mySwiper", {
            spaceBetween: 10,
            slidesPerView: 4,
            freeMode: true,
            watchSlidesProgress: true,
        });
        var swiper2 = new Swiper(".mySwiper2", {
            spaceBetween: 10,
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },
            thumbs: {
                swiper: swiper,
            },
        });
    </script>

@endsection
