<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrOrgTree_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-org-tree';

    protected static $web_url_admin = '/admin/hr-org-tree';

    protected static $api_url_member = '/api/member-hr-org-tree';

    protected static $web_url_member = '/member/hr-org-tree';

    public static $folderParentClass = HrOrgTree::class;

    public static $modelClass = HrOrgTree::class;

    public static $userArrayAdminTree = null;

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
            //HrOrgTree edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public static function FGetArrayTreeWithAdminUid($uid)
    {
        $mRet = [$uid];
        $mRet[$uid] = [];
        $mm = HrOrgTree::all();
        //List cây con của tree này
        foreach ($mm as $obj) {
            $mRet[$uid][] = $obj->id;
        }

        return $mRet;
    }

    public static function FGetArrayUserManageTree()
    {

        //Tìm tất cả các user có quyền Mng
        $mRet = [];
        $mm = HrEmployee::where('admin_this_tree', 1)->get();
        foreach ($mm as $hrEmp) {
            //Tìm Tree chứa UID này
            $oneNode = HrOrgTree::where('id', $hrEmp->parent_id)->first();
            if (! $oneNode) {
                continue;
            }
            //List cây con của tree này
            if ($oneNode instanceof HrOrgTree);
            $m1 = $oneNode->getAllTreeDeep($oneNode->id);
            //            echo "<br/>\n  ---- $hrEmp->user_id ";
            $mRet[$hrEmp->user_id] = [];
            foreach ($m1 as $obj) {
                $mRet[$hrEmp->user_id][] = $obj['id'];
            }
        }

        self::$userArrayAdminTree = $mRet;

        //        dump($mRet);
        return $mRet;
    }

    //Kiểm tra 1 user có thuộc cây quản lý của user khác hay ko:
    public static function FCheckUserIdBelongMngOfUserid($userId, $uidParent)
    {
        //Tìm cây của userid:
        $hrEmp = HrEmployee::where('user_id', $userId)->first();
        if (! $hrEmp) {
            return 0;
        }
        $pidOfEmp = $hrEmp->parent_id;
        $mUidAndTree = self::FGetArrayUserManageTree();

        if ($mUidAndTree) {
            foreach ($mUidAndTree as $uidMng => $mTreeId) {
                if ($uidMng == $uidParent && in_array($pidOfEmp, $mTreeId)) {
                    return 1;
                }
            }
        }

        return 0;
    }

    //OrgTree thuộc User này hay không:
    public static function FCheckTreeBelongUidMng($userId, $idTree)
    {
        $mUidAndTree = self::FGetArrayUserManageTree();
        foreach ($mUidAndTree as $uid => $mIdTree) {
            if ($uid == $userId && in_array($idTree, $mIdTree)) {
                return 1;
            }
        }

        return 0;
    }

    //...
}
