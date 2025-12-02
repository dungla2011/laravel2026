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

$filePage1 = '/share/1/1.png';
//
//$pdf = new Spatie\PdfToImage\Pdf($file);
////$pdf->setCompressionQuality(100);
//$pdf->setOutputFormat('png');
//$pdf->setPage(1)->saveImage("/share/1/4.png");
//
//return;

$mm = \App\Models\MyDocument::all();
$cc = 0;
foreach ($mm as $one) {
    $cc++;

    echo "<br/>\n$cc. $one->id / $one->file_list";
    if ($one->image_list) {
        continue;
    }

    if ($file = \App\Models\FileUpload::find($one->file_list)) {

        $fileP = $file->file_path;

        if (! strstr($file->name, '.pdf')) {
            continue;
        }

        if (file_exists($fileP)) {

            try {

                $pdf = new Spatie\PdfToImage\Pdf($fileP);
                //$pdf->setCompressionQuality(100);
                $pdf->setOutputFormat('png');
                @unlink($filePage1);
                $pdf->setPage(1)->saveImage($filePage1);

                $cc1 = 0;
                while (! file_exists($filePage1)) {
                    sleep(1);
                    $cc1++;
                    echo "<br/>\n $cc1. while file ok: $filePage1";
                }

                $ret1 = \App\Models\FileUpload::uploadFileApiV2(
                    'https://toantin.mytree.vn/api/member-file/upload',
                    $tk,
                    file_get_contents($filePage1),
                    "Thumb - $one->name.png",
                    '',
                    ['set_parent_id' => 0]
                );

                print_r($ret1);
                $rt1 = json_decode($ret1);
                $retId = $rt1->payload->id;

                if ($retId) {
                    $one->image_list = $retId;
                    $one->addLog('Add image thumb from first page!');
                    $one->save();
                    //                getch("...");
                }

            } catch (Throwable $e) { // For PHP 7
                echo "<br/>\n Error1: ".$e->getMessage();
                getch('...');
            } catch (Exception $exception) {
                echo "<br/>\n Error2: ".$exception->getMessage();
                getch('...');
            }

        }
    }

}
