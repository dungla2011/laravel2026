<?php
header('Access-Control-Allow-Origin: *'); // Hoặc domain cụ thể
header('Access-Control-Allow-Headers: X-Locale, X-Locale-Mobile-Glx, X-Api-Key, Content-Type, Accept, Authorization, User-Agent, Sec-Ch-Ua, Sec-Ch-Ua-Mobile, Sec-Ch-Ua-Platform, Referer');

header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Credentials: true');

$GLOBALS['DISABLE_DEBUG_BAR'] = 1;

use \Illuminate\Support\Facades\Route;

error_reporting(E_ALL);
ini_set('display_errors', 1);

//require_once "/var/www/html/public/index.php";

require '/var/www/html/vendor/autoload.php';
$app = require_once '/var/www/html/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);


try {

    $user = getCurrentUserId(1);
    $uid = $user->id ?? 0;

    if ($uid)
        if ($_COOKIE['firebase_token_cookie'] ?? "") {
            $firebase_token = $_COOKIE['firebase_token_cookie'];

//    die("FBT = $firebase_token");

            if ($setting = \App\Models\MonitorSetting::where("user_id", $uid)->first()) {
                if ($setting->firebase_token != $firebase_token) {
                    //Update
                    $setting->firebase_token = $firebase_token;
                    $setting->addLog("update fb token");
                    $setting->update();
                }
            }

        }

    if(request('update_firebase_token')){
        echo "Updated firebase token for user id = $uid, only return this is ok for app";
//        return rtJsonApiDone("update ok");
        exit;
    }

    $tableName = request('table');
    if (!$tableName) {
        echo "Please input table name";
        exit;
    }

    $field_details = request('field_details', 0);

    $urlCurrent = \LadLib\Common\UrlHelper1::getFullUrl();

    $metaModelOfTable = \LadLib\Common\Database\MetaTableCommon::getMetaObjFromTableName($tableName);

    $mEdit = $metaModelOfTable->getShowEditAllowFieldList(3);
    $mEditAdmin = $metaModelOfTable->getShowEditAllowFieldList(1);

    $mFieldShowInIndexMember = $metaModelOfTable->getShowIndexAllowFieldList(3);
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mFieldShowInIndexMember);
//echo "</pre>";
    $mFieldShowInGetOneMember = $metaModelOfTable->getShowGetOneAllowFieldList(3);
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mFieldShowInGetOneMember);
//echo "</pre>";
//die();

    $allFieldsShow = array_unique(array_merge($mFieldShowInIndexMember, $mFieldShowInGetOneMember));

// Nếu muốn reset lại index từ 0
    $allFieldsShow = array_values($allFieldsShow);
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($result);
//echo "</pre>";
//
//die();

    $model = \Illuminate\Support\Str::studly(\Illuminate\Support\Str::singular($tableName));


    $model = "App\\Models\\$model";
//echo link Return

    $fieldFullInfoDb = $model::getArrayFieldFullInfo();
//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($fieldFullInfoDb);
//echo "</pre>";
//die();
    $mFieldFullDetail = [];


//foreach ($fieldFullInfoDb AS $field=>$info){
    foreach ($allFieldsShow as $key => $field) {
//    echo "<br/>\n --- Field $field --- " . get_class($metaModelOfTable);

        $info = $fieldFullInfoDb[$field] ?? null;

        if (!in_array($field, $mEdit))
            if (!in_array($field, $mFieldShowInIndexMember))
                if (!in_array($field, $mFieldShowInGetOneMember))
                    continue;


        $fieldDetail = [
            'field_name' => $field,
            'description' => $metaModelOfTable->getDescOfField($field, 0, $user->language ?? ''),
            'data_type' => $metaModelOfTable->getDbDataType($field),
        ];



        if (!in_array($field, $mEdit)) {
//        continue;
            $fieldDetail['editable'] = 'no';
        } else {
            $fieldDetail['editable'] = 'yes';
        }

        if (!in_array($field, $mFieldShowInGetOneMember)) {
            $fieldDetail['show_in_api_edit_one'] = 'no';
        } else {
            $fieldDetail['show_in_api_edit_one'] = 'yes';
        }
        if (!in_array($field, $mFieldShowInIndexMember)) {
            $fieldDetail['show_in_api_list'] = 'no';
        } else {
            $fieldDetail['show_in_api_list'] = 'yes';
        }

        $fieldDetail['show_dependency'] = null;
        if ($showHide = $metaModelOfTable->getFieldShowDependency($field)) {
            $fieldDetail['show_dependency'] = $showHide;
        }

        $fieldDetail['show_mobile_field'] = 0;
        if ($metaModelOfTable->isShowMobileFields($field)) {
            $fieldDetail['show_mobile_field'] = 'yes';
        }

        $opt = $metaModelOfTable->getHardCodeMetaObj($field);
        $fieldDBType = $metaModelOfTable->getDbDataType($field);
//    echo "<br/>\n Field Type = <b>$fieldDBType </b>";

        if ($opt->dataType == DEF_DATA_TYPE_HTML_SELECT_OPTION) {
            $fieldEx = "$field";
            if ($field[0] != '_')
                $fieldEx = "_$field";
            if (method_exists($metaModelOfTable, $fieldEx)) {
                $arrOpt = $metaModelOfTable->$fieldEx(0, 0, 0);
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($arrOpt);
//            echo "</pre>";
                $fieldDetail['select_option_value'] = $arrOpt;
            }
        }
        if ($opt->dataType == DEF_DATA_TYPE_HTML_SELECT_OPTION_MULTI_VALUE) {
            $fieldEx = "$field";
            if ($field[0] != '_')
                $fieldEx = "_$field";
            if (method_exists($metaModelOfTable, $fieldEx)) {
                $arrOpt = $metaModelOfTable->$fieldEx(0, 0, 0);
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($arrOpt);
//            echo "</pre>";
                $fieldDetail['select_option_multi_value'] = $arrOpt;
            }
        }

        if ($opt->dataType == DEF_DATA_TYPE_STATUS) {
            $fieldDetail['data_type'] = 'boolean_status';
        }

        if ($opt->dataType == DEF_DATA_TYPE_IS_ERROR_STATUS) {
            $fieldDetail['data_type'] = 'error_status';
        }

        $fieldDetail['default_value'] = null;
        if($metaModelOfTable->getDefaultValue($field) !== null){
            $fieldDetail['default_value'] = $metaModelOfTable->getDefaultValue($field);
        }

        $fieldDetail['required'] = 'no';
        if (array_key_exists($field, $fieldFullInfoDb)) {
//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($fieldFullInfoDb[$field]);
//        echo "</pre>";
            // nêu $fieldFullInfoDb[$field]['Null'] == 'YES' thì required = no
            if ($info['Null'] ?? '')
                if ($info['Null'] !== 'YES') {
//            echo "<br/>\n YES $field . " . $fieldFullInfoDb[$field]['Null'];
                    $fieldDetail['required'] = 'yes';
                }
        }

        if ($ret = $metaModelOfTable->getMobileAction($field)) {
            $fieldDetail['mobile_action'] = $ret;
        }

        $fieldDetail['extra_mobile_info'] = null;
        if($extra = $metaModelOfTable->getExtraFieldTypeMobile($field)){
            $fieldDetail['extra_mobile_info'] = $extra;
        }

        $mFieldFullDetail[] = $fieldDetail;


    }

//echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//print_r($mFieldFullDetail);
//echo "</pre>";

//echo "<br/>\n --- <br>\n";
    if (request('field_details')) {
//    ob_clean();
        echo json_encode($mFieldFullDetail, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        die();
    }

    if (request('api_list')) {
//    ob_clean();
        $mx = [];
        foreach ($mFieldShowInIndexMember as $field)
            $mx[$field] = '...';
        $mx['id'] = 1;

        $mx2 = (array)json_decode(json_encode($mx));
        $mx2['id'] = 2;

        $mx3 = (array)json_decode(json_encode($mx));
        $mx3['id'] = 3;
        $mret = ['code' => 1,
            'guide' => "code = 1 => success; code !=1 =>  error",
            'message' => " some string ",
            'payload' => [
                'data' => [$mx, $mx2, $mx3],
                "current_page" => 1,
                "total" => 100,
                "per_page" => 10
            ]
        ];

//    array_map(function($item){ return $item; }, $mFieldShowInIndexMember)
//    ];

        echo json_encode($mret, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        die();
    }

    if (request('api_get_one')) {

//    ob_clean();
        $mx = [];
        foreach ($mFieldShowInIndexMember as $field) {
            $mx[$field] = '...';
        }
        $mx['id'] = 1;

        $mret = ['code' => 1,
            'guide' => "code = 1 => success; code !=1 =>  error",
            'message' => " some string ",
            'payload' =>
                $mx
        ];
//    array_map(function($item){ return $item; }, $mFieldShowInIndexMember)
//    ];
        echo json_encode($mret, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT);
        die();
    }


    if (!isSupperAdmin_()) {
        echo "Not allow access1!";
        exit;
    }
} catch (Exception $ex) {
    echo "Error: " . $ex->getMessage();
    exit;
}

echo "<a href='/admin/db-permission?table=$tableName' target='_blank'> RETURN ADM </a>";

echo "<br/>\n - Mô tả các trường trong DB, và các API liên quan";

echo "<br/>\n Model: $model";

echo "\n<br> - API Editable Field Member: ";
echo implode(", ", $mEdit);

$linkJsonField = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('field_details', 1);
echo "<br/>\n Link Json : <a target='_blank' href='$linkJsonField'> LINK </a>";
$linkJsonApiList = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('api_list', 1);
echo "<br/>\n Link Json API List : <a target='_blank' href='$linkJsonApiList'> LINK </a>";
$linkJsonApiGetOne = \LadLib\Common\UrlHelper1::setUrlParamThisUrl('api_get_one', 1);
echo "<br/>\n Link Json API Get One : <a  target='_blank' href='$linkJsonApiGetOne'> LINK </a>";

echo "\n<br> - API Editable Field Admin: ";
echo implode(", ", $mEditAdmin);
//Get List all field of model laravel, from tablename

if ($model instanceof \App\Models\ModelGlxBase) ;

$fieldNames = $model::getArrayField();

$strField = implode(",", $fieldNames);

echo "<br/>\n- Field: $strField";

//echo "<br/>\n";
//echo "<pre>- All Field List: <br>";
//print_r($fieldFullInfoDb);
//echo "</pre>";


$clsMeta = $urlApi = null;
if ($metaModelOfTable) {
    $urlApi = $metaModelOfTable?->getApiUrl('admin', 0);
    $clsMeta = get_class($metaModelOfTable);

    if ($clsMeta instanceof \LadLib\Common\Database\MetaOfTableInDb) ;

}

$routeCollection = Route::getRoutes();

echo "<br/>\n - All API ADMIn";
foreach ($routeCollection as $value) {
    if ($value instanceof \Illuminate\Routing\Route) ;
    $uri0 = $value->uri();
    $urlApi0 = trim($urlApi, '/');
    if (strstr($uri0, $urlApi0)) {

        if (str_contains($uri0, 'delete')) {
            $uri0 .= "?id=1,2,3...";
        }
        echo "<br/>\n $uri0";
    }
}

$apiForTable = $metaModelOfTable::$api_url_member;

?>
<br>

<pre>


-----------------------------------------
Toàn bộ về API CRUD: <?php echo $tableName ?>:


- JSON thông tin các trường, fetch realtime tại đây: mỗi khi app khởi động sẽ fetch link này để áp dụng vào các Form CRUD, link này là đặc điểm chi tiết các trường,ở trang List hoặc trang Edit, có nhứng trươờng được phép edit, hoặc chi Xem:
https://mon.lad.vn/tool/common/get-api-info.php?table=<?php echo $tableName ?>&field_details=1
- Link này là mẫu trả lại của api/monitor-item/list :
https://mon.lad.vn/tool/common/get-api-info.php?table=<?php echo $tableName ?>&api_list=1
- Link này là mẫu json trả lại của api/monitor-item/get/{id} :
https://mon.lad.vn/tool/common/get-api-info.php?table=<?php echo $tableName ?>&api_get_one=1

- Danh sách các API chính thức
<?php echo $apiForTable ?>/list
<?php echo $apiForTable ?>/update/{id}
<?php echo $apiForTable ?>/get/{id}
<?php echo $apiForTable ?>/add
<?php echo $apiForTable ?>/update/{id}
<?php echo $apiForTable ?>/delete?id=1,2,3,4,5... (delete dùng method GET)

Các api chính thức này sẽ return
{
    'code': 1, // 1 = thành công, <>1 : không thành công
    'message'=> '...',
    'payload' => '...'
}

Bạn xem các thông tin trên, fetch 3 link đầu tiên về xem sao


</pre>
