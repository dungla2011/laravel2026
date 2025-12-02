<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;

class FolderFile_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/admin-folder-file';

    protected static $api_url_member = '/api/member-folder-file';

    protected static $web_url_admin = '/admin/folder-file';

    protected static $web_url_member = '/member/folder-file';
    public static $titleMeta = "Danh sách Folders";
    protected static $tree_view = 'admin.folder-file.tree';

    public static $folderParentClass = FolderFile::class;

    //    protected static $index_view_member = "";

    //Hình như chưa dùng hàm này, mà chỉ dựa vào /member , api/member để xly quyền UID
    public function setBelongUserId()
    {
        return 1;
    }

    public function isUseRandId()
    {
        return SiteMng::isEncodeIdCloud();
    }

    public function afterInsertApi($obj, $get = null, $post = null)
    {
        if($obj instanceof FolderFile){
            if($obj->hasField('link1'))
                if(!$obj->link1){
                    $obj->link1 = eth1b($obj->id);
                    $obj->save();
                }
        }
    }

    public function getHardCodeMetaObj($field)
    {

        $objMeta = new MetaOfTableInDb();

        //Riêng Data type của Field, Lấy ra các field datatype mặc định
        //Nếu có thay đổi sẽ SET bên dưới
        $objSetDefault = new MetaOfTableInDb();
        $objSetDefault->setDefaultMetaTypeField($field);

        $objMeta->dataType = $objSetDefault->dataType;

        if ($field == 'parent_id') {
            $objMeta->dataType = DEF_DATA_TYPE_TREE_SELECT;
            $objMeta->join_api = '/api/member-folder-file';
            //            $objMeta->join_func = 'App\Models\DemoFolderTbl::joinFuncPathNameFullTree';
        }


        return $objMeta;
    }
    public function getNeedIndexFieldDb()
    {
        return ['user_id', 'link1', 'parent_id' ,'ide__' ];
    }

    public static  function templateNodeFolderFile($folderId, $name, $isRoot = 0)
    {
        $skey = \App\Models\FileUpload_Meta::getSearchKeyFromField('parent_id');

        $link = "/member/file?seoby_s2=C&$skey=$folderId";
        if($isRoot)
            $link = "/member/file";

        if(!$isRoot)
            $str = '<div data-tree-node-id="'.$folderId.'" class="x btn btn-default btn-sm my-1 mr-2 real_node_item  one_item_folder text-left">
    <span class="menu_one_node"><i title="Menu" class="fas fa-bars text-primary "></i>
    </span>
    <a title="Browse: '.$name.'" href="'.$link.'">
    <i class="fa fa-folder text-primary"></i> <span class="ml-1 node_name ">'.$name.'</span> </a>
    </div>';
        else{
            $str = '<div style="display: inline-block;" data-tree-node-id="'.$folderId.'" class="my-1 text-left">
            <span class="menu_one_node"><i title="Menu" class="fas fa-bars text-primary "></i>
            </span>
            <a title="Browse: '.$name.'" href="'.$link.'">
            <i class="fa fa-folder text-primary"></i> <span class="ml-1 node_name ">'.$name.'</span> </a>
            </div>';
        }



        return $str;
    }
}
