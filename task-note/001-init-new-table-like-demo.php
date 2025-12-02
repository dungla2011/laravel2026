<?php

/** 13.12.22
 * Tạo Bộ đầy đủ API, Amin... cho một model  mới
 * Clone từ DemoTbl
 */
use Illuminate\Support\Str;

//require_once __DIR__.'/../public/index.php';


require '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$dbInfo = (\App\Components\Helper1::getDBInfo());
\Illuminate\Support\Facades\DB::setDatabaseName($dbInfo['database']);
//\Illuminate\Support\Facades\DB::statement("SELECT * FROM users");

$bpath = base_path();
echo "\n bPath = $bpath";

getch(' nếu thêm /member, thì chỉ cần copy 2 file webdamin, và api , rồi đổi route group... là xong');

$newModel = 'MyTreeInfo';
$newModel = 'OrderItem';
$newModel = 'OrderInfo';
$newModel = 'BlockUi';
$newModel = 'DonViHanhChinh';
$newModel = 'OrderShip';
$newModel = 'Telesale';
$newModel = 'PartnerInfo';
$newModel = 'Spending';
$newModel = 'SiteMng';
$newModel = 'TreeMngColFix';
$newModel = 'NetworkMarketing';
$newModel = 'QuizQuestion';
$newModel = 'OcrImage';
$newModel = 'QuizChoice';
$newModel = 'QuizUserAnswer';
$newModel = 'QuizTest';
//$newModel = "QuizTestQuestion";
//$newModel = "QuizTestQuestion";
$newModel = 'QuizUserAndTest';

//quiz_questions
//quiz_tests
//quiz_test_questions
//quiz_user_answers
//hr_employe
//hr_department
//hr_job
//hr_company

//$newModel = "HrEmployee";
////$newModel = "HrDepartment";
//$newModel = "HrJob";
//$newModel = "HrCompany";
$newModel = 'HrSalary';
//$newModel = "HrTask";
//$newModel = "HrContract";
//$newModel = "HrLogTask";
//$newModel = "HrJobTitle";
//$newModel = "HrMessageTask";
//$newModel = "HrOrgTree";
//$newModel = "HrTimeSheet";
//$newModel = "LogUser";
//Timekeeping
$newModel = 'HrSalaryMonthUser';
$newModel = 'TypingTestResult';
$newModel = 'TypingLesson';
$newModel = 'HrExtraCostEmployee';
$newModel = 'ProductFolder';
$newModel = 'QuizLike';

$newModel = 'HrSampleTimeEvent';
$newModel = 'HrUserExpense';
$newModel = 'HrConfigSessionOrgIdSalary';
$newModel = 'HrSessionType';
$newModel = 'HrOrgSetting';
$newModel = 'HrLateConfig';
$newModel = 'QuizFlashCard';
$newModel = 'HrKpiCldv';
$newModel = 'HrExpenseColMng';

$newModel = 'HatecoCertificate';
$newModel = 'MonitorItem';
$newModel = 'RoleUser';
$newModel = 'QuizFolder';
$newModel = 'QuizSessionInfoTest';
$newModel = 'QuizUserClass';
$newModel = 'UserGroup';
$newModel = 'MyDocument';
$newModel = 'MyDocumentCat';

$newModel = 'MediaItem';
$newModel = 'MediaFolder';
$newModel = 'MediaLink';

$newModel = 'EventInfo';
//$newModel = "EventFolder";
//$newModel = "EventUserInfo";
//$newModel = "EventUserGroup";
//$newModel = "EventAndUser";
//$newModel = "EventSendSmsLog";
$newModel = 'EventSendInfoLog';
$newModel = 'EventSendAction';
$newModel = 'FileRefer';
$newModel = 'DownloadLog';

$newModel = 'PayMoneylog';
$newModel = 'Notification';
$newModel = 'CloudServer';
$newModel = 'TmpDownloadSession';
$newModel = 'ConferenceInfo';

$newModel = 'ConferenceCat';
$newModel = 'EventRegister';

$newModel = 'FileSharePermission';

$newModel = 'Department';
$newModel = 'AssetCategory';

$newModel = 'ProductAttribute';
$newModel = 'ProductUsage';

$newModel = 'DepartmentUser';
$newModel = 'DepartmentEvent';
$newModel = 'EventSetting';
$newModel = 'Cart';
$newModel = 'CartItem';
$newModel = 'Payment';

$newModel = 'AffiliateLog';
$newModel = 'UploaderInfo';

$newModel = 'TaskInfo';
$newModel = 'MediaItem';

$newModel = 'MediaCat';
$newModel = 'MediaAuthor';
$newModel = 'MediaLink';
$newModel = 'MediaVendor';

$newModel = 'CrmMessage';
//$newModel = 'MediaActor';
$newModel = 'TestMongo1';
$newModel = "EventUserPayment";

$newModel = "PlanCostItem";
$newModel = "PlanName";

$newModel = 'CrmMessageGroup';
$newModel = 'CrmAppInfo';

$newModel = 'PlanDefine';
$newModel = 'PlanDefineValue';

$newModel = 'MonitorConfig';
$newModel = 'MonitorSetting';
$newModel = 'EventPayment';

getch(" Model: $newModel ");

$tblNew = \LadLib\Laravel\Database\DbHelperLaravel::getTableNameFromModelName($newModel);

$newModelKeba = Str::kebab($newModel);
$lk = '/admin/'.$newModelKeba;
if (! \App\Models\MenuTree::where(['link' => $lk, 'parent_id' => 4])->first()) {
    echo "\n Create Menu: $lk";
    $menu = new \App\Models\MenuTree();
    $menu->link = $lk;
    $menu->name = $newModel;
    $menu->gid_allow = '1,2';
    $menu->parent_id = 4;
    $menu->save();
}

$mm = [
    '/app/Http/ControllerApi/DemoControllerApi.php' => "/app/Http/ControllerApi/$newModel".'ControllerApi.php',

    '/app/Http/Controllers/DemoUseApiController.php' => "/app/Http/Controllers/$newModel".'Controller.php',

    '/app/Models/DemoTbl_basic.txt' => "/app/Models/$newModel.php",
    '/app/Models/DemoTbl_Meta_basic.html' => "/app/Models/$newModel".'_Meta.php',

    '/app/Repositories/DemoRepositoryInterface.php' => '/app/Repositories/'.$newModel.'RepositoryInterface.php',

    '/app/Repositories/DemoRepositorySql.php' => '/app/Repositories/'.$newModel.'RepositorySql.php',

    '/routes/api_demo.php' => '/routes/api_'.strtolower($newModel).'.php',

    '/routes/web_admin_demo.php' => '/routes/web_admin_'.strtolower($newModel).'.php',
];

$cc = 0;

$fileService = "$bpath/app/Providers/AppServiceProvider.php";
$c1 = file_get_contents($fileService);

$tmp = '$this->app->bind(DemoRepositoryInterface::class, DemoRepositorySql::class);';
$tmp1 = $tmp."\n".'$this->app->bind('.$newModel.'RepositoryInterface::class, '.$newModel.'RepositorySql::class);'."\n";
if (! strstr($c1, $tmp1)) {
    $c1 = str_replace($tmp, $tmp1, $c1);
}

$tmp = 'use App\Repositories\DemoRepositorySql;';
$tmp1 = $tmp."\n".'use App\Repositories\\'.$newModel.'RepositorySql;'."\n";
if (! strstr($c1, $tmp1)) {
    $c1 = str_replace($tmp, $tmp1, $c1);
}

$tmp = 'use App\Repositories\DemoRepositoryInterface;';
$tmp1 = $tmp."\n".'use App\Repositories\\'.$newModel.'RepositoryInterface;'."\n";
if (! strstr($c1, $tmp1)) {
    $c1 = str_replace($tmp, $tmp1, $c1);
}

file_put_contents($fileService, $c1);

getch("Done app $fileService");

///////
$mrep = ['DemoControllerApi' => $newModel.'ControllerApi',
    'DemoUseApiController' => $newModel.'Controller',
    'DemoRepositoryInterface' => $newModel.'RepositoryInterface',
    'DemoRepositorySql' => $newModel.'RepositorySql',
    'DemoUseApi' => $newModel.'',
    'DemoTbl' => $newModel,
    'demo-api' => $newModelKeba,
    'Demo' => $newModel,
    'demo' => $newModelKeba,
];

getch("Create table now: $tblNew");

try {
    $sql = "CREATE TABLE IF NOT EXISTS $tblNew (
      `id` int(11) NOT NULL,
      `name` varchar(256) DEFAULT NULL,
      `user_id` int(11) DEFAULT NULL,
      `status` int(11) DEFAULT NULL,
      `created_at` timestamp NULL DEFAULT current_timestamp(),
      `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
      `deleted_at` timestamp NULL DEFAULT NULL,
      `image_list` varchar(256) DEFAULT NULL,
      `log` text DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    \Illuminate\Support\Facades\DB::statement($sql);
} catch (Throwable $e) { // For PHP 7
    echo "<br/>\n Error1: ".$e->getMessage();
}

try {
    $sql = "ALTER TABLE `$tblNew`
      ADD PRIMARY KEY (`id`),
      ADD KEY `user_id` (`user_id`),
      ADD KEY `status` (`status`)";
    \Illuminate\Support\Facades\DB::statement($sql);
} catch (Throwable $e) { // For PHP 7
    echo "<br/>\n Error1: ".$e->getMessage();
}

try {
    $sql = "ALTER TABLE `$tblNew` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1;";
    \Illuminate\Support\Facades\DB::statement($sql);
} catch (Throwable $e) { // For PHP 7
    echo "<br/>\n Error1: ".$e->getMessage();
}

foreach ($mm as $filepath => $newFile) {
    $cc++;
    $filepath = $bpath."$filepath";
    $newFile = $bpath.$newFile.'';
    echo "\n $cc . --- $filepath";

    if (file_exists($newFile)) {
        echo "\n\n *** file exist please check and delete it to continue...:  $newFile";

        continue;
    }

    if (file_exists($filepath)) {
        echo "\n Copy file $filepath -> $newFile";
        $cont = file_get_contents($filepath);
        foreach ($mrep as $str0 => $str1) {
            $cont = str_replace($str0, $str1, $cont);
        }

        file_put_contents($newFile, $cont);

        getch('...');
    } else {
        echo "\n *** Not found $filepath";
    }
}
