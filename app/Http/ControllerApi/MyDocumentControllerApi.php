<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\FileCloud;
use App\Models\FileUpload;
use App\Models\MyDocument;
use App\Models\User;
use App\Repositories\MyDocumentRepositoryInterface;
use LadLib\Common\UrlHelper1;



class MyDocumentControllerApi extends BaseApiController
{
    public function __construct(MyDocumentRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;
    }

    function genImageThumb()
    {
        $id = request('id');
        $idf = MyDocument::genImageThumbFromPdfInFileList($id);
        if($idf)
            die(" Check ok ? id = $idf");

        die("ID = $id, đã xong từ trước (idf = $idf), hoặc có lỗi gì đó? ");
    }
}
