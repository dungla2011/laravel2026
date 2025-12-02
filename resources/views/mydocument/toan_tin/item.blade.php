
@extends(getLayoutNameMultiReturnDefaultIfNull())

@section('css')

    <style>
        .row1 {
            border-bottom: 2px solid darkorange;
        }

        .heading1 {
            background-color: darkorange;
            color: white;
            display: inline-block;
            font-weight: bold;
            padding: 7px 30px 7px 15px;
            font-size: 20px;
            text-transform: uppercase;
        }
        .brc_path {
            font-weight: bolder;
            padding-bottom: 10px;
            border-bottom: 1px solid #ccc;
        }
        .loading-icon {
            display: none;
            margin-left: 10px;
            color: red;
        }

    </style>


@endsection

<?php

$uid = getCurrentUserId();

if($fid = request('fid')){

    if(!is_numeric($fid)){
        $fid = qqgetIdFromRand_($fid);
    }

if($obj = \App\Models\MyDocument::find($fid)){

    ?>
@section('title') Tải sách: {{$obj->name}}  @endsection
    <?php
}
}
?>
@section('content')

    <?php


    ?>

    <div class="container py-3">


        <?php
        $obj = new \App\Models\MyDocument();
        $file = null;
        $linkDl = null;
        $imgThumb = '/images/no-image.png';

        if($fid = request('fid')){

            if(!is_numeric($fid)){
                $fid = qqgetIdFromRand_($fid);
            }
            if($obj = \App\Models\MyDocument::find($fid)){

                // Hiển thị breadcrumb và tên luôn
                echo $obj->getBreakumPathHtml(0, 1);
                ?>

                <div class="row" data-code-pos="qqq17067778112190">
                    <div class="col-sm-8">
                        <?php
                        echo "<h1 style='font-size: 15px' class='my-3'> $obj->name ";

                        if (isSupperAdmin_()) {
                            echo "\n <a href='/admin/my-document/edit/$obj->id' target='_blank'> <i class='fa fa-edit'></i> </a>";
                        }

                        echo "\n</h1>";
                        ?>

                        <div style="padding: 10px; background-color: #eee; border-radius: 5px; font-size: 90%; margin-bottom: 10px">
                        {{$obj->summary}}
                        </div>
                        <?php



                        // Kiểm tra file_list
                        if(!$obj->file_list){
                            echo "<div class='alert alert-warning'>Tài liệu này chưa có file đính kèm.</div>";
                        } else {
                            $file = \App\Models\FileUpload::find($obj->file_list);
                            if($file){
                                $linkDl = $file->getCloudLinkEnc(0);
                                $imgThumb = $obj->getThumbSmall(800) ?? '/images/no-image.png';
                            }
                        }

                        // Hiển thị nội dung nếu có link download
                        if($linkDl){
                        ?>


                        <div data-code-pos="qqq1706777815668" class="txt-center pt-3" style="
                        max-width: 400px;
                        margin: 0 auto;
                        text-align: center">
                            <img data-code-pos='ppp17334143744151' alt="Download {{$obj->name}}"
                                 style="border: 1px solid #eee; width: 100%" src="{{$imgThumb}}" alt="">



                            <div class="my-3">
                                <button title="Đăng nhập Tải sách"
                                        class="btn btn-sm btn-info text-white g-recaptcha"
                                        id="<?php if($uid) echo 'get_link_download_doc' ?>">
                                        <?php
                                        if(!$uid)
                                            echo "<a href='/login' style='color: white'> Đăng nhập để tải sách </a>";
                                        else
                                            echo "Lấy link tải sách";
                                        ?>
                                </button>
                                <i class="fa fa-spinner fa-spin loading-icon" id="loading_icon"></i>
                            </div>
                        </div>
                        <?php
                        } // end if($linkDl)
                        ?>
                    </div>
                    <div class="col-sm-4 txt-center">


                        <h2 style="margin: auto; text-align: center; font-size: 20px" class="my-3">
                            Xem thêm
                        </h2>
                            <?php

                            $mDoc = \App\Models\MyDocument::where(['parent_id' => $obj->parent_id])->whereNotNull('file_list')->where(function ($query) {
                                $query->where('name', 'like', "%toán%")->orWhere('name', 'like', "%tin học%");
                            })->limit(10)->get();

                        foreach ($mDoc AS $doc){
                            ?>

                        <p>
                            <a href="/tai-lieu/chi-tiet?fid={{qqgetRandFromId_($doc->getId())}}">
                                <img src="{{$img = ($doc->getThumbSmall(300) ?? '/images/no-image.png')}}"
                                     style="width: 200px; display: block; margin: auto" alt="">
                            </a>
                        </p>

                            <?php
                        }
                            ?>

                    </div>
                </div>
                <?php
            } // end if($obj = \App\Models\MyDocument::find($fid))
        } // end if($fid = request('fid'))

            ?>


        </div>
    </div>

    <p></p>
    <p></p>


    <script src="https://www.google.com/recaptcha/api.js?render={{ env("RECAPTCHA_SITE_KEY_2025") }}"></script>
    <script>
        window.addEventListener('load', function () {
            document.getElementById('get_link_download_doc')?.addEventListener('click', function(e) {
                e.preventDefault();

                if (document.getElementById('download_this_link')) {
                    alert('Bạn đã thấy link tải?');
                    return;
                }

                document.getElementById('loading_icon').style.display = 'inline-block';
                grecaptcha.ready(function() {
                    grecaptcha.execute('{{ env('RECAPTCHA_SITE_KEY_2025') }}', {action: 'get_link_download_doc'})
                        .then(function(token) {
                            // Log token để debug
                            console.log('reCAPTCHA token:', token);

                            // Gọi API với token reCAPTCHA
                            fetch('/api/tai-lieu/getLinkDownloadDoc', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    recaptcha_token: token,
                                    doc_id: {{ $fid }}
                                })
                            })
                                .then(response => response.text())
                                .then(data0 => {
                                    document.getElementById('loading_icon').style.display = 'none';
                                    let data;
                                    try {
                                        data = JSON.parse(data0);
                                    } catch (e) {

                                        console.error('Invalid JSON:', e);
                                        alert('Đã có lỗi xảy ra: ' + data0);
                                        return;
                                    }
                                    if(data.error == 'need_login'){
                                        alert(data.message);
                                        return;
                                    }

                                    console.log('API response:', data); // Thêm log để debug
                                    if (data.success) {

                                        if(data.download_link){

                                            //view_book thêm 1 nút a + button dưới nút  này,  click vào tải link
                                            var a = document.createElement('a');
                                            var linkText = document.createTextNode("Tải sách");
                                            a.appendChild(linkText);
                                            a.title = "Tải sách";
                                            a.id = "download_this_link";
                                            a.href = data.download_link;
                                            a.target = "_blank";
                                            a.className = "btn btn-sm btn-primary text-white mx-3";
                                            document.getElementById('get_link_download_doc').insertAdjacentElement('afterend', a);


                                        }else{
                                            alert("Có Lỗi: không tìm thấy link download?");
                                        }

                                    } else {
                                        alert('Xác thực không thành công: ' + data.message);
                                    }
                                })
                                .catch(error => {
                                    console.error('Lỗi:', error);
                                    alert('Đã có lỗi xảy ra. Vui lòng thử lại sau.');
                                });
                        });
                });
            });
        });
    </script>


@endsection
