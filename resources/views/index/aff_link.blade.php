@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('content')

    <div style="text-align: center; margin-top: 100px">

        <a style="color: royalblue" href="/">
            Chào mừng bạn đến với
            <?php

                echo getDomainHostName();
            ?>

            <br>

            Trở lại Trang chủ
        </a>
    </div>


@endsection
