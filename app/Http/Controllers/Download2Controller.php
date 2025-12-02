<?php

namespace App\Http\Controllers;

use App\Http\ControllerApi\Api4sV1Controller;

use App\Models\AffiliateLog;
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

class Download2Controller extends Controller
{

    function folder_list($ide)
    {
        return $this->getViewLayout(null, compact(['ide']));
    }
    /**
     * Tải file, sinh ra token để tải, và link để forward đển download server
     * @param $ide
     * @param $name
     * @return void
     */
    public function dlfile($ide, $name = '') {

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($obj->toArray());
//        echo "</pre>";
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($clf->toArray());
//        echo "</pre>";


        return $this->getViewLayout(null, compact(['ide', 'name']));
    }

    function search_file() {

        return $this->getViewLayout();

    }
}
