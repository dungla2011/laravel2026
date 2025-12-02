@extends(getLayoutNameMultiReturnDefaultIfNull())

@section("title")
 <?php
     $title = 'Tin tức';
    if($pid = request('id')){
        if($fold = \App\Models\NewsFolder::find($pid)){
            $title .= ": " . $fold->name;
        }
    }
    echo $title
 ?>
@endsection
@section("og_title")
    <?php
    echo "Tin mới nhất"
    ?>
@endsection
@section("css")
    <style>
        .banner--clone {
            display: none;
        }
        .position-absolute {
            position: relative!important;
        }
        .navbar{
            padding-top: 0px!important;
            background-color: #0c183e;
        }

        .news1 {
            color: black!important;
        }

        body {
            font-weight: unset!important;
        }


    </style>
@endsection

@section("content")

    <style>

        .zone_main h2, .zone_main a.news1 {
            color: #686868!important;
        }

        .ladcont   {
            margin-top: 20px;
        }
    </style>
    <div class="ladcont" data-code-pos="ppp16822843756">
        <div class="container">
            <div class="row">
                <div class="col-sm-9 top-brc" style="">

                    <?php
                    $pid = request('id');
                    $padBrc = '';
                    if($pid){
                        $fold = new \App\Models\NewsFolder();
                        if($fold = $fold->find($pid)){
                            $metaFold = new \App\Models\NewsFolder_Meta();
                            $link = $metaFold->getPublicLink($fold);
                            $padBrc = "<a href='$link'> $fold->name </a> / ";
                        }
                    }
                    ?>

                    <div style="" class="brc">
                        <b>
                            <a href="/">Trang chủ </a> / <a href="/tin-tuc">Tin tức </a> /    <?php echo $padBrc ?> </b>
                    </div>

                    <section style="max-width: 1200px; margin: 0 auto; border-left: 0px solid #ddd; border-right: 0px solid #ddd;">


                        <!-- Main content -->



                        <div id="zone_filter_mobi" class="wrapper1">


                        </div>



                        <select id="select_sort_by" class="form-control form-control-sm" style="display: none" onchange="location = this.value;">
                            <option value="news.html">---Sắp xếp---</option>

                            <option value="/news?&amp;sortField=createdAt&amp;sortType=desc"> Mới nhất                            </option>
                            <option value="/news?&amp;sortField=createdAt&amp;sortType=asc"> Cũ nhất                            </option>
                        </select>




                        <!-- /.box-header -->
                        <div class="zone_main" style="padding-top: 0px">

                            <div class="clearfix"></div>
                        <?php

                        if($pid = request('id')){
                            $mm = \App\Models\News::where(['status' => 1, 'parent_id'=>$pid])->orderByDesc('orders', 'DESC')->orderByDesc('created_at')->get();
                        }
                        else
                            $mm = \App\Models\News::where(['status' => 1])->orderByDesc('orders', 'DESC')->orderByDesc('created_at')->get();

                        if($mm)
                            foreach ($mm AS $obj){

                                $slug = Str::slug($obj->name);
                                $link = "/tin-tuc/".$slug.".".$obj->id.'.html';
                                echo "\n <hr>  <h2 style='font-size: 18px; margin: 5px 0px'>   <a class='news1' href='$link'> $obj->name </h2>
<span style='font-size: small; font-style: italic'>
$obj->created_at
</span>
<br>
$obj->summary
 </a>
";

                            }



                        ?>

                        <!-- Limit per page-->
                            <!--Data Table-->

                            <!--table content-->


                        </div>
                        <!-- /.box-body -->
                        <br>
                        <br>

                        <div class="clearfix"></div>

                    {{--                        <div class="paginator_lad"><center><br><a href="/news?&amp;page=1">«</a>  <a href="#">‹</a>  <a class="active">1</a><a href="/news?&amp;page=2">2</a><a href="/news?&amp;page=3">3</a> ...  ... <a href="/news?&amp;page=21">21</a> <a href="/news?&amp;page=2">›</a> <a href="/news?&amp;page=21">»</a><select class="select_glx" onchange="window.location.href=this.value"><option selected="" value="/news?&amp;page=1">Page 1</option><option value="/news?&amp;page=2">Page 2</option><option value="/news?&amp;page=3">Page 3</option><option value="/news?&amp;page=4">Page 4</option><option value="/news?&amp;page=5">Page 5</option><option value="/news?&amp;page=6">Page 6</option><option value="/news?&amp;page=7">Page 7</option><option value="/news?&amp;page=8">Page 8</option><option value="/news?&amp;page=9">Page 9</option><option value="/news?&amp;page=10">Page 10</option><option value="/news?&amp;page=11">Page 11</option><option value="/news?&amp;page=12">Page 12</option><option value="/news?&amp;page=13">Page 13</option><option value="/news?&amp;page=14">Page 14</option><option value="/news?&amp;page=15">Page 15</option><option value="/news?&amp;page=16">Page 16</option><option value="/news?&amp;page=17">Page 17</option><option value="/news?&amp;page=18">Page 18</option><option value="/news?&amp;page=19">Page 19</option><option value="/news?&amp;page=20">Page 20</option><option value="/news?&amp;page=21">Page 21</option></select> <span style="color: ; font-size: "> (207)</span><br></center> </div>                    <br>--}}

                    <!-- /.content -->
                        <!-- Main content -->


                        <!--SORT BY MOBI-->
                        <div class="modal" id="modalSortBy" tabindex="-1" role="dialog" aria-labelledby="modalSortBy">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h4 class="modal-title" id="">Sắp xếp theo</h4>
                                        <button type="button" class="close" data-dismiss="modal">×</button>
                                    </div>
                                    <div class="modal-body" style="padding-left: 10px">


                                        <a style="text-decoration: none" href="/news?&amp;sortField=createdAt&amp;sortType=desc">
                                            <div class="sort_choice">
                                                <input type="checkbox">
                                                Mới nhất                                            </div>
                                        </a>


                                        <a style="text-decoration: none" href="/news?&amp;sortField=createdAt&amp;sortType=asc">
                                            <div class="sort_choice">
                                                <input type="checkbox">
                                                Cũ nhất                                            </div>
                                        </a>


                                        <a style="text-decoration: none" href="/news.html">
                                            <div class="sort_choice" style="font-weight: bolder">
                                                HỦY SẮP XẾP
                                            </div>
                                        </a>

                                    </div>

                                    <div class="modal-footer">
                                        <button style="" type="button" class="btn btn-default" data-dismiss="modal">Bỏ qua
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>

                    </section>
                </div>

                <div class="col-sm-3" style="border: 0px solid gray; text-align: center">

                    <div style="display: ">
                        <br>
                        <a id="editable_glx_news_52" href="#">
                            <img style="width: 100%" src="/images/store/glx_banner.jpg">
                        </a>

                        <br><br>
                        <a id="editable_glx_news_54" href="#">
                            <img style="width: 100%" src="/images/store/ads_quangcao_nghigiau.gif"></a>

                        <br><br>

                        <a id="editable_glx_news_53" href="#">
                            <img style="width: 100%" src="/images/store/suport_girl.png"></a>


                        <br><br>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
