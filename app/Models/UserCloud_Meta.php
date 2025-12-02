<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Meta;

class UserCloud_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/user-cloud';

    protected static $web_url_admin = '/admin/user-cloud';

    public static $disableAddItem = 0;

    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();

        if ($field == 'id') {
            //$objMeta->join_func = 'App\Models\FileUpload_Meta::joinIdFileUpload';
        }

        if ($field == 'quota_size') {
            $objMeta->dataType = DEF_DATA_TYPE_HTML_SELECT_OPTION;
        }

        return $objMeta;

    }

    function executeBeforeIndex($params = null)
    {

        //Lay tat ca id trong bang users, model Users:
        $mm = User::all()->pluck('id');
        foreach ($mm AS $uid){
            UserCloud::getOrCreateNewUserCloud($uid);
        }

    }

    public function _user_id($obj, $val)
    {
        $user = User::find($val);
        if ($user) {
            return " <div style='font-size: small; padding: 3px'> $user->email </div> ";
        }
    }

    public function getFullSearchJoinField()
    {
        return [
            'users.email'  => "equal",
        ];
    }



    //...
    function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        return $x->leftJoin('users', 'user_id', '=', 'users.id')
            ->addSelect([
                'users.email AS _email',
            ]);
    }

    function _email($obj, $val, $field)
    {
        return $val;
    }

    public function _quota_size($obj, $value)
    {
        $mm = [0 => ' -- Chọn Quota --',
            1 * _MB => ' 1 MB',
            10 * _MB => ' 10 MB',
            100 * _MB => ' 100 MB',
            200 * _MB => ' 200 MB',
            500 * _MB => ' 500 MB',
            1000 * _MB => ' 1 GB',
            1000 * 2 * _MB => ' 2 GB',
            1000 * 5 * _MB => ' 5 GB',
            1000 * 10 * _MB => ' 10 GB',
            1000 * 20 * _MB => ' 20 GB',
            1000 * 50 * _MB => ' 50 GB',
            1000 * 100 * _MB => ' 100 GB',
            1000 * 110 * _MB => ' 110 GB',
            1000 * 120 * _MB => ' 120 GB',
            1000 * 150 * _MB => ' 150 GB',
            1000 * 200 * _MB => ' 200 GB',
            1000 * 500 * _MB => ' 500 GB',
            1000 * 1024 * _MB => ' 1 TB',
            1000 * 1024 * 2 * _MB => ' 2 TB',
            1000 * 1024 * 5 * _MB => ' 5 TB',
            1000 * 1024 * 8 * _MB => ' 8 TB',
            1000 * 1024 * 10 * _MB => ' 10 TB',
            1000 * 1024 * 20 * _MB => ' 20 TB',
            1000 * 1024 * 50 * _MB => ' 50 TB',
            1000 * 1024 * 100 * _MB => ' 100 TB',
            1000 * 1024 * 110 * _MB => ' 110 TB',
            1000 * 1024 * 120 * _MB => ' 120 TB',
            1000 * 1024 * 150 * _MB => ' 150 TB',
            1000 * 1024 * 200 * _MB => ' 200 TB',
        ];

        if ($obj) {
            if (isset($mm[$value]) && $value) {
                return [$value => $mm[$value]];
            } else {
                return null;
            }
        }

        return $mm;
    }

    public function getNeedIndexFieldDb()
    {
        return ['user_id', 'deleted_at'];
    }
}
