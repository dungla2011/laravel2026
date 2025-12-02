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

$link = "https://www.duytan.com/Product/SearchResults.aspx?q=gh%e1%ba%bf+nh%e1%bb%b1a";

$ct = file_get_html($link);

//Tìm link a, img, title, price
/*
<div class="product-item">
            <div class="product-item-img">
              <figure>
                <a href="https://www.duytan.com/san-pham/ghe-wendy" target="_self" title="Ghế Wendy">
                  <img class="lazy entered loaded" data-src="/Data/Sites/1/Product/7267/ghe-wendy-xam.png" alt="Ghế Wendy" data-ll-status="loaded" src="/Data/Sites/1/Product/7267/ghe-wendy-xam.png">
                </a>
              </figure>
            </div>
            <div class="product-item-caption">
              <div class="product-item-title"><a href="https://www.duytan.com/san-pham/ghe-wendy" target="_self" title="Ghế Wendy">Ghế Wendy</a></div>
              <div class="product-item-bottom">
                <div class="product-item-label">
                  <span>New</span>
                </div>
              </div>
            </div>
          </div>
 */

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
