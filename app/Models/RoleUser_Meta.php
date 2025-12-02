<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class RoleUser_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/role-user';

    protected static $web_url_admin = '/admin/role-user';

    protected static $api_url_member = '/api/member-role-user';

    protected static $web_url_member = '/member/role-user';

    //public static $folderParentClass = RoleUserFolderTbl::class;
    public static $modelClass = RoleUser::class;

    /**
     * @return MetaOfTableInDb
     */
    public function getHardCodeMetaObj($field)
    {
        $objMeta = new MetaOfTableInDb();
        if ($field == 'status') {
            $objMeta->dataType = DEF_DATA_TYPE_STATUS;
        }
        if ($field == 'tag_list_id') {
            $objMeta->join_api_field = 'name';
            //          $objMeta->join_func = 'joinTags';
            //RoleUser edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function findRoleUsersWithoutUsers()
    {
        return \DB::table('role_user')
            ->leftJoin('users', 'role_user.user_id', '=', 'users.id')
            ->whereNull('users.id')
            ->select('role_user.*')
            ->get();
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {

        $link = UrlHelper1::getUriWithoutParam();


        $searchName = $this->getSearchKeyField('role_id');


        ?>

        <div class="row mb-2">
            <div class="col-md-12">
                <select
                    onchange="location.href = this.value;"
                    class="form-control-sm" name="" id="" style="border: 1px solid #ccc; font-size:small">
                    <option value="<?= $link ?>">--- Chọn Role ---</option>
                    <?php
                    //Lấy ra danh sách role
                    $roles = \App\Models\Role::orderBy('name', 'asc')->get();
                    foreach ($roles as $role) {

                        $selected = '';
                        if (request($searchName) == $role->id) {
                            $selected = 'selected';
                        }

                        ?>
                        <option  <?=  $selected  ?> value="<?= $link."?$searchName=$role->id" ?>"><?= $role->name ?></option>
                        <?php
                    }
                    ?>
                </select>

            </div>

        </div>

<?php

    }

    public function executeBeforeIndex($param = null)
    {
        return;
        //Tìm các role mà khônn có user nào sử dụng
        $mm = $this->findRoleUsersWithoutUsers();
        foreach ($mm as $m) {
//            RoleUser::where('id', $m->id)->delete();
//            echo " $m->id, ";
        }

    }

    public function getSqlOrJoinExtraIndex(\Illuminate\Database\Eloquent\Builder &$x = null, $getSelect = 0)
    {
        //Join với bảng users và bảng roles,  để lấy thông tin user và role
        return $x->leftJoin('users', 'users.id', '=', 'role_user.user_id')
            ->leftJoin('roles', 'roles.id', '=', 'role_user.role_id')
            ->addSelect('users.name as _user_name')
            ->addSelect('users.email as _email')
            ->addSelect('roles.name as _role_name');
    }
    public function getFullSearchJoinField()
    {
        return [
            'users.name'  => "like",
            'users.email'  => "like",
            'roles.name'  => "like",
        ];
    }

    function _user_name($obj, $val)
    {
        return $val;
    }

    function _email($obj, $val)
    {
        return $val;
    }

    function _role_name($obj, $val)
    {
        return $val;
    }


    //...
}
