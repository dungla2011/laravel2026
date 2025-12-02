<?php

namespace App\Http\ControllerApi;

use App\Components\ClassMail1;
use App\Components\ClassMailV2;
use App\Components\clsParamRequestEx;
use App\Components\Helper1;
use App\Models\Data;
use App\Models\EventAndUser;
use App\Models\EventInfo;
use App\Models\EventInfo_Meta;
use App\Models\EventRegister;
use App\Models\EventSendAction;
use App\Models\EventSendInfoLog;
use App\Models\EventUserInfo;
use App\Models\EventUserInfo_Meta;
use App\Models\FileCloud;
use App\Models\FileUpload;
use App\Models\ModelGlxBase;
use App\Models\SiteMng;
use App\Models\User;
use App\Repositories\EventInfoRepositoryInterface;
use Illuminate\Support\Facades\DB;
use LadLib\Common\cstring2;
use LadLib\Common\UrlHelper1;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Pusher\Pusher;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

use Workerman\Connection\AsyncTcpConnection;
use function GuzzleHttp\Promise\all;
use Workerman\Worker;
use Workerman\Connection\TcpConnection;


define("DEF_EVENT_SMS_EV_ID_STR", "[DAV-");

function ol1($eventSendAction = null, $msg, $hideEcho = 0)
{
    ol0($msg, $hideEcho);

    if ($eventSendAction && $eventSendAction instanceof EventSendAction)
        $eventSendAction->addLog($msg, 1);
}


function readExcelFile($filePath)
{
    // Load the Excel file

    $spreadsheet = IOFactory::load($filePath);
    $sheet = $spreadsheet->getActiveSheet();

    // Iterate through each row
    $rows = [];
    foreach ($sheet->getRowIterator() as $row) {
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false); // Loop through all cells, even if a cell value is not set
        $rowData = [];
        foreach ($cellIterator as $cell) {
            $rowData[] = $cell->getValue();
        }
        $rows[] = $rowData;
    }

    return $rows;
}

class EventInfoControllerApi extends BaseApiController
{
    public function __construct(EventInfoRepositoryInterface $data, clsParamRequestEx $objPrEx)
    {
        $this->data = $data;
        $objPrEx->need_set_uid = 0;
        $this->objParamEx = $objPrEx;

        parent::__construct();
    }


    public function get($id): \Illuminate\Http\JsonResponse
    {
        try {

            $lang1 = request("lang1");

            $this->objParamEx->return_laravel_type = 1;
            $obj = $this->data->get($id, $this->objParamEx);
            if($obj instanceof EventInfo);
            if(request('cmd_ev') == 'get_content_preview'){
//                die("get_content_preview");
                //Tim tất cả các attribute của obj, bắt đầu bằng content, thay thế nó rỗng
                $mm = $obj->getAttributes();
                foreach ($mm as $key => $value) {
                    if (str_starts_with($key, 'content') || str_starts_with($key, 'sms_') ) {
                        $ct = $obj->$key;

                        $ct = str_replace(EventInfo::$DEF_EVENT_NAME[0], $obj->getName($lang1), $ct);
                        $ct = str_replace(EventInfo::$DEF_START_TIME[0], $obj->getTimeStartVn(), $ct);
                        $ct = str_replace(EventInfo::$DEF_END_TIME[0], $obj->getTimeEndVn(), $ct);
                        $ct = str_replace(EventInfo::$DEF_ADDRESS_LOCATION[0], $obj->getLocation(), $ct);

                        if (str_starts_with($key, 'content')) {
//                $ct = str_replace(EventInfo::$DEF_TENKHACH[0], $nameFull, $ct);
                            $obj->$key = removeCommentsWithDOM2($ct);
                        }
                        if (str_starts_with($key, 'sms_')) {
                            $obj->$key = removeSMSTextComments($ct);
                        }
                    }
                }
            }

            return rtJsonApiDone($obj, null, 1);

        } catch (\Throwable $exception) { // For PHP 7
            return rtJsonApiError("get error: " . $exception->getMessage());
        } catch (\Exception $exception) {
            return rtJsonApiError("get error: " . $exception->getMessage());
        }
    }

    static function sendMessageWebsocketCli($str)
    {

        $domain = UrlHelper1::getDomainHostName();

        $tk = User::getTokenByUserId(getCurrentUserId());

// Địa chỉ WebSocket Server
        $websocketServerUrl = "wss://$domain:51111tkx=$tk";
//        ws = new WebSocket('wss://$domain:51111?tkx=' + token);
// Tạo Worker (client)
        $worker = new Worker();

// Số lượng tiến trình
        $worker->count = 1;

// Xử lý khi worker được khởi động
        $worker->onWorkerStart = function () use ($websocketServerUrl, $str) {
            // Kết nối tới WebSocket server
            $connection = new AsyncTcpConnection($websocketServerUrl);

            // Xử lý khi kết nối thành công
            $connection->onConnect = function ($connection) use ($str) {
                echo "Connected to WebSocket server\n";
                // Gửi message tới server
                $message = json_encode(['action' => 'hello', 'data' => 'Hello WebSocket Server']);
                $message = "$str";
                $connection->send($message);
                echo "Message sent: $message\n";
            };

            // Xử lý khi nhận được message từ server
            $connection->onMessage = function ($connection, $data) {
                echo "Message received from server: $data\n";
            };

            // Xử lý khi kết nối đóng
            $connection->onClose = function ($connection) {
                echo "Connection closed\n";
            };

            // Xử lý khi có lỗi
            $connection->onError = function ($connection, $code, $msg) {
                echo "Error: $msg\n";
            };

            // Kết nối tới server
            $connection->connect();
        };

// Chạy Worker
        Worker::runAll();

    }

    static function sendMailRegEvent($newId)
    {
        if (!$eventRegister = \App\Models\EventRegister::find($newId)) {
            loi("Event register not found $newId");
        }

        $lang = $eventRegister->lang;

        $ev = \App\Models\EventInfo::find($eventRegister->event_id);
        if (!$ev) {
            loi("Event not found");
        }

        $metaObj = new \App\Models\EventInfo_Meta();

        $first_name = $eventRegister->first_name;
        $last_name = $eventRegister->last_name;
        $email = $eventRegister->email;
        $userTitle = $eventRegister->title;

        $regCode = eth1b($newId . "." . $email . "." . microtime());
        $linkRegister = "https://" . \LadLib\Common\UrlHelper1::getDomainHostName() . "/event-register/verify-email/$regCode";
        $linkRegister = "<a href='$linkRegister'>$linkRegister</a>";


        $fieldTitle1 = "reg_mail_title_$lang" . "1";
        $title = trim($ev->$fieldTitle1);
        if (!$title)
            $title = $metaObj->setDefaultValue($fieldTitle1);


        $mmReplace = [
            EventInfo::$DEF_TENKHACH[0] => "$userTitle $last_name $first_name",
            EventInfo::$DEF_USER_NAME[0]=> "$userTitle $last_name $first_name",
            EventInfo::$DEF_EVENT_NAME[0]=> $ev->name,
            EventInfo::$DEF_REG_LINK_OLD[0] => $linkRegister,
            EventInfo::$DEF_CONFIRM_EMAIL[0] => $linkRegister,
            "\n" => "<br>",
        ];

        if(isDebugIp()){
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($mmReplace);
//            echo "</pre>";
        }

        $title = cstring2::replaceByArray($title, $mmReplace);

//        $title = str_replace(\App\Models\EventInfo::$DEF_EVENT_NAME[0], $ev->name, $title);
//        $title = str_replace(\App\Models\EventInfo::$DEF_USER_NAME[0], $ev->name, $title);
//        $title = str_replace(\App\Models\EventInfo::$DEF_TENKHACH[0], $ev->name, $title);

        $fieldCont1 = "reg_mail_01_$lang";
        $content0 = $content = trim($ev->$fieldCont1);
        if (!$content)
            $content = $metaObj->setDefaultValue($fieldCont1);



//        $content = str_replace(\App\Models\EventInfo::$DEF_EVENT_NAME[0], $ev->name, $content);
//        $content = str_replace(\App\Models\EventInfo::$DEF_USER_NAME[0], "$first_name $last_name", $content);
//        $content = str_replace(\App\Models\EventInfo::$DEF_TENKHACH[0], "$first_name $last_name", $content);
//        $content = str_replace(\App\Models\EventInfo::$DEF_CONFIRM_EMAIL[0], $linkRegister, $content);
//        $content = str_replace(\App\Models\EventInfo::$DEF_REG_LINK_OLD[0], $linkRegister, $content);
//        $content = str_replace("\n", "<br>", $content);
        $content = cstring2::replaceByArray($content, $mmReplace);

        //                            $content = "
        //                        Dear $first_name $last_name,
        //                        <br>
        //                        You are registering for event: $ev->name
        //                        <br>
        //                        Please click the link below to confirm your registration:
        //                        <br>
        //                        $linkRegister
        //                        <br>
        //                        (Or copy and paste the link into your browser)
        //                        <br>
        //                        Thank and best regards!
        //                        <br>
        //                        -------------
        //                        <br>
        //                        For more information, please visit:
        //                        <br>
        //                        https://" . \LadLib\Common\UrlHelper1::getDomainHostName() . "
        //                        ";

        $eventRegister->reg_code = $regCode;
//                            $eventRegister->lang = $lang;

        $eventRegister->addLog("Register event: $eventRegister->email");
        $eventRegister->addLog('Send mail event: ' . $title);
        $eventRegister->addLog($content);

        $eventRegister->content_mail1 = "$title<br>$content";

        $eventRegister->save();

//        die(" $email, $title");

//        dumpdebug("EM=$email    TT=$title C0=$content0\ 1 = $content ");

        return sendMailNcbd($email, $title, $content);
    }

    function importExcelUserEvent()
    {
        $ggUrl = request('link_excel');
        if (!$ggUrl)
            return rtJsonApiError("Not found link_excel");
        try {
            if (!str_contains($ggUrl, "/d/") || !str_contains($ggUrl, "docs.google.com"))
                loi("Link Google Sheet không đúng ? ");

//            $link = "https://docs.google.com/spreadsheets/d/18p7P-7uVCIUWlr-8p3cF0i4rW3H39HTi/edit?gid=1593466212#gid=1593466212";
            $link = $ggUrl;
            //Lấy ra ID của sheet từ link tren
            //ID sau chu /d/ và trước /edit
            $idPart = explode("/d/", $link)[1];
            $idOK = explode("/", $idPart)[0];
            $link = "https://docs.google.com/spreadsheets/d/$idOK/export?format=xlsx";
            $cont = file_get_contents($link);
            $uid = getCurrentUserId();
            $fpath = "/share/uinfo_import_user_event.$uid.xlsx";
            file_put_contents($fpath, $cont);
            $nImport = \App\Http\ControllerApi\EventInfoControllerApi::importNewUserExcel($fpath);
            return rtJsonApiDone($nImport, "Nhập thành công: '$nImport' thành viên mới, F5 để xem danh sách!");
        } catch (\Exception $e) {
            echo($e->getMessage());
//            return rtJsonApiError($e->getMessage());
            return;
        }
    }

    static function importNewUserExcel($filePathOrFid)
    {
        $time = time();
        $cuid = getCurrentUserId();
        $filePath = $filePathOrFid;
        if (is_numeric($filePathOrFid)) {
            $fid = $filePathOrFid;
            if (!$fileCl = FileUpload::getCloudObj($fid))
                return rtJsonApiError("Not found file upload $fid");
            $filePath = $fileCl->file_path;
        }
        if (!file_exists($filePath))
            return rtJsonApiError("Not found file $filePath");
        if (filesize($filePath) > 10000000)
            return rtJsonApiError("File too large, max 10MB");

        $meta = new EventUserInfo_Meta();
        $mf = $meta->getFieldToImportExcel();

        $mmUser = [];
        $rows = readExcelFile($filePath);

        //Kiểm tra cấu trúc
        //Lấy ra dòng 2
        foreach ($rows as $index => $oneRow) {
            if ($index == 1) {
                $col = 0;
                foreach ($mf as $field => $info) {
                    if ($oneRow[$col] != $field)
                        die("Cấu trúc file không đúng, cột $col hàng $index đang là '" . $oneRow[$col] . "', đúng phải là '$field'.\nHãy tải lại file mẫu và chỉnh lại Link GoogleExcel theo mẫu");
                    $col++;
                }
                break;
            }
        }


        $strError = "";

        foreach ($rows as $index => $oneRow) {
            if ($index < 3) {
                continue;
            }
            $col = 0;
            $evu = new EventUserInfo();
            $haveEmail = 0;

            $haveFirstName = 0;
            $havePhone = '';
            foreach ($mf as $field => $info) {

                if ($field == 'first_name' && $oneRow[$col]) {
//                    return rtJsonApiError("Error: Missing first_name in row: $index");
                    $haveFirstName = $oneRow[$col];
                }

                if ($field == 'email') {
                    if($oneRow[$col])
                        $haveEmail = 1;
                    elseif(!$oneRow[$col]){
                        $oneRow[$col] = "$time.$index@KhongCoEmail.com";
                        $haveEmail = 1;
                    }
                }
                if ($field == 'phone') {
                    $phonex = $oneRow[$col] = fixPhoneNumber($oneRow[$col]);
                    if($phonex)
                        $havePhone = $phonex;

                }

                $evu->$field = $oneRow[$col];
                $col++;
            }

//            $evu->user_id = $cuid;

            if(!$haveFirstName)
                continue;

            if (!$haveEmail)
                continue;

            //Kiểm tra xem $haveFirstName và $havePhone có trùng với các user trong db không?
            //Nếu có thì bỏ qua
            if(EventUserInfo::where('first_name', $haveFirstName)
                ->where('phone', $havePhone)
                ->count() > 0){
                die("Trùng tên '$haveFirstName' và phone '$havePhone' trong db, không thể nhập thêm!");
                continue;
            }

//
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($evu->toArray());
//            echo "</pre>";

            $mmUser[] = $evu;

            if ($rl = $evu->getValidateRuleInsert()) {
                //$request->validate($this->model::::$createRules);
                $validator = \Illuminate\Support\Facades\Validator::make($evu->toArray(), $rl, Helper1::getValidateStringAlt());

                if ($validator->fails()) {
                    $errors = $validator->errors();
                    $messages = [];
                    foreach ($errors->all() as $message) {
                        $field = $errors->keys()[array_search($message, $errors->all())];
                        $invalidValue = $evu->toArray()[$field] ?? 'N/A';
                        $messages[] = $message . ' (Invalid value: ' . $invalidValue . ')';
                    }
                    $strError .= "\n- " . implode("\r\n", $messages);
//                    return rtJsonApiError(implode("\n", $messages));
                }

//                if(0)
//                if ($validator->fails()) {
//                    $mE = $validator->errors()->all();
////                    echo "<pre>";
//                    $strEvu = implode(" | " , $evu->toArray());
////                    echo "</pre>";
//                    loi($strEvu . "\r\n - " . implode("\r\n", $mE));
//                }
            }
        }

        if ($strError)
            die($strError);

        //Nếu không có email:
        //Kiểm tra trùng lặp tên, phone, nếu có rổi thì bỏ qua
        //9 số cuối của phone
        //Nếu khách không có email, phone thì sẽ check firstname lastname trùng trong db không
        //nếu có trùng Firstname + Lastname thì không nhập? Hay vẫn cho nhập? vì trùng tên là bình thường?
        //Nên tốt nhất vẫn là phải có 1 mail giả, để cảnh báo đây là 1 bất thường

        $mm2 = [];
        foreach ($mmUser as $evu) {
            $evu->addLog("Import from excel");
            $evu->save();
            $mm2[] = $evu->toArray();
        }

//        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//        print_r($mm2);
//        echo "</pre>";
//        die('Import done!');

        return count($mmUser);
    }

    public function list(): \Illuminate\Http\JsonResponse
    {
        $_only_fields_ = null;
        $params = request()->all();
        if($params['_fields_'] ?? ''){

//            $mt = EventInfo::getMetaObj();
//            $mt->getShowIndexAllowFieldList(1);
//
//
//
//            $_only_fields_ = explode(",", $params['_fields_']);
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($_only_fields_);
//            echo "</pre>";
//            echo "</pre>";
//            die();
        }

        return parent::list(); // TODO: Change the autogenerated stub
    }

    function getUserListEvent() {
        $eventId = request('eid'); // Get event ID from request
        if (!$eventId) {
            return rtJsonApiError("Event ID is required");
        }

        $results = DB::select('SELECT
            eu.id as user_id,
            eu.email,
            eu.phone,
            eu.language,
            eu.title,
            TRIM(CONCAT_WS(" ", NULLIF(eu.last_name, ""), NULLIF(eu.first_name, ""))) AS name,
            ea.note as note_eau,
            ea.confirm_join_at,
            ea.deny_join_at,
            ea.attend_at,
            eu.note as note_u,
            eu.organization,
            eug.name AS parent_name
        FROM
            event_and_users ea
        JOIN
            event_user_infos eu ON ea.user_event_id = eu.id
        LEFT JOIN
            event_user_groups eug ON eu.parent_id = eug.id
        WHERE
            ea.event_id = ?
            AND ea.deleted_at IS NULL
            AND eu.deleted_at IS NULL
        ORDER BY
            eu.first_name, eu.last_name
        ', [$eventId]);

        //Trong results, loại bỏ các user_id trùng nhau
        $uniqueUserIds = [];
        $uniqueResults = [];
        foreach ($results as $user) {
            if (!in_array($user->user_id, $uniqueUserIds)) {
                $uniqueUserIds[] = $user->user_id;
                $uniqueResults[] = $user;
            }
        }
        $results = $uniqueResults;


        return rtJsonApiDone($results);

//
//        $ret = '<table class="table table-bordered" data-code-pos="ppp174338831p">';
//        $ret .= '<thead class="thead-light"><tr>
//<th>#</th>
//<th>Select</th>
//<th>Lang</th>
//<th>Name</th>
//<th>Email</th>
//<th>Phone</th>
//<th>Organization</th>
//<th>Group</th>
//<th>Note</th>
//</tr></thead>';
//        $ret .= '<tbody>';
//
//        $counter = 1;
//        foreach ($results as $user) {
//            $dataAll = json_encode($user);
//            $ret .= '<tr data-code-pos="ppp174338833817" class="one_user" data-all="'.$dataAll.'">';
//            $ret .= '<td>' . $counter++ . '</td>';
//            $ret .= '<td style="text-align: center"><input type="checkbox" data-user-id="' . $user->user_id . '"> </td>';
//            $ret .= '<td>' . htmlspecialchars($user->language) . '</td>';
//            $ret .= '<td>' . htmlspecialchars($user->name) . '</td>';
//            $ret .= '<td>' . htmlspecialchars($user->email) . '</td>';
//            $ret .= '<td>' . htmlspecialchars($user->phone) . '</td>';
//            $ret .= '<td>' . htmlspecialchars($user->organization) . '</td>';
//            $ret .= '<td>' . htmlspecialchars($user->parent_name ?: '') . '</td>';
//            $ret .= '<td>' . htmlspecialchars($user->note_u) . '</td>';
//            $ret .= '</tr>';
//        }
//
//        $ret .= '</tbody></table>';
//        return rtJsonApiDone($ret);
    }

    function getUserListEvent1()
    {
        $eid = request('eid');

        $mm = EventAndUser::where("event_id", $eid)->get();







        $uniqueUserEventIds = [];
        $ret = '<table class="table table-bordered" data-code-pos="ppp174338831p">';
        $ret .= '<thead class="thead-light"><tr><th>#</th><th>Name</th><th>Name</th><th>Email</th><th>Organization</th></tr></thead>';
        $ret .= '<tbody>';

        $counter = 1;
        foreach ($mm as $one) {
            if (!in_array($one->user_event_id, $uniqueUserEventIds)) {
                $uniqueUserEventIds[] = $one->user_event_id;
                $user = EventUserInfo::find($one->user_event_id);
                if ($user) {
                    $name = $user->first_name . " " . $user->last_name;
                    $ret .= '<tr data-code-pos="ppp174338833817">';
                    $ret .= '<td>' . $counter++ . '</td>';
                    $ret .= '<td style="text-align: center"><input type="checkbox"> </td>';
                    $ret .= '<td>' . htmlspecialchars($name) . '</td>';
                    $ret .= '<td>' . htmlspecialchars($user->email) . '</td>';
                    $ret .= '<td>' . htmlspecialchars($user->organization) . '</td>';
                    $ret .= '</tr>';
                }
            }
        }

        $ret .= '</tbody></table>';
        return rtJsonApiDone("$ret");

    }

    function copyTemp()
    {
        $field = request('field');

        $idTemp = EventInfo_Meta::getDefaultTemplateId();

        if($obj = EventInfo::find($idTemp)){
            return rtJsonApiDone($obj->$field);
        }
    }

    function saveSignatureUser()
    {
        $uid = request('uid');
        $eventId = request('eventId');
        $signatureImgId = request('signatureImgId');
        if (!$evu = EventUserInfo::find($uid)) {
            return rtJsonApiError("Not found user1 $uid");
        }
        if (!$ev = EventInfo::find($eventId)) {
            return rtJsonApiError("Not found event $eventId");
        }
        if (!$signatureImgId) {
            return rtJsonApiError("Not found signature");
        }
        if (!$file = FileUpload::find($signatureImgId)) {
            return rtJsonApiError("Not found file $signatureImgId");
        }

        if (!$evAndUser = EventAndUser::where(['event_id' => $eventId, 'user_event_id' => $uid])->first()) {
            return rtJsonApiError("Not found event and user $eventId / $uid");
        }

        //Neu user chua co chu ky bao gio:
        if (!$evu->signature) {
            $evu->signature = $signatureImgId;
            $evu->addLog("Save signature from event $eventId");
            $evu->save();
        }

        $evAndUser->signature = $signatureImgId;
        $evAndUser->addLog("Save signature from event $eventId");
        $evAndUser->save();

        return rtJsonApiDone("Save signature done!");
    }

    public function sendRegConfirmMail()
    {
        if (!$idx = request('reg_id')) {
            return rtJsonApiError("Not found id");
        }
        if (self::sendMailRegEvent($idx))
            return rtJsonApiDone("Send mail done!");

        return rtJsonApiError("Error send mail!");
    }

    function addSubEventAndGetHtml()
    {
        $evid = request('sub_id');
        if(!$ev = EventInfo::find($evid))
            return rtJsonApiError("Not found event $evid");

        $parent_id = request('parent_id');
        if(!$parent = EventInfo::find($parent_id))
            return rtJsonApiError("Not found parent event $parent_id");

        if($ev->id == $parent_id){
            return rtJsonApiDone('' , "Không thể thêm chính sự kiện này vào chính nó! $evid -> $parent_id");
        }

        //Neu co roi thi bao loi
        if($ev->parent_id == $parent_id)
            return rtJsonApiDone('' , "Đã thêm từ trước, $evid -> $parent_id");
//        return rtJsonApiError("Sub event $evid is already added to parent event");

        $ev->parent_id = $parent_id;
        $ev->addLog("Add sub event, admin: $parent_id");
        $ev->save();

        $html = EventInfo_Meta::htmlDivSubEventAdmin($ev);

        return rtJsonApiDone($html , "Đã Thêm 1 sự kiện con!");

    }

    function removeSubEvent()
    {
        $uid = getCurrentUserId();

        $evid = request('sub_id');
        if(!$ev = EventInfo::find($evid))
            return rtJsonApiError("Not found event: $evid");

        $ev->parent_id = 0;
        $ev->addLog("Remove sub event, admin: $uid");
        $ev->save();

        return rtJsonApiDone("Remove Done!");
    }

    public function approvePublicUser()
    {
        //Nếu cân có thể kiểm tra thêm evuid của user admin này...


        $uid = getCurrentUserId();

        $GLOBALS['_log_file'] = "/var/glx/weblog/event_approve_public_user.log";
        $GLOBALS['_error_file'] = __FILE__ . " : " . __LINE__;
//        die("ABC");
        if (!$idx = request('event_register_id')) {
            return rtJsonApiError("Not found event_register_id");
        }

        if (!$evReg = EventRegister::find($idx)) {
            return rtJsonApiError("Not found event_register_id $idx");
        }

        //Kiem tra xem co phai su kien con khong $idx

        $email = $evReg->email;
        if (!$evu = EventUserInfo::where('email', $email)->first()) {
            $evu = new EventUserInfo();
            $evu->email = $email;
            $evu->title = $evReg->title;
            $evu->first_name = $evReg->first_name;
            $evu->last_name = $evReg->last_name;
            $evu->phone = $evReg->phone;
            $evu->image_list = $evReg->image_list;
            $evu->organization = $evReg->organization;
            $evu->designation = $evReg->designation;
            $evu->tax_number = $evReg->tax_number;
            $evu->id_number = $evReg->id_number;
            $evu->bank_name_text = $evReg->bank_name_text;
            $evu->bank_acc_number = $evReg->bank_acc_number;

//            $evu->title = $evReg->title;
            $evu->addLog("Add from approvePublicUser, admin : $uid/" . getCurrentUserEmail($uid));

            if (!$evu->save()) {
                return rtJsonApiError("Error save user info");
            }
            $evReg->user_event_id = $evu->id;
            $evReg->save();
        }


        //Neu da dang ky thi bao da dang ky xong:
        if ($eAU_save_before = EventAndUser::where(['event_id' => $evReg->event_id, 'user_event_id' => $evu->id])->first()) {
//            return rtJsonApiDone("Register successfully at $evAndU->created_at !");
        }

        $ev = EventInfo::find($evReg->event_id);
        if($ev instanceof EventInfo);

        //Dem so user dang ky event, neu qua gioi han thi se bao loi:
        if($ev->limit_max_member)
        {
            $countUser = EventAndUser::where(['event_id' => $evReg->event_id])->count();
            if($countUser > $ev->limit_max_member)
                return rtJsonApiError("Event is full, max $ev->limit_max_member members, can not register more, $countUser > $ev->limit_max_member");
        }




        //Kiem tra xem ev co phai su kien con khong
        $isSubEvent = 0;
        if($ev->parent_id)
            $isSubEvent = "(SubEvent, Not send Mail)";

        $metaObj = new \App\Models\EventInfo_Meta();

        $lang = $evReg->lang;
        if ($lang != 'vi' && $lang != 'en')
            $lang = 'vi';


        $domain = UrlHelper1::getDomainHostName();
        $linkQr = self::genLinkQr($domain, $evReg->event_id, $email, $evu->id);
//        $codeEvent = EventInfo::$DEF_EVENT_NAME[0];

        $mmReplace = [
            EventInfo::$DEF_TENKHACH[0] => $evu->getFullnameAndTitle(),
            EventInfo::$DEF_USER_NAME[0]=> $evu->getFullname(),
            EventInfo::$DEF_EVENT_NAME[0]=> $ev->name,
            EventInfo::$DEF_QRCODE[0] => "<img src='$linkQr' >",
            "\n" => "<br>",
        ];

        $fieldTitle1 = "reg_mail_title_$lang" . "2";
        $title = trim($ev->$fieldTitle1);
        if (!$title)
            $title = $metaObj->setDefaultValue($fieldTitle1);

        //$mmReplace thay the theo mang nay
        $mailTitle = cstring2::replaceByArray($title, $mmReplace);

//        $mailTitle = $title = str_replace($codeEvent, $ev->name, $title);
//        $mailTitle = str_replace(EventInfo::$DEF_TENKHACH[0], $evu->getFullnameAndTitle(), $mailTitle);
//        $mailTitle = str_replace(EventInfo::$DEF_USER_NAME[0], $evu->getFullname(), $mailTitle);
//        $mailTitle = str_replace(EventInfo::$DEF_EVENT_NAME[0], $ev->name, $mailTitle);

        $fieldCont1 = "reg_mail_02_$lang";
        $content = trim($ev->$fieldCont1);
        if (!$content)
            $content = $metaObj->setDefaultValue($fieldCont1);

//        $content = str_replace($codeEvent, $ev->name, $content);
//        $content = str_replace(EventInfo::$DEF_TENKHACH[0], "$first_name $last_name", $content);
//        $content = str_replace(EventInfo::$DEF_USER_NAME[0], "$first_name $last_name", $content);
//        $content = str_replace(EventInfo::$DEF_QRCODE[0], "<img src='$linkQr' >", $content);

        $content = cstring2::replaceByArray($content, $mmReplace);
        $mailCont = $content;

//        $mailCont =  "Dear $first_name $last_name,
//<br>
//You are approved for event: $ev->name
//<br>
//We will contact you soon.
//<br>
//Thank and best regards!
//<br>
//-------------
//<br>
//For more information, please visit:
//<br>
//https://" . \LadLib\Common\UrlHelper1::getDomainHostName() . "
//";


        //Tìm tat ca cac sub event, de save vao event and user
        if(0) //Không laàm cái này, vì cái này sẽ duyệt từng event con
        if($evReg->sub_event_list){
            $mmSubEvent = explode(",", $evReg->sub_event_list);
            if($mmSubEvent)
            foreach ($mmSubEvent as $subEventId){
                $subEventId = trim($subEventId);
                if(!$subEventId)
                    continue;
                if(!$evSub = EventInfo::find($subEventId))
                    continue;
                if(!$evAndUser = EventAndUser::where(['event_id' => $subEventId, 'user_event_id' => $evu->id])->first()){
                    $evAndUser = new EventAndUser();
                    $evAndUser->event_id = $subEventId;
                    $evAndUser->user_event_id = $evu->id;
                    $evAndUser->created_at = nowyh();
                    $evAndUser->confirm_join_at = $evReg->reg_confirm_time ?: nowyh();
                    $evAndUser->addLog("Add from approvePublicUser, admin : $uid/" . getCurrentUserEmail($uid));
                    $evAndUser->save();
                }
            }
        }

        if(!$eAU_save_before){
            $evAndU = new EventAndUser();
            $evAndU->event_id = $evReg->event_id;
            $evAndU->user_event_id = $evu->id;
            $evAndU->created_at = nowyh();
            //Lấy time bấm vào link trong email , là time xác nhận tham gia
            $evAndU->confirm_join_at = $evReg->reg_confirm_time ?: nowyh();
            $evAndU->addLog("$isSubEvent Add from approvePublicUser, admin : $uid/" . getCurrentUserEmail($uid));

            if(!$isSubEvent)
                $evAndU->addLog("$mailTitle\n$mailCont");

            $evAndU->save();
        }

        //Neu khong phai Sub Event thi moi gui mail
        if(!$isSubEvent) {
            //Check valid email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return rtJsonApiError("Invalid email: $email");
            }

            $adminEmail = getCurrentUserEmail();

            $evReg->addLog("Send Mail Approve (by admin: $uid - $adminEmail) :\n$mailTitle\n$mailCont");
            $evReg->content_mail2 = $mailTitle . "<br>\n" . $mailCont;
            $evReg->save();

            sendMailNcbd($email, $mailTitle, $mailCont);

//        if($nTimeSend == 1)
            if ($eAU_save_before)
                return rtJsonApiDone("Send Approve Register (Re-send)!");
        }
        return rtJsonApiDone("Approve Register1 $isSubEvent!");
    }

    public function saveEventChannel()
    {

        if (isAdminACP_()) {
            $chanel_name = \request('chanel_name');
            if (preg_match('/^[a-zA-Z0-9\-]+$/', $chanel_name)) {
                $uid = getCurrentUserId();
                outputW("/var/glx/weblog/event_chanel_name.$uid", $chanel_name);

                return rtJsonApiDone('Save Chanel name DONE!');
            } else {
                return rtJsonApiError('Chỉ cho phép ký tự và số');
            }
        }

        return rtJsonApiError('Not admin to Save Chanel name?');
    }

    public function sendOneMail($objEventOrId, $toEmail = null)
    {

        if (!$toEmail) {
            $toEmail = env('SAMPLE_EMAIL1');
        }

        if (is_numeric($objEventOrId)) {
            if (!$objEventOrId = EventInfo::find($objEventOrId)) {
                loi("Not found id $objEventOrId!");
            }
            $idf = $objEventOrId;
        } else {
            $idf = $objEventOrId->id;
        }

        $domain = UrlHelper1::getDomainHostName();
        $ct = $objEventOrId->content;
        $ct = str_replace('<img src="/', '<img src="https://' . $domain . '/', $ct);
        if (ClassMail1::sendMail(env('SAMPLE_EMAIL2'), 'LAD01', $toEmail, 'Test', $ct)) {
            return rtJsonApiDone("Done id: $idf!");
        }

        return rtJsonApiDone("Send error: $idf!");
    }

    public static function markJoinOrDenyEventSms($mmDataFixSms)
    {

        $mmDataFixSms = array_reverse($mmDataFixSms);

        try {
            $tt = count($mmDataFixSms);
            $cc = 0;

            file_put_contents("/var/glx/weblog/sms_tmp", json_encode($mmDataFixSms));

            for ($i = 0; $i < count($mmDataFixSms); $i++) {

                if ($mmDataFixSms[$i]->type == 2)
                    $mmDataFixSms[$i]->type = 'send';
                if ($mmDataFixSms[$i]->type == 1)
                    $mmDataFixSms[$i]->type = 'get';
                $mmDataFixSms[$i]->phone = fixPhoneNumber($mmDataFixSms[$i]->address);
                $mmDataFixSms[$i]->time = fixPhoneNumber($mmDataFixSms[$i]->date);
                $mmDataFixSms[$i]->content = $mmDataFixSms[$i]->body;
            }

            //            if(0)
            for ($i = 0; $i < count($mmDataFixSms); $i++) {

//                echo "\n $i . PHONE = ".  $mmDataFixSms[$i]->date_format . " / ". $mmDataFixSms[$i]->address . " / " .$mmDataFixSms[$i]->phone;

//                if($mmDataFixSms[$i]->phone ?? '')
//                    continue;

//                continue;
                $objx = $mmDataFixSms[$i];
                $cc++;


//                $objx->content = $mmDataFixSms[$i]->body;


//            echo "<pre> >>> ";
//            print_r($mmDataFixSms[$i]);
//            echo "</pre>";
//
//            die("    111222 / $tt ");
                //                $objx = (object)$one;
                //ol00("onex = $one->time"); //  . " --- ". serialize($one)
                ol00("$cc/$tt . +++ onesms +++");
                ol00(' - Time =  ' . nowyh($objx->time / 1000));
                ol00(" - Phone =  $objx->phone");
                ol00(" - Type =  $objx->type");
                ol00(" - Content =  $objx->content");

                $contentLower = trim(strtolower($objx->content));

                $phone = $objx->phone;


//            echo "<pre> >>> ";
//            print_r($objx);
//            echo "</pre>";
//            die("    111222 / $tt ");
                $evSendId = 0;
                // Tim trong content co  [SMS-<number>], lay ra number
                preg_match('/\[(?:SMS-)?(\d+)\]/', $objx->content, $matches);
                if (isset($matches[1])) {
                    $evSendId = $matches[1]; // Kết quả: 11803
                }
                if (!$evSendId) {
                    continue;
                }
                //Tim user to $evSendId
                $evsLog = EventSendInfoLog::find($evSendId);
                if (!$evsLog)
                    continue;

                $evUid = $evsLog->event_user_id;


                //Tìm user với phone này, và event id tương ứng:
                //nếu thấy thì tìm sms tiếp theo của user này, nếu có thấy và trả lời y/n thì sẽ
                //Điền tham gia/ không tham gia tương ứng:
                if (!$evui = EventUserInfo::find($evUid)) {
                    ol00("Not found phone: $evUid");
                } else {
                    ol00("Found evui: $evui->id");

                    /*
                     * //Khong insert nua, vi da insert o doan sync sau
                    $ssid = "sms_$objx->phone" . '_' . nowyh($objx->time / 1000);

                    if (!EventSendInfoLog::where('sms_unique_session', $ssid)->first()) {
                        ol00(" Not found sid $ssid, insert");
                        $evLogInsert = new EventSendInfoLog();
                        $evLogInsert->sms_unique_session = $ssid;
    //                    $evLogInsert->session_id = $ssid;
                        $evLogInsert->event_user_id = $evui->id;
                        $evLogInsert->event_id = $evId;
                        $evLogInsert->type = EventInfo_Meta::$typeSms;
                        $evLogInsert->send_or_get = $objx->type;
                        $evLogInsert->content_sms = $objx->content;
                        $evLogInsert->comment = 'Đọc SMS từ điện thoại, ' . nowyh($objx->time / 1000);
                        $evLogInsert->addLog('Sync from phone, insert log');
                        $evLogInsert->save();
                    } else {
                        ol00(" Found sid $ssid, not insert");
                    }
                    */

//                $ct = $contentLower;
                    //Neeus la send, tìm sự kiện id nếu có
                    if ($objx->type == 'send' && str_starts_with($contentLower, strtolower(DEF_EVENT_SMS_EV_ID_STR)) && strstr($contentLower, ']')) {
                        $evId = trim(str_replace(strtolower(DEF_EVENT_SMS_EV_ID_STR), '', explode(']', $contentLower)[0]));
                        ol00(" Check EVID $evId");
                        $foundConfirmDeny = 0;
                        if (is_numeric($evId)) {

                            //Nếu có eventID này
                            if (!$ev = EventInfo::find($evId)) {
                                ol00("*** Error: Not found event id $evId");
                            } else {
                                if (!$eau = EventAndUser::where(['event_id' => $evId, 'user_event_id' => $evui->id])->first()) {
                                    ol00("*** Error: Not found event and userd $evId / $evui->id");
                                } else {
                                    ol00(" Found event and userd: $evId / $evui->id");

//                                    echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//                                    print_r($mmDataFixSms);
//                                    echo "</pre>";
//                                    die();

                                    //Tìm tin nhắn 'get' ngay TIME sau đó của user này, nếu thấy trả lời là y thì đồng ý, n là ko đồng ý
                                    for ($j = $i; $j < count($mmDataFixSms); $j++) {
                                        $objxAfter = $mmDataFixSms[$j];


                                        if ($objxAfter->phone != $phone) {
                                            continue;
                                        }

                                        if ($objxAfter->type != 'get') {
                                            continue;
                                        }



                                        //Nếu thấy một sự kiện khác sau đó thì break luôn, vì mọi thông tin sau đó có thể là của sk khác
                                        $ctAfter = strtolower($objxAfter->content);
                                        if (str_starts_with($ctAfter, strtolower(DEF_EVENT_SMS_EV_ID_STR)) && strstr($ctAfter, ']')) {
                                            ol00(' Có sự kiện khác, bỏ qua! ');
//                                            die(" --xxx111 / $evui->id / $evId  / $objxAfter->phone != $phone");
                                            continue;
                                        }



                                        $timex = nowyh(round($objxAfter->time / 1000));
                                        //Đồng ý tham gia
                                        if (strtolower(trim($objxAfter->content)) == 'y') {

//                                            die("Thay mot dong y tham gia");

                                            $foundConfirmDeny = 1;
                                            ol00(" !!! Found confirm join: $phone / $evId");
                                            if ($eau->confirm_join_at != $timex) {
                                                ol00(' save db now');
                                                $eau->confirm_join_at = $timex;
                                                $eau->deny_join_at = null;
                                                $eau->addLog("Confirm join sms: $eau->confirm_join_at | " . serialize($objxAfter));
                                                $eau->save();
                                            } else {
                                                ol00(' save before, not need save');
                                            }
                                        }
                                        //Từ chối tham gia
                                        if (strtolower(trim($objxAfter->content)) == 'n') {
                                            $foundConfirmDeny = 1;
                                            ol00(" !!! Found confirm deny: $phone / $evId");
                                            if ($eau->deny_join_at != $timex) {
                                                $eau->confirm_join_at = null;
                                                $eau->deny_join_at = $timex;
                                                $eau->addLog("Deny join sms: $eau->deny_join_at | " . serialize($objxAfter));
                                                $eau->save();
                                            } else {
                                                ol00(' save before, not need save');
                                            }
                                        }

                                    }
                                }
                                if (!$foundConfirmDeny) {
                                    ol00(" Not found confirm from $phone / $evId");
                                }
                            }
                        }
                    }
                }
            }

        }
        catch (\Exception $e) {
            die("Error: " . $e->getMessage() ."/". $e->getLine());
        }
    }


    public function syncSms2()
    {

        //App gửi N sms lên server
        if ($data = request('sync_all_sms')) {

            //            return rtJsonApiError("Sync all Done1");
            $mmDataSms = [];

            ol00('------syncSms2---------------');
            ol00('app send n sms len server = ' . serialize($data));

            return;
            $cc = 0;
            $tt = count($data);
            $mmDataFix = [];
            foreach ($data as $one) {
                $cc++;
                $objx = (object)$one;
                //ol00("onex = $one->time"); //  . " --- ". serialize($one)
                //                ol00("$cc/$tt . onesms = " . nowyh($objx->time / 1000) ." ". serialize($one));
                $objx->phone = fixPhoneNumber($objx->phone);
                //                $objx->content = trim(strtolower($objx->content));
                $mmDataFix[] = $objx;
            }

            //            ol00("app send n sms len server2 = " . serialize($data));

            $mmDataFix = array_reverse($mmDataFix);


            //            ol00("array_reverse = " . serialize($mmDataFix));
            return rtJsonApiDone('Sync all Done');
        }
    }

    public function syncSms()
    {

        //        return rtJsonApiError("Not found event 1");

        setLogFile(DEF_GLX_LOG_FILE_EVENT);
        $ip = \request()->ip();
        ol00("Sync SMS / $ip");

        $allPhone = [];
        if ($evid = \request('evid')) {
            if (!$ev = EventInfo::find($evid)) {
                return rtJsonApiError("Not found event $evid");
            }
            //Tìm các user của event này, lấy số phone của event
            $mEvU = EventAndUser::where('event_id', $evid)->get();
            $mUid = [];
            foreach ($mEvU as $obj) {
                if ($obj instanceof EventAndUser) ;
                $mUid[] = $obj->user_event_id;
            }
            $mm = EventUserInfo::whereIn('id', $mUid)->get();
            foreach ($mm as $oneU) {
                //            if($oneU->phone)
                $allPhone[] = $oneU->phone;
            }
            $allPhone = array_unique($allPhone);
            $nPhone = count($allPhone);
            $allPhone = implode(',', $allPhone);
        }

        $cmd = \request('cmd');
        ol00("CMD: $cmd");

        //Ra lệnh từ server xuống app
        //App Gửi n sms cuối lên server
        if ($cmd == 'sync_sms_request') {
            //Đưa pusher vào đây:
            $options = [
                'cluster' => 'ap1',
                'useTLS' => true,
            ];
            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );
            $nSms = 1000;
            $timeFrom = (time() - 24 * 60 * 60 * 30) * 1000; //Mac dinh 30  ngay
            //            if($ev->created_at)
            //                $timeFrom = strtotime($ev->created_at) * 1000;

            ol00("Time from = $timeFrom , " . nowyh($timeFrom / 1000));
            $data = ['number_sms' => $nSms, 'time_after' => $timeFrom, 'list_phone_number' => $allPhone];
            $cname = EventInfo_Meta::getEventChanelName();
            ol00("sync_sms_request Send Pusher (cname = $cname) = " . serialize($data));
            $pusher->trigger($cname, 'sync_sms', $data);

            return rtJsonApiDone("Da thong bao App gui $nSms SMS cua $nPhone phone!");
        }

        //App gửi N sms lên server
        if ($data = request('sync_all_sms')) {

            //            return rtJsonApiError("Sync all Done1");
            $mmDataSms = [];

            ol00('---------------------');
            ol00('app send n sms len server = ' . serialize($data));
            $cc = 0;
            $tt = count($data);
            $mmDataFix = [];
            foreach ($data as $one) {
                $cc++;
                $objx = (object)$one;
                //ol00("onex = $one->time"); //  . " --- ". serialize($one)
                //                ol00("$cc/$tt . onesms = " . nowyh($objx->time / 1000) ." ". serialize($one));
                $objx->phone = fixPhoneNumber($objx->phone);
                //                $objx->content = trim(strtolower($objx->content));
                $mmDataFix[] = $objx;
            }

            //            ol00("app send n sms len server2 = " . serialize($data));

//            $mmDataFix = array_reverse($mmDataFix);

            self::markJoinOrDenyEventSms($mmDataFix, $evid);

            //            ol00("array_reverse = " . serialize($mmDataFix));
            return rtJsonApiDone('Sync all Done');
        }

        //API app báo 1 sms đã gửi xong lên server
        //App gửi all sms lên
        if ($data = request('sync_one_sms')) {
            ol00('sync_one_sms Data sync_one_sms = ' . serialize($data));

            return rtJsonApiDone('Sync one Done');
        }

        return 1;
    }

    static function splitContent($content, $maxLength)
    {
        $parts = [];
        $words = explode(" ", $content);
        $part = "";

        foreach ($words as $word) {
            if (strlen($part . " " . $word) > $maxLength) {
                array_push($parts, $part);
                $part = $word;
            } else {
                $part .= " " . $word;
            }
        }

        if ($part != "") {
            array_push($parts, $part);
        }

        return $parts;
    }


    public static function sendSms($phone, $content, $chanel)
    {
        //Đưa pusher vào đây:
        //        sleep(1);

        if (!is_numeric($phone)) {
            return 0;
        }

        $options = [
            'cluster' => 'ap1',
            'useTLS' => true,
        ];

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $data = ['phone' => '0968902116', 'msg' => 'Xin chao'];
        $data = ['phone' => $phone, 'msg' => $content];

        $parts = self::splitContent($content, 250);
        ol0("Npart = " . count($parts), 0);

        //Bỏ qua chia part...
        if (0 && count($parts) > 1)
            for ($i = 0; $i < count($parts); $i++) {
                $ii = $i + 1;
                $data['msg'] = " ($ii) " . $parts[$i];
                ol0("part $i= " . $parts[$i], 0);
                $pusher->trigger($chanel, 'send_sms', $data);
                sleep(2);
            }
        else
            $pusher->trigger($chanel, 'send_sms', $data);

        return 1;
    }

    static function genLinkQr($domain, $eventId, $email, $uid)
    {
        $pathWeb = "images/code_gen/ncbd-event-$eventId-$uid.png";
        $fileImg = "/var/www/html/public/$pathWeb";
        $linkDirectImg = "https://$domain/$pathWeb";

        if (file_exists($fileImg) && filesize($fileImg) > 0)
            return $linkDirectImg;

        $eventIdEnc = qqgetRandFromId_($eventId);
//        $linkQR = "https://$domain/user-confirm-event?data=$eventIdEnc|".eth1b($email);
        $linkQR = "https://$domain/user-confirm-event/data/$eventIdEnc|" . eth1b($uid);
        $linkImg = "https://$domain/tool1/_site/event_mng/gen-qr-code.php?str=$linkQR";

        $dataQrImg = QrCode::size(200)->margin(1)->format('png')->encoding('UTF-8')->generate($linkQR);

        if (file_exists($fileImg) && file_get_contents($fileImg) == $dataQrImg)
            return $linkDirectImg;

        file_put_contents($fileImg, $dataQrImg);
        return $linkDirectImg;
    }


    public static function sendAllMessageLoop($evId = null, $typeEmailSms = null, $ignoreEcho = 0)
    {

//        error_reporting(E_ALL);
//        ini_set('display_errors', 1);
        $eventSendActionCurrent = null;

        try {

            setLogFile(DEF_GLX_LOG_FILE_EVENT);
            $options = [
                'cluster' => 'ap1',
                'useTLS' => true,
            ];

            $pusher = new Pusher(
                env('PUSHER_APP_KEY'),
                env('PUSHER_APP_SECRET'),
                env('PUSHER_APP_ID'),
                $options
            );

            if ($evId) {
                $mm = EventSendAction::where('event_id', $evId)->where("type", $typeEmailSms)->get();
            } else
                $mm = EventSendAction::all();

//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($mm->toArray());
//            echo "</pre>";

            $cc = 0;
            $tt = count($mm);
            if(!$ignoreEcho)
                echo("\n--- (Total = $tt), TYPE, Start sendAllMessageLoop $evId / $typeEmailSms");

            if (!$mm || $tt == 0 ) {
                echo("\n *** Not have event?");
                return;
            }

            foreach ($mm as $eventSendAction) {
                $eventSendActionCurrent = $eventSendAction;
                $cc++;
                echo "\n --- check now: $eventSendAction->id ";
                usleep(1);
                //kiem tra cac che do phu hop,
                //  'select_content'
                //  'select_user_type'
                $chanelPusher = $eventSendAction->pusher_chanel;
                if ($eventSendAction->done) {
                    echo "\n $eventSendAction->id, Ignore because done";
                    continue;
                }

                if ($eventSendAction->pushed_all_sms_to_queue) {
                    echo "\n pushed_all_sms_to_queue, so continue";
//                    echo "\n $eventSendAction->id, Ignore because pushed_all_sms_to_queue = $eventSendAction->pushed_all_sms_to_queue";
                    continue;
                }

                usleep(1000);

//                if ($eventSendAction->create_at < nowyh(time() - 3600 * 6)) {
//
//                }
                //Qua 6h khong gui nua:
                if ($eventSendAction->last_force_send && $eventSendAction->last_force_send < nowyh(time() - 3600 * 6)) {
                    ol1($eventSendAction, "\n - <6h Skip send, last_force_send = $eventSendAction->last_force_send", $ignoreEcho);
                    continue;
                }

                $eventId = $eventSendAction->event_id;

                ol1($eventSendAction, "\n---$cc/$tt. EventIdx = $eventId, SendId CMD: $eventSendAction->id", $ignoreEcho);

                if (!$ev = EventInfo::find($eventId)) {
                    ol1($eventSendAction, "Not found event id $eventId", $ignoreEcho);
                    continue;
                }
                if($ev instanceof EventInfo);

                $user_email_send_override = trim($eventSendAction->user_email_send_override);
                $user_email_send_override = str_replace(" ", '', $user_email_send_override);
                $user_email_send_override = trim($user_email_send_override, ',');
                $user_email_send_override = strtolower($user_email_send_override);
                $mmEmailOverride = explode(",", $user_email_send_override);


                //Tìm all mail để gửi:
                $mmEAU = EventAndUser::where(['event_id' => $eventId])->get();

                $domain = UrlHelper1::getDomainHostName();

                $fromMail = SiteMng::getEmailAdmin(1);
                $fromName = SiteMng::getSiteCode(1);


                //Chi lay cac user phu hop

                $domain = UrlHelper1::getDomainHostName();
                $doneBefore = $ignore = $done = $cc = 0;
                $tt = count($mmEAU);

                ol1($eventSendAction, "\n--- Start User List To send ---", $ignoreEcho);

                //Tất cả user của evAndUser
                foreach ($mmEAU as $eventAndUser) {

                    ol1($eventSendAction, "--- $cc/$tt. check User $eventAndUser->user_event_id", $ignoreEcho);
                    $cc++;
                    if (!$evUser = EventUserInfo::find($eventAndUser->user_event_id)) {
                        ol1($eventSendAction, "  *** Not found user ev: $eventAndUser->user_event_id, so Ignore", $ignoreEcho);
                        $ignore++;
                        continue;
                    }

                    //Tìm EventSendInfoLog xem có chưa nếu cos rồi thì thôi, không insert vào nữa
                    if ($evs = EventSendInfoLog::where(['session_id' => $eventSendAction->id,
                        'event_user_id'=>$eventAndUser->user_event_id])->first()) {

                        //Bo qua SMS, vi sms se gui thong qua APP
                        if($evs->type == 'sms'){
//                            echo "\n Ignore UID ....";
                            ol1($eventSendAction, "Skip send, already queue", $ignoreEcho);
                            continue;
                        }
                    }

                    $evUser->email = strtolower($evUser->email);

                    ol1($eventSendAction, " _Email: $evUser->email", $ignoreEcho);

                    $languageUser = '';
                    if ($evUser->language == 'en')
                        $languageUser = 'en';

//                only_confirmed_user
//                only_denied_user
//                only_attended_user

                    if (strstr($evUser->email, '@ymail.com')) {
                        $ignore++;
                        ol1($eventSendAction, "Ignore not valid $evUser->email ", $ignoreEcho);
                        continue;
                    }

                    if (strstr($evUser->email, '@khongcomail')
                    || strstr($evUser->email, '@khongcoemail')
                    ) {
                        $ignore++;
                        ol1($eventSendAction, "Ignore not valid: $evUser->email ", $ignoreEcho);
                        continue;
                    }

                    $select_user_type = $eventSendAction->select_user_type;

//                getch("select_user_type = $select_user_type");

                    $evUser->email = strtolower($evUser->email);

                    //Nếu list email có thì bỏ qua
                    if ($user_email_send_override) {
//                        ol1($eventSendAction," send override ",$ignoreEcho);
//                    getch("...... $user_email_send_override ");
                        //Nếu email ko có trog list user_email_send_override, thì bỏ qua
                        if (!$evUser->email || !in_array($evUser->email, $mmEmailOverride)) {
                            //ol1($eventSendAction," * Không gửi, vỉ không nằm trong mail limit: $evUser->email",$ignoreEcho);
                            $ignore++;
                            continue;
                        }
                    } else {

                        ol1($eventSendAction, "UID = $eventAndUser->user_event_id . Kiểu user: $select_user_type \n", $ignoreEcho);
                        if ($user_email_send_override)
                            ol1($eventSendAction, "user_email_send_override1 = $user_email_send_override \n", $ignoreEcho);


                        //Nếu list user, đã gửi cho user rồi thì bỏ qua, muốn gửi lại thì sao? làm lệnh mới
                        // Ở đây đề phòng khi đang gửi mà bị lỗi, thì khi loop chạy gửi lại, sẽ biết bỏ qua các user đã gửi rồi, không bị gửi lại nhiều lần
                        if (str_contains(",$eventSendAction->list_uid_send_done,", ",$evUser->id,")) {
                            $ignore++;
                            $doneBefore++;
                            ol1($eventSendAction,
                                " user đã gửi ở lần trước  - bỏ qua (có thể đây là gửi lại lần nữa, hoặc gửi dở bị lỗi, và gửi tiếp) ", $ignoreEcho);
                            continue;
                        }


                        if ($select_user_type == 'only_confirmed_but_not_checkin') {
                            if (!$eventAndUser->confirm_join_at) {
                                $ignore++;
                                ol1($eventSendAction, " user chưa xác nhận, bỏ qua ", $ignoreEcho);
                                continue;
                            }
                            if ($eventAndUser->attend_at) {
                                ol1($eventSendAction, " user đã checkin, bỏ qua ", $ignoreEcho);
                                $ignore++;
                                continue;
                            }
                        } elseif ($select_user_type == 'only_not_yet_confirmed_user') {
                            if ($eventAndUser->confirm_join_at) {
                                $ignore++;
                                ol1($eventSendAction, " user đã xác nhận, bỏ qua", $ignoreEcho);
                                continue;
                            }
                        } elseif ($select_user_type == 'only_confirmed_user') {
                            if (!$eventAndUser->confirm_join_at) {
                                $ignore++;
                                ol1($eventSendAction, " user chưa xác nhận, bỏ qua ", $ignoreEcho);
                                continue;
                            }
                        } elseif ($select_user_type == 'only_denied_user') {
                            if (!$eventAndUser->deny_join_at) {
                                $ignore++;
                                ol1($eventSendAction, " user chưa từ chối, bỏ qua ", $ignoreEcho);
                                continue;
                            }
                        } elseif ($select_user_type == 'only_attended_user') {
                            if (!$eventAndUser->attend_at) {
                                $ignore++;
                                ol1($eventSendAction, " user chưa tham dự, bỏ qua ", $ignoreEcho);
                                continue;
                            }
                        } elseif ($select_user_type == 'only_not_attended_user') {
                            if ($eventAndUser->attend_at) {
                                $ignore++;
                                ol1($eventSendAction, " user da check in, bỏ qua ", $ignoreEcho);
                                continue;
                            }
                        }
                    }

                    ol1($eventSendAction, "-> $cc/$tt. Send to $eventAndUser->user_event_id... ", $ignoreEcho);

                    //Nội dung gửi:
                    $nameFull = "$evUser->title $evUser->last_name $evUser->first_name";

                    usleep(1000);


                    if(!$evs)
                        $evs = new EventSendInfoLog();

                    $evs->event_user_id = $evUser->id;
                    $evs->event_id = $eventId;
                    $evs->session_id = $eventSendAction->id;
                    $evs->type = $eventSendAction->type;
                    $evs->count_retry_send = 0;
                    //Save để lấy ID
                    $evs->save();


                    $select_content = $eventSendAction->select_content;

                    //Nếu ngôn ngũ cửa user là TA thì chuyển sang TA
                    //Cho cả email và sms
                    if ($languageUser == 'en') {
                        $select_content .= "_en";
                    }

                    ol1($eventSendAction, "Select content  x: $select_content", $ignoreEcho);

                    $ct = trim($ev->$select_content);
                    if (!$ct) {
                        ol1($eventSendAction, "*** Error: empty content $select_content, ev = $eventId", $ignoreEcho);
                        continue;
                    }

                    $eventSendAction->list_uid_send_done .= ",$evUser->id,";
                    $eventSendAction->list_uid_send_done = str_replace(',,', ',', $eventSendAction->list_uid_send_done);

                    $eventIdEnc = qqgetRandFromId_($eventId);
                    $linkXacNhan = "https://$domain/user-confirm-event?id=$eventIdEnc&data_ev=" . eth1b($evUser->email);
                    $linkXacNhan = "https://$domain/user-confirm-event/id/$eventIdEnc/data_ev/" . eth1b($evUser->id);
                    $txt = "Xác nhận tham dự";
                    if ($languageUser == 'en') {
                        $txt = "Confirm attendance";
                    }
                    $urlXacNhan = "<a target='_blank' href='$linkXacNhan'> $txt</a>";

                    $ct = str_replace(EventInfo::$DEF_EXT1[0], $eventAndUser->extra_info1, $ct);
                    $ct = str_replace(EventInfo::$DEF_EXT2[0], $eventAndUser->extra_info2, $ct);
                    $ct = str_replace(EventInfo::$DEF_EXT3[0], $eventAndUser->extra_info3, $ct);
                    $ct = str_replace(EventInfo::$DEF_EXT4[0], $eventAndUser->extra_info4, $ct);
                    $ct = str_replace(EventInfo::$DEF_EXT5[0], $eventAndUser->extra_info5, $ct);

                    $ct = str_replace(EventInfo::$DEF_EVENT_NAME[0], $ev->getName($evUser->language), $ct);
                    $ct = str_replace(EventInfo::$DEF_START_TIME[0], $ev->getTimeStartVn(), $ct);
                    $ct = str_replace(EventInfo::$DEF_END_TIME[0], $ev->getTimeEndVn(), $ct);
                    $ct = str_replace(EventInfo::$DEF_ADDRESS_LOCATION[0], $ev->getLocation($evUser->language), $ct);
                    $ct = str_replace(EventInfo::$DEF_TENKHACH[0], $nameFull, $ct);
                    $ct = str_replace(EventInfo::$DEF_LINKTHAMDU[0], $urlXacNhan, $ct);

//                    $ct = removeHtmlComments($ct);




//                $ctSMS = cstring2::convert_codau_khong_dau($ct);


                    if ($eventSendAction->type == EventInfo_Meta::$typeEmail) {

                        $ct = removeCommentsWithDOM2($ct);
                        $eventSendAction->content_raw_send = $ct;

                        if (!checkMailValidNcbd($evUser->email)) {
                            $ignore++;
                            ol1($eventSendAction, " Không có email ($evUser->email), hoặc mail không hợp lệ, bỏ qua ", $ignoreEcho);
                            continue;
                        }

                        //Check valid email
                        if (!filter_var($evUser->email, FILTER_VALIDATE_EMAIL)) {
                            $ignore++;
                            ol1($eventSendAction, "Not send because Invalid email: $evUser->email ", $ignoreEcho);
                            continue;
                        }

                        $selectTitle = str_replace('content', 'mail_title', $select_content);
                        $ct = str_replace('<img src="/', '<img src="https://' . $domain . '/', $ct);

//                    $linkQR = "https://$domain/user-confirm-event?data=$eventIdEnc|".eth1b($evUser->id);
                        $linkQR = "https://$domain/user-confirm-event/data/$eventIdEnc|" . eth1b($evUser->id);
                        $linkImg = "https://$domain/tool1/_site/event_mng/gen-qr-code.php?str=$linkQR";

                        ol1($eventSendAction, "evUser->id = $evUser->id, Link QR = $linkQR", $ignoreEcho);

                        $dataQrImg = QrCode::size(200)->margin(1)->format('png')->encoding('UTF-8')->generate($linkQR);
                        $pathWeb = "images/code_gen/ncbd-event-$eventId-$evUser->id.png";
                        $fileImg = "/var/www/html/public/$pathWeb";
                        file_put_contents($fileImg, $dataQrImg);
                        chown($fileImg, 'www-data');

                        $strImg = "<div data-code-pos='ppp1716081' style='text-align: center; display: block; margin: 0 auto'>
<img src='https://$domain/$pathWeb'/> <br>
<span style='color: royalblue; font-size: 120%; font-family: courier, monospace'> <b> $eventId-$evUser->id </b></span> </div> ";

                        $ct = str_replace(EventInfo::$DEF_QRCODE[0], $strImg, $ct);

                        $titleMail = $ev->$selectTitle;
                        $titleMail = str_replace(EventInfo::$DEF_EVENT_NAME[0], $ev->getName(), $titleMail);

//                    $ct .= $strImg;
                        $evs->content = $ct;
                        $evs->title_email = $titleMail;
                        $evs->save();

                        if ($ev instanceof ModelGlxBase) ;
                        $obj = new ClassMailV2();
//                        $obj->Username = $fromMail;
                        $obj->Username = explode(',', env('NCBD_ACC'))[0];
                        //Chua co cho luu password
                        $obj->Password = dfh1b(explode(',', env('NCBD_ACC'))[1]);
                        $obj->Host = "smtp.office365.com";
                        $obj->Port = "587";
                        $obj->SMTPSecure = 'tls';
                        $obj->From = $fromMail;
                        $obj->addReplyTo($fromMail, $fromName);
                        $obj->FromName = $fromName;
                        $obj->toAddress = $evUser->email;
                        $obj->Body = $ct;
                        $obj->Subject = $titleMail;
                        $obj->debug = 0;

                        $attachFileField = str_replace("content", 'attached_files_email', $select_content);
                        ol1($eventSendAction, " -- attachFileField = $attachFileField", $ignoreEcho);
                        if ($mFile = $ev->getAllFileList($attachFileField, 2)) {
                            foreach ($mFile as $file) {
                                if (file_exists($file->file_path)) {
                                    $obj->attachFile[$file->file_path] = $file->name;
                                }
                            }
                        }

                        $isErrorSendMail = 0;

                        if (!$obj->sendMailGlx()) {
                            ol1($eventSendAction, " *** Có lỗi send  mail : $obj->ErrorInfo", $ignoreEcho);
                            echo $obj->ErrorInfo;
                            $isErrorSendMail = 1;
                        } else {
                            ol1($eventSendAction, " Send mail done: $evUser->email", $ignoreEcho);
                            $evs->done_at = nowyh();
                            $evs->save();
                            $done++;
                        }

                        ClassMail1::$attachedFile = [];
                        $eventAndUser->sent_mail_at = nowyh();
                        $eventAndUser->save();
                        ol1($eventSendAction, " Pushing done to Web , chanel = $chanelPusher...", $ignoreEcho);
                        $data['message'] = " <i class='fa fa-spinner fa-spin'></i>  (Lệnh: $eventSendAction->id) Sending $cc/$tt: $evUser->email";
                        if ($cc == $tt) {
                            $data['message'] = "Đã hoàn thành $done/$tt email, bỏ qua : $ignore";
                        }
                        $data['event_id'] = $eventId;
                        $pusher->trigger($chanelPusher, "my-event-pusher-web-$eventId", $data);

                    } elseif ($eventSendAction->type == EventInfo_Meta::$typeSms) {

                        $ctSMS = ($ct);
                        $ct = removeSMSTextComments($ct);
                        $eventSendAction->content_raw_send = $ct;

                        ol1($eventSendAction, " SMS to  $evUser->phone", $ignoreEcho);


                        //static::sendOneMail($objEventOrId);
                        $ct = DEF_EVENT_SMS_EV_ID_STR . "$eventId] " . $ctSMS;
                        $domain = UrlHelper1::getDomainHostName();
                        $linkQrCodeSmS = "https://$domain/qr/$eventId-$evUser->id";
                        $ct = str_replace(EventInfo::$DEF_LINKTHAMDU[0], $linkXacNhan, $ct);
                        $ct = str_replace(EventInfo::$DEF_QRCODE[0], $linkQrCodeSmS, $ct);



                        $ct .= "\n[SMS-$evs->id]";

                        //phone chi giữ lại số
                        $phone = preg_replace('/[^0-9]/', '', $evUser->phone);
//                    $phone = str_replace(['+', ' ','.','-',',','(', ')'], '', $evUser->phone);

                        if (!$phone || !is_numeric($phone) || strlen($phone) < 10 || strlen($phone) > 12) {
                            $log = "Not valid phone not send?: ($evUser->phone/ $phone), bỏ qua";
                            $evs->done_at = "Not valid phone!";
                            $evs->addLog($log,1);
                            //Số phone không hợp le:
                            $ignore++;
                            ol1($eventSendAction, $log, $ignoreEcho);
                            continue;
                        } else {
                            ol1($eventSendAction, " Queue Sms: $evUser->phone, content: $ct", $ignoreEcho);
                            $eventPusherChanel = $eventSendAction->pusher_chanel;
//                            ol1($eventSendAction, " Channel : $evUser->$eventPusherChanel");

                            $evs->content_sms = $ct;
//                            $evs->comment = nowyh() . '# Đưa vào hàng đợi gửi SMS';
                            $evs->addLog(' Đưa vào hàng đợi gửi SMS');
                            $evs->save();

                            if(0){
                                if (self::sendSms(
                                    $phone, $ct, $eventPusherChanel
                                )) {
                                    $done++;
                                    $evs->content_sms = $ct;
                                    $evs->comment = nowyh() . ' Đưa vào hàng đợi gửi SMS';
                                    $evs->save();
                                    ol1($eventSendAction, " Send Sms done: $evUser->phone", $ignoreEcho);
                                } else {
                                    ol1($eventSendAction, " Có lỗi send Sms : $evUser->phone", $ignoreEcho);
                                }

                                {
                                    $eventAndUser->sent_sms_at = nowyh();
                                    $eventAndUser->save();
                                    $log = (' Pushing done to Web ...');
                                }
                            }

                        }

                        //SMS khong gui qua day nua , ma gui qua App SMS:
                        if(0){
                            ol1($eventSendAction, $log);
                            $data['message'] = " <i class='fa fa-spinner fa-spin'></i>  Sending $cc/$tt: <br> $evUser->phone";
                            if ($cc == $tt) {
                                $data['message'] = "Đã hoàn thành $done/$tt sms, bỏ qua : $ignore";
                            }
                            $data['event_id'] = $eventId;
                            $pusher->trigger($chanelPusher, "my-event-pusher-web-$eventId", $data);
                        }

                    } else {
                        ol1($eventSendAction, ' *** Error, not valid type event ?', $ignoreEcho);
                        $data['message'] = " <i class='fa fa-spinner fa-spin'></i>  Error sending not valid type";
                        if ($cc == $tt) {
                            $data['message'] = "Error send $cc/$tt";
                        }
                        $data['event_id'] = $eventId;
                        $pusher->trigger($chanelPusher, "my-event-pusher-web-$eventId", $data);
                    }
                }

                if($eventSendAction->type == EventInfo_Meta::$typeSms){

                    if (!$eventSendAction->count_send) {
                        $eventSendAction->count_send = 1;
                    } else {
                        $eventSendAction->count_send++;
                    }


                    $eventSendAction->pushed_all_sms_to_queue = nowyh();
                    $eventSendAction->save();
                    ol1($eventSendAction, " --- DONE QUEUE SMS --- ? ", $ignoreEcho);


                    //SMS return vi chi dua vao QUEUE
                    if($ignoreEcho)
                        ob_clean();
                    return 1;
                }

                ol1($eventSendAction, " --- DONE SEND $eventSendAction->id ? ", $ignoreEcho);

                $data['message'] = "Hoàn thành gửi: $done/$tt Tin, bỏ qua $ignore tin. (Lệnh gửi: <a href='/admin/event-send-action/edit/$eventSendAction->id'> $eventSendAction->id </a>)";
//<a target='_blank' href='/admin/event-send-info-log?seby_s4=$eventId&seoby_s5=C&seoby_s15=C&seby_s15=$eventSendAction->id'> <u>Xem chi tiết Tại đây! </u> </a> ";
                $data['event_id'] = $eventId;
                $pusher->trigger($chanelPusher, "my-event-pusher-web-$eventId", $data);

                $eventSendAction->done = 1;
                if (!$eventSendAction->count_send) {
                    $eventSendAction->count_send = 1;
                } else {
                    $eventSendAction->count_send++;
                }
                $doneAll = $doneBefore + $done;
                $eventSendAction->count_success = "$doneAll/$tt";

                $eventSendAction->addLog("Hoàn thành gửi:  ($done + $doneBefore) / $tt Tin, bỏ qua $ignore");
                $eventSendAction->save();
            }

            if($ignoreEcho){
                ob_clean();
                return 1;
            }

            echo "\n...END SEND \n";

        } catch (\Throwable $e) {
//            echo "\n\n\n  ***  ERROR ... " . $e->getMessage() ."\n".  $e->getTraceAsString();
//            die(" xxxx = ");
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r( $e->getTraceAsString());
//            echo "</pre>";

            ol1($eventSendActionCurrent, "\n\n*** ERRROR LOG1: " . substr($e->getMessage() . "... \n" . $e->getTraceAsString(), 0, 5000));

        }

    }

    public function sendTinAll()
    {
        $idf = \request('event_id');
        if (!$objEv = EventInfo::find($idf)) {
            return rtJsonApiError("Not found eid $idf!");
        }

        $uid0 = getCurrentUserId();

        $typeX = \request('typeX');
        if (!in_array($typeX, ['email', 'sms'])) {
            return rtJsonApiError('Not valid typex?');
        }


        $select_content = \request('select_content');
        $select_user_type = \request('select_user_type');

        if ($typeX == 'email') {
            //Kiem tra title/content empty? cả VI và EN...
//            die("select_content = $select_content");
            if (str_starts_with($select_content, 'content')) {
//                $n = str_replace('content', '');
            }
        }

        $user_email_send_override = \request('user_email_send_override');
        $user_email_send_override = strtolower($user_email_send_override);

        //Kiểm tra xem chiến dịch đã done chưa:
        //Nếu đã được thực hiện, thì báo lại hỏi xem có thêm send_action mới không
        //Nếu có tham số force_send thì thêm send_action mới

        //Tránh bị check empty, vì DB luôn là null
        if (!$user_email_send_override)
            $user_email_send_override = null;
        else{
            $user_email_send_override = trim($user_email_send_override, ',');
            //Kiêểm tra xem email này co nam trong su kien khong:
            $mmCheck = explode(",", $user_email_send_override);
            foreach ($mmCheck as $email) {
                $emailCheck = strtolower(trim($email));
                $uCheck = EventUserInfo::where("email", $emailCheck)->first();
                if (!$uCheck) {
                    return rtJsonApiError("***Lỗi: Email '$emailCheck' không tồn tại!");
                }
                if (!EventAndUser::where(['event_id' => $idf, 'user_event_id' => $uCheck->id])->first())
                    return rtJsonApiError("***Lỗi: Email '$emailCheck' không tham gia sự kiện $idf!");
            }
        }


        //if($user_email_send_override)
        {
            $evSend = EventSendAction::where(
                ['event_id' => $idf,
                    'type' => $typeX,
                    'select_content' => $select_content,
                    'select_user_type' => $select_user_type,
                    'user_email_send_override' => $user_email_send_override,
                ])
                ->where("created_at", '>', nowyh(time() - 3600 * 24))
                ->latest('id')->first();
        }
//        else
//            $evSend = EventSendAction::where(
//                [   'event_id' => $idf,
//                    'type' => $typeX,
//                    'select_content' => $select_content,
//                    'select_user_type' => $select_user_type,
//                ])->latest('id')->first();

        if ($evSend) {
//            die(" $user_email_send_override $evSend->id ");
            if (\request('force_send')) {
//                die("x1 $user_email_send_override $evSend->id ");
                //Đánh dấu done = 0, để loop sẽ thực hiện lại evSend này
                /*
                $evSend->last_force_send = nowyh();
                $evSend->done = 0;
                $evSend->addLog('Force resend by : ' . $uid0);
                $evSend->pusher_chanel = EventInfo_Meta::getEventChanelName();
                $evSend->save();
                return rtJsonApiDone(2, 'Chiến dịch gửi sẽ được thực thi (1)');
                */

                //Ta sẽ tạo mới 1 send_action mới
                goto _create_new;
            }
            if ($evSend->done == 1) {
                return rtJsonApiDone(-1, "Chiến dịch gửi Đã hoàn thành ($evSend->id)?");
            }

//            self::sendMessageWebsocket("send_all_sms_events_in_back_ground");

            $email = '';
            if($us = User::find($evSend->user_id)){
                $email =  $us->email;
            }

            //Kiểm tra xem user này

            return rtJsonApiDone(2, "Chiến dịch đã tạo từ trước \nBởi $email, \nID = $evSend->id \nNgày $evSend->created_at \nVà đang chờ thực thi (2)");
        }

        _create_new:

        {
            $user_email_send_override = strtolower(trim($user_email_send_override));
            $user_email_send_override = str_replace(" ", '', $user_email_send_override);
            $user_email_send_override = trim($user_email_send_override, ',');
            $evSend = new EventSendAction();
            $evSend->event_id = $idf;
            $evSend->type = $typeX;
            $evSend->select_content = $select_content;
            $evSend->user_id = $uid0;
            $evSend->addLog('Send by : ' . $uid0);
            $evSend->last_force_send = nowyh();
//            die("UID = $uid0");
            //Tránh bị insert empty
            if (!$user_email_send_override)
                $user_email_send_override = null;
            $evSend->user_email_send_override = $user_email_send_override;
            $evSend->select_user_type = $select_user_type;
            $evSend->pusher_chanel = EventInfo_Meta::getEventChanelName();
            $evSend->save();


//            if(!self::sendAllMessageLoop($idf, $typeX, $ignoreEcho = 1)){
//                return rtJsonApiError("Có lỗi xảy ra, sendAllMessageLoop!");
//            }

//            self::sendMessageWebsocket("send_all_sms_events_in_back_ground");
            return rtJsonApiDone(3, "Đã tạo lệnh gửi và chờ thực thi, mã lệnh: $evSend->id");
        }
        return rtJsonApiDone(1, "Send ok: $idf!");
    }

    public function stopSendTinAll()
    {

        $idf = \request('event_id');
        if (!$obj = EventInfo::find($idf)) {
            return rtJsonApiError("Not found eid $idf!");
        }

        //        $ct = $obj->content;
        //        $ct = str_replace('<img src="/', '<img src="https://'.$domain.'/',$ct);
        return rtJsonApiDone("Stop Send ok: $idf!");
    }

    public function sendMailTest()
    {
        $idf = \request('testId');

        if (!$obj = EventInfo::find($idf)) {
            return rtJsonApiError("Not found id $idf!");
        }

        $domain = UrlHelper1::getDomainHostName();
        $title = $obj->mail_title1;

        $eventIdEnc = qqgetRandFromId_($obj->id);

        $ct = $obj->content;
        $ct = str_replace('<img src="/', '<img src="https://' . $domain . '/', $ct);
        //$ct = str_replace('/xacnhan', "https://$domain/user-confirm-event?id=$eventIdEnc&data_ev=".eth1b(env('SAMPLE_EMAIL1')), $ct);
        $ct = str_replace('/xacnhan', "https://$domain/user-confirm-event/id/$eventIdEnc/data_ev/" . eth1b(env('SAMPLE_EMAIL1')), $ct);

        $adminEmail = SiteMng::getEmailAdmin();

        if (ClassMail1::sendMail(env('SAMPLE_EMAIL1'), env('SAMPLE_NAME1'), $adminEmail, $title, $ct)) {
            return rtJsonApiDone("Done id: $idf!");
        }

        return rtJsonApiDone("Send error: $idf!");
    }

    public function addUserToEvent()
    {
        $mmSelectId = \request('mmSelectId');
        $mmEventCheck = \request('mmEventCheck');

        //        print_r($mmSelectId);
        //        print_r($mmEventCheck);

        $nAddBefore = 0;
        $nAddNew = 0;
        $ttUs = count($mmSelectId);
        foreach ($mmSelectId as $uid) {
            //Kiểm tra UID có tồn tại khong
            if (!EventUserInfo::find($uid)) {
                continue;
            }
            foreach ($mmEventCheck as $evid) {
                if (!EventInfo::find($evid)) {
                    continue;
                }
                if (!$obj = EventAndUser::where(['user_event_id' => $uid, 'event_id' => $evid])->first()) {
                    $obj = new EventAndUser();
                    $obj->user_event_id = $uid;
                    $obj->event_id = $evid;
                    $obj->save();
                    $nAddNew++;
                } else {
                    $nAddBefore++;
                }
            }
        }
        $pad = '';
        if ($nAddBefore) {
            $pad = "\n(Đã thêm từ trước: $nAddBefore)";
        }

        return rtJsonApiDone("Thêm thành công $nAddNew/$ttUs $pad");
    }
}
