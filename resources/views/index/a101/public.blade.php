@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('content')
<main role="main" class="container">


    <div class="jumbotron p-3 p-md-5 text-white rounded bg-dark" style="background-color: #dbe2e9!important; color: #236db6!important">
        <div class="col-md-10 px-0">
            <h1 class="display-9 font-italic">Giải pháp quản lý thay thế Excel,  GoogleSheet</h1>
            <p class="lead my-3">Việc quản lý thông tin trên Excel tiện lợi nhưng có nhiều hạn chế, giải pháp của GalaxyCloud khắc phục những hạn chế, mở rộng thêm tính năng cho người dùng!
                <br>
                Giúp Bạn quản lý dễ dàng: Quản lý đơn hàng, quản lý nhân viên, quản lý học viên...
            </p>
            <p class="lead mb-0"><a href="#" class=" font-weight-bold">Xem thêm...</a></p>
        </div>
    </div>


    <div class="row">
        <div class="col-md-8 blog-main">

            <div class="blog-post">
                <h2 class="blog-post-title">Ứng dụng</h2>
                <p class="blog-post-meta">03.2023 <a href="#">By LAD</a></p>

                <p>
                    - Excel tiện dụng nhưng có hạn chế tính năng quản lý, thống kê báo cáo...
                    <br>
                    - Các phần mềm quản lý không tiện lợi, nhiều bước phức tạp
                    <br>
                    - Làm sao đơn giản hóa?
                </p>
                <hr>

                <h2 class="blog-post-title">Demo Video</h2>


                <iframe width="560" height="315" src="https://www.youtube.com/embed/3HB8VW7Ce5s" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>

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
