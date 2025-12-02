<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\UrlHelper1;
use LadLib\Laravel\Database\TraitModelExtra;

class MyDocument extends ModelGlxBase
{
    use HasFactory, SoftDeletes, TraitModelExtra;

    protected $guarded = [];

    static function getElasticDbName()
    {
        return "db_tai_lieu_chuan";
    }

    public function getLinkPublic()
    {
        return "/tai-lieu/chi-tiet?fid=" . qqgetRandFromId_($this->id);
    }

    public function getParentObj()
    {
        return MyDocumentCat::find($this->parent_id);
    }

    static function genImageThumbFromPdfInFileList($id)
    {
        $mydoc = MyDocument::find($id);
        if ($mydoc->image_list)
            return " Đã có ảnh: " . $mydoc->image_list;
        if ($mydoc->file_list) {
            $idF = explode(',', $mydoc->file_list)[0];
            if ($fileU = FileUpload::getCloudObj($idF)) {

                if (file_exists($fileU->file_path)) {
                    //Get Mime file:
                    $mime = mime_content_type($fileU->file_path);
                    //Neu la PDF:
                    if ($mime == 'application/pdf') {
                        $imagick = new \Imagick();
                        $imagick->setResolution(300, 300);
                        $imagick->readImage($fileU->file_path . '[0]');
                        $imagick->setImageFormat('jpeg');
                        $imagePath = '/share/upload_file_mydocument.jpg';
                        $imagick->writeImage($imagePath);
                        $imagick->clear();
                        $imagick->destroy();
                        //Resize image to 800px width:
                        $imagick = new \Imagick($imagePath);
                        $imagick->resizeImage(1000, 0, \Imagick::FILTER_LANCZOS, 1);
                        $imagick->writeImage($imagePath);

                        $tk = User::where("email", env('AUTO_SET_ADMIN_EMAIL'))->first()->getJWTUserToken();

                        $domain = UrlHelper1::getDomainHostName();


                        $fid = FileUpload::uploadFileLocal($imagePath, '', 165, 0, 2);

                        //Upload file này lenn cloud lay lai ID
//                        $fid = FileUpload::uploadFileContentByApi("https://$domain/api/member-file/upload", $tk,
//                            file_get_contents($imagePath),
//                            "thumb.jpg", 'image/jpeg', 0);

                        if ($fid) {
                            if (!$mydoc->image_list)
                                $mydoc->image_list = $fid;
                            elseif (!str_contains(",$mydoc->image_list,", ",$fid,"))
                                $mydoc->image_list .= ',' . $fid;
                            $mydoc->save();

                            return $fid;
//                        die("FID = $fid, Refresh to check!");
                        }
                    }
                }
            }
        }
        return 0;
//    die("ID = $id, Something not ok?");
    }


    function htmlBlockOneItem()
    {


        $link = $this->getLinkPublic();
        $cat = $this->getParentObj();
        $linkCat = "#";
        $catName = "";
        if ($cat && $cat instanceof \App\Models\MyDocumentCat) {
            $catName = $cat->name;
            $linkCat = $cat->getLinkPublic();
            $catName = $cat->getBreakumPathHtml(0,0, "::");
        }
        $thumb = $this->getThumbInImageList('image_list', 1);
        ?>
        <div class="one_item qqqq1111">
            <?php

            BlockUi::showEditLink_("/admin/my-document/edit/$this->id");

            ?>
            <div style="height:10px;"></div>
            <table width="100%" border="0">
                <tbody>
                <tr>
                    <td>
                        <div style="color:#428BB2; font:15px arial;"><span class="category"><a
                                    href="<?= $link ?>"> <?= $catName ?> </a> ::
                                    <h2>
                                        <a href="<?= $link ?>"><?= $this->name ?></a>
                                    </h2></span>
                        </div>
                        <span class="category"> </span>
                    </td>
                    <td width="100">
                        <div class="rating">
                            <ul class="unit-rating">
                                <li class="current-rating" style="width:0%;">0</li>
                            </ul>
                        </div>
                    </td>
                </tr>
                </tbody>
            </table>

            <div
                style="padding:20px; color:#333; font:8pt Verdana; text-align:justify; line-height:15px; padding-bottom:0;">
                <div style="text-align:center;">
                    <!--dle_image_begin:https://i124.fastpic.org/big/2025/0221/37/8c3eeb8317e4c9e8ae507ba236814d37.webp|-->
                    <img src="<?= $thumb ?>" style="max-width:200px; width: 100%" data-maxwidth="300"
                         alt=" <?= $this->name ?> "><!--dle_image_end--><br>

                    Download
                    <b>
                        <?= $this->name ?>
                    </b>

                    <br>
                    <?= $this->summary ?>
                </div>
                <br>
                <div style="padding-top:10px; line-height:100%;"><img src="/images/datapro/dlet_artblock_point_1.gif"
                                                                      align="absmiddle" width="13" height="9">
                    <a href="/software/842757-tableplus-633.html">Read More</a></div>
            </div>
            <div
                style="background: url('/images/datapro/seperator-bg.gif') no-repeat center bottom; border-bottom:1px solid #00659a; height:16px;">
            </div>
        </div>


        <?php
    }
}
