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
            border: 1px #ccc solid;
            padding: 3px 5px 3px 5px;
            font-size: small;
            font-family: courier, monospace;
        }

        table.glx01 th {
            border: 1px #ccc solid;
            padding: 3px 5px 3px 5px;
            font-size: small;
            font-family: courier, monospace;
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
            top: 35%;

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
                                Triệu phú chỉ sau một đêm ...
                            </div>

                            <br>
                            <?php
                            if(!getCurrentUserId()){
                            ?>
                            <a class="btn btn-primary blink_me" href="" style="font-">

                                Đăng ký Ngay!

                            </a>
                            <?php
                            }else{
                            ?>
                            <a class="btn btn-primary blink_me1" href="/member/network-marketing/abc" style="">
                                Truy cập kênh của bạn!
                            </a>
                            <?php
                            }
                            ?>
                        </div>
                        <div>

                        </div>
                        <img style=""
                             src="https://cafefcdn.com/203337114487263232/2022/11/5/photo-1-16676218975261864385687.jpg?x=1"/>

                    </div>
                    <div class="swiper-slide">
                        <img
                            src="https://cafefcdn.com/203337114487263232/2022/11/5/photo-1-16676218975261864385687.jpg?x=1"/>
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
            <h2>Hướng dẫn</h2>
        </div>

        <br>
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
                Tải App của công ty ABC tại đây, đăng ký tài khoản công ty theo Hướng dẫn, nhập Mã Giới
                thiệu trên App ABC là :
                <span class="bold_sub_li">
                12345685....
                </span>
                <br>
                <i class="sub_li">Bạn cần nhập mã giới thiệu này để được ngay 20k sau khi hoàn tất, và tham gia vào mạng
                    lưới Kiếm tiền quá dễ của chúng tôi.

                </i>
            </li>
            <li>
                <b>
                    Bước 4:
                </b>
                Chuyển vào tài khoản 50k, sau đó chuyển ra tối thiểu 50k (nghĩa là bạn có thể chuyển hết và không cần số dư trong TK), và
                chụp ảnh gửi vào Zalo:
                <span class="bold_sub_li">
                09.....
                </span>
            </li>
            <li>
                <b>
                    Bước 5:
                </b>
                Hoàn tất, bạn sẽ được chúng tôi chuyển khoản ngay 20k vì đã ra nhập hệ thống!
                Ngoài ra bạn nhận được 30k khi nạp thẻ điện thoại

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
                    Bước 6:
                </b>
                Bạn đã hoàn tất, và hãy hướng dẫn người khác ra nhập mạng của bạn với ID của bạn, để họ nhận
                được 20k chuyển khoản
                và có cơ hội LỚN để nhận được <b> hàng trăm triệu </b> đồng
            </li>

        </ul>
        <br>

        <b>
            Chú ý:
        </b>
        <i>
            <br>
            - công ty không bắt buộc phải có số dư tài khoản. Sau tất cả các bước trên bạn sẽ có số lãi 20k + 30K sẵn
            sàng để nạp thẻ điện thoại

            <br>
            - Nếu người mà bạn giới thiệu nhập mã giới thiệu của bạn, mà không nhập mã giới thiệu của chúng tôi, nghĩa là bạn cũng như người đó sẽ không tham gia vào hệ thống.
            Bạn có thể được 50k từ công ty, người bạn giới thiệu sẽ không nhận được 20k từ chúng tôi.
            <br>
            Bạn có thể thu số tiền nhỏ, nhưng quan trọng hơn là bạn mất đi cơ hội lớn được hưởng tiền thưởng có thể lớn gấp hàng trăm, nghìn lần từ hệ thống.
        </i>



    </div>


    <div class="jumbotron container mt-4">
        <h3>Cơ chế phân bổ giải thưởng</h3>

        <br>
        Giải thưởng dựa trên số lượng người trong Cây của bạn, giả sử cây của bạn có 10 người đầu tiên, mỗi người lại giới thiệu cho 10 người khác, cứ như vậy đến câp 5, thì tiền thưởng của bạn sẽ tăng mười lần qua mỗi cấp,
        tổng số sẽ như sau:

        <table class="glx01 mt-2">
            <tr>
                <th>Cấp</th>
                <th>Số lượng người</th>
                <th>Tiền thưởng</th>
                <th>Tổng Tiền thưởng</th>
                <th>Bằng chữ</th>
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
                echo "<td> $mn2 đ</td>";
                echo "<td> $vn </td>";
                echo "</tr>";
            }
            $money = $oneBonus * $ttsl;
            $mn2 = number_format($money);
            $vn = \LadLib\Common\cstring2::toTienVietNamString3($money);
            echo "<tr>";
            echo "<td class='redbold'>Tổng số</td>";
            echo "<td class='redbold'style='font-family: Courier'> $ttsl </td>";
            echo "<td class='redbold'> $oneBonus đ</td>";
            echo "<td class='redbold'> $mn2 đ</td>";
            echo "<td class='redbold'> $vn </td>";
            echo "</tr>";


            ?>
        </table>

        <br>
        Dựa trên mạng lưới phân phối của bạn, bạn có thể có lợi nhuận khổng lồ
        <br>
        Ví dụ bảng trên:
        <br>
        - Bạn giới thiệu được cho 10 người đăng ký (bạn bè ...) tài khoản công ty
        <b>
        ABC
        </b>
        , họ nghiễm nhiên sẽ được nhận 15k/1 người, 30k tiền
        nạp điện thoại
        <br>
        Đây là đại lý Cấp 1 của bạn, bạn sẽ nhận được 5 nghìn/1 người thuộc đại lý cấp 1, nghĩa là bạn thu 50K (5x10)
        <br>
        - Sau đó, mỗi người đó giới thiệu cho 10 người khác, như vậy bạn sẽ có 100 đại lý cấp 2.
        <br>
        Bạn sẽ thu được 5x100 = 500k cho 100 người ở đại lý cấp 2
        <br>
        ... Cứ như vậy bạn sẽ thu được tận đến đại lý cấp 5, số tiền có thể là 500 triệu đồng, hoặc hoàn toàn có thể cao
        hơn nếu bất chợt ở cây của bạn có 1 vài người có số thành viên lớn!

        Vì chính Bạn hoặc có những người có thể giới thiệu hàng trăm thành viên tham gia một cách dễ dàng!

        <br>
        - Không những vậy, bất cứ ai cũng có thể thu được đến đại lý Cấp 5 dù người đó thuộc cây bên dưới hay bên trên
        của bạn!

        <br>
        <br>
        <div style="padding: 10px 15px; background-color: lavender; border-radius: 10px">
            Đây là Mô hình Kinh doanh theo mạng rất khoa học, mà cả những người nổi tiếng như BillGate cũng từng ca
            ngợi.
            <br> Đơn giản là vì, thay vì chia sẻ cho các đại lý chuyên nghiệp, thì giờ chia sẻ cho chính bạn, một người
            thông minh để chớp cơ hội
            <br> Riêng với chúng tôi, cách làm này hoàn toàn khác biệt với hầu hết các sản phẩm khác, các sản phẩm khác
            yêu cầu bạn phải nộp tiền, và thường là một số tiền không nhỏ.
            <br>
            Ở đây bạn được tiền, không phải mất một đồng nào (chuyển vào rồi lại chuyển ra), và được đảm bảo bởi Ngân
            hàng Uy tín hàng đầu Việt nam - <b>ABC </b>
            <br>
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

        <br>
        <i>
            Đặc biệt: Không chỉ dừng lại ở đây, Khi tham gia, chắc chắn bạn sẽ được ưu tiên cơ hội hợp tác với chúng tôi trong các chương trình tiếp theo!
        </i>
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
