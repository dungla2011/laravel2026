<?php

namespace App\Models;

use App\Components\Helper1;
use LadLib\Common\Database\MetaOfTableInDb;

class VpsInstance_Meta extends MetaOfTableInDb
{
    public static $folderParentClass = VpsInstance::class;

    public static $modelClass = VpsInstance::class;

    public function getHardCodeMetaObj($field) {

        $objMeta = new MetaOfTableInDb();
        if($field == 'log' || $field == 'note' || $field == 'comment' ) {
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



    function _name($obj, $val, $field)
    {
        $vpsUsage = VpsUsage::where('instance_id', $obj->id)->orderBy('id', "DESC")->first();

        $consoleText = '';
//        if($obj->power_state == 'POWERED_ON')

            $consoleText = "<a href='/_site/hosting_site/console/?instance_id=$obj->id' target='_blank'> <button type='button' class='my-2 mx-2 btn btn-sm btn-outline-primary'> <i class='fa fa-desktop' aria-hidden='true'></i>
 Điều khiển Console </button> </a>";



        $ipList = '';
        if($vpsUsage){

                if(!$vpsUsage->last_host_ip){
                    $consoleText = '';
                }

            $ips = $vpsUsage->list_ip_address ?? '';
            $ips = str_replace(",", "<br>", $ips);
            //Delta Time with time()

            $lastFoundIP = $vpsUsage->last_found_ip;

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

            $ipList = " <div style='color: $color; font-size: smaller; margin-left: 10px'> IP:<span> $ips  </span>| Update $display</div> $consoleText";
        }

        if(!Helper1::isAdminModule())
            return $ipList;

        $ret = "$obj->_email UID: $obj->user_id";

        $md5 = '';
        if(Helper1::isAdminModule())
            $md5 = substr(md5($obj->bios_uuid), -8);


        return "<div style='color: transparent'> P$md5</div> <div class='m-2 mx-3'> $ret </div> $ipList";

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
