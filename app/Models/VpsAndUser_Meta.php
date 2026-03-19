<?php

namespace App\Models;
use App\Components\Helper1;
use LadLib\Common\cstring2;
use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

class VpsAndUser_Meta extends MetaOfTableInDb
{
    public static $modelClass = \App\Models\VpsAndUser::class;
    public static $modelName = 'VpsAndUser';

//    public static $disableAddItem = 1;
//    public static $disableSaveAllButton = 1;



    public function getHardCodeMetaObj($field) {
        $objMeta = new MetaOfTableInDb();
        if($field == 'log' || $field == 'note' || $field == 'comment' ) {
            $objMeta->dataType = DEF_DATA_TYPE_TEXT_AREA;
        }

        if ($field == 'user_id_vendor') {
            $objMeta->join_api_field = 'email';
            //            $objMeta->join_func = 'joinUserEmailUserId';
            $objMeta->join_api = '/api/user/search';
        }

//        if(!$objMeta->dataType)
//            return null;

        return $objMeta;
    }

    function getMapJoinFieldAlias()
    {
        return [
            '_email' => 'users.email',
            '_name' => 'vps_instances.name',
        ];
    }

    public function getFullSearchJoinField()
    {
        return [
            'users.email'  => "like",
            'vps_instances.name'  => "like",
        ];
    }

    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        if(Helper1::isAdminModule())
        return $x->leftJoin('vps_instances', 'instance_id', '=', 'vps_instances.id')
            ->leftJoin('users', 'vps_instances.user_id', '=', 'users.id')
            ->addSelect([
                'users.email AS _email',
                'vps_instances.name AS _name',
            ]);
    }

    function _name($obj, $val, $field)
    {
        return $obj->_name;
        // $instance = "<a target='_blank' href='/admin/vps-instance/edit/$obj->instance_id'> Instance </a>";
        // return "$instance | $obj->_email";
    }
    function _email($obj, $val, $field)
    {
        return "$val";
    }
    public function executeBeforeIndex($param = null)
    {
        // Get all VpsInstance IDs
        $allVpsIds = VpsInstance::whereNull('deleted_at')->pluck('id')->toArray();

        // Find VpsInstance IDs that already have records in vps_and_users
        $existingInstanceIds = VpsAndUser::whereNull('deleted_at')->distinct()->pluck('instance_id')->toArray();

        // Find missing instance IDs (VPS instances without any user association)
        $missingIds = array_diff($allVpsIds, $existingInstanceIds);

        // Add records for missing IDs with null user_id
        foreach ($missingIds as $instanceId) {
            VpsAndUser::create([
                'instance_id' => $instanceId,
                'user_id_vendor' => 1,
                // 'role' => VpsAndUser::ROLE_OWNER,
            ]);
        }
    }

    function _user_id_vendor($obj, $val, $field)
    {
        return  User_Meta::search_user_email($obj, $val, $field);
//        $user = User::find($val);
//        if($user)
//            return " <div style='font-size: small; margin-left: 10px'> $user->email </div> ";
    }


}
