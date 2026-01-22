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

        if(!$objMeta->dataType)
            return null;
        return $objMeta;
    }

    function _name($obj, $val, $field)
    {
        $md5 = substr(md5($obj->bios_uuid), -8);
        return "<span style='color: transparent'> P$md5</span>";
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
        return [
            'users.email'  => "like",
        ];
    }

    function _user_id($obj, $val)
    {
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
            .input_value_to_post.readonly.name{
                min-width: 250px;
            }
        </style>
<?php
    }

}
