@extends("layouts_multi.glx2021")

@section("title")

        VIP ACC
    @endsection

@section("css")

    <style>
        .vip_acc div {
            border: 1px solid #ccc;
            padding: 20px 30px;
            height: 300px;
            border-radius: 10px;
            text-align: center;
        }
        .vip_acc button{
            margin: 30px auto 10px auto
        }
    </style>

@endsection

@section("content")
    <div class="ladcont" data-code-pos="ppp1682132821092">
        <br><br><br><br><br>
        <div data-code-pos="ppp1676853896774" class="container">

            <div data-code-pos="ppp1676853893743" class="row">
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-sm-4 vip_acc">
                            <div  class="price_div">
                                <h2>
                                    Gói FREE
                                </h2>
                                <p>
                                    * Miễn phí 200 thành viên
                                </p>
                                <p>
                                    * Tối đa 100 ảnh
                                </p>

                                <button class="btn btn-info"> Đăng ký </button>
                            </div>
                        </div>
                        <div data-code-pos="ppp167dfd6853900973" class="col-sm-4 vip_acc" style="">
                            <div class="price_div">
                                <h2>
                                   Gói BASIC
                                </h2>
                                <p>
                                    * 1000 Member
                                </p>
                                <p>
                                    * Tối đa 1000 ảnh
                                </p>

                                <button class="btn btn-info"> Đăng ký </button>
                            </div>
                        </div>
                        <div data-code-pos="ppp167dfd6853900973" class="col-sm-4 vip_acc" style="">
                            <div  class="price_div">

                                <h2>
                                    Gói VIP
                                </h2>

                                <p>
                                * 3000 Member
                                </p>
                                <p>
                                * Tối đa 3000 ảnh
                                </p>

                                <button class="btn btn-info"> Đăng ký </button>
                            </div>
                        </div>
                    </div>

                </div>

                <div data-code-pos="ppp " class="col-sm-3" style="">

                </div>
            </div>


        </div>

    </div>
@endsection
