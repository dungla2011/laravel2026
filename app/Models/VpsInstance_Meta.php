<?php

namespace App\Models;

use App\Components\Helper1;
use App\Components\HtmlTableRenderer;
use LadLib\Common\Database\MetaOfTableInDb;

class VpsInstance_Meta extends MetaOfTableInDb
{
    public static $folderParentClass = VpsInstance::class;

    public static $modelClass = VpsInstance::class;

    public static $disableAddItem = true;

    public function getHardCodeMetaObj($field) {

        $objMeta = new MetaOfTableInDb();
        if($field == 'log' || $field == 'note' || $field == 'comment' ||$field == 'user_comment' || $field == 'create_vps_progress1') {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }
        if ($field == 'image_list')
            $objMeta->dataType = DEF_DATA_TYPE_IS_MULTI_IMAGE_BROWSE;


        if($field == 'content' ) {
            $objMeta->dataType = DEF_DATA_TYPE_RICH_TEXT;
        }
        if($field == 'status'){
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if($field == 'type' || $field == 'create_status'){
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        if($field == 'init_os'){
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }
        if ($field == 'user_id') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/user/search';
        }

//        if(!$objMeta->dataType)
//            return null;

        return $objMeta;
    }

    public function extraHtmlIncludeEdit0($obj = null, $v2 = null, $v3 = null){

        if(!$obj)
            return;
        $obj1 = $obj->find($obj->id);
        if(!$obj1)
            return;
//        echo "<br/>\n $obj1->created_at " . nowyh(time() - 10000);
        if($obj1->create_status == 'vps_new_create' || $obj1->create_status == 'vps_creating' )
            echo "<div class='mb-3 p-2 px-3 bg-primary'> Sau khi VPS tạo xong, một email thông tin đăng nhập VPS sẽ được gửi đến bạn! F5 để cập nhật trạng thái Tạo VPS - xem bên dưới</div>";
        if($obj1->create_status == 'vps_create_done' && $obj1->created_at > nowyh(time() - 3600 * 24 * 3)){
            echo "<div class='mb-3 p-2 px-3 bg-primary'> VPS đã tạo xong, Bạn vui lòng xem email để có thông tin truy cập! </div>";
            echo "<div class='mb-3 p-2 px-3 bg-orange' style='color: white!important;'>Bạn nên đổi mật khẩu được cấp ban đầu để bảo mật VPS!</div>";
        }
        if($obj1->create_status == 'vps_create_done'){

        }
        if($obj1->create_status == 'vps_create_error'){
            echo "<div class='mb-3 p-2 px-3 bg-orange' style='color: white!important;'> Có lỗi tạo VPS vui lòng liên hệ admin, mã số VPS: $obj->id !</div>";
        }

    }


    public function extraHtmlIncludeEdit1($v1 = null, $v2 = null, $v3 = null)
    {
        $v2 = $v1->find($v1->id);
        if(!$v2)
            return;
        $status =json_decode($v2->create_vps_progress);
        // echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        // print_r($status);
        // echo "</pre>";

        $renderer = new HtmlTableRenderer('Trạng thái Khởi tạo VPS');
        $renderer->render($status);

    }

    public function extraContentIndexButton2($v1 = null, $v2 = null, $v3 = null)
    {
        ?>
        <a href="/service/cloud-vps">
        <button class="float-right mt-2 ml-3 btn btn-sm btn-primary"> <i class="fa fa-plus"></i> Tạo </button>
        </a>
        <?php
    }

    function _name($obj, $val, $field)
    {
//        if(isAdminCookie())
//            return "11111";

        $vpsUsage = VpsUsage::where('instance_id', $obj->id)->orderBy('id', "DESC")->first();

        $vpsInDB = VpsInstance::find($obj->id);

        if(!$vpsInDB)
            return;

        $consoleText = '';
//        if($obj->power_state == 'POWERED_ON')
        if($vpsUsage && ($vpsInDB->create_status == 'vps_create_done' || !$vpsInDB->create_status))
                $consoleText = " <a href='/_site/hosting_site/console/?instance_id=$obj->id' target='_blank'> <button type='button' class='my-2 mx-2 btn btn-sm btn-outline-primary'> <i class='fa fa-desktop' aria-hidden='true'></i>
 Điều khiển Console </button> </a>";

        $vpsOs = VpsOsVersion::find($vpsInDB->init_os ?? '');
        $username = '';
        if($vpsOs)
        if($vpsOs->username){
            $username = $vpsOs->username;
        }

        $ipList = "IP Khởi tạo: $vpsInDB->init_ip";
        if($vpsUsage){

            $ips = $vpsUsage->list_ip_address ?? '';
            $ips = str_replace(",", "<br>", $ips);
            //Delta Time with time()

            $lastFoundIP = $vpsUsage->last_found_ip ?? '';

            $dtime = time() - strtotime($lastFoundIP);
            $minute = floor($dtime / 60);
            $hour = floor($minute / 60);
            $day = floor($hour / 24);

            // Determine color based on freshness
            if ($minute < 5) {
                $color = "green";
            } elseif ($hour < 24) {
                $color = "orange";
            } else {
                $color = "red";
            }

            // Format time display

            if ($minute < 60) {
                $display = "$minute phút trước";
            } elseif ($hour < 24) {
                $display = "$hour giờ trước";
            } else{
                $display = "$day ngày trước";
            }
            if(!$lastFoundIP)
                $display = '';

            if(!$ips){
                $ips = " Khởi tạo: $vpsInDB->init_ip ";
            }

            $ipList = " <div style='color: $color; font-size: smaller; margin-left: 10px'> IP:<span> $ips  </span>| Update $display</div> ";

        }

        $uinfo = '';
        if(Helper1::isAdminModule())
//            return $ipList;
//        else
            $uinfo = "$obj->_email UID: $obj->user_id";

//        $md5 = '';
        $upw = '';
        if(Helper1::getCurrentActionMethod() == 'edit')
            $upw = "$username / P".substr(md5($vpsInDB->bios_uuid), -8)."@12";

        return " <div class='m-2 mx-3'> $uinfo </div> $ipList  $consoleText <div style='color: transparent'> $upw </div>";

    }

    function _init_os($obj, $val, $field)
    {
        $mm = VpsOsVersion::where("is_active", 1)->get();

        $m1 = [];
        $m1[0] = '-Chọn-';
        foreach ($mm AS $one){
            $m1[$one->id] = $one->name;
        }

        return $m1;
    }


    function getMapJoinFieldAlias()
    {
        return [
            '_email' => 'users.email',
        ];
    }
    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {


        if(Helper1::isManagerModule()){
            $uid = getCurrentUserId();
            $x->join('vps_and_users', 'vps_instances.id', '=', 'vps_and_users.instance_id')
            ->where('vps_and_users.user_id_vendor', $uid);
        }

        if(Helper1::isAdminModule())
        return $x->leftJoin('users', 'user_id', '=', 'users.id')
            ->addSelect([
                'users.email AS _email',
            ]);
    }

    function _email($obj, $val, $field)
    {
        return "$val";
    }

    public function getFullSearchJoinField()
    {
        if(Helper1::isAdminModule())
        return [
            'users.email'  => "like",
        ];
    }

    function _image_list($obj, $val, $field){
        return Helper1::imageShow1($obj, $val, $field);
    }

    function _number_ip_address($obj, $val, $field){

        $vpsUsage = VpsUsage::where('instance_id', $obj->id)->orderBy('id', "DESC")->first();

        if($vpsUsage){
            return "<div class='m-2 text-sm'> $vpsUsage->list_ip_address </div>";
        }

    }

    function _user_id($obj, $val, $field)
    {
        return  User_Meta::search_user_email($obj, $val, $field);
//        $user = User::find($val);
//        if($user)
//            return " <div style='font-size: small; margin-left: 10px'> $user->email </div> ";
    }

    function _type($obj, $val)
    {
        $mm = [
            "" => "Chọn",
            "backup_glx" => "Backup",
            'ignore_compare_config' => "Ignore config"
        ];

        return $mm;

    }

    function _create_status($obj, $val)
    {
        $mm = [
            "" => "--Chọn--",
            "vps_new_create" => "Cần tạo mới",
            'vps_creating' => "Đang tạo",
            'vps_create_done' => "Đã tạo xong",
            'vps_create_error' => "Có lỗi tạo VPS",
        ];

        return $mm;

    }

    public function extraCssInclude()
    {
        ?>


    <style>
        .remove_item {
            display: none;
        }
        .divTable2Cell td input{
            text-align: center;
        }
        input.number_ip_address {
            display: none!important;
        }
        .input_value_to_post.name{
            min-width: 250px;
        }
    </style>


<?php
    }

}
