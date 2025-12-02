<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('DEF_TOOL_CMS', 1);
$_SERVER['SERVER_NAME'] = 'toantin.mytree.vn';

require_once __DIR__.'/../../index.php';
require_once __DIR__.'/../../../app/Components/qA_W3School.php';
//
//require_once "../../vendor/_ex/simple_html_dom.php";

$tk = '3333333331d0205061d02050b03040b03060007979669879879';

//$ret = \App\Models\FileUpload::uploadFileApiV2(
//    "https://toantin.mytree.vn/api/member-file/upload",
//    $tk,
//    file_get_contents('i:/download/book/1nDnGqSwJhPuw-LaNjHSLo6-WgOGBfJKp.pdf'),
//    'file2.pdf',
//    '',
//    ['set_parent_id'=>5, 'refer'=> '1nDnGqSwJhPuw-LaNjHSLo6-WgOGBfJKp']
//);
//
//$rt = json_decode($ret);
//
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($rt->payload);
//echo "</pre>";
//echo ("\n ID = " . $rt->payload->id);
//
//$mm = \App\Models\MyDocument::all();
//
//$cc = 0;
//foreach ($mm AS $obj){
//    $cc++;
//    echo "\n $cc. $obj->id ";
//    if($obj instanceof \App\Models\ModelGlxBase);
//    $obj->updateParentList();
//}
//return;

$mm = [
    ['Kết Nối Tri Thức', 'https://www.vniteach.com/sach-dien-tu-ket-noi-tri-thuc-voi-cuoc-song/'],
    ['Cánh Diều', 'https://www.vniteach.com/sach-dien-tu-canh-dieu/'],
    ['Trân trời sáng tạo', 'https://www.vniteach.com/sach-dien-tu-chan-troi-sang-tao'],
];

$elm = $mm[1];

foreach ($mm as $elm) {

    echo '<pre> >>> '.__FILE__.'('.__LINE__.')<br/>';
    print_r($elm);
    echo '</pre>';
    //getch();
    $theLoai = $elm[0];
    $ct = file_get_content_cache($elm[1]);
    //$theLoai = "Cánh Diều";
    //$ct = file_get_content_cache("https://www.vniteach.com/sach-dien-tu-canh-dieu/");
    //$theLoai = "Trân trời sáng tạo";
    //$ct = file_get_content_cache("https://www.vniteach.com/sach-dien-tu-chan-troi-sang-tao");

    $pCatId = 0;
    if (! $obj = \App\Models\MyDocumentCat::where(['name' => $theLoai, 'parent_id' => 23])->first()) {
        $obj = new \App\Models\MyDocumentCat();
        $obj->name = $theLoai;
        $obj->save();
    }

    $pCatId = $obj->id;

    //    getch(" PCID = $pCatId");

    $x = str_get_html($ct);
    $x->find('.the_content', 0);

    $cc = 0;
    foreach ($x->find('.wp-block-table') as $div) {

        echo "<br/>\n xxx";
        $title = $div->next_sibling('h1.wp-block-heading', 0)->innertext;

        $clsOK = '';
        foreach ($div->find('tr') as $tr) {

            $cls = trim(strip_tags($tr->find('td', 0)->innertext));
            if ($cls && is_numeric($cls)) {
                $clsOK = $cls;
            }
            echo "<br> Class = $clsOK";

            $lopName = "Lớp $clsOK";

            if (isCli()) {
                if ($clsOK) {
                    if (! $objDocCat = \App\Models\MyDocumentCat::where('name', $lopName)->first()) {
                        getch("Class =  $lopName");
                        $objDocCat = new \App\Models\MyDocumentCat();
                        $objDocCat->name = $lopName;
                        $objDocCat->parent_id = 0;
                        $objDocCat->save();
                        getch('...');
                    }
                }
            }

            foreach ($tr->find('a') as $lk) {
                $link = trim($lk->href);
                $name = trim(strip_tags($lk->innertext()));

                echo "<br/>\n ===== ($clsOK) Name/Link = $name  / $link";

                $nameFull = "$name - $theLoai";

                if (isCli()) {

                    if (! $objDoc = \App\Models\MyDocument::where(['name' => $nameFull, 'refer' => $link])->first()) {
                        //                    getch("Name =  $name");
                        $objDoc = new \App\Models\MyDocument();
                        $objDoc->name = $nameFull;
                        $objDoc->parent_id = $objDocCat->id;
                        $objDoc->refer = $link;
                        $objDoc->save();
                        //                    getch("...");
                    }

                    if ($pCatId && strstr($objDoc->parent_extra, ",$pCatId,") === false) {
                        $objDoc->parent_extra .= ",$pCatId,";
                        $objDoc->save();
                        echo "\n Update parent";
                    }

                    $ct2 = file_get_content_cache("$link");
                    $x2 = str_get_html($ct2);
                    if ($x2) {
                        $cc++;
                        $linkGG = $x2->find('iframe', 0)->src ?? '';
                        echo "<br/>\n $cc. -- linkGG = $linkGG";

                        $idF = str_replace('https://drive.google.com/file/d/', '', $linkGG);
                        $idF = explode('/', $idF)[0];
                        echo "<br/>\n IDF = $idF";

                        //Nếu up rồi thì ko up nữa:
                        if ($fileUpObj = \App\Models\FileUpload::where(['refer' => $idF])->first()) {
                            $retId = $fileUpObj->id;
                            echo "\n Đã có file, ko upload";
                        } else {

                            $saveTo = "i:/download/book/$idF.pdf";
                            $cmd = "py E:/Projects/_python/doing/02-download-google-driver-ok2.py $idF $saveTo";
                            //Tải file:
                            if (! file_exists($saveTo)) {
                                exec($cmd);
                            }

                            //Upload file:
                            if (file_exists($saveTo)) {

                                echo " \n Filesize = ".filesize($saveTo);

                                if (filesize($saveTo) < 5000) {
                                    continue;
                                }

                                $ret1 = \App\Models\FileUpload::uploadFileApiV2(
                                    'https://toantin.mytree.vn/api/member-file/upload',
                                    $tk,
                                    file_get_contents($saveTo),
                                    $nameFull.'.pdf',
                                    '',
                                    ['set_parent_id' => 0, 'refer' => $idF]
                                );

                                print_r($ret1);

                                $rt1 = json_decode($ret1);

                                if (! ($rt1->payload ?? '') || ! ($rt1->payload->id ?? '')) {
                                    getch("\n Can not upload file? ... ");
                                }

                                //                        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                                //                        print_r($rt->payload);
                                //                        echo "</pre>";
                                $retId = $rt1->payload->id;
                                echo "\n ID = ".$rt1->payload->id;

                                //                                getch("2...");

                            }
                        }

                        if (! $objDoc->file_list) {
                            //Gán file cho Docx
                            if (! $objDoc->file_list) {
                                $objDoc->file_list = $retId;
                            } else {
                                $objDoc->file_list .= ','.$retId;
                            }

                            $objDoc->save();
                        }

                        //                    getch("1...");

                    }
                }
            }
        }
        //    echo "<br/>\n $title";
    }
}
