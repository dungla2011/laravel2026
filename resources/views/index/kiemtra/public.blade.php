@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    Online Testing
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



    <div class="jumbotron container mt-4">


        <img src="/images/logo/online-testing.png" alt="">

        <p></p>
        <center>
        <h3 style="">Kiểm tra kiến thức</h3>

        <ul>
            <li>Ôn tập bài học</li>
            <li>Nâng cao trình độ</li>
            <li>Đánh giá, tuyển dụng</li>
        </ul>
        </center>

    </div>


@endsection
