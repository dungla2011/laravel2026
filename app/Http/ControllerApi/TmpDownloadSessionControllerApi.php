<?php

namespace App\Http\ControllerApi;

use App\Components\clsParamRequestEx;
use App\Models\TmpDownloadSession;
use App\Repositories\TmpDownloadSessionRepositoryInterface;

class TmpDownloadSessionControllerApi extends BaseApiController
{
    public function __construct(TmpDownloadSessionRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }

    function getDlink4s(){
        $ide = \request('ide');
        $uid = getCurrentUserId();
        try {
            //Kiểm tra download allow:
            $mm = TmpDownloadSession::getLinkDownload4s($ide, $uid);

        } catch (\Exception $exception) {
            $error = $exception->getMessage();
            die("Some error: $error");
        }

        die(json_encode($mm));
    }

}
