<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define("DEF_TOOL_CMS", 1);
//$_SERVER['SERVER_NAME'] = '';

function setDomainHostNameGlx1($hname)
{
    $_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = $hname;
}
setDomainHostNameGlx1('test2023.mytree.vn');
require_once "/var/www/html/public/index.php";

$domain = getDomainHostName();

$link = "https://songlongplastic.com/danh-muc-san-pham/ghe-banh/";

$ct = file_get_html($link);

//Tìm link a, img, title, price
/*
<div class="product-item relative box-shadow swiper-slide">
                      <span class="tag"><img src="https://songlongplastic.com/wp-content/themes/songlong-plastic/assets/images/newtop.png" alt=""></span>
                    <a href="https://songlongplastic.com/san-pham/ghe-banh-thuy-si/" class="--image">
                <div class="product-img">
          <div class="img-list-wrap">
                                <div class="img-trigger-hover">
                  </div>
                  <img src="https://songlongplastic.com/wp-content/uploads/2021/12/Ghe-banh-thuy-si-3.jpg" alt="">
                                  <div class="img-trigger-hover">
                  </div>
                  <img src="https://songlongplastic.com/wp-content/uploads/2021/12/Ghe-banh-Thuy-Sy-01.jpg" alt="">
                                  <div class="img-trigger-hover">
                  </div>
                  <img src="https://songlongplastic.com/wp-content/uploads/2021/12/Ghe-banh-thuy-si.jpg" alt="">
                                  <div class="img-trigger-hover">
                  </div>
                  <img src="https://songlongplastic.com/wp-content/uploads/2021/12/Ghe-banh-thuy-si-1-1.jpg" alt="">
                                  <div class="img-trigger-hover">
                  </div>
                  <img src="https://songlongplastic.com/wp-content/uploads/2021/12/Ghe-banh-thuy-si-2-1.jpg" alt="">
                                  <div class="img-trigger-hover">
                  </div>
                  <img src="https://songlongplastic.com/wp-content/uploads/2021/12/Ghe-banh-Thuy-Si-6.jpg" alt="">
                                  <div class="img-trigger-hover">
                  </div>
                  <img src="https://songlongplastic.com/wp-content/uploads/2021/12/Ghe-banh-thuy-si-4-1.jpg" alt="">
                                  <div class="img-trigger-hover">
                  </div>
                  <img src="https://songlongplastic.com/wp-content/uploads/2021/12/Ghe-banh-thuy-si-3-1.jpg" alt="">
                          </div>
      </div>
            </a>
        <a href="https://songlongplastic.com/san-pham/ghe-banh-thuy-si/" class="--title" title="GHẾ BÀNH THỤY SĨ">
          GHẾ BÀNH THỤY SĨ        </a>
        <a href="https://songlongplastic.com/san-pham/ghe-banh-thuy-si/" class="--btn-detail">
            <span>CHI TIẾT</span>
        </a>
    </div>
 */
//Html o tren, hãy viết lại như đoaạn dưới
foreach ($ct->find(".product-item") AS $one) {
    $a1 = $one->find("a", 0)->href;

    $img = $one->find("img" , 0);
    $linkImg= "".$img->src;
    if(!$img->src) {
        //Get data-src
        $linkImg = "" . $img->getAttribute('data-src');
    }

    if(str_contains($linkImg, 'newtop.png')){
        $img = $one->find("img" , 1);
        $linkImg= "".$img->src;
        if(!$img->src) {
            //Get data-src
            $linkImg = "" . $img->getAttribute('data-src');
        }
    }


    echo "<br/>\n IMG = $linkImg   ";

    $title = $one->find('.--title', 0)->text();
    echo "<br> Title = $title";
    //Price in strong
    //    <div class="gia"><span class="nhan">Giá KM:</span> <strong>35.000</strong> <span>VND</span></div>
//    $price = $one->find('.gia strong', 0)->text();
//    $price = str_replace(".", "", $price);
//    echo "<br/>\n Price = $price";


    $title = strip_tags($title);
    $title = trim($title);
    if($pr  = \App\Models\Product::where("refer", $a1)->where("name",$title)->first()){
        echo "<br/>\n *** Not inserted, already exist product!";
        if(isCli())
            if($linkImg){
                echo "\n ---> Need image";
                $ctImg = "";
                try {
                    $ctImg = file_get_content_cache($linkImg);
                }
                catch (Exception $e) {
                    echo "<br/>\n Error: " . $e->getMessage();
                    continue;
                }

                $tk = "TK1_eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoxLCJleHAiOjE3NjEwMzYzNDh9.ejDVggYgZarfWkbL__yinhTR2FAEwqiSOMRfe1HnsPo";
                //Upload ảnh
                $ret = \App\Models\FileUpload::uploadFileApiV2('https://test2023.mytree.vn/api/member-file/upload', $tk, $ctImg, "$title.png", 'image/png');

                $js = json_decode($ret);
                $id = $js->payload->id;

                echo "<br/>\n ID = $id ";
                if($id ?? ''){
                    $pr->image_list = $id;
                    $pr->save();
//                    $pr->price = $price;
                }

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($ret);
//            echo "</pre>";

            }
        continue;
    }

    if($pr){

    }

    $pr = new \App\Models\Product();
    $pr->name = $title;
    $pr->refer = $a1;
    $pr->save();
}


if(0)
foreach ($ct->find(".product-item") AS $one) {
    $img = $one->find("img", 0);
    // get first a href link
    $a1 = $one->find("a", 0)->href;
    echo "<br/>\n ALINK = $a1";

    $linkImg = "https://www.duytan.com/" . $img->src;
    if (!$img->src) {
        //Get data-src
        $linkImg = "https://www.duytan.com/" . $img->getAttribute('data-src');
    }
    echo "<br/>\n IMG = $linkImg   ";

    $title = $one->find('.product-item-title', 0)->text();
    echo "<br> Title = $title";

    $title = strip_tags($title);
    $title = trim($title);
    if($pr  = \App\Models\Product::where("refer", $a1)->where("name",$title)->first()){
        echo "<br/>\n *** Not inserted, already exist product!";
        if(isCli())
        if(!$pr->image_list && $linkImg){
            echo "\n ---> Need image";
            $ctImg = "";
            try {
                $ctImg = file_get_content_cache($linkImg);
            }
            catch (Exception $e) {
                echo "<br/>\n Error: " . $e->getMessage();
                continue;
            }

            $tk = "TK1_eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VyX2lkIjoxLCJleHAiOjE3NjEwMzYzNDh9.ejDVggYgZarfWkbL__yinhTR2FAEwqiSOMRfe1HnsPo";
            //Upload ảnh
            $ret = \App\Models\FileUpload::uploadFileApiV2('https://test2023.mytree.vn/api/member-file/upload', $tk, $ctImg, "$title.png", 'image/png');

            $js = json_decode($ret);
            $id = $js->payload->id;

            echo "<br/>\n ID = $id ";
            if($id ?? ''){
                $pr->image_list = $id;
                $pr->save();
            }

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($ret);
//            echo "</pre>";

        }
        continue;
    }

    if($pr){

    }

    $pr = new \App\Models\Product();
    $pr->name = $title;
    $pr->refer = $a1;
    $pr->save();





    // Tim gia trong strong
}
