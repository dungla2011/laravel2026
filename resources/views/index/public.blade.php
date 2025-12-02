@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('content')


    <main role="main" class="container">

        <div class="jumbotron p-3 p-md-5 text-white rounded bg-dark" style="background-color: #dbe2e9!important; color: #236db6!important">
            <div class="col-md-10 px-0">
                <h2>
                    Chúng tôi chuyên cung cấp các Giải pháp Công nghệ thông tin chuyên nghiệp, tự động hóa
                </h2>
                - Giải pháp marketting, quản lý Sale số lượng lớn
                <br>
                - Thiết kế, lập trình phần mềm: phần mềm ứng dụng, App di động
                <br>
                - Cung cấp, tư vấn Hosting VPS server, hosting Dedicated server, web hosting, file hosting, image hosting, video hosting
                <br>
                - Thiết kế website, portal cho doanh nghiệp, cơ quan báo chí, thương mại điện tử
                <br>
                - Vận hành các hệ thống IT: quản trị hệ thống Server và chuyên gia về các hệ Cơ sở dữ liệu

                <p class="lead mb-0"><a href="" class=" font-weight-bold">Xem thêm...</a></p>
            </div>
        </div>


    <div class="row">
        <div class="col-md-8 blog-main">

            <div class="blog-post">
                <h2 class="blog-post-title">Dịch vụ</h2>
                <p class="blog-post-meta">03.2015 <a href="#">By LAD</a></p>

                <p>
                    - Cung cấp VPS
                    <br>
                    - Cung cấp Webhosting
                    <br>
                    - Cung cấp dịch vụ Lập trình Website
                </p>
                <hr>


            </div><!-- /.blog-post -->

        </div><!-- /.blog-main -->

        <aside class="col-md-4 blog-sidebar">
            <div class="p-3 mb-3 bg-light rounded">
                <h4 class="font-italic">About</h4>
                <p class="mb-0">Etiam porta <em>sem malesuada magna</em> mollis euismod. Cras mattis consectetur purus sit amet fermentum. Aenean lacinia bibendum nulla sed consectetur.</p>
            </div>

            <div class="p-3">
                <h4 class="font-italic">Archives</h4>
                <ol class="list-unstyled mb-0">
                    <li><a href="#">March 2014</a></li>
                    <li><a href="#">February 2014</a></li>
                    <li><a href="#">January 2014</a></li>
                    <li><a href="#">December 2013</a></li>
                    <li><a href="#">November 2013</a></li>
                    <li><a href="#">October 2013</a></li>
                    <li><a href="#">September 2013</a></li>
                    <li><a href="#">August 2013</a></li>
                    <li><a href="#">July 2013</a></li>
                    <li><a href="#">June 2013</a></li>
                    <li><a href="#">May 2013</a></li>
                    <li><a href="#">April 2013</a></li>
                </ol>
            </div>

            <div class="p-3">
                <h4 class="font-italic">Elsewhere</h4>
                <ol class="list-unstyled">
                    <li><a href="#">GitHub</a></li>
                    <li><a href="#">Twitter</a></li>
                    <li><a href="#">Facebook</a></li>
                </ol>
            </div>
        </aside><!-- /.blog-sidebar -->

    </div><!-- /.row -->

</main><!-- /.container -->


@endsection
