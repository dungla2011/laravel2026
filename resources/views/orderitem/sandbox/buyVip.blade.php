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

    <div class="content-wrapper mb-8">

        <?php
        if(request('mrc_order_id')){
        ?>
            <div class="container mt-5">
                <div class="my-10 p-8 rounded" style="background-color: lavender; text-align: center; font-size: 130% " data-code-pos='ppp17395182494401'>
                    @include('parts.buyVipPost')
                </div>

            </div>
        <?php
        }else{
        ?>
        <section class="wrapper bg-light" data-code-pos="ppp1734592016236">
            <div class="container py-3">
                <div class="pricing-wrapper position-relative">
                    <div class="shape bg-dot primary rellax w-16 h-18" data-rellax-speed="1"
                         style="top: 2rem; right: -2.4rem; transform: translate3d(0px, -4px, 0px);"></div>
                    <div class="shape rounded-circle bg-line red rellax w-18 h-18 d-none d-lg-block"
                         data-rellax-speed="1"
                         style="bottom: 0.5rem; left: -2.5rem; transform: translate3d(0px, 63px, 0px);"></div>

                    <div class="row gy-1 mt-1 mt-md-1 ">
                        @if(request('prid'))
                            @include('orderitem.sandbox.selectOne')
                        @else
                        <?php
                        $mP = \App\Models\ProductFolder::whereIn("id", [4, 5])->orderBy('id', 'desc')->get();
                        ?>
                        @foreach( $mP AS $oneFolder)
                                <?php
                                $mm = \App\Models\Product::where(["status" => 1, 'parent_id' => $oneFolder->id])->get();
                                if (count($mm) == 0)
                                    continue;
                                $time = time();
                                ?>
                            <h1 data-code-pos='ppp17339752991601' class="text-center pb-8"> {{ $oneFolder->name }} </h1>
                            @foreach( $mm AS $oneProd )

                                    <?php

                                    $mA = \App\Models\ProductAttribute::where('product_id', $oneProd->id)->get();

                                    $dungLuong = $soLanDownload = $hanDung = '';
                                    foreach ($mA AS $oneA) {
                                        if ($oneA->attribute_name == 'download_limit_size')
                                            $dungLuong = $oneA->attribute_value;
                                        if ($oneA->attribute_name == 'time_limit') {
                                            $hanDung = $oneProd->getQuotaDateText();

                                        }
                                        if ($oneA->attribute_name == 'download_limit_count')
                                            $soLanDownload = $oneA->attribute_value;
//                                        echo "<br/>\n $oneA->attribute_name: $oneA->attribute_value";
                                    }
                                    ?>
                                <div class="col-md-6 col-lg-4 mb-5 qqqq1111">
                                        <?php
                                        \App\Models\BlockUi::showEditLink_("/admin/product/edit/$oneProd->id");
                                        ?>
                                    <div class="pricing card text-center">
                                        <div class="card-body pricex ">
                                            <form method="post" action="/buy-vip" onsubmit="" style="display: inline">
                                                <input type="hidden" name="description" value="Mua gói download"
                                                       readonly>
                                                <input type="hidden" name="customer_email" value="">
                                                <input type="hidden" id="customer_phone" name="customer_phone" value=''>
                                                <input type='hidden' name='mrc_order_id'
                                                       value='{{$uid}}.{{$time}}-{{$oneProd->id}}'>
                                                <img src="/template/sandbo/assets/img/icons/shopping-basket.svg"
                                                     class="svg-inject icon-svg icon-svg-md text-primary mb-3" alt="">
                                                <h4 class="card-title mb-1">{{$oneProd->name}}</h4>

                                                <div class="product_limit my-2">
                                                    Dung lượng tải : {{number_formatvn0($dungLuong)}} GB <br/>
                                                    Hạn dùng : {{$hanDung}} <br/>
                                                    Số lượt tải : {{number_formatvn0($soLanDownload)}} lượt <br/>
                                                </div>
                                                <div class="prices text-dark">
                                                    <h2>
                                                        {{ number_formatvn0($oneProd->price)}} VND
                                                    </h2>
                                                </div>
                                                    <p>
                                                        <ion-icon name="heart-outline"></ion-icon>
                                                        Không giới hạn tải theo ngày
                                                    </p>
                                                    <p>
                                                        <ion-icon name="heart-outline"></ion-icon>
                                                        Có thể dùng chung 5 người <br>
                                                        <i style="font-size: small">
                                                        (5 IP tải tại 1 thời điểm)
                                                        </i>
                                                    </p>
                                                    <p>
                                                        <ion-icon name="heart-outline"></ion-icon>
                                                        Sử dụng API
                                                    </p>
                                                <p></p>
                                                <?php
//                                                if(isDebugIp())
                                                {
                                                    $pidx = qqgetRandFromId_($oneProd->id);
                                                    echo "<a href='/buy-vip?prid=$pidx' class='btn btn-primary rounded-pill'>Chọn</a>";
                                                }
                                                ?>
{{--                                                <button class="btn btn-primary rounded-pill">Chọn</button>--}}
                                            </form>
                                        </div>
                                        <!--/.card-body -->
                                    </div>
                                    <!--/.pricing -->
                                </div>
                            @endforeach
                        @endforeach
                        @endif
                    </div>
                    <!--/.row -->
                </div>
                <!--/.pricing-wrapper -->
            </div>
            <!-- /.container -->
        </section>
        <?php
        }
        ?>
    </div>
@endsection
