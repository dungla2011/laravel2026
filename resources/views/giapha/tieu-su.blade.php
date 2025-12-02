@extends("layouts_multi.glx2021")


@section("title")
    <?php
    $id0 = $id = request("idString");
    if (!is_numeric($id))
        $id = qqgetIdFromRand_($id);
    $obj = \App\Models\GiaPha::find($id);
    if ($obj instanceof \App\Models\GiaPha) ;
    ?>

        <?php
        if ($obj) {
            echo $obj->getObjNameAndTitle(1);
        }
        ?>
@endsection
@section('description')
    <?php
    if ($obj ?? '') {
        echo $obj->getObjNameAndTitle(1);
    }
    ?>
@endsection



@section("css")
    <style>
        .banner--clone {
            display: none;
        }

        .position-absolute {
            position: relative !important;
        }

        .navbar {
            padding-top: 0px !important;
            background-color: #393939;
        }

        .ladcont {
            padding-top: 20px;
        }
    </style>
@endsection

@section("content")
    <div class="ladcont" data-code-pos="ppp1682132840631">
        <div data-code-pos="ppp1676853896774" class="container">
            <br>
            <?php

            if(!isset($obj) || !$obj){
                bl("Không tồn tại thành viên!");
                goto _END;
            }

            //if($obj instanceof \App\Models\ModelGlxBase);
            $imgLink = "/images/no-img.jpg";
            if ($obj->image_list) {
                if (is_numeric($obj->image_list)) {
                    $file = new \App\Models\FileUpload();
                    if ($file = $file->find($obj->image_list)) {
                        $img = $file->getCloudLinkImage();
                        $imgLink = $img;
                    }
                } elseif (strstr($obj->image_list, "/")) {
                    $imgLink = $obj->image_list;
                } else {

                }
            } else {

            }

            ?>
            <div data-code-pos="ppp1676853893743" class="row">
                <div class="col-sm-9">
                    <div class="row">
                        <div class="col-sm-3">
                            <?php
                            echo "<img src='$imgLink' style='width: 100%; max-width: 300px; border: 1px solid #eee'>";
                            ?>
                        </div>
                        <div data-code-pos="ppp1676853900973" class="col-sm-9" style="">
                            <div style="float:right">
                                <?php
                                echo "<a href='/my-tree?pid=$id0'>  <img style='width: 16px' src='/assert/seekicon.com/tree_9.svg' alt=''> Xem cây</a>";
                                ?>

                            </div>
                            <h1 style="margin-top: 5px;">
                                <?php
                                if ($obj->user_id == getUserIdCurrent_()) {
                                    echo "<a href='/member/tree-mng/edit/$id' target='_blank' title='Soạn thảo nội dung'>[E]</a>";
                                }
                                ?>
                                <b>
                                    <?php
                                    echo $obj->name;
                                    ?>
                                </b>
                            </h1>

                            <?php
                            if ($obj->title) {
                                echo "<h5 style='font-style: italic'> $obj->title</h5>";
                            }
                            ?>

                            <?php
                            if ($obj->birthday) {
//                                echo "<b style=''> $obj->birthday</b>";
                            }
                            ?>

{{--                            <div style="font-size: small; margin-top: 5px">--}}
{{--                                Ngày tạo: <?php--}}
{{--                                echo $obj->created_at--}}
{{--                                ?>--}}
{{--                            </div>--}}

                            <i>
                            <?php
                            echo $obj->summary
                            ?>
                            </i>
                        </div>
                    </div>

                    <div data-code-pos="ppp1676853905199" style="  text-align: justify;
  text-justify: inter-word;">



                        <br>
                        <?php
                        echo $obj->content
                        ?>

                    </div>
                    <br>
                    <br><br>

                </div>

                <div  data-code-pos="ppp "class="col-sm-3" style="">
                    <br>
                    <br><br>
                    <a id="editable_glx_news_54" href="#">
                        <img style="width: 100%" src="/images/store/ads_quangcao_nghigiau.gif"></a>

                    <br><br>

                    <a id="editable_glx_news_53" href="#">
                        <img style="width: 100%" src="/images/store/suport_girl.png"></a>

                    <br><br>

                </div>
            </div>

            <?php
            _END:
            ?>

        </div>

    </div>
@endsection
