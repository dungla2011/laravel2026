<?php
$uid = getCurrentUserId();

?>
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('title')
    {{
    \App\Models\SiteMng::getTitle()
    }}
@endsection

@section('meta-description')
    <?php
    \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('content')

    <style>
        .doiTac img {
            width: 120px;
            height: 60px;
            margin: 5px;
            border-radius: 5px;
        }
    </style>

    <div class="content-wrapper">
        <section class="wrapper bg-light" data-code-pos="ppp1734592016236">
            <div class="container py-3">
                <h1 style="text-align: center" class="mt-5">THANH TOÁN MOMO</h1>

                <?php
                \clsMomo::momoNotifyOrReturnWeb(request()->all(),1);
                ?>

            </div>
        </section>

    </div>

@endsection
