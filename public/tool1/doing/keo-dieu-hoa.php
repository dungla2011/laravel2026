<?php

$_SERVER['HTTP_HOST'] = $_SERVER['SERVER_NAME'] = 'dieuhoa2023.mytree.vn';

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../../index.php';

require_once '../../../vendor/_ex/simple_html_dom.php';

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r(\App\Components\Helper1::getDBInfo());
//echo "</pre>";
//
//die();

$mm = \App\Models\Product::all()->toArray();

echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
print_r($mm);
echo '</pre>';

$link = 'https://codienlanhbacninh.com/danh-muc/dieu-hoa/';

$cc = 0;
for ($i = 1; $i <= 8; $i++) {

    $link1 = $link."/page/$i/";

    $m1 = file_get_html($link1);
    sleep(1);

    foreach ($m1->find('.product-small.box') as $one) {
        sleep(1);
        $cc++;
        $href = $one->find('a', 0)->href;
        echo "\n $cc . $href";

        try {
            $x2 = file_get_html($href);

        } catch (\Throwable $e) { // For PHP 7
            echo "<br/>\n Error1: ".$e->getMessage();

            continue;
        } catch (\Exception $exception) {
            echo "<br/>\n Error2: ".$exception->getMessage();

            continue;
        }

        $img = $x2->find('.product-images', 0)->find('img', 0)->getAttribute('data-src');

        $name = $x2->find('h1', 0)->innertext;

        $des = $x2->find('.product-short-description', 0)?->innertext;

        $product_meta = $x2->find('.product_meta', 0)?->innertext;

        echo "\n IMG = $img";
        echo "\n name = $name";
        echo "\n DES: $des";
        echo "\n Meta: $product_meta";
        $href = trim($href);
        $img = trim($img);

        if (! \App\Models\Product::where('refer', $href)->first()) {
            $new = new \App\Models\Product();
            $new->name = $name;
            $new->refer = $href;
            $new->image_list = $img;
            $new->summary = $des;
            $new->meta = $product_meta;
            $new->status = 1;
            $new->save();
        } else {
            echo "\n Đã insert ...";
        }

    }

}
