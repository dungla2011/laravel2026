<?php

namespace App\Http\ControllerApi;

use App\Models\Data;
use App\Models\ModelMetaInfo;
use App\Models\Role;
use App\Models\SiteMng;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use LadLib\Common\Database\MetaClassCommon;
use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

class CommonControllerApi extends BaseController
{
    public function __construct()
    {
    }

    public function index()
    {
    }

    public function loginApi(Request $request)
    {

        if (! $request->email && ! $request->username) {
            return rtJsonApiError('Not valid email/username login?');
        }
        if (! $request->password) {
            return rtJsonApiError('Not valid password login?');
        }
        $request->password = trim($request->password);

        if(!$uname = trim($request->email)){
            $uname = $request->username;
        }

        if(!$uname)
            return rtJsonApiError('Not valid email/username/password0');

        $user = User::where('email', $uname)
                ->orWhere('username', $uname)->first();
        if(!$user)
            return rtJsonApiError('Not valid email/username/password1');

        if(SiteMng::getAuthTypeSha1()){
            $sha1 = sha1($request->password.$user->id);
            if($user->password != $sha1)
                return rtJsonApiError("Not valid email/username/password20" );
        }
        else
            if(!Hash::check($request->password, $user->password))
                return rtJsonApiError("Not valid email/username/password21");

        return rtJsonApiDone($user->getJWTUserToken(),'Token OK');
    }

    public function saveRole(Request $request)
    {

        //
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($request->toArray());
        //        echo "</pre>";
        //
        //        return;

        try {

            //            $validated = $request->validated();

            $role = Role::findOrFail($request->id);

            //            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
            //            print_r($request->route_name_code);
            //            echo "</pre>";
            //            return;

            DB::beginTransaction();

            if ($role instanceof Role);

            $mUpdate = [
                'display_name' => $request->display_name,
                'name' => $request->name,
            ];

            $role->update($mUpdate);

            //            DB::connection()->enableQueryLog();

            $role->permissions()->sync($request->route_name_code);

            if ($role instanceof Role);

            DB::commit();

            //            dd(DB::getQueryLog());

            return rtJsonApiDone('DONE');
            //            return redirect()->route("admin.role.index");

        } catch (\Throwable $e) { // For PHP 7
            DB::rollBack();
            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Error1: '.$e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Có lỗi xảy ra: '.$e->getMessage());

            return rtJsonApiError('Error2: '.$e->getMessage());
        }
    }

    public function copyFromTable(Request $request)
    {
        //Chỉ sadmin mới có quyền xly metadata, nếu ko sẽ dễ bị sai sót vì user khác ko hiểu
        if (getUserEmailCurrent_() != env('AUTO_SET_EMAIL_DB_MATRIX_ACCESS')) {
            return rtJsonApiError('Can not access meta!');
        }
        $tbl = $request->fromTbl;

        $mm = ModelMetaInfo::where('table_name_model', $tbl)->get()->toArray();

        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($mm);
        //        echo "</pre>";
        $ret = [];
        foreach ($mm as $o1) {
            //            $ret[$o1['field']] = [];
            foreach ($o1 as $k => $v) {
                if ($k == 'id') {
                    continue;
                }
                //$ret[$o1['field']."[$k]"] = $v;
                $key = $o1['field']."[$k]";
                $ret[$key] = $v;
                //echo "<br/>\n " . $o1['field']."[$k]" . " = " . $v ;
            }
        }
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        print_r($ret);
        //        echo "</pre>";

        return rtJsonApiDone($ret);
        //        die("xxxxxx");

    }

    public function saveMetaData2()
    {
        //Adm IP mới có thể truy cập
        $ctx = stream_context_create(['http' => ['timeout' => 1]]);
        if (isDebugIp()) {

        } elseif //Chỉ sadmin mới có quyền xly metadata, nếu ko sẽ dễ bị sai sót vì user khác ko hiểu
        (getUserEmailCurrent_() != env('AUTO_SET_EMAIL_DB_MATRIX_ACCESS')) {
            return rtJsonApiError('Can not access meta!');
        }

        $all = \request()->all();
        if (! $all) {
            return rtJsonApiError('Not valid param!');
        }
        $tableName = \request('table_name');
        MetaOfTableInDb::deleteClearCacheMetaApi($tableName);
        if (\request('one-item')) {
            //            $tableNameMetaInfo = $all['table_name'];
            $objMeta = new MetaOfTableInDb();
            $fid = intval($all['id']);
            $valUpdate = $all['value'];
            $fieldMeta = $all['fieldMeta'];

            ////            $objMeta->setTableName($tableNameMetaInfo);
            //
            //            $objMeta->clearField();
            //            $objMeta->id = $fid;
            //            $objMeta->update([$fieldMeta=>$valUpdate]);

            $objMeta = ModelMetaInfo::find($fid);
            $objMeta->$fieldMeta = $valUpdate;
            $objMeta->update();

        } else {
            foreach ($all as $field => $mmMetaData) {

                if (! is_array($mmMetaData) || ! isset($mmMetaData['id'])) {
                    continue;
                }

                $objMeta = new MetaOfTableInDb();
                $objMeta->loadFromObjOrArray($mmMetaData);
                //$objDb = MetaOfTableInDb::getOneId($mmMetaData['id']);
                $objDb = ModelMetaInfo::find($mmMetaData['id']);
                if (! $objDb) {
                    unset($objMeta->id);
                    //$objMeta->insert();
                    unset($mmMetaData['id']);
                    ModelMetaInfo::insert($mmMetaData);
                } else {

                    foreach ($mmMetaData as $field => $val) {
                        $objDb->$field = $val;
                    }
                    $objDb->update();
                    //                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                    //                    print_r($mmMetaData);
                    //                    echo "</pre>";
                    //                    die(" ID = " . $mmMetaData['id']);

                    //                    $objUpdate = new MetaOfTableInDb();
                    //                    $objUpdate->loadFromObjOrArray($mmMetaData);
                    //                    $objUpdate->id = $mmMetaData['id'];
                    //                    $objUpdate->update($mmMetaData);
                }
            }
        }

        //        $cacheName = "meta_data_table_cache_".$tableName;
        //        Cache::forget($cacheName);

        echo " saveMetaData2 DONE!!!\n";

    }

}
