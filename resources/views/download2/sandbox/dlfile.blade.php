<?php

use App\Models\OrderItem;
use App\Models\CloudServer_Meta;
use App\Models\DownloadLog;
use App\Models\TmpDownloadSession;
use App\Models\FileCloud;
use App\Models\FileRefer;
use App\Models\FileUpload;
use App\Models\Product;
use App\Models\User;
use Base\ModelCloudServer;
use Illuminate\Support\Facades\Auth;
use LadLib\Common\UrlHelper1;
use App\Models\AffiliateLog;

$uid = getCurrentUserId();

if ($affCode = request("aff_code"))
    AffiliateLog::checkAffCode($uid, $affCode);

////////////////////
$fid = dfh1b($ide);
$obj = FileUpload::find($fid);


if($src_from = request('src_from')){
    $src_from = "FID = $fid, src_from:" . strip_tags($src_from) . " - " . $_SERVER['REMOTE_ADDR'] . " - " . $_SERVER['HTTP_USER_AGENT'];
    outputT("/var/glx/weblog/src_from.txt", $src_from);
}


$u4s = new \App\Components\U4sHelper($uid);
$expire = $u4s->getVipExpiredDate();
$domain = UrlHelper1::getDomainHostName();

?>
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

        .heading1 a {
            color: white;
        }
    </style>
@endsection

@section('meta-description')
    <?php
    echo \App\Models\SiteMng::getDesc()
    ?>
@endsection

@section('meta-keywords')
    <?php
    echo \App\Models\SiteMng::getKeyword()
    ?>
@endsection

@section('content')
    <div class="container pt-5 mt-5 " style="min-height: 500px">

        <div data-code-pos='ppp172829542511' STYLE="text-align: center; max-width: 800px;
        position: relative;
        background-color: #eee;
        padding: 30px; margin: 0px auto; border: 1px solid #eee; border-radius: 5px">

            <div style="position: absolute; top: 20px; right: 20px" data-code-pos='ppp17373793693871'>
                <?php


                if ($uid = getCurrentUserId()){
                    $user = User::find($uid);
                    $idAff = $user->id;
                    if ($uidE = $user->ide__)
                        $idAff = $uidE;
                    $affLink = "https://{$domain}/f/$ide?aff_code={$idAff}";
                    ?>
                <span id="data-link-aff" data-val="{{$affLink}}" title="Copy link Affiliate : {{ $affLink }}"
                      onclick="copy_link_aff()">
                    <i class="fa fa-copy"></i>
                </span>
                <script>
                    function copy_link_aff() {
                        //copy from id="data-link-aff", data-val
                        var copyText = document.getElementById("data-link-aff");
                        var text = copyText.getAttribute('data-val');
                        var input = document.createElement('input');
                        input.setAttribute('value', text);
                        document.body.appendChild(input);
                        input.select();
                        var result = document.execCommand('copy');
                        document.body.removeChild(input);
                        if (result)
                            showToastInfoTop('Đã copy link Affiliate: ' + text);
                        else
                            alert('Có lỗi: Không thể copy link Affiliate: ' + text);


                    }
                </script>

                    <?php
                }
                ?>
            </div>


            <?php
//        echo "<br/>\n $ide";

            $obj = FileUpload::find($fid);
            if (!$obj) {
                bl("File không tồn tại hoặc đã bị xoá");
                //    return;
                goto _END;
            }
            $clObj = null;
            if ($obj->cloud_id)
                $clObj = FileCloud::find($obj->cloud_id);
            if (!$clObj)
                $clObj = FileCloud::find($fid);


            ?>

            <h4 class="qqqq1111"
                style="
                   color: dodgerblue;
                   /*text-shadow: 1px 0px 1px rgba(0, 0, 0, 0.5); !* Add text shadow *!"
                title="{{$obj->name}}" class="text-center pt-3" data-code-pos='ppp17282972582511'>

                <?php
                \App\Models\BlockUi::showEditLink_('/admin/file/edit/' . $fid);

                ?>
                {{  \LadLib\Common\cstring2::substr_fit_char_unicode($obj->name, 0, 60, 1) }}
            </h4>
            <div>
                {{ ByteSize($obj->file_size) }}
            </div>

            <?php
            if (!$uid) {
                echo "<div class='my-5'> <a href='/login'> Đăng nhập để tải file này </a></div>";
                goto _END;
            }

            if ($expire < nowyh()) {
                ?>
                <a href='/buy-vip'>
                    <div style='font-size: 110%'>
                        Tài khoản đã hết hạn VIP
                        <div style='color: red!important; font-weight: bold!important'>
                        Bấm vào đây để Gia hạn VIP tải file
                        </div>
                        Tải file miễn phí đang bảo dưỡng
                 </div>
                </a>
            <?php
                goto _END;
            }

            try {
                //Kiểm tra download allow:
//                $mm = TmpDownloadSession::getLinkDownload4s($ide, $uid);
            } catch (Exception $exception) {
                bl("Some error: " . $exception->getMessage());
                if (isDevEmail()) {
                    bl("Error: " . $exception->getTraceAsString());
                }
                goto _END;
            }
//$mm['sid']
//$mm['done_bytes']
//            $link = $mm['dlink'];
//        echo "\n $link";


            ?>

            <div class="m-4" data-code-pos='ppp17282972605731'>
                <?php


                if (!$clObj) {
                    echo "Có lỗi : File Cloud không tồn tại";
                    goto _END;
                }

                if(!$clObj->server1){
                    bl("Bạn đợi ít phút để tải file này!");
                    goto _END;
                }

                $proxy = \App\Models\CloudServer::getProxyDomainServer($clObj->server1);
                try{
                    if(!\App\Components\U4sHelper::checkDownloadAble_($clObj->id, $proxy, $clObj->location1, $clObj->size)){
                        bl("File không tồn tại hoặc đã bị xoá");
                        goto _END;
                    }
                }
                catch (Exception $exception){
                    bl("Kiểm tra file: " . $exception->getMessage());
                    goto _END;
                }


                if (!$clObj->server1) {
                    if ($clObj->created_at > nowyh(time() - _NSECOND_DAY))
                        bl("Bạn đợi ít phút để tải file này");
                    else
                        bl("Có lỗi xảy ra, file chưa được xử lý ($clObj->created_at) !", 'Hãy liên hệ Admin');
                    goto _END;
                }
                ?>
                <button class='btn btn-primary btn-sm txt-light p-1 px-3' id="download_btn"
                        style="        box-shadow : 0px 0px 5px 2px #bbb;"
                > Tải xuống
                </button>
            </div>

            <?php
            _END:
            ?>


            <div>

            </div>


        </div>


        <div class="mt-3" STYLE="text-align: center; max-width: 800px; margin: 0px auto;">

{{--            <img class="mb-2" style="width: 100%"  src="/images/banner/banner-tet-chuc-mung-nam-moi2.png" alt="">--}}

{{--            <a style="color: royalblue" href="https://support.galaxycloud.vn/" target="_blank">--}}
{{--                <div>--}}
{{--                <img src="https://support.galaxycloud.vn/images/icon/zalo2.png" style="max-width: 50px" alt="">--}}
{{--                </div>--}}
{{--                <p style="" class="mt-2">--}}

{{--                Chạy nhiều tài khoản Zalo trên Máy tính PC--}}
{{--                </p>--}}
{{--            </a>--}}

        </div>

    </div>

    <script>

        window.addEventListener('load', function () {
            let lastClickTime = 0;
            $('#download_btn').click(function () {

                let nSecWait = 0
                //Neu click qua nhanh, 10 giay, thi khong cho click
                if (new Date().getTime() - lastClickTime < 20000) {
                    nSecWait = 20 - Math.floor((new Date().getTime() - lastClickTime) / 1000);
                    // Thêm số nSecWait giây vào nut Tải xuống (nSecWait) và đếm ngược về 0
                    setInterval(function () {
                        nSecWait--;
                        if (nSecWait > 0) {
                            $('#download_btn').text("Đợi (" + nSecWait + ") giây");
                        } else {
                            $('#download_btn').text("Tải xuống");
                        }
                    }, 1000);

                    alert("File đang được tải, hoặc Bạn hãy đợi " + nSecWait + " giây để tiếp tục tải file này!")
                    return;
                }

                lastClickTime = new Date().getTime();

                async function downloadFile(dlink) {
                    const a = document.createElement('a');
                    a.href = dlink;
                    a.download = ''; // Tên file có thể tùy chọn, để rỗng thì lấy từ URL
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }


                console.log(" Clicked at " + lastClickTime);

                async function getLinkDownload() {
                    try {
                        // Yêu cầu fetch đầu tiên
                        const response1 = await fetch('/api/download-session/getDlink4s?ide={{$ide}}');
                        // Kiểm tra trạng thái của response1
                        if (!response1.ok) {
                            throw new Error(`HTTP error! status: ${response1.status}`);
                        }
                        const ret = await response1.text(); // Lấy văn bản từ phản hồi

                        let retJson = null;
                        try {
                            retJson = JSON.parse(ret);
                        } catch (e) {
                            alert('Error getLink1: ' + ret);
                            return;
                        }

                        console.log('Response 1:', ret); // Xem văn bản trả về từ server
                        // Kiểm tra nội dung của text1
                        if (!retJson.dlink) {
                            throw new Error('GetLink: Response 1 không phù hợp');
                        }

                        console.log(" DLINK = ", retJson.dlink);

                        downloadFile(retJson.dlink);
                        // alert(ret.dlink);
                    } catch (error) {
                        alert(`Error occurred: ${error.message}`);
                    }
                }

                getLinkDownload();

            });
        });

    </script>

@endsection

@section('title')
    Tải file {{ $obj->name ??'' }} - {{\LadLib\Common\UrlHelper1::getDomainHostName() }}
@endsection
