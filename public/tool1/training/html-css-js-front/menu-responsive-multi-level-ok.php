<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu recursive, multi level</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">


</head>
<body>

<style>
    * {
        box-sizing: border-box;
        margin: 0px;
        padding: 0px;
        font-family: Arial, Helvetica, sans-serif
    }

    a {
        text-decoration: none;
        /*color: #ccc;*/
    }

    .nav > li {
        display: inline-block;
        color: white;
    }

    .nav li {
        position: relative;
        list-style: none;
        border-bottom: 1px dashed #ccc;
        border-left: 1px dashed #ccc;
    }

    .nav li a {
        padding: 10px;
        /*line-height: 20px;*/
        display: inline-block;
        color: white;
    }

    .nav .sub-menu {
        display: none;
        position: absolute;
        top: 0;
        left: 100%;
        width: 200px;
        background-color: darkblue;
    }

    .nav li:hover > .sub-menu {
        display: block;
    }

    .nav li:hover {
        background-color: green;
    }

    .nav > li > .sub-menu {
        top: 39px;
        left: 0;
    }

    i.more {
        display: none;
        font-size: 130%;
    }

    div.nav-cover {
        background-color: darkblue;
        position: sticky;
        top: 0px;
    }

    .mobile_button {

        display: none;
    }

    .banner {
        z-index: 1000;
        /*!* Để luôn ontop khi kéo xuống*!*/
        /*position: sticky;*/
        /*top: 0px;*/
        /*display: none;*/
        background-color: #282828;
        color: greenyellow;
        height: 39px;
        font-size: 120%;
        padding: 10px;

    }


    @media screen and (max-width: 600px) {
        .nav .sub-menu {
            display: none;
            position: static;
            /*top: 0;*/
            /*left: 100%;*/
            width: 100%;
            background-color: black;
            padding-left: 10px;
        }

        .nav li {
            display: block;

            color: white;
            border-left: 0px solid red;
        }

        i.more {
            display: inline;
            position: absolute;
            right: 10px;
            top: 10px;
            cursor: pointer;
            float: right;
        }

        .mobile_button {
            display: inline-block;
            position: absolute;
            right: 20px;
            top: 10px
        }

        .banner {
            display: block;
            position: sticky;
            top: 0px;
        }

        div.nav-cover {
            display: none;
            position: static;
        }
    }

</style>


<script>

    window.onload = function () {
        document.querySelectorAll("i.more").forEach(function (elm) {
            elm.addEventListener('click', function () {
                console.log("Click ...");
                let sub = this.parentElement.querySelector('ul.sub-menu')
                // var x = document.getElementById("myDIV");
                if (sub.style.display === "none" || !sub.style.display) {
                    sub.style.display = "block";
                } else {
                    sub.style.display = "none";
                }
            })
        })

        document.querySelector('.mobile_button').addEventListener('click', function () {
            let divNav = document.querySelector('div.nav-cover')
            // var x = document.getElementById("myDIV");
            console.log("Click 1", divNav.style.display);
            if (divNav.style.display === "none" || !divNav.style.display) {
                console.log("Click 2");
                divNav.style.display = "block";
            } else {
                divNav.style.display = "none";
                console.log("Click 3");
            }

            //Về top để menu show được lên khi ở chế độ sticky
            window.scrollTo(0, 0);

        })

        //Ở Mobile nếu đang ẩn menu, thì phóng to ra Wide size, sẽ phải hiện lại menu
        window.addEventListener("resize", function () {
            let width = document.body.clientWidth;
            if (width > 600) {
                console.log("resize > 600, show");
                document.querySelector('div.nav-cover').style.display = "block";
            }
        });

        //Khi scrool, đang ở mobile thì ẩn menu đi
        // window.onscroll = function (e) {
        //     console.log("window.scrollY " , window.scrollY);
        //     if(window.scrollY > 10) {
        //         console.log(" > 10 ...");
        //         if(document.body.clientWidth < 600)
        //             document.querySelector('div.nav-cover').style.display = 'none';
        //     }
        // };

    }

</script>

<div class="banner">
    <!--    <a class="logo" href="#">-->
    <b>LOGO</b>
    <!--    </a>-->
    <i class="mobile_button fa fa-bars"></i>
</div>

<div class="nav-cover">
    <ul class="nav">
        <li><a href="#">Trang chủ</a>
        </li>
        <li>
            <a href="#">Sản Phẩm</a>

            <i class="more fa fa-caret-down"></i>
            <ul class="sub-menu">
                <li>
                    <a href="#">SP1</a>
                    <i class="more fa fa-caret-down"></i>
                    <ul class="sub-menu">
                        <li><a href="#">SP11</a></li>
                        <li><a href="#">SP12</a></li>
                        <li><a href="#">SP13</a>

                            <i class="more fa fa-caret-down"></i>

                            <ul class="sub-menu">
                                <li><a href="#">SP131</a></li>
                                <li><a href="#">SP132</a>
                                    <i class="more fa fa-caret-down"></i>
                                    <ul class="sub-menu">
                                        <li><a href="#">SP1321</a></li>
                                        <li><a href="#">SP1322</a></li>
                                        <li><a href="#">SP1323</a></li>
                                    </ul>

                                </li>
                                <li><a href="#">SP133</a></li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <li><a href="#">SP2</a></li>
                <li><a href="#">SP3</a></li>
                <li><a href="#">SP4</a></li>
            </ul>
        </li>
        <li><a href="#">Dịch vụ</a></li>
        <li><a href="#">Blog</a></li>
        <li><a href="#">Liên hệ</a></li>
    </ul>
    <div style="clear:both;"></div>
</div>

<br>
<h1> Menu NAV, Multilevel (Đa cấp), Responsive, Sticky top</h1>

<br><br>
<hr>
Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>
Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>Lorem ipsum dolor sit amet consectetur adipisicing elit.
<br>

<br>
<footer style="padding: 10px; color: greenyellow; background-color: #0a0a0a; text-align: center">
    FOOTER
</footer>

</body>
</html>


