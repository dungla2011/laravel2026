@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("fav_icon")<?php
echo \App\Models\SiteMng::getLogoIcon()
?>@endsection


@section("og_desc")<?php
echo \App\Models\SiteMng::getDesc()
?>@endsection

@section("og_image")<?php
echo \App\Models\SiteMng::getLogo()
?>@endsection

@section("title")<?php
echo \App\Models\SiteMng::getTitle()
?>@endsection


@section('content')

    <style>
{{--     add style for paginator laravel  --}}
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            list-style: none;
            padding: 0;
            margin: 25px;

        }

        .pagination li {
            margin: 0 5px;
        }

        .pagination li a {
            text-decoration: none;
            color: #333;
            padding: 5px 10px;
            border: 1px solid #333;
            border-radius: 5px;
            background-color: lavender;
        }

        .pagination li a:hover {
            background-color: #333;
            color: #fff;
        }

        .pagination .active a {
            background-color: #333;
            color: #fff;
        }

    </style>

    <td valign="top" style="padding:0px 10px;" class="center-panel">

        <h1 style="font-size:18px; font-weight:bold;">DataProVn - <strong>Download free For All</strong>: software and educational resource.</h1>
        <p>Free software and educational resource. With a little searching, you can download what you need for free without spending any money.</p>
        <p>Our site - <b>"DataProVn"</b> offers free software downloads including antivirus software, office suites and video editing software. Here you can find software reviews and ratings so you can understand which apps are worth downloading. If you're looking for free educational programs or materials in the US and Canada, we'll help you find what you need for students and learners.</p>


        <div id='dle-content'>

            <?php

                $limit = request('limit', 10);

            //Thêm pagniator ở đây, limit 10
            $mm = \App\Models\MyDocument::where('status',1)->orderBy('orders','desc')->paginate($limit);

//            $mm = \App\Models\MyDocument::where('status',1)->orderBy('orders','desc')->limit(10)->get();

            foreach ($mm as $doc) {

                if($doc instanceof \App\Models\MyDocument);

                $doc->htmlBlockOneItem();


            }

            //Echo paginator button here
            echo $mm->links();

            ?>



        <p>	If you are looking for educational materials, our website offers free downloadable online courses. From computer science to business and humanities. The courses are taught by professors from leading universities around the world, and you can earn a certificate of completion from many of them.</p>
        <p> There is also a wide range of courses available, from engineering to languages and social sciences. The video courses are taught by professors from the best universities, and you can get a certificate of completion from many of them.</p>
        <p>You can also search for free software and educational resources on Google or other search engines. Just remember to read reviews and check the website's reputation before downloading anything.</p>
        <p><b>DataProVn is download free for all software and educational resource</b>. With a little searching, you can download what you need for free without spending any money.</p>

    </td>

@endsection
