<?php

namespace App\Models;

use App\Components\Helper1;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use LadLib\Common\cstring2;
use LadLib\Laravel\Database\TraitModelExtra;
use ReflectionClass;
use ReflectionProperty;
class EventInfo extends ModelGlxBase {

    use HasFactory, SoftDeletes, TraitModelExtra;
    protected $guarded = [];
    static $DEF_TENKHACH = ['[TENKHACH]' , "Gồm TITLE + LASTNAME + FIRSTNAME  "];
    static $DEF_USER_NAME = ['[USER_NAME]' , "Gồm TITLE + LASTNAME + FIRSTNAME "];
    static $DEF_LINKTHAMDU = ['[LINKTHAMDU]' , "Link bấm vào xác nhận tham dự"];
    static $DEF_EVENT_NAME = ['[EVENT_NAME]' , "Tên sự kiện"];
    static $DEF_QRCODE = ['[QRCODE]' , "QR CODE"];

    static $DEF_ADDRESS_LOCATION = ['[LOCATION]' , "Địa điểm"];
    static $DEF_START_TIME = ['[START_TIME]' , "Thời gian bắt đầu"];
    static $DEF_END_TIME = ['[END_TIME]' , "Thời gian kết thúc"];

    static $DEF_CONFIRM_EMAIL = ['[CONFIRM_EMAIL]' , "Link xác nhận Email"];

    static $DEF_REG_LINK_OLD = ['[REG_LINK]' , "Link xác nhận Email (cũ) - không sử dụng nữa"];

    static $DEF_EXT1= ['[EXT1]' , "Trường mở rộng thông tin User theo Sự kiện, 5 trường, từ EXT1->EXT5"];
    static $DEF_EXT2= ['[EXT2]' , "Trường mở rộng thông tin User theo Sự kiện, 5 trường, từ EXT1->EXT5"];
    static $DEF_EXT3= ['[EXT3]' , "Trường mở rộng thông tin User theo Sự kiện, 5 trường, từ EXT1->EXT5"];
    static $DEF_EXT4= ['[EXT4]' , "Trường mở rộng thông tin User theo Sự kiện, 5 trường, từ EXT1->EXT5"];
    static $DEF_EXT5= ['[EXT5]' , "Trường mở rộng thông tin User theo Sự kiện, 5 trường, từ EXT1->EXT5"];


    static function replaceAllMarkText($str, $ev, $eu, $evReg = null )
    {

        $userTitle = $eu->title;
        $first_name = $eu->first_name;
        $last_name = $eu->last_name;

        $email = $eu->email;

        $linkRegister = '';
        if($evReg){
            $regCode = eth1b($evReg->id . "." . $email . "." . microtime());
            $linkRegister = "https://" . \LadLib\Common\UrlHelper1::getDomainHostName() . "/event-register/verify-email/$regCode";
            $linkRegister = "<a href='$linkRegister'>$linkRegister</a>";
        }
        $evName = $ev->name;
        $location = $ev->location;

        if($eu->language == 'en'){
            if($ev->name_en)
                $evName = $ev->name_en;
            if($ev->location_en)
                $location = $ev->location_en;
        }

        $mmReplace = [
            EventInfo::$DEF_EVENT_NAME[0]=> $evName,
            EventInfo::$DEF_ADDRESS_LOCATION[0] => "$location",
            EventInfo::$DEF_TENKHACH[0] => "$userTitle $last_name $first_name",
            EventInfo::$DEF_USER_NAME[0]=> "$userTitle $last_name $first_name",
            EventInfo::$DEF_REG_LINK_OLD[0] => $linkRegister,
            EventInfo::$DEF_CONFIRM_EMAIL[0] => $linkRegister,
            EventInfo::$DEF_START_TIME[0] => substr($ev->time_start, 0,16),
            EventInfo::$DEF_END_TIME[0] => substr($ev->time_end, 0,16),
//            EventInfo::$DEF_QRCODE[0] => $ev->getQrCode(),
        ];

        return cstring2::replaceByArray($str, $mmReplace);
    }

    function getTimeStartVn()
    {
        if($this->time_start)
            return date('H:i d/m/Y', strtotime($this->time_start));
        return '';
    }

    function getTimeEndVn()
    {
        if($this->time_end)
            return date('H:i d/m/Y', strtotime($this->time_end));
        return '';
    }

    function getLocation($lang = 'vi')
    {
        if($lang = 'en')
            return strip_tags($this->location_en ?: $this->location);
        return strip_tags($this->location);
    }

    function getName($lang = 'vi')
    {
        if($lang == 'en')
            return strip_tags($this->name_en ?: $this->name);
        return strip_tags($this->name);
    }

    static function getStaticProperties($className = null, $prefix = "DEF_")
    {
        if (!$className) {
            $className = __CLASS__;
        }
        $reflection = new ReflectionClass($className);
        $staticProperties = $reflection->getProperties(ReflectionProperty::IS_STATIC);
        $result = [];

        foreach ($staticProperties as $property) {
            if ($property->isStatic()) {
                $propertyName = $property->getName();
                if(str_starts_with($propertyName, $prefix))
                    $result[$propertyName] = $property->getValue();
            }
        }

        return $result;
    }


    static  function getStrTimeStartEnd($ev) {

        $strFullTIme = "";
        if($ev->time_start){
            $timeStart = strtotime($ev->time_start);
            $hourStart = $dateEnd = $dateStart = $hourEnd = '';
            $keyTo = __('reg_event_ncbd.to_str');
            if($ev->time_start) {
                $dateStart = date('d/m/Y', strtotime($ev->time_start));
                $hourStart = date('H:i', strtotime($ev->time_start));
            }
            if($ev->time_end) {
                $dateEnd = " " . date('d/m/Y', strtotime($ev->time_end));

                $hourEnd = " $keyTo " . date('H:i', strtotime($ev->time_end));
            }

            $keyFrom = __('reg_event_ncbd.from_str');
            $keyDateStr = __('reg_event_ncbd.date_str');
            $keyHourStr = __('reg_event_ncbd.hour_str');

            if(date('d/m/Y', strtotime($ev->time_start)) == date('d/m/Y', strtotime($ev->time_end)))
                $strFullTIme = "$keyDateStr: $keyFrom $hourStart  $hourEnd $dateStart ";
            else{
//                $hourEnd = trim($hourEnd, '-');
//                $dateEnd = trim($dateEnd, '-');
                $strFullTIme = "$keyDateStr: $keyFrom $hourStart $dateStart $hourEnd $dateEnd ";
                if(!$ev->time_end)
                    $strFullTIme = "$keyDateStr: $keyFrom  $hourStart  $dateStart ";
            }
        }
        return $strFullTIme;
    }

    static function htmlSubEventInputCheck($pEv, $listSubEv = [])
    {
        $html = '';
        $strFullTIme = \App\Models\EventInfo::getStrTimeStartEnd($pEv);
        $mmChildEv = \App\Models\EventInfo::where('parent_id', $pEv->id)->get();
        if (!$mmChildEv->count()) {
            return '';
        }
        $html .= "<div class='sub_event_zone '>";
        foreach ($mmChildEv as $evc) {

            if($evc->allow_public_reg == 0)
                continue;

            $strTime = \App\Models\EventInfo::getStrTimeStartEnd($evc);
            $html .= '<div class="sub_event_info" style="">';

            $padChecked = '';
            if($listSubEv && in_array($evc->id, $listSubEv))
                $padChecked = 'checked';
            $html .= "<table><tr><td style='' class='first_td'>";
                $html .= '<input '.$padChecked.' type="checkbox" class="check_sub_event" name="sub_event_' . $evc->id . '" id="sub_event_' . $evc->id . '">';
            $html .= "</td>";
            $html .= "<td style=''>";
                $html .= ' <label style=""> <b>' .  htmlspecialchars($evc->name) . ' </b> </label>';

                if($strTime)
                    $html .= '<div class="mt-2">' . " <i class='iconx fa '></i> &#9656; " . __('reg_event_ncbd.time_str') . ': ' . $strTime ?: $strFullTIme ."</div>";
                if ($evc->location) {
                    $html .= '<div class="mt-1">'.' <i class="iconx fa "></i>  &#9656; ' . __('reg_event_ncbd.location') . ': ' . htmlspecialchars($evc->location) ."</div>";
                }

            $html .= "</td></tr></table>";

            $html .= '</div>';
        }
        $html .= "</div>";

        return $html;
    }


    static function getEventIdListInDeparmentOfUser($uid, $getObj = 0)
    {
        $email = getCurrentUserEmail();
        //Timf Department cua User
        $depUser = DepartmentUser::where('user_id', $uid)->first();
        if (!$depUser || !$depUser->department_id) {
            return [];
            loi("$email, Bạn không thuộc phòng ban nào?");
        }
        $dep = Department::find($depUser->department_id);
        if (!$dep) {
            return [];
            loi("$email, Phòng ban không tồn tại: $depUser->department_id");
        }
        if($getObj)
            return EventInfo::where('department', $dep->id)->get();

        return $eventIdList = EventInfo::where('department', $dep->id)->pluck('id')->toArray();
    }

    static function getHtmlSelectEvent($linkOpt, $mmEv = null, $key = '_event_id_')
    {

        if(!$mmEv){
            $mmEv = EventInfo::latest()->get();
        }

        echo "<select onchange='location.href=this.value || \"$linkOpt\"' class='select_event1 form-control form-control-sm bg-light text-primary mb-3' style='color: royalblue!important; font-weight: bold'>";
        echo "\n <option value=''> -- Tất cả Sự Kiện -- </option>";
        foreach ($mmEv as $ev) {
            $evId = $ev->getId();
            if(request()->input($key) == $evId)
                echo "\n <option value='$linkOpt?$key=$evId' selected> ($evId) $ev->name </option>";
            else
                echo "\n <option value='$linkOpt?$key=$evId'> ($evId) $ev->name </option>";
        }
        echo "\n </select>";
    }

    static function getDepartmentIdOfUser($uid, $getObj = 0)
    {
        //Timf Department cua User
        $depUser = DepartmentUser::where('user_id', $uid)->first();
        if (!$depUser || !$depUser->department_id) {
//            loi("$email, Bạn không thuộc phòng ban nào?");
            return null;
        }
        $dep = Department::find($depUser->department_id);
        if (!$dep) {
//            loi("$email, Phòng ban không tồn tại: $depUser->department_id");
            return null;
        }

        if($getObj)
            return $dep;
        return $dep->id;
    }

    function getAdminEmail()
    {
//        return $this->department;
        return User::find($this->user_id)?->email ?? '';
    }

    function getDepartmentName()
    {
//        return $this->department;
        return Department::find($this->department)?->name ?? '';
    }

    static function getListCodeReplace($toArray = 0)
    {
        $arr = self::getStaticProperties(__CLASS__);
        echo "<table class='glx03'>";

        $ret = [];


        foreach ($arr as $key => $value) {
            if(str_starts_with($key, 'DEF_')) {
                echo "\n <tr>";
                echo "\n <td>";
                echo "{$value[0]}";
                echo "\n </td>";
                    echo "\n <td>";
                    echo "{$value[1]}";
                    echo "\n </td>";
                echo "\n <tr>";
                if($toArray)
                    $ret[$value[0]] = $value[1];
            }
//            echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
//            print_r($value);
//            echo "</pre>";
        }
        echo "\n</table> ";
        if($toArray)
            return $ret;
    }

    public function getValidateRuleInsert()
    {
        return [
//            'user_id' => 'required|integer|unique:'.$this->getTable(),
            'sms_content1' => 'sometimes|min:0|max:800',
            'sms_content2' => 'sometimes|min:0|max:800',
            'sms_content3' => 'sometimes|min:0|max:800',
            'sms_content4' => 'sometimes|min:0|max:800',
//            'phone_number' => 'sometimes|numeric|digits_between:10,11',
//            'last_name' => 'sometimes|min:1|max:100|nullable',
        ];
    }

    public function getValidateRuleUpdate($id = null)
    {
        $mm = $this->getValidateRuleInsert();
//        $mm['user_id'] = 'required|integer|unique:'.$this->getTable().",user_id,$id";
        //'username'=>'sometimes|required|regex:/\w*$/|alpha_dash|regex:/\w*$/|max:50|min:6|unique:users,username,'.$id,

        return $mm;
    }


    static function checkEventBelongUser($id, $class)
    {
        if (Helper1::isMemberModule()) {
            $id = intval($id ?? 0);

            $obj = $class::find($id);
            if(!$obj){
                die("Not found $class! $id");
            }
            $eventId = $obj->event_id;
            //Xem su kien thuoc phong ban nao:
            $event = EventInfo::find($eventId);
            if(!$event){
                die("Not found event! $eventId");
            }
            $email = getCurrentUserEmail();

            $depId = EventInfo::getDepartmentIdOfUser(getCurrentUserId());
            //Neu su kien khong thuoc phong ban cua User thi bao loi:
            if ($event->department != $depId) {
                die("$email, Dữ liệu không thuộc quyền của bạn (Event: $eventId)!");
            }
        }

    }


}
