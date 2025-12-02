<!DOCTYPE html>
<html lang="en">
<head>
    <title> Export API Auto </title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assert/css/common.css">
</head>
<body ng-app="myApp" ng-controller="MyController">

<a href="/admin">Admin</a>
<h1>Auto Generate Api Document</h1>
<hr>
<?php

if(isWindow1())
    $fileGen = base_path(). '/public/api-guide/dump_api_laravel_2022.php';
else
    $fileGen = "/var/www/html/public/api-guide/dump_api_laravel_2022.php";

$folder = dirname($fileGen);
if(!is_writable($folder))
    die("Can not write: $folder,<br>chown www-data:www-data $folder");

if(!file_exists(dirname($fileGen)))
    mkdir(dirname($fileGen), 0755,1);

if(!file_exists(dirname($fileGen)))
    die("Not found $fileGen folder ");


echo "- File này được tự sinh ra khi web này mở: $fileGen | " . gethostname();
$basePath = base_path();
echo "<br/>- Sau đó chạy lệnh để sinh API: <br>apidoc -o ".dirname($fileGen)." -i $basePath/public/api-guide/ -f php";
?>
<br>
<a target="_blank" href="/api-guide/index.html">- Và xem Kết quả tại đây </a>
<br>

<?php

use App\Components\Route2;

$gid = request('id');
$data = \App\Models\Role::find($gid);
if ($data instanceof \App\Models\Role) ;

$allPermisionAndRole = $data->permissions;

if ($allPermisionAndRole instanceof \Illuminate\Support\Collection) ;

$allPerOfRole = $allPermisionAndRole->sortBy('url');


function getParamExFromFunctionDoc($classCtr, $func, $getExt = 0){

    $paramEx = [];
    $ref = new ReflectionMethod($classCtr, $func);
    $docFunc = $ref->getDocComment();
//    $docFunc = str_replace("  ", ' ', $docFunc);
//    $docFunc = str_replace("  ", ' ', $docFunc);$docFunc = str_replace("  ", ' ', $docFunc);
    //trong doc, có API: mới xly tiếp
    if(strstr($docFunc, 'API:')){
        $docFunc = explode("API:", $docFunc)[1];

        $docFunc = str_replace("*/", "*", $docFunc);
        return $docFunc;



        $docFunc = str_replace(["*/", "*"], '', $docFunc);

        if($getExt == 'get_sample'){
            $ret =  \LadLib\Common\cstring2::getStringBetween2String($docFunc, '@apiExample', '');
            if(is_array($ret))
                $ret = implode("\n", $ret);
            return $ret;
        }

        echo "<pre>";
        print_r($docFunc);
        echo "</pre>";

        $mLine = explode("\n", $docFunc);
        foreach ($mLine AS $line){
            $line = trim($line);
            if(str_starts_with($line, "@param")){

                $line = str_replace("@param", '', $line);
                echo "<br/>\n --- $line";
                $m1 = explode(":", $line);
//                                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                                print_r($m1);
//                                echo "</pre>";
                $m2 = explode(' ' , trim($m1[0]));
//                                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                                print_r($m2);
//                                echo "</pre>";

                $paramEx[] = [ trim($m2[1]), trim($m2[0]), trim($m1[1])];
//                                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                                print_r($paramEx);
//                                echo "</pre>";

            }
        }


    }
    return $paramEx;
}
function showOneRow($field, $des, $fullDes, $docApi = null)
{
    $exportString = '';

    echo "\n <tr> ";
    echo "\n <td style='width: 10%'> ";
    echo "$field";
    echo "\n </td> ";
    echo "\n <td style='width: 30%'> ";
    echo "$des";
    if (strtolower($fullDes) !== $field)
        echo "<br/>\n $fullDes";
    echo "\n </td> ";
    echo "\n <td> ";


    echo "<pre>";
    print_r($docApi);
    echo "</pre>";

    echo "\n </td> ";
    echo "\n </tr> ";
}

use Illuminate\Support\Facades\Route;
$routeCollection = Route2::getRoutes();

$exportString = '';

//Liệt kê mọi role trong db có url là api/
foreach ($allPerOfRole AS $role1) {
    if (!str_starts_with($role1->url, 'api/'))
        continue;

    echo "\n <br> <b> $role1->url </b>";
    //List mọi route, tìm route tương ứng với url trong db
    foreach ($routeCollection AS $route) {

        if($route instanceof Route2);

        if(isset($route->showApi_))
        if(!$route->showApi_)
            continue;

        //Nếu url db bằng route url, thì sẽ xly tiếp
        if ($role1->url == $route->uri()) {
//            dump($route);

            echo "<br/>\n - Found Route ...";


            //Phân tích Route của Code:
            $ctrl = $route?->getController()::class;
            $act = $route->getActionName();
            $method = implode(',', $route->methods());

            //Lấy ra methode, bỏ head
            $method = str_replace(",HEAD", '', $method);
            $method = str_replace("HEAD,", '', $method);
            $method = strtolower($method);

            if($method == 'get,post' || $method == 'post,get')
                $method = 'post';

            ///Tham số mở rộng lấy từ Doc của Hàm php
            $paramEx = [];

            //Nếu có ModelUsign trong route, thì mới tiếp tục phân tích Meta của model..
            //Để xem các quyền, các field...
            if (!isset($route->modelUsing_)){
                echo "<br/>\n - Not found Model Using, Ignore";
            } else
            {
                $model = new $route->modelUsing_;
                echo "<br/>\n modelUsing_ = ". get_class($model);
                if ($model instanceof \App\Models\ModelGlxBase) ;
                $metaObj = $model::getMetaObj();
                $allMeta = $model::getApiMetaArray();
//                echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                print_r($allMeta);
//                echo "</pre>";

                //Đưa vào mấy cái mặc định
                $tmp1 = "/**
* @api { $method } $role1->url        $route->route_desc_
* @apiVersion 1.0.1
* @apiUse token1
* @apiGroup $route->route_group_desc_
* @apiName $route->route_desc_
* @apiDescription DESC: $route->route_desc_
*
";

                $tmp1 = str_replace("{ ", "{", $tmp1);
                $tmp1 = str_replace(" }", "}", $tmp1);

                $exportString .= "
$tmp1

";
                if(isset($route->docs_)){
                    echo "<br/>\n $route->docs_";
                    $exportString .="\n$route->docs_\n";
                }

                if (!$metaObj) {
                    echo "<br/>\n Not found MetaObj";
                }else{
                    echo "<b> <hr> $role1->url </b>";
                    echo "<br/>\n- Task: $route->route_desc_ ";

                    $func = explode("@", $act)[1];
                    $classCtr = explode("@", $act)[0];
                    echo "<br>- Method: $method";
                    echo "\n  <br> ACT: $act , FUNC = $func";
//                    echo "<br/>\n -*** Meta Obj: " . $metaObj::class;

                    //Tỉm các tham số ở Function của Controller nếu có  khai báo
                    //$get_sample = getParamExFromFunctionDoc($classCtr, $func);

                    //Tìm apiExample ở Function của Controller  nếu có khai báo
                    $docEx1 = getParamExFromFunctionDoc($classCtr, $func, 'get_sample');
                    if($docEx1)
                        $exportString .= "\n$docEx1";

//                    echo "<br/>\n Example: $get_sample ";
                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
                    print_r($docEx1);
                    echo "</pre>";

                    if ($metaObj instanceof \LadLib\Common\Database\MetaOfTableInDb) ;

                    //Xem các quyền đang là gì, tương ứng với function ở route
                    $mEditField = $metaObj->getShowEditAllowFieldList($gid);
                    $mEditGetOne = $metaObj->getShowGetOneAllowFieldList($gid);
                    $mEditGetIndex = $metaObj->getShowIndexAllowFieldList($gid);
//                    echo "<pre>";
//                    print_r($mEdit);
//                    echo "</pre>";


                    $tmp = null;
                    if ($func == 'add' || $func == 'update')
                        $tmp = $mEditField;
                    if ($func == 'get') {
                        $tmp = $mEditGetOne;

//                        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                        print_r($tmp);
//                        echo "</pre>";
                    }
                    if ($func == 'list') {
                        $tmp = $mEditGetIndex;

//                        return;

                    }

                    echo "\n<table class='glx03' style=''>";

                    echo "\n <tr> ";
                    echo "\n <th> Field </th>";
                    echo "\n <th> Des </th>";
                    echo "\n <th> Detail</th>";
                    echo "\n </tr> ";

                    if ($tmp) {

                        foreach ($tmp AS $field) {

                            $docApi = null;
                            $des = $metaObj->getDescOfField($field);

                            $fullDes = $metaObj->getFullDescField($field);


                            //add, update thì truyền param :
                            if ($func == 'add' || $func == 'update' || $func == 'upload')
                                $exportString .= "\n* @apiParam {String} $field $des $fullDes";
                            else {
                                //Còn lại là nhận param
                                $exportString .= "\n* @apiSuccess {String} $field $des $fullDes";
                            }

                            showOneRow($field, $des, $fullDes, null);

                            //Nếu có method thì thêm mô tả về field ở DOC của mô tả đó
                            $fieldEx = "_$field";


                            //Nếu có method với tên là fieldEx, thì thêm mô tả về field ở DOC của mô tả đó
                            if (method_exists($metaObj, $fieldEx)) {

                                //Lấy ra docs hàm fieldEx
                                $ref = new ReflectionMethod($metaObj, $fieldEx);
                                $doc = $ref->getDocComment();
                                if ($doc) {
                                    $tmp = explode("API:", $doc);
                                    if (isset($tmp[1])) {
                                        $docApi = $tmp[1];
                                        $docApi = str_replace("*/", '', $docApi);
//                                        $docApi = str_replace("/*", '', $docApi);
//                                        $docApi = str_replace("*", '', $docApi);
                                    }
                                }
                                $docApi = strip_tags($docApi);
//                                $docApi = "$docApi";

                                //Param trả về
                                if (!($func == 'add' || $func == 'update' || $func == 'upload')) {
                                    $fieldEx = "_$field";
                                    $exportString .= "\n* @apiSuccess {String} $fieldEx $des $fullDes \n\n $docApi";
                                }

                                showOneRow($fieldEx, $des, $fullDes, $docApi);

                            }
                        }
                    }

                    //Thêm các field ex nếu có:
                    if(0)
                    if($paramEx){
                        foreach ($paramEx AS $one){
//                            echo "<pre>";
//                            print_r($one);
//                            echo "</pre>";
                            showOneRow($one[0], $one[1], '', $one[2]);
                            $field = $one[0];
                            $des = $one[1];
                            $fullDes = $one[2];
                            $exportString .= "\n* @apiParam {String} $field $des $fullDes";
                        }
                    }
                    echo "\n</table>";
                }

                $exportString .= "\n\n* @apiSuccess (- Nếu Thành công) {json}  ReturnJson {code: >0 , payload: Data or Message }
* @apiSuccessExample {json} Ví dụ thành công:
* {
* 'code': > 0,
* 'payload': <Kết quả trả về, có thể là chuỗi, số, json>
* }
* @apiUse Error0
*/\n";
            }
        }
    }
}

echo "<br/><hr>";
//echo "$exportString"



$exportString = "<?php

/**
 * @apiDefine token1
 * @apiHeader {String='Bearer <token>'} [Authorization='Bearer 123456'] Authorization Replace <code>token</code> with supplied Auth Token
 * Token sẽ hết hạn sau ... ngày
 */

/**
 * @apiDefine SuccessAndError0
 * @apiSuccess (- Nếu Thành công) {json}  ReturnJson {code: >0 , payload: Data or Message}
 * @apiSuccessExample {json} Ví dụ thành công:
 * {
 * \"code\": >0,
 * \"payload\": \"Command success\",
 * }
 * @apiError  (- Nếu Có lỗi) {json}  ReturnJson {code: <0 , payload: ErrorMessage }
 * @apiErrorExample {json} Ví dụ khi lỗi:
 * {
 * \"code\": < 0,
 * \"payload\": \"Some error: ...\",
 * }
 */

/**
 * @apiDefine Success0
 * @apiSuccess (- Nếu Thành công) {json}  ReturnJson code: >0: ; payload: Message
 * @apiSuccessExample {json} Ví dụ thành công:
 * {
 * \"code\": >0,
 * \"payload\": \"Command success\",
 * }
 */

/**
 * @apiDefine Error0
 * @apiError  (- Nếu Có lỗi) {json}  ReturnJson code: <0: ; payload: Message
 * @apiErrorExample {json} Ví dụ khi lỗi:
 * {
 * \"code\": < 0,
 * \"payload\": \"Some error: ...\",
 * }
 */

class mustHaveThisClass1
{
    $exportString
}
?>";

if(file_put_contents($fileGen, $exportString))
    echo "<br/>\n Export done $fileGen";
else{
    echo "<br/>\n *** Export Error: $fileGen";
}

?>

</body>
</html>
