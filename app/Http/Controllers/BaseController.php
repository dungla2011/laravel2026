<?php

namespace App\Http\Controllers;

use App\Components\clsParamRequestEx;
use App\Components\Helper1;
use App\Models\ModelGlxBase;
use App\Models\SiteMng;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;
use LadLib\Laravel\Database\DbHelperLaravel;

class BaseController extends Controller
{

    public $objParamEx;

    public function __construct()
    {

        if(SiteMng::getInstance()?->maintain_text){
            echo SiteMng::getInstance()?->maintain_text;
            die();
        }

        //Nếu child không init param thì ở đây sẽ setup default
        if (! $this->objParamEx) {
            $this->objParamEx = new clsParamRequestEx();
        }
    }

    //Lấy data từ API
    public function index()
    {

        //\//ladDebug::addTime(__FILE__ . " basecontroller.php index func ", __LINE__);

        $t1 = microtime(1);
        $page = \request()->get('page');
        if ($this->data instanceof ModelGlxBase);

        $objMeta = $this->data::getMetaObj();

        if(!$objMeta)
            die("Not obj meta? " . $this->data::class);


        if ($objMeta instanceof MetaOfTableInDb);
        $objParamEx = $this->objParamEx;

        $retEx = $objMeta->executeBeforeIndex();

        //        $urlApi = $objMeta->getApiUrl($objParamEx->module) . "/list?return_laravel_type=1&page=$page";

        //Đưa tất cả tham số trên URL gửi sang API:
        $params = \request()->toArray();
        //        $urlApi = UrlHelper1::setUrlParamArray($urlApi, $params);
        $user = \auth()->user();
        $email = $user?->email;
        $tokenAccess = $user?->getJWTUserToken();
        if (! $tokenAccess) {
            //            echo "<br/>\n Not valid token access?";
            //            return;
        }

        //\//ladDebug::addTime(__FILE__ . " basecontroller.php index func ", __LINE__);
        $dataView = null;
        //        if ($retEx)
        if ($retEx !== -1 && $this->data) {

//            if($this->data instanceof ModelGlxBase);

            $dataView = $this->data->queryDataWithParams($params, $objParamEx);
            $dt = (microtime(1) - $t1);




        } else {

            //            try {
            //                $client = new \GuzzleHttp\Client();
            //                $res = $client->request('GET', $urlApi,
            //                    ['headers' => ['Authorization' => 'Bearer ' . $tokenAccess],
            //                        'timeout' => 5,
            //                        'connect_timeout' => 5,
            //                    ]);
            //                $res = $res->getBody();
            //                if (!$retObj = json_decode($res))
            //                    loi2("Not valid data get1: " . $res);
            //
            //                \clsDebugHelper::$lastQuery = $retObj->payloadEx;
            //
            //                if (!isset($retObj->code))
            //                    loi2("Not valid data get2?");
            //                if ($retObj->code < 0)
            //                    loi2("Error Data get, code = $retObj->code, $retObj->message");
            //                if (!$dataView = unserialize($retObj->payload))
            //                    loi2("Not valid data get3?");
            //
            //            } catch (\Throwable $e) { // For PHP 7
            //                echo("<br>\n API-ErrorCode: ($email) " . $e->getCode() . "<br>\n API-ErrorMesage: " . $e->getMessage());
            //                return;
            //            } catch (\Exception $e) {
            //                echo("<br/>\n API-ErrorCode: ($email) " . $e->getCode() . "<br/>\n API-ErrorMesage: " . $e->getMessage());
            //                return;
            //            }
        }


        //\//ladDebug::addTime(__FILE__ . " basecontroller.php index func ", __LINE__);
        //UnSerialize để gửi ra View
        //        if (!$dataView = unserialize($retObj->payload))
        //            dd("Data not valid? can not unserialize!");
        //        $dataView = $dataRet['dataRet'];
        //        if (!isset($dataRet['dataRet']))
        //            dd("Data not valid? Not found dataRet!");
        //        $dataView = $dataRet['dataRet'];

        //        dd($dataView);

        //Dữ liệu nhận về là có phân trang
        if ($dataView instanceof LengthAwarePaginator) {
            //Path return từ API sẽ đổi thành url hiện tại;
            $dataView->setPath(UrlHelper1::setUrlParamThisUrl('page', null));
        }

        //        if(!$dataView)
        //            return "Empty content!";

        //Truyền ra view
        $mMetaAll = $objMeta->getMetaDataApi();
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($objMeta);
        //        echo "</pre>";

        if (! $mMetaAll) {
            echo "<br/>\n Not found MetaAll3 (May be not have table: $objMeta->table_name_model)";

            return;
        }

        $dataApiUrl = $objMeta->getApiUrl($objParamEx->module ) ;

        //\//ladDebug::addTime(__FILE__ . " basecontroller.php index func ", __LINE__);
        $indexView = $objMeta->getIndexViewName(\request()->getRequestUri());

        if (isset($params['browse_file_iframe'])) {
            $indexView = 'admin.demo-api.index-browse-file';
        }

        //        if($dataView)
        //            $dataView->___dtime = $dt;
        //\//ladDebug::addTime(__FILE__ . " basecontroller.php index func before load vìew ", __LINE__);

        return view($indexView, compact('dataView', 'mMetaAll', 'dataApiUrl', 'objParamEx'));
    }

    //Sử dụng Meta from Mongo
    //    public function index2()
    //    {
    //
    //        $page = \request()->get('page');
    //        if ($this->data instanceof ModelGlxBase) ;
    //
    //        $tableName = $this->data->getTable();
    //        $objMeta = DbHelperLaravel::getMetaObjFromTableName($tableName);
    //
    //        $url = $objMeta->getApiUrl() . "?return_laravel_type=1&page=$page";
    //
    //        $ctx = stream_context_create(array('http' => [
    //            'timeout' => 5, 'header' => 'Authorization: Bearer 123456']));
    //        $ret = file_get_contents($url, false, $ctx);
    //
    //        $dataRet = unserialize($ret);
    //
    //
    //        $dataView = $dataRet['dataRet'];
    //
    //        if ($dataView instanceof LengthAwarePaginator) ;
    //        //Path return từ API sẽ đổi thành url hiện tại;
    //        $cUri = \request()->path();
    ////            $dataView->setPath('http://127.0.0.1:8002/' . $cUri);
    //        $dataView->setPath('/' . $cUri);
    //
    //        //Lấy MetaData truyền ra view
    //
    //        $con = \Illuminate\Support\Facades\DB::getPdo();
    //        $objMeta->setDbInfoGetMeta($tableName, $con);
    //        $mMetaAll = $objMeta->getMetaDataApi();
    //
    //        $dataApiUrl = $objMeta->getApiUrl();
    //
    //        return view("admin.demo-api.index", compact('dataView', 'mMetaAll', 'dataApiUrl'));
    //    }

    public function edit($id)
    {


        //\//ladDebug::addTime(__FILE__ . " basecontroller.php edit func ", __LINE__);
//        $objParamEx = new clsParamRequestEx();
        $objParamEx =  $this->objParamEx;
        $objParamEx->setParamsEx(\request());

        $params = \request()->toArray();

        //        $data = $this->data->get($id, $objParamEx);

        $tableName = $this->data->getTable();
        $objMeta = $this->data::getMetaObj();
        if ($objMeta instanceof MetaOfTableInDb);

        $url = $objMeta->getApiUrl($objParamEx->module)."/get/$id?return_laravel_type=1";
        //\//ladDebug::addTime(__FILE__ . " basecontroller.php edit func ", __LINE__);
        $mMetaAll = $objMeta->getMetaDataApi();
        if (isset($mMetaAll['user_id'])) {
            $objParamEx->setUidIfMust();
        }

        $dataApiUrl = $objMeta->getApiUrl($objParamEx->module);

        $id0 = $id;
        //neu la dang rand UUID thi chuyen sang id
        if (isUUidStr($id) && $this->data->hasField('ide__')) {
            if(isDebugIp()){
//                die("ID1 = $id/ " . $this->data::class);
            }
            $id = trim($id);
            $data = $this->data::where('ide__', $id)->first();
            if(!$data)
                die("Not found data1: $id");
            $id = $data?->id;
//                  die("IDx1 = $id");
        }else
        if (! is_numeric($id)) {
            $id = qqgetIdFromRand_($id);
        }
        $objParamEx->return_laravel_type = 1;
        //        $data = $this->data::find($id);
        if ($this->data instanceof ModelGlxBase);

        $ret = $this->data->queryGetOne($id, $objParamEx);
        if (! $ret) {
            $cls = explode('\\', get_class($this->data));

            $pad = '';
            if(isSupperAdmin_() && Helper1::isMemberModule())
            {
                $link = UrlHelper1::getFullUrl();
                $link = str_replace("/member/", "/admin/", $link);
                $pad = " | <a href='$link'>Admin Item</a>";
            }
            return response("Not found data20:  $id0 | ".end($cls) . " $pad ", 400);
            //            loi("Not found data: $id0 | " . get_class($this->data));
        }

        $data = $ret;

        //\//ladDebug::addTime(__FILE__ . " basecontroller.php edit func ", __LINE__);
        //Bỏ qua api, lấy trực tiếp luôn từ db
        if (0) {
            try {
                //            $ret = $this->data->get($id, $objParamEx);
                //$url = "http://127.0.0.1:8001/api/demo/get/$id?return_laravel_type=1";
                $tokenAccess = \auth()->user()->getUserToken();
                $ctx = stream_context_create(['http' => [
                    'timeout' => 5, 'header' => 'Authorization: Bearer '.$tokenAccess]]);
                $ret = file_get_contents($url, false, $ctx);
            } catch (\Throwable $e) { // For PHP 7
                return 'Có lỗi: '.$e->getMessage();
            } catch (\Exception $e) {
                return 'Có lỗi: '.$e->getMessage();
            }

            $retObj = json_decode($ret);

            $data = unserialize($retObj->payload);

            \clsDebugHelper::$lastQuery = $retObj->payloadEx;
        }

        //\//ladDebug::addTime(__FILE__ . " basecontroller.php edit func before load view ", __LINE__);
        return view('admin.demo-api.edit', compact('data', 'mMetaAll', 'dataApiUrl'));
    }

    public function create()
    {
        $objMeta = $this->data::getMetaObj();
        $mMetaAll = $objMeta->getMetaDataApi();
        $objParamEx = new clsParamRequestEx();
        $objParamEx->setParamsEx(\request());
        $dataApiUrl = $objMeta->getApiUrl($objParamEx->module);
        $data = $this->data;

        return view('admin.demo-api.edit', compact('data', 'mMetaAll', 'dataApiUrl'));
    }

    public function delete(Request $rq)
    {

        echo "\n ID = $rq->id ";

    }

    //    function store()
    //    {
    //
    //    }
    //
    //    function update()
    //    {
    //
    //    }
    //
    //    function add()
    //    {
    //
    //    }
    //
    //    function delete()
    //    {
    //
    //    }
}
