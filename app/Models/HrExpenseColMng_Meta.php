<?php

namespace App\Models;

use LadLib\Common\Database\MetaOfTableInDb;
use LadLib\Common\UrlHelper1;

/**
 * ABC123
 *
 * @param  null  $objData
 */
class HrExpenseColMng_Meta extends MetaOfTableInDb
{
    protected static $api_url_admin = '/api/hr-expense-col-mng';

    protected static $web_url_admin = '/admin/hr-expense-col-mng';

    protected static $api_url_member = '/api/member-hr-expense-col-mng';

    protected static $web_url_member = '/member/hr-expense-col-mng';

    //public static $folderParentClass = HrExpenseColMngFolderTbl::class;
    public static $modelClass = HrExpenseColMng::class;

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
            //HrExpenseColMng edit, tag sẽ ko update được?
            $objMeta->join_relation_func = 'joinTags';
            $objMeta->join_api = '/api/tags/search';
            $objMeta->dataType = DEF_DATA_TYPE_ARRAY_NUMBER;
        }

        return $objMeta;
    }

    public function extraContentIndex1($v1 = null, $v2 = null, $v3 = null)
    {
        $time = microtime(1);
        $k = $this->getSearchKeyField('org_id');
        $mOrg = HrOrgTree::get();

        $x = request($k);
        $link = null;
        echo "<div class='mb-1 select_org'> <button class='btn btn-info btn-sm mx-1' > Chọn nhánh: </button> ";
        foreach ($mOrg as $og) {
            $pad = 'btn-outline-info';
            if ($x == $og->id) {
                $pad = 'btn-info';
            }
            //$link = "/admin/hr-config-session-org-id-salary?$k=$og->id";
            $link = UrlHelper1::setUrlParam($link, $k, $og->id);
            echo "\n <a title='$og->name' class='btn $pad btn-sm mx-1  text-left' href='$link'> ($og->id) $og->name  </a> ";
        }
        echo '</div>';

        //        echo "<br/>\n DTIME = " . (microtime(1) - $time);
    }

    public function executeBeforeIndex($param = null)
    {

        $k = $this->getSearchKeyField('org_id');
        if ($orgId = request($k)) {
            if (! HrOrgTree::find($orgId)) {
                bl("Not found Orgid: $orgId");

                return -1;
            }
        } else {
            return -1;
        }

        $m1 = HrUserExpense::getArrayFieldList();
        //        echo "<pre> >>> " . __FILE__ . "(" . __LINE__ . ")<br/>";
        //        dump($m1);
        //        echo "</pre>";
        foreach ($m1 as $field) {
            if (str_starts_with($field, 'num')) {
                if (! HrExpenseColMng::where('field', $field)->where('org_id', $orgId)->first()) {
                    $obj = new HrExpenseColMng();
                    $obj->field = $field;
                    $obj->name = $field;
                    $obj->status = 1;
                    $obj->org_id = $orgId;
                    $obj->save();
                }
            }
        }

        return 1;
    }

    //...
}
